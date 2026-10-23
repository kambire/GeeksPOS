<?php
// Banco de pruebas de la librería SIFEN (sin base de datos): XML contra el XSD oficial v150,
// firma XMLDSig (suelta y dentro del sobre SOAP), QR, eventos, lote y conexión TLS con
// sifen-test usando un certificado autofirmado (debe ser rechazado). Solo por consola:
//   docker compose exec app php application/third_party/sifen/test_sifen.php
if (PHP_SAPI !== 'cli') {
    exit('Solo CLI');
}
define('BASEPATH', dirname(__DIR__, 3) . '/system/');
define('APPPATH', dirname(__DIR__, 2) . '/');
define('FCPATH', dirname(__DIR__, 3) . '/');
function &get_instance() { static $ci; $ci = new stdClass(); return $ci; }
function log_message($l, $m) { echo "   [log] $m\n"; }
require APPPATH . 'libraries/Sifen.php';

$fallos = 0;
function check($ok, $msg) { global $fallos; echo ($ok ? "  OK   " : "  FALLA ") . $msg . "\n"; if (!$ok) $fallos++; }

/** Valida $xml contra un XSD; imprime los errores si falla. */
function valida_xsd($xml, $xsd)
{
    libxml_use_internal_errors(true);
    libxml_clear_errors();
    $d = new DOMDocument();
    $d->loadXML($xml);
    $ok = $d->schemaValidate($xsd);
    if (!$ok) foreach (libxml_get_errors() as $e) echo "        L{$e->line}: " . trim($e->message) . "\n";
    return $ok;
}

/** Verifica la firma (Reference + SignatureValue) del nodo con Id $id dentro del XML $xml, tal como lo recibiría un tercero. */
function verifica_firma($xml, $id)
{
    $d = new DOMDocument();
    $d->loadXML($xml);
    $x = new DOMXPath($d);
    $x->registerNamespace('ds', Sifen::NS_DSIG);
    $nodo = $x->query('//*[@Id="' . $id . '"]')->item(0);
    $sig = $x->query('//ds:Signature[ds:SignedInfo/ds:Reference/@URI="#' . $id . '"]')->item(0);
    if (!$nodo || !$sig) return false;
    $digest_ok = base64_encode(hash('sha256', $nodo->C14N(true, false), true)) === $x->query('.//ds:DigestValue', $sig)->item(0)->textContent;
    $pem = "-----BEGIN CERTIFICATE-----\n" . chunk_split($x->query('.//ds:X509Certificate', $sig)->item(0)->textContent, 64, "\n") . "-----END CERTIFICATE-----\n";
    $firma = base64_decode($x->query('.//ds:SignatureValue', $sig)->item(0)->textContent);
    $si = $x->query('ds:SignedInfo', $sig)->item(0);
    return $digest_ok && openssl_verify($si->C14N(true, false), $firma, openssl_pkey_get_public($pem), OPENSSL_ALGO_SHA256) === 1;
}

function sobre($body)
{
    return '<?xml version="1.0" encoding="UTF-8"?><env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope"><env:Header/><env:Body>' . $body . '</env:Body></env:Envelope>';
}

$sifen = new Sifen();
$xsd_de = APPPATH . 'third_party/sifen/xsd/siRecepDE_v150.xsd';
$xsd_ev = APPPATH . 'third_party/sifen/xsd/siRecepEvento_v150.xsd';

// --- 1. Algoritmo del QR contra el ejemplo oficial del Manual Técnico (13.8.4) ---
echo "\n[1] Hash del QR vs ejemplo oficial del manual\n";
$url = $sifen->generar_qr_url('01444444017001001001452822017012515873260988', '2017-01-25 09:35:17', '88899990',
    300000, 27272, 2, 'yzGYhUx1/XYYzksWB+fPR3Qc50c=', ['ambiente' => 'prod', 'csc_id' => '0001', 'csc_val' => 'ABCD0000000000000000000000000000']);
check(strpos($url, 'cHashQR=97ddbb3c1e7d65af03a70ffe21f2b34846ab1c89e0566c35222086766b7374ed') !== false, 'cHashQR coincide con el del manual');
check(strpos($url, 'https://ekuatia.set.gov.py/consultas/qr?') === 0, 'URL base de producción correcta');
$url_t = $sifen->generar_qr_url('01444444017001001001452822017012515873260988', '2017-01-25 09:35:17', '44444401-7',
    1, 0, 1, 'x', ['ambiente' => 'test', 'csc_id' => '0001', 'csc_val' => 'ABCD0000000000000000000000000000']);
check(strpos($url_t, 'https://ekuatia.set.gov.py/consultas-test/qr?') === 0, 'URL base de test correcta');
check(strpos($url_t, 'dRucRec=44444401&') !== false, 'RUC del receptor en el QR va sin DV');

// --- Certificado de prueba autofirmado (solo para validar la mecánica de firma y TLS) ---
$cert_rel = 'application/third_party/sifen/_test_cert.p12';
$tmp = FCPATH . $cert_rel;
$key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
$csr = openssl_csr_new(['commonName' => 'PRUEBA GEEKSPOS', 'serialNumber' => 'RUC80012345-6'], $key, ['digest_alg' => 'sha256']);
$crt = openssl_csr_sign($csr, null, $key, 30, ['digest_alg' => 'sha256']);
openssl_pkcs12_export_to_file($crt, $tmp, $key, 'clave123');

$cfg = [
    'ambiente' => 'test', 'ruc' => '80012345', 'dv' => '6', 'razon_social' => 'GEEKS S.A.', 'nombre_fantasia' => 'GeeksPOS',
    'timbrado' => '12560693', 'timbrado_fecha' => '2026-01-01', 'establecimiento' => '001', 'punto_expedicion' => '001',
    'actividad_economica' => '56101', 'actividad_descripcion' => 'Servicios de restaurantes', 'direccion' => 'Av. Mcal. López 1234',
    'telefono' => '021000000', 'email' => 'facturacion@geeks.com.py', 'departamento' => '1', 'distrito' => '1', 'ciudad' => '1',
    'csc_id' => '0001', 'csc_val' => 'ABCD0000000000000000000000000000', 'cert_file' => $cert_rel, 'cert_pass' => 'clave123'
];

$casos = [
    'Consumidor final (datos reales de la factura #32)' => [
        ['invoicedate' => '2026-10-22', 'r_time' => '12:30:00', 'total' => 100000, 'shipping' => 0],
        [
            ['pid' => 1, 'product' => 'Cheese Combo (Doble)', 'code' => 'BURG-CHEESE', 'qty' => 1, 'price' => 35000, 'tax' => 0, 'subtotal' => 35000, 'totaldiscount' => 0],
            ['pid' => 2, 'product' => 'Bronx Combo (Doble)', 'code' => 'BURG-BRONX', 'qty' => 1, 'price' => 35000, 'tax' => 0, 'subtotal' => 35000, 'totaldiscount' => 0],
            ['pid' => 41, 'product' => 'Birriamen', 'code' => 'BIRRIAMEN', 'qty' => 1, 'price' => 30000, 'tax' => 0, 'subtotal' => 30000, 'totaldiscount' => 0],
        ],
        ['name' => 'Walk-in Client', 'taxid' => null, 'docid' => null],
    ],
    'Cliente con RUC, IVA 5%, descuento y envío' => [
        ['invoicedate' => '2026-10-23', 'r_time' => '20:05:10', 'total' => 141000, 'shipping' => 10000],
        [
            ['pid' => 5, 'product' => 'Pizza Pepperoni Gigante', 'code' => 'PIZ-PEPP-G', 'qty' => 2, 'price' => 50000, 'tax' => 10, 'subtotal' => 90000, 'totaldiscount' => 10000],
            ['pid' => 9, 'product' => 'Yerba mate 500g', 'code' => 'YERBA', 'qty' => 3, 'price' => 15000, 'tax' => 5, 'subtotal' => 45000, 'totaldiscount' => 0],
        ],
        ['name' => 'Juan Pérez (Paraguay)', 'taxid' => '44444401-7', 'docid' => null],
    ],
];

$docs_lote = [];
$n = 0;
foreach ($casos as $nombre => [$inv, $items, $cust]) {
    $n++;
    echo "\n[" . ($n + 1) . "] DE: $nombre\n";
    $liq = $sifen->liquidar_factura($inv, $items);
    $t = $liq['totales'];
    $tipo = $sifen->tipo_contribuyente($cfg['ruc']);
    $cdc = $sifen->generar_cdc(1, $cfg['ruc'], $cfg['dv'], '001', '001', 100 + $n, $tipo, $inv['invoicedate']);
    $doc = $sifen->generar_xml_dte($inv, $liq, $cust, $cfg, $cdc, sprintf('001-001-%07d', 100 + $n), $tipo);
    check($sifen->firmar_documento($doc, $tmp, 'clave123'), 'firma generada');
    $digest = $sifen->calcular_digest_de($doc);
    $rec = $sifen->datos_receptor($cust);
    $qr = $sifen->generar_qr_url($cdc['cdc'], $inv['invoicedate'] . ' ' . $inv['r_time'], $rec['qr_id'], $t['tot_gral'], $t['tot_iva'], $t['items_count'], $digest, $cfg);
    $sifen->agregar_qr($doc, $qr);
    $xml = $doc->saveXML();
    $docs_lote[] = $doc;

    check(valida_xsd($xml, $xsd_de), 'XML válido contra siRecepDE_v150.xsd');
    check(verifica_firma($xml, $cdc['cdc']), 'firma del DE verifica (documento suelto)');
    $env = sobre('<rEnviDe xmlns="' . Sifen::NS_SIFEN . '"><dId>1</dId><xDE>' . $doc->saveXML($doc->documentElement) . '</xDE></rEnviDe>');
    check(verifica_firma($env, $cdc['cdc']), 'firma del DE verifica dentro del sobre SOAP rEnviDe');

    $v = new DOMDocument();
    $v->loadXML($xml);
    $x = new DOMXPath($v);
    $x->registerNamespace('s', Sifen::NS_SIFEN);
    $x->registerNamespace('ds', Sifen::NS_DSIG);
    $hijos = [];
    foreach ($v->documentElement->childNodes as $c) if ($c->nodeType === XML_ELEMENT_NODE) $hijos[] = $c->localName;
    check($hijos === ['dVerFor', 'DE', 'Signature', 'gCamFuFD'], 'rDE = dVerFor, DE, Signature, gCamFuFD');
    check(strpos($x->query('//s:dCarQR')->item(0)->textContent, 'DigestValue=' . bin2hex($x->query('//ds:DigestValue')->item(0)->textContent) . '&') !== false, 'QR usa el DigestValue real de la firma');
    check($x->query('//s:gEmis/s:dNomEmi')->item(0)->textContent === Sifen::LEYENDA_TEST, 'dNomEmi lleva la leyenda de prueba');
    check($x->query('//s:gCamItem[1]/s:dDesProSer')->item(0)->textContent === Sifen::LEYENDA_TEST, 'primer ítem lleva la leyenda de prueba');

    $ok_items = true;
    foreach ($x->query('//s:gCamItem') as $it) {
        $g = function ($q) use ($x, $it) { return (float)$x->query($q, $it)->item(0)->textContent; };
        $tasa = $g('s:gCamIVA/s:dTasaIVA');
        $tot = $g('s:gValorItem/s:gValorRestaItem/s:dTotOpeItem');
        $calc_tot = ($g('s:gValorItem/s:dPUniProSer') - $g('s:gValorItem/s:gValorRestaItem/s:dDescItem') - $g('s:gValorItem/s:gValorRestaItem/s:dDescGloItem')) * $g('s:dCantProSer');
        $base = $tasa > 0 ? $tot / (1 + $tasa / 100) : 0;
        $ok_items = $ok_items && abs($calc_tot - $tot) < 0.0001
            && abs($g('s:gCamIVA/s:dBasGravIVA') - $base) < 0.0001
            && abs($g('s:gCamIVA/s:dLiqIVAItem') - $base * $tasa / 100) < 0.0001;
    }
    check($ok_items, 'por ítem: dTotOpeItem, dBasGravIVA y dLiqIVAItem según fórmulas E7xx');
    $f = function ($n) use ($x) { return (float)$x->query("//s:gTotSub/s:$n")->item(0)->textContent; };
    check(abs($f('dTotGralOpe') - $inv['total']) < 0.0001, 'dTotGralOpe = total de la factura (' . $f('dTotGralOpe') . ')');
    check(abs($f('dTotIVA') - ($f('dIVA5') + $f('dIVA10'))) < 0.0001, 'dTotIVA = dIVA5 + dIVA10 (' . $f('dTotIVA') . ')');
}

// --- Eventos ---
$cdc_ev = '01800123456001001000010222026102312738592731';
$eventos = [
    'cancelación' => function ($g) use ($sifen, $cdc_ev) {
        $m = new ReflectionMethod($sifen, 'el');
        $m->setAccessible(true);
        $c = $m->invoke($sifen, $g, 'rGeVeCan');
        $m->invoke($sifen, $c, 'Id', $cdc_ev);
        $m->invoke($sifen, $c, 'mOtEve', 'Error en el monto facturado');
    },
    'inutilización' => function ($g) use ($sifen) {
        $m = new ReflectionMethod($sifen, 'el');
        $m->setAccessible(true);
        $i = $m->invoke($sifen, $g, 'rGeVeInu');
        foreach (['dNumTim' => '12560693', 'dEst' => '001', 'dPunExp' => '001', 'dNumIn' => '0000150', 'dNumFin' => '0000155', 'iTiDE' => '1', 'mOtEve' => 'Numeración salteada por error'] as $k => $v) {
            $m->invoke($sifen, $i, $k, $v);
        }
    },
];
$id_ev = 900;
foreach ($eventos as $nombre => $llenar) {
    $id_ev++;
    echo "\n[" . (++$n + 1) . "] Evento de $nombre\n";
    $ev = $sifen->armar_evento($id_ev, $llenar, $tmp, 'clave123');
    check($ev instanceof DOMDocument, 'evento armado y firmado');
    $xml_ev = $ev->saveXML();
    check(valida_xsd($xml_ev, $xsd_ev), 'gGroupGesEve válido contra siRecepEvento_v150.xsd');
    check(verifica_firma($xml_ev, $id_ev), 'firma de rEve verifica (evento suelto)');
    $env = sobre('<rEnviEventoDe xmlns="' . Sifen::NS_SIFEN . '"><dId>' . $id_ev . '</dId><dEvReg>' . $ev->saveXML($ev->documentElement) . '</dEvReg></rEnviEventoDe>');
    check(verifica_firma($env, $id_ev), 'firma de rEve verifica dentro del sobre rEnviEventoDe');
}

// --- Lote ---
echo "\n[" . (++$n + 1) . "] Lote asíncrono\n";
$b64 = $sifen->armar_lote($docs_lote);
$zip_path = tempnam(sys_get_temp_dir(), 'z');
file_put_contents($zip_path, base64_decode($b64, true));
$z = new ZipArchive();
check($z->open($zip_path) === true && $z->numFiles === 1 && $z->getNameIndex(0) === 'xml_file.xml', 'zip con un único xml_file.xml');
$contenido = $z->getFromName('xml_file.xml');
$z->close();
@unlink($zip_path);
$lote = new DOMDocument();
check(@$lote->loadXML($contenido) && $lote->documentElement->localName === 'rLoteDE', 'raíz rLoteDE');
$rdes = $lote->getElementsByTagNameNS(Sifen::NS_SIFEN, 'rDE');
check($rdes->length === count($docs_lote), 'contiene ' . count($docs_lote) . ' rDE');
$ok_lote = true;
foreach ($rdes as $r) {
    $suelto = new DOMDocument();
    $suelto->appendChild($suelto->importNode($r, true));
    $ok_lote = $ok_lote && valida_xsd($suelto->saveXML(), $xsd_de) && verifica_firma($contenido, $r->getElementsByTagNameNS(Sifen::NS_SIFEN, 'DE')->item(0)->getAttribute('Id'));
}
check($ok_lote, 'cada rDE del lote es válido y su firma verifica');

// --- Conexión real con sifen-test (certificado autofirmado: debe rechazarse con un mensaje claro) ---
echo "\n[" . (++$n + 1) . "] Conexión TLS con sifen-test.set.gov.py\n";
$ws = new ReflectionMethod($sifen, 'ws_llamar');
$ws->setAccessible(true);
$r = $ws->invoke($sifen, 'consulta-ruc', '<rEnviConsRUC xmlns="' . Sifen::NS_SIFEN . '"><dId>1</dId><dRUCCons>80012345</dRUCCons></rEnviConsRUC>', $cfg, 20);
echo "        HTTP {$r['http']} - {$r['error']}\n";
check(!$r['ok'] && $r['error'] !== '', 'certificado no cualificado rechazado y reportado (sin colgarse ni romper)');
check(strpos($sifen->ws_url('recibe', $cfg), 'https://sifen-test.set.gov.py/de/ws/sync/recibe.wsdl') === 0, 'endpoint de test correcto');

@unlink($tmp);
echo "\n" . ($fallos ? "$fallos verificaciones fallaron" : "Todas las verificaciones pasaron") . "\n";
exit($fallos ? 1 : 0);
