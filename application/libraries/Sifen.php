<?php
/**
 * GeeksPOS - SIFEN Paraguay Electronic Invoicing Library
 * Cumplimiento con la Dirección Nacional de Ingresos Tributarios (DNIT / SIFEN v150)
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Sifen
{
    const NS_SIFEN = 'http://ekuatia.set.gov.py/sifen/xsd';
    const NS_DSIG = 'http://www.w3.org/2000/09/xmldsig#';
    // Leyenda obligatoria en el ambiente de pruebas (Guía de Pruebas e-kuatia, DNIT)
    const LEYENDA_TEST = 'DOCUMENTO ELECTRÓNICO SIN VALOR COMERCIAL NI FISCAL - GENERADO EN AMBIENTE DE PRUEBA';

    protected $CI;
    protected $config_cache = null;

    // Tabla de departamentos (Departamentos_v141.xsd)
    protected $departamentos = [
        1 => 'CAPITAL', 2 => 'CONCEPCION', 3 => 'SAN PEDRO', 4 => 'CORDILLERA', 5 => 'GUAIRA',
        6 => 'CAAGUAZU', 7 => 'CAAZAPA', 8 => 'ITAPUA', 9 => 'MISIONES', 10 => 'PARAGUARI',
        11 => 'ALTO PARANA', 12 => 'CENTRAL', 13 => 'NEEMBUCU', 14 => 'AMAMBAY', 15 => 'PTE. HAYES',
        16 => 'BOQUERON', 17 => 'ALTO PARAGUAY', 18 => 'CANINDEYU', 19 => 'CHACO', 20 => 'NUEVA ASUNCION'
    ];

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /**
     * Obtener configuración actual de SIFEN
     */
    public function get_config()
    {
        if ($this->config_cache !== null) {
            return $this->config_cache;
        }

        if (isset($this->CI->db) && $this->CI->db) {
            $query = $this->CI->db->get('geopos_sifen_config', 1);
            if ($query && $query->num_rows() > 0) {
                $this->config_cache = $query->row_array();
                return $this->config_cache;
            }
        }

        $cfg = [
            'habilitado'           => 0,
            'ambiente'             => 'test',
            'ruc'                  => '80000000',
            'dv'                   => '1',
            'razon_social'         => 'Mi Empresa S.A.',
            'nombre_fantasia'      => 'Mi Empresa',
            'timbrado'             => '12345678',
            'timbrado_fecha'       => date('Y-01-01'),
            'establecimiento'      => '001',
            'punto_expedicion'     => '001',
            'secuencia_actual'     => 1,
            'actividad_economica'  => '56101',
            'actividad_descripcion'=> 'Servicios de restaurantes',
            'direccion'            => 'Asunción, Paraguay',
            'telefono'             => '021-000000',
            'email'                => 'facturacion@miempresa.com.py',
            'departamento'         => '1',
            'distrito'             => '1',
            'ciudad'               => '1',
            'csc_id'               => '0001',
            'csc_val'              => 'ABCD0000000000000000000000000000',
            'cert_file'            => '',
            'cert_pass'            => '',
            'auto_enviar'          => 1
        ];
        $this->config_cache = $cfg;
        return $cfg;
    }

    /**
     * Guardar configuración
     */
    public function save_config($data)
    {
        $this->CI->db->where('id', 1);
        $res = $this->CI->db->update('geopos_sifen_config', $data);
        $this->config_cache = null;
        return $res;
    }

    /**
     * Algoritmo oficial de Módulo 11 para el Dígito Verificador del RUC (Paraguay)
     */
    public function calcular_dv_ruc($ruc)
    {
        $ruc = preg_replace('/[^0-9]/', '', (string)$ruc);
        $k = 2;
        $total = 0;
        for ($i = strlen($ruc) - 1; $i >= 0; $i--) {
            $total += (int)$ruc[$i] * $k;
            $k++;
            if ($k > 11) $k = 2;
        }
        $resto = $total % 11;
        if ($resto > 1) {
            return (string)(11 - $resto);
        } else {
            return '0';
        }
    }

    /**
     * Consultar RUC o Cédula de Identidad en el Padrón Nacional DNIT / Marangatu (Paraguay)
     * Con fallback multinivel: Base Local (geopos_customers) -> API DNIT Pública (TuRuc)
     *
     * @param string $ruc_input Número de RUC o Cédula (ej: 80000001, 80000001-3, 4567890)
     * @param bool $auto_create Si es true y el cliente está en DNIT pero no local, lo crea en geopos_customers
     * @return array
     */
    public function consultar_ruc($ruc_input, $auto_create = false)
    {
        $ruc_clean = trim((string)$ruc_input);
        // Remover puntos y espacios
        $ruc_clean = str_replace(['.', ' '], '', $ruc_clean);

        if (empty($ruc_clean)) {
            return [
                'status' => 'Error',
                'found' => false,
                'message' => 'Debe ingresar un número de RUC o Cédula de Identidad.'
            ];
        }

        $partes = explode('-', $ruc_clean);
        $base_ruc = preg_replace('/[^0-9]/', '', $partes[0]);
        $dv_ingresado = isset($partes[1]) ? preg_replace('/[^0-9]/', '', $partes[1]) : null;

        if (empty($base_ruc) || strlen($base_ruc) < 3) {
            return [
                'status' => 'Error',
                'found' => false,
                'message' => 'El número ingresado es demasiado corto o no contiene dígitos válidos.'
            ];
        }

        // 1. Calcular DV oficial con algoritmo oficial Módulo 11
        $dv_oficial = $this->calcular_dv_ruc($base_ruc);
        $ruc_completo = $base_ruc . '-' . $dv_oficial;

        // 2. Nivel 1: Buscar en base de datos local (geopos_customers)
        if (isset($this->CI->db) && $this->CI->db) {
            $this->CI->db->group_start();
            $this->CI->db->where('taxid', $ruc_completo);
            $this->CI->db->or_where('taxid', $base_ruc);
            $this->CI->db->or_where('docid', $base_ruc);
            if ($dv_ingresado !== null && $dv_ingresado !== '') {
                $this->CI->db->or_where('taxid', $base_ruc . '-' . $dv_ingresado);
            }
            $this->CI->db->group_end();
            $query = $this->CI->db->get('geopos_customers', 1);

            if ($query && $query->num_rows() > 0) {
                $c = $query->row_array();
                return [
                    'status' => 'Success',
                    'found' => true,
                    'source' => 'local',
                    'customer_id' => (int)$c['id'],
                    'name' => $c['name'],
                    'company' => !empty($c['company']) ? $c['company'] : $c['name'],
                    'taxid' => !empty($c['taxid']) ? $c['taxid'] : $ruc_completo,
                    'docid' => !empty($c['docid']) ? $c['docid'] : $base_ruc,
                    'ruc' => $base_ruc,
                    'dv' => $dv_oficial,
                    'phone' => !empty($c['phone']) ? $c['phone'] : '',
                    'email' => !empty($c['email']) ? $c['email'] : '',
                    'address' => !empty($c['address']) ? $c['address'] : '',
                    'city' => !empty($c['city']) ? $c['city'] : '',
                    'discount' => isset($c['discount_c']) ? (float)$c['discount_c'] : 0,
                    'estado' => 'REGISTRADO_LOCAL',
                    'message' => 'Cliente encontrado en el sistema local.'
                ];
            }
        }

        // 3. Nivel 2: Consultar servicio público DNIT / Marangatu (TuRuc API)
        $url = 'https://turuc.com.py/api/contribuyente/' . $ruc_completo;
        $dnit_data = null;

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'GeeksPOS-Paraguay/1.0');
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code == 200 && $response) {
                $json = json_decode($response, true);
                if (isset($json['data']) && !empty($json['data']) && !empty($json['data']['razonSocial'])) {
                    $dnit_data = $json['data'];
                }
            }
        }

        // Fallback si cURL no devolvió datos y allow_url_fopen está habilitado
        if (!$dnit_data && ini_get('allow_url_fopen')) {
            $ctx = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 3,
                    'header' => "User-Agent: GeeksPOS-Paraguay/1.0\r\nAccept: application/json\r\n"
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ]);
            $response = @file_get_contents($url, false, $ctx);
            if ($response) {
                $json = json_decode($response, true);
                if (isset($json['data']) && !empty($json['data']) && !empty($json['data']['razonSocial'])) {
                    $dnit_data = $json['data'];
                }
            }
        }

        if ($dnit_data) {
            $razon_social = trim($dnit_data['razonSocial']);
            $estado = isset($dnit_data['estado']) ? $dnit_data['estado'] : 'ACTIVO';
            $es_juridica = !empty($dnit_data['esPersonaJuridica']);
            $dv_res = isset($dnit_data['dv']) ? (string)$dnit_data['dv'] : $dv_oficial;
            $ruc_oficial = $base_ruc . '-' . $dv_res;

            $cid = null;
            if ($auto_create && isset($this->CI->db) && $this->CI->db) {
                $user_loc = 0;
                if (isset($this->CI->aauth) && $this->CI->aauth->get_user()) {
                    $user_loc = (int)$this->CI->aauth->get_user()->loc;
                }
                $new_customer = [
                    'name' => $razon_social,
                    'company' => $es_juridica ? $razon_social : '',
                    'taxid' => $ruc_oficial,
                    'docid' => $base_ruc,
                    'phone' => '',
                    'email' => '',
                    'address' => 'Paraguay',
                    'city' => 'Asunción',
                    'country' => 'Paraguay',
                    'gid' => 1,
                    'loc' => $user_loc
                ];
                $this->CI->db->insert('geopos_customers', $new_customer);
                $cid = (int)$this->CI->db->insert_id();
            }

            return [
                'status' => 'Success',
                'found' => true,
                'source' => 'dnit',
                'customer_id' => $cid,
                'name' => $razon_social,
                'company' => $es_juridica ? $razon_social : '',
                'taxid' => $ruc_oficial,
                'docid' => $base_ruc,
                'ruc' => $base_ruc,
                'dv' => $dv_res,
                'estado' => $estado,
                'es_juridica' => $es_juridica,
                'phone' => '',
                'email' => '',
                'address' => '',
                'city' => '',
                'discount' => 0,
                'message' => 'Contribuyente verificado exitosamente en DNIT / Marangatu.'
            ];
        }

        // Si no está registrado en el padrón de contribuyentes de DNIT
        return [
            'status' => 'Success',
            'found' => false,
            'source' => 'dnit_unregistered',
            'customer_id' => null,
            'name' => '',
            'company' => '',
            'taxid' => $ruc_completo,
            'docid' => $base_ruc,
            'ruc' => $base_ruc,
            'dv' => $dv_oficial,
            'estado' => 'NO_CONTRIBUYENTE',
            'discount' => 0,
            'message' => 'Cédula no figura en el padrón de contribuyentes DNIT. Dígito verificador sugerido: ' . $dv_oficial . '. Puede registrarse como Consumidor Final.'
        ];
    }

    /**
     * Algoritmo oficial de Módulo 11 ponderado para el Código de Control (CDC) de 44 dígitos
     * Recibe la cadena de 43 dígitos numéricos y calcula el dígito 44.
     */
    public function calcular_dv_cdc($cadena43)
    {
        $cadena43 = preg_replace('/[^0-9]/', '', (string)$cadena43);
        if (strlen($cadena43) !== 43) {
            return 0;
        }

        $k = 2;
        $total = 0;
        // Ponderación de derecha a izquierda: 2 a 11
        for ($i = strlen($cadena43) - 1; $i >= 0; $i--) {
            $total += (int)$cadena43[$i] * $k;
            $k++;
            if ($k > 11) $k = 2;
        }

        $resto = $total % 11;
        if ($resto == 0 || $resto == 1) {
            return 0;
        } else {
            return 11 - $resto;
        }
    }

    /**
     * Generar CDC de 44 dígitos oficial de SIFEN
     */
    public function generar_cdc($tipo_doc, $ruc_emisor, $dv_emisor, $establecimiento, $punto_exp, $numero_sec, $tipo_contribuyente, $fecha_emision, $tipo_emision = 1, $codigo_seguridad = null)
    {
        // 1. Tipo de documento (2 dígitos): 01 = Factura Electrónica
        $dCodDoc = sprintf('%02d', (int)$tipo_doc);

        // 2. RUC del emisor (8 dígitos numéricos con ceros a la izquierda)
        $clean_ruc = preg_replace('/[^0-9]/', '', (string)$ruc_emisor);
        $dRucEm = sprintf('%08d', (int)$clean_ruc);

        // 3. Dígito Verificador del RUC (1 dígito)
        $dDVEmi = (string)$dv_emisor;

        // 4. Establecimiento (3 dígitos)
        $dEst = sprintf('%03d', (int)$establecimiento);

        // 5. Punto de expedición (3 dígitos)
        $dPunExp = sprintf('%03d', (int)$punto_exp);

        // 6. Número secuencial del documento (7 dígitos)
        $dNumDoc = sprintf('%07d', (int)$numero_sec);

        // 7. Tipo de contribuyente (1 dígito: 1 = Física, 2 = Jurídica)
        $dTipCont = in_array((int)$tipo_contribuyente, [1, 2]) ? (int)$tipo_contribuyente : 2;

        // 8. Fecha de emisión (YYYYMMDD) (8 dígitos)
        $dFeEmiDE = date('Ymd', strtotime($fecha_emision));

        // 9. Tipo de emisión (1 dígito: 1 = Normal, 2 = Contingencia)
        $dTipEmi = ((int)$tipo_emision === 2) ? 2 : 1;

        // 10. Código de seguridad aleatorio generado por el sistema (9 dígitos)
        if (empty($codigo_seguridad)) {
            $codigo_seguridad = sprintf('%09d', mt_rand(100000000, 999999999));
        } else {
            $codigo_seguridad = sprintf('%09d', (int)$codigo_seguridad);
        }

        // Concatenar los 43 dígitos
        $cadena43 = $dCodDoc . $dRucEm . $dDVEmi . $dEst . $dPunExp . $dNumDoc . $dTipCont . $dFeEmiDE . $dTipEmi . $codigo_seguridad;

        // 11. Calcular dígito verificador del CDC (1 dígito)
        $dDVId = $this->calcular_dv_cdc($cadena43);

        $cdc = $cadena43 . $dDVId;

        return [
            'cdc'              => $cdc,
            'cadena43'         => $cadena43,
            'dv'               => $dDVId,
            'codigo_seguridad' => $codigo_seguridad
        ];
    }

    /**
     * Formatear CDC con separadores para fácil lectura humana en el ticket KuDE
     * Ej: 0108 0000 0011 0010 0100 0000 1220 2610 2110 1234 5678
     */
    public function formatear_cdc($cdc)
    {
        $clean = preg_replace('/[^0-9]/', '', (string)$cdc);
        if (strlen($clean) !== 44) {
            return $cdc;
        }
        return trim(chunk_split($clean, 4, ' '));
    }

    /**
     * Desglose de IVA por ítem con precio IVA incluido, según el Manual Técnico v150
     * (campos E735 dBasGravIVA y E736 dLiqIVAItem):
     * - 10%: base = monto / 1,1  ; IVA = base * 10 / 100
     * - 5%:  base = monto / 1,05 ; IVA = base * 5 / 100
     * - Cualquier otra tasa: exenta (base 0, IVA 0)
     */
    public function desglosar_iva($monto, $tasa = 10)
    {
        $monto = (float)$monto;
        $tasa = (int)round((float)$tasa);

        if ($tasa === 10 || $tasa === 5) {
            $base = round($monto / (1 + $tasa / 100), 8);
            $iva = round($base * $tasa / 100, 8);
            return [
                'tasa'       => $tasa,
                'total'      => $monto,
                'base'       => $base,
                'iva'        => $iva,
                'iva_10'     => $tasa === 10 ? $iva : 0.0,
                'iva_5'      => $tasa === 5 ? $iva : 0.0,
                'gravada_10' => $tasa === 10 ? $base : 0.0,
                'gravada_5'  => $tasa === 5 ? $base : 0.0,
                'exenta'     => 0.0
            ];
        }

        return [
            'tasa'       => 0,
            'total'      => $monto,
            'base'       => 0.0,
            'iva'        => 0.0,
            'iva_10'     => 0.0,
            'iva_5'      => 0.0,
            'gravada_10' => 0.0,
            'gravada_5'  => 0.0,
            'exenta'     => $monto
        ];
    }

    /**
     * Tasa de IVA de un ítem de GeoPOS. 5 y 10 se respetan; si el producto no tiene
     * tasa cargada (0) se asume el IVA general del 10% con precio IVA incluido.
     */
    protected function tasa_iva_item($tasa)
    {
        return (abs((float)$tasa - 5) < 0.001) ? 5 : 10;
    }

    /**
     * Tipo de contribuyente (1 = persona física, 2 = persona jurídica).
     * Usa el valor explícito si existe; si no, los RUC de personas jurídicas
     * en Paraguay comienzan en 80.000.000.
     */
    public function tipo_contribuyente($ruc, $explicito = null)
    {
        if (in_array((int)$explicito, [1, 2], true)) {
            return (int)$explicito;
        }
        $num = (int)preg_replace('/[^0-9]/', '', (string)$ruc);
        return $num >= 80000000 ? 2 : 1;
    }

    /**
     * Formato numérico del XML: punto decimal, sin separador de miles,
     * hasta $decimales decimales y sin ceros sobrantes.
     */
    protected function num($valor, $decimales = 8)
    {
        $s = number_format(round((float)$valor, $decimales), $decimales, '.', '');
        if (strpos($s, '.') !== false) {
            $s = rtrim(rtrim($s, '0'), '.');
        }
        return ($s === '' || $s === '-0') ? '0' : $s;
    }

    /**
     * Generar URL del Código QR oficial para el ticket KuDE
     */
    public function generar_qr_url($cdc, $fecha_hora_iso, $ruc_receptor, $total, $total_iva, $items_count, $digest_value = '', $config = null)
    {
        if (!$config) {
            $config = $this->get_config();
        }

        // URL de consulta del QR según el Manual Técnico v150 (13.8.2)
        $base_url_qr = ($config['ambiente'] === 'prod')
            ? 'https://ekuatia.set.gov.py/consultas/qr?'
            : 'https://ekuatia.set.gov.py/consultas-test/qr?';

        $nVersion = '150';
        $Id = $cdc;
        // Fecha en formato YYYY-MM-DDTHH:MM:SS
        $dFeEmiDE = date('Y-m-d\TH:i:s', strtotime($fecha_hora_iso));
        // En SIFEN la fecha en el QR se codifica en hexadecimal UTF-8
        $dFeEmiDE_hex = bin2hex($dFeEmiDE);

        // dRucRec (RUC sin DV) o dNumIDRec (documento; "0" si es innominado)
        $rec = trim((string)$ruc_receptor);
        if (preg_match('/^(\d+)-\d$/', $rec, $m)) {
            $rec = $m[1];
        }
        $clean_rec = preg_replace('/[^0-9A-Za-z]/', '', $rec);
        if ($clean_rec === '') {
            $clean_rec = '0';
        }

        // Mismo formato que en el XML del DE (dTotGralOpe / dTotIVA)
        $dTotGralOpe = $this->num($total);
        $dTotIVA = $this->num($total_iva);
        $cItems = (int)$items_count;

        // DigestValue real de la firma del DE (XS17), convertido a hexadecimal
        $digest_hex = bin2hex((string)$digest_value);

        $IdCSC = $config['csc_id'] ?: '0001';
        $csc_secret = $config['csc_val'] ?: 'ABCD0000000000000000000000000000';

        // Cadena para generar el Hash SHA-256
        $cadena_hash = "nVersion={$nVersion}&Id={$Id}&dFeEmiDE={$dFeEmiDE_hex}&dRucRec={$clean_rec}&dTotGralOpe={$dTotGralOpe}&dTotIVA={$dTotIVA}&cItems={$cItems}&DigestValue={$digest_hex}&IdCSC={$IdCSC}";
        $cHashQR = hash('sha256', $cadena_hash . $csc_secret);

        $url_final = $base_url_qr . $cadena_hash . "&cHashQR={$cHashQR}";
        return $url_final;
    }

    /**
     * Generar imagen QR en Data URI Base64 para incrustación directa en ticket térmico KuDE
     */
    public function generar_qr_data_uri($qr_url, $size = 180)
    {
        if (empty($qr_url)) {
            return '';
        }
        try {
            require_once APPPATH . 'third_party/qrcode/vendor/autoload.php';
            $qr = \Endroid\QrCode\QrCode::create($qr_url)
                ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
                ->setSize($size)
                ->setMargin(4);
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $res = $writer->write($qr);
            return $res->getDataUri();
        } catch (\Throwable $e) {
            log_message('error', 'SIFEN QR Data URI Error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Procesar una factura en GeeksPOS con numeración y CDC de Paraguay
     */
    public function procesar_factura_sifen($invoice_id)
    {
        $config = $this->get_config();
        if (empty($config['habilitado'])) {
            return ['status' => false, 'message' => 'SIFEN no está habilitado'];
        }

        $this->CI->db->select('*');
        $this->CI->db->from('geopos_invoices');
        $this->CI->db->where('id', (int)$invoice_id);
        $invoice = $this->CI->db->get()->row_array();

        if (!$invoice) {
            return ['status' => false, 'message' => 'Factura no encontrada'];
        }

        // Si ya tiene CDC asignado, devolver los datos existentes
        if (!empty($invoice['cdc'])) {
            return [
                'status'       => true,
                'cdc'          => $invoice['cdc'],
                'sifen_numero' => $invoice['sifen_numero'],
                'sifen_qr'     => $invoice['sifen_qr']
            ];
        }

        // Obtener cliente
        $this->CI->db->select('*');
        $this->CI->db->from('geopos_customers');
        $this->CI->db->where('id', $invoice['csd']);
        $customer = $this->CI->db->get()->row_array();

        // Obtener ítems
        $this->CI->db->select('*');
        $this->CI->db->from('geopos_invoice_items');
        $this->CI->db->where('tid', (int)$invoice_id);
        $items = $this->CI->db->get()->result_array();

        // 1. Asignar secuencial atómicamente
        $this->CI->db->trans_start();

        $this->CI->db->query("UPDATE geopos_sifen_config SET secuencia_actual = secuencia_actual + 1 WHERE id = 1");
        $q_sec = $this->CI->db->query("SELECT secuencia_actual, establecimiento, punto_expedicion, timbrado, ruc, dv, razon_social FROM geopos_sifen_config WHERE id = 1")->row_array();
        $secuencia = $q_sec['secuencia_actual'] - 1;
        if ($secuencia < 1) $secuencia = 1;

        $establecimiento = $config['establecimiento'] ?: '001';
        $punto_exp = $config['punto_expedicion'] ?: '001';
        $sifen_numero = sprintf('%03d-%03d-%07d', $establecimiento, $punto_exp, $secuencia);

        // 2. Generar CDC de 44 dígitos
        $tipo_cont = $this->tipo_contribuyente($config['ruc'], $config['tipo_contribuyente'] ?? null);
        $cdc_data = $this->generar_cdc(
            1, // Factura Electrónica
            $config['ruc'],
            $config['dv'],
            $establecimiento,
            $punto_exp,
            $secuencia,
            $tipo_cont,
            $invoice['invoicedate'] ?: date('Y-m-d'),
            1 // Normal
        );
        $cdc = $cdc_data['cdc'];

        // 3. XML + firma + QR
        $dte = $this->construir_dte($invoice, $items, $customer, $config, $cdc_data, $sifen_numero, $tipo_cont);
        $t = $dte['totales'];

        // 4. Actualizar factura (montos redondeados a guaraníes para el ticket/KuDE)
        $update_data = $this->datos_factura_dte($dte) + [
            'cdc'          => $cdc,
            'sifen_numero' => $sifen_numero,
            'sifen_estado' => 'borrador'
        ];

        $this->CI->db->where('id', (int)$invoice_id);
        $this->CI->db->update('geopos_invoices', $update_data);

        $this->CI->db->trans_complete();

        $resultado = [
            'status'       => true,
            'cdc'          => $cdc,
            'cdc_formato'  => $this->formatear_cdc($cdc),
            'sifen_numero' => $sifen_numero,
            'sifen_qr'     => $dte['qr_url'],
            'firmado'      => $dte['firmado'],
            'iva_10'       => round($t['iva_10']),
            'iva_5'        => round($t['iva_5']),
            'iva_total'    => round($t['tot_iva']),
            'total'        => $invoice['total']
        ];

        // 5. Envío automático al WS síncrono si está activado. Nunca interrumpe la venta:
        //    si SIFEN no responde, la factura queda pendiente para reenviar desde el panel.
        if (!empty($config['auto_enviar']) && $dte['firmado']) {
            $envio = $this->enviar_de($invoice_id, 15);
            $resultado['sifen_envio'] = $envio;
        }

        return $resultado;
    }

    /**
     * Arma el XML del DE, lo firma (si hay certificado), agrega el QR con el
     * DigestValue real y lo guarda en userfiles/sifen/xml/.
     */
    protected function construir_dte($invoice, $items, $customer, $config, $cdc_data, $sifen_numero, $tipo_cont)
    {
        $liq = $this->liquidar_factura($invoice, $items);
        $t = $liq['totales'];
        $doc = $this->generar_xml_dte($invoice, $liq, $customer, $config, $cdc_data, $sifen_numero, $tipo_cont);

        $firmado = false;
        $cert = $this->ruta_certificado($config);
        if ($cert) {
            $firmado = $this->firmar_documento($doc, $cert, $config['cert_pass']);
        }
        $digest = $this->calcular_digest_de($doc);

        $receptor = $this->datos_receptor($customer);
        $fecha_hora = $invoice['invoicedate'] . ' ' . ($invoice['r_time'] ?: date('H:i:s'));
        $qr_url = $this->generar_qr_url($cdc_data['cdc'], $fecha_hora, $receptor['qr_id'], $t['tot_gral'], $t['tot_iva'], $t['items_count'], $digest, $config);
        $this->agregar_qr($doc, $qr_url);

        $xml_dir = FCPATH . 'userfiles/sifen/xml/';
        if (!is_dir($xml_dir)) {
            @mkdir($xml_dir, 0775, true);
        }
        $xml_path = 'userfiles/sifen/xml/DTE_' . $cdc_data['cdc'] . '.xml';
        @file_put_contents(FCPATH . $xml_path, $doc->saveXML());
        if (!$firmado) {
            log_message('error', 'SIFEN: DE ' . $cdc_data['cdc'] . ' generado sin firma (falta certificado o contraseña).');
        }

        return ['doc' => $doc, 'firmado' => $firmado, 'qr_url' => $qr_url, 'xml_path' => $xml_path, 'totales' => $t];
    }

    /**
     * Columnas de geopos_invoices que se actualizan a partir de un DTE construido
     */
    protected function datos_factura_dte($dte)
    {
        $t = $dte['totales'];
        return [
            'sifen_qr'   => $dte['qr_url'],
            'sifen_xml'  => $dte['xml_path'],
            'iva_10'     => round($t['iva_10']),
            'iva_5'      => round($t['iva_5']),
            'iva_exenta' => round($t['sub_exe']),
            'gravada_10' => round($t['base_10']),
            'gravada_5'  => round($t['base_5'])
        ];
    }

    /**
     * Vuelve a armar y firmar el XML de una factura que ya tiene CDC y número,
     * conservando ambos. Sirve para facturas generadas antes de cargar el
     * certificado o con la versión anterior de la librería. No aplica a DTE ya aprobados.
     */
    public function regenerar_xml($invoice_id)
    {
        $config = $this->get_config();
        $invoice = $this->CI->db->get_where('geopos_invoices', ['id' => (int)$invoice_id])->row_array();
        if (!$invoice || strlen((string)$invoice['cdc']) !== 44) {
            return ['status' => false, 'message' => 'La factura no tiene CDC.'];
        }
        if (in_array($invoice['sifen_estado'], ['aprobado', 'aprobado_obs', 'cancelado'], true)) {
            return ['status' => false, 'message' => 'El DTE ya fue aprobado por SIFEN; no se puede regenerar.'];
        }
        $customer = $this->CI->db->get_where('geopos_customers', ['id' => $invoice['csd']])->row_array();
        $items = $this->CI->db->get_where('geopos_invoice_items', ['tid' => (int)$invoice_id])->result_array();

        $cdc = $invoice['cdc'];
        $cdc_data = ['cdc' => $cdc, 'dv' => substr($cdc, 43, 1), 'codigo_seguridad' => substr($cdc, 34, 9)];
        $dte = $this->construir_dte($invoice, $items, $customer, $config, $cdc_data, $invoice['sifen_numero'], (int)substr($cdc, 24, 1));

        $this->CI->db->where('id', (int)$invoice_id)->update('geopos_invoices', $this->datos_factura_dte($dte));
        return ['status' => true, 'firmado' => $dte['firmado'], 'doc' => $dte['doc']];
    }

    /**
     * Normaliza los ítems de GeoPOS (y el envío) en líneas SIFEN y calcula los
     * totales del grupo F con las fórmulas del Manual Técnico v150.
     * Precios con IVA incluido; el descuento adicional de la factura que no esté
     * en los ítems se prorratea como descuento global por ítem (dDescGloItem).
     */
    public function liquidar_factura($invoice, $items)
    {
        $lineas = [];
        foreach ($items as $it) {
            $qty = (float)$it['qty'];
            if ($qty <= 0) {
                continue;
            }
            $total_linea = (float)$it['subtotal'];
            $bruto = (float)$it['price'] * $qty;
            if ($bruto >= $total_linea) {
                $precio = (float)$it['price'];
                $desc_unit = ($bruto - $total_linea) / $qty;
            } else {
                // GeoPOS sumó el impuesto aparte: el precio IVA incluido es subtotal / cantidad
                $precio = $total_linea / $qty;
                $desc_unit = 0.0;
            }
            $lineas[] = [
                'codigo'        => !empty($it['code']) ? $it['code'] : 'PROD' . $it['pid'],
                'descripcion'   => $it['product'],
                'cantidad'      => $qty,
                'precio'        => round($precio, 8),
                'desc_unit'     => round($desc_unit, 8),
                'desc_glo_unit' => 0.0,
                'tasa'          => $this->tasa_iva_item($it['tax'])
            ];
        }

        if (!empty($invoice['shipping']) && (float)$invoice['shipping'] > 0) {
            $lineas[] = [
                'codigo'        => 'ENVIO',
                'descripcion'   => 'Costo de envío',
                'cantidad'      => 1.0,
                'precio'        => round((float)$invoice['shipping'], 8),
                'desc_unit'     => 0.0,
                'desc_glo_unit' => 0.0,
                'tasa'          => 10
            ];
        }

        // Descuento de la factura no reflejado en los ítems -> descuento global prorrateado
        $suma = 0.0;
        foreach ($lineas as $l) {
            $suma += ($l['precio'] - $l['desc_unit']) * $l['cantidad'];
        }
        $desc_global = $suma - (float)$invoice['total'];
        if ($desc_global > 0.5 && $suma > 0) {
            foreach ($lineas as &$l) {
                $parte = $desc_global * (($l['precio'] - $l['desc_unit']) * $l['cantidad']) / $suma;
                $l['desc_glo_unit'] = round($parte / $l['cantidad'], 8);
            }
            unset($l);
        }

        $t = [
            'sub_exe' => 0.0, 'sub_5' => 0.0, 'sub_10' => 0.0,
            'tot_desc' => 0.0, 'tot_desc_glo' => 0.0,
            'iva_5' => 0.0, 'iva_10' => 0.0, 'base_5' => 0.0, 'base_10' => 0.0
        ];
        foreach ($lineas as &$l) {
            $l['total_bruto'] = round($l['precio'] * $l['cantidad'], 8);
            $l['total'] = round(($l['precio'] - $l['desc_unit'] - $l['desc_glo_unit']) * $l['cantidad'], 8);
            $d = $this->desglosar_iva($l['total'], $l['tasa']);
            $l['tasa'] = $d['tasa'];
            $l['base'] = $d['base'];
            $l['iva'] = $d['iva'];

            if ($d['tasa'] === 10) {
                $t['sub_10'] += $l['total'];
                $t['base_10'] += $d['base'];
                $t['iva_10'] += $d['iva'];
            } elseif ($d['tasa'] === 5) {
                $t['sub_5'] += $l['total'];
                $t['base_5'] += $d['base'];
                $t['iva_5'] += $d['iva'];
            } else {
                $t['sub_exe'] += $l['total'];
            }
            $t['tot_desc'] += $l['desc_unit'] * $l['cantidad'];
            $t['tot_desc_glo'] += $l['desc_glo_unit'] * $l['cantidad'];
        }
        unset($l);

        foreach ($t as $k => $v) {
            $t[$k] = round($v, 8);
        }
        $t['tot_ope'] = round($t['sub_exe'] + $t['sub_5'] + $t['sub_10'], 8);
        $t['desc_total'] = round($t['tot_desc'] + $t['tot_desc_glo'], 8);
        $t['redondeo'] = 0.0;
        $t['tot_gral'] = round($t['tot_ope'] - $t['redondeo'], 8);
        $t['tot_iva'] = round($t['iva_5'] + $t['iva_10'], 8);
        $t['tot_base'] = round($t['base_5'] + $t['base_10'], 8);
        $t['items_count'] = count($lineas);

        return ['lineas' => $lineas, 'totales' => $t];
    }

    /**
     * Datos del receptor según el grupo D2 del Manual Técnico:
     * contribuyente (RUC con DV), persona con cédula, o consumidor innominado.
     */
    public function datos_receptor($cust)
    {
        $cust = $cust ?: [];
        $nombre = trim((string)($cust['name'] ?? ''));
        $taxid = str_replace(['.', ' '], '', trim((string)($cust['taxid'] ?? '')));

        if ($taxid !== '' && preg_match('/^(\d{3,8})(?:-(\d))?$/', $taxid, $m)) {
            $ruc = $m[1];
            $dv = isset($m[2]) && $m[2] !== '' ? $m[2] : $this->calcular_dv_ruc($ruc);
            return [
                'tipo'      => 'ruc',
                'ruc'       => $ruc,
                'dv'        => $dv,
                'nombre'    => $nombre !== '' ? $nombre : 'Sin Nombre',
                'tipo_cont' => $this->tipo_contribuyente($ruc),
                'qr_id'     => $ruc
            ];
        }

        $docid = preg_replace('/[^0-9]/', '', (string)($cust['docid'] ?? ''));
        if ($docid !== '' && $nombre !== '') {
            return ['tipo' => 'ci', 'doc' => $docid, 'nombre' => $nombre, 'qr_id' => $docid];
        }

        return ['tipo' => 'innominado', 'doc' => '0', 'nombre' => 'Sin Nombre', 'qr_id' => '0'];
    }

    /**
     * Arma el DOM del rDE (Factura Electrónica) siguiendo la secuencia del XSD
     * siRecepDE_v150 / DE_v150. Devuelve el documento SIN firma ni QR: se agregan
     * después con firmar_documento() y agregar_qr().
     *
     * @param array $liq Resultado de liquidar_factura()
     * @param array $cdc_data Resultado de generar_cdc()
     */
    public function generar_xml_dte($inv, $liq, $cust, $cfg, $cdc_data, $sifen_num, $tipo_cont)
    {
        $es_test = ($cfg['ambiente'] ?? 'test') !== 'prod';
        $t = $liq['totales'];
        list($est, $pun, $sec) = array_pad(explode('-', $sifen_num), 3, '');
        $fecha_emi = date('Y-m-d\TH:i:s', strtotime($inv['invoicedate'] . ' ' . ($inv['r_time'] ?: date('H:i:s'))));

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;

        $rde = $doc->createElementNS(self::NS_SIFEN, 'rDE');
        $rde->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $rde->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:schemaLocation', self::NS_SIFEN . ' siRecepDE_v150.xsd');
        $doc->appendChild($rde);
        $this->el($rde, 'dVerFor', '150');

        // A. Campos firmados del DE
        $de = $this->el($rde, 'DE');
        $de->setAttribute('Id', $cdc_data['cdc']);
        $this->el($de, 'dDVId', $cdc_data['dv']);
        $this->el($de, 'dFecFirma', date('Y-m-d\TH:i:s'));
        $this->el($de, 'dSisFact', '1'); // 1 = Sistema de facturación del contribuyente

        // B. Operación del DE
        $ope = $this->el($de, 'gOpeDE');
        $this->el($ope, 'iTipEmi', '1');
        $this->el($ope, 'dDesTipEmi', 'Normal');
        $this->el($ope, 'dCodSeg', $cdc_data['codigo_seguridad']);

        // C. Timbrado
        $tim = $this->el($de, 'gTimb');
        $this->el($tim, 'iTiDE', '1');
        $this->el($tim, 'dDesTiDE', 'Factura electrónica');
        $this->el($tim, 'dNumTim', sprintf('%08d', (int)$cfg['timbrado']));
        $this->el($tim, 'dEst', sprintf('%03d', (int)$est));
        $this->el($tim, 'dPunExp', sprintf('%03d', (int)$pun));
        $this->el($tim, 'dNumDoc', sprintf('%07d', (int)$sec));
        $this->el($tim, 'dFeIniT', $cfg['timbrado_fecha'] ?: date('Y-01-01'));

        // D. Datos generales de la operación
        $gral = $this->el($de, 'gDatGralOpe');
        $this->el($gral, 'dFeEmiDE', $fecha_emi);

        $com = $this->el($gral, 'gOpeCom');
        $this->el($com, 'iTipTra', '1');
        $this->el($com, 'dDesTipTra', 'Venta de mercadería');
        $this->el($com, 'iTImp', '1');
        $this->el($com, 'dDesTImp', 'IVA');
        $this->el($com, 'cMoneOpe', 'PYG');
        $this->el($com, 'dDesMoneOpe', 'Guarani');

        // D2. Emisor
        $dep = (int)($cfg['departamento'] ?: 1);
        $emis = $this->el($gral, 'gEmis');
        $this->el($emis, 'dRucEm', preg_replace('/[^0-9]/', '', (string)$cfg['ruc']));
        $this->el($emis, 'dDVEmi', $cfg['dv']);
        $this->el($emis, 'iTipCont', (string)$tipo_cont);
        $this->el($emis, 'dNomEmi', $es_test ? self::LEYENDA_TEST : $cfg['razon_social']);
        if (!empty($cfg['nombre_fantasia'])) {
            $this->el($emis, 'dNomFanEmi', $cfg['nombre_fantasia']);
        }
        $this->el($emis, 'dDirEmi', $cfg['direccion'] ?: 'Asuncion');
        $this->el($emis, 'dNumCas', '0');
        $this->el($emis, 'cDepEmi', (string)$dep);
        $this->el($emis, 'dDesDepEmi', $this->departamentos[$dep] ?? 'CAPITAL');
        if (!empty($cfg['distrito']) && !empty($cfg['distrito_desc'])) {
            $this->el($emis, 'cDisEmi', (string)(int)$cfg['distrito']);
            $this->el($emis, 'dDesDisEmi', $cfg['distrito_desc']);
        }
        $this->el($emis, 'cCiuEmi', (string)(int)($cfg['ciudad'] ?: 1));
        $this->el($emis, 'dDesCiuEmi', $cfg['ciudad_desc'] ?? 'ASUNCION (DISTRITO)');
        $this->el($emis, 'dTelEmi', substr($cfg['telefono'] ?: '021000000', 0, 15));
        $this->el($emis, 'dEmailE', $cfg['email'] ?: 'admin@empresa.com.py');
        $act = $this->el($emis, 'gActEco');
        $this->el($act, 'cActEco', $cfg['actividad_economica'] ?: '56101');
        $this->el($act, 'dDesActEco', $cfg['actividad_descripcion'] ?: 'Servicios de restaurantes');

        // D3. Receptor
        $r = $this->datos_receptor($cust);
        $rec = $this->el($gral, 'gDatRec');
        if ($r['tipo'] === 'ruc') {
            $this->el($rec, 'iNatRec', '1');   // Contribuyente
            $this->el($rec, 'iTiOpe', '1');    // B2B
            $this->el($rec, 'cPaisRec', 'PRY');
            $this->el($rec, 'dDesPaisRe', 'Paraguay');
            $this->el($rec, 'iTiContRec', (string)$r['tipo_cont']);
            $this->el($rec, 'dRucRec', $r['ruc']);
            $this->el($rec, 'dDVRec', $r['dv']);
        } else {
            $this->el($rec, 'iNatRec', '2');   // No contribuyente
            $this->el($rec, 'iTiOpe', '2');    // B2C
            $this->el($rec, 'cPaisRec', 'PRY');
            $this->el($rec, 'dDesPaisRe', 'Paraguay');
            if ($r['tipo'] === 'ci') {
                $this->el($rec, 'iTipIDRec', '1');
                $this->el($rec, 'dDTipIDRec', 'Cédula paraguaya');
            } else {
                $this->el($rec, 'iTipIDRec', '5');
                $this->el($rec, 'dDTipIDRec', 'Innominado');
            }
            $this->el($rec, 'dNumIDRec', $r['doc']);
        }
        $this->el($rec, 'dNomRec', $r['nombre']);

        // E. Campos específicos de la Factura Electrónica
        $dtip = $this->el($de, 'gDtipDE');
        $fe = $this->el($dtip, 'gCamFE');
        $this->el($fe, 'iIndPres', '1');
        $this->el($fe, 'dDesIndPres', 'Operación presencial');

        $cond = $this->el($dtip, 'gCamCond');
        $this->el($cond, 'iCondOpe', '1');
        $this->el($cond, 'dDCondOpe', 'Contado');
        $pago = $this->el($cond, 'gPaConEIni');
        $this->el($pago, 'iTiPago', '1');
        $this->el($pago, 'dDesTiPag', 'Efectivo');
        $this->el($pago, 'dMonTiPag', $this->num($t['tot_gral'], 4));
        $this->el($pago, 'cMoneTiPag', 'PYG');
        $this->el($pago, 'dDMoneTiPag', 'Guarani');

        foreach ($liq['lineas'] as $i => $l) {
            $item = $this->el($dtip, 'gCamItem');
            $this->el($item, 'dCodInt', mb_substr($l['codigo'], 0, 50));
            // En test, el primer ítem debe llevar la leyenda de la Guía de Pruebas
            $this->el($item, 'dDesProSer', ($es_test && $i === 0) ? self::LEYENDA_TEST : mb_substr($l['descripcion'], 0, 120));
            $this->el($item, 'cUniMed', '77');
            $this->el($item, 'dDesUniMed', 'UNI');
            $this->el($item, 'dCantProSer', $this->num($l['cantidad']));

            $val = $this->el($item, 'gValorItem');
            $this->el($val, 'dPUniProSer', $this->num($l['precio']));
            $this->el($val, 'dTotBruOpeItem', $this->num($l['total_bruto']));
            $resta = $this->el($val, 'gValorRestaItem');
            $this->el($resta, 'dDescItem', $this->num($l['desc_unit']));
            $this->el($resta, 'dDescGloItem', $this->num($l['desc_glo_unit']));
            $this->el($resta, 'dAntPreUniIt', '0');
            $this->el($resta, 'dAntGloPreUniIt', '0');
            $this->el($resta, 'dTotOpeItem', $this->num($l['total']));

            $iva = $this->el($item, 'gCamIVA');
            if ($l['tasa'] > 0) {
                $this->el($iva, 'iAfecIVA', '1');
                $this->el($iva, 'dDesAfecIVA', 'Gravado IVA');
                $this->el($iva, 'dPropIVA', '100');
            } else {
                $this->el($iva, 'iAfecIVA', '3');
                $this->el($iva, 'dDesAfecIVA', 'Exento');
                $this->el($iva, 'dPropIVA', '0');
            }
            $this->el($iva, 'dTasaIVA', (string)$l['tasa']);
            $this->el($iva, 'dBasGravIVA', $this->num($l['base']));
            $this->el($iva, 'dLiqIVAItem', $this->num($l['iva']));
            $this->el($iva, 'dBasExe', '0');
        }

        // F. Subtotales y totales
        $tot = $this->el($de, 'gTotSub');
        $this->el($tot, 'dSubExe', $this->num($t['sub_exe']));
        $this->el($tot, 'dSubExo', '0');
        $this->el($tot, 'dSub5', $this->num($t['sub_5']));
        $this->el($tot, 'dSub10', $this->num($t['sub_10']));
        $this->el($tot, 'dTotOpe', $this->num($t['tot_ope']));
        $this->el($tot, 'dTotDesc', $this->num($t['tot_desc']));
        $this->el($tot, 'dTotDescGlotem', $this->num($t['tot_desc_glo']));
        $this->el($tot, 'dTotAntItem', '0');
        $this->el($tot, 'dTotAnt', '0');
        $this->el($tot, 'dPorcDescTotal', '0');
        $this->el($tot, 'dDescTotal', $this->num($t['desc_total']));
        $this->el($tot, 'dAnticipo', '0');
        $this->el($tot, 'dRedon', $this->num($t['redondeo'], 4));
        $this->el($tot, 'dTotGralOpe', $this->num($t['tot_gral']));
        $this->el($tot, 'dIVA5', $this->num($t['iva_5']));
        $this->el($tot, 'dIVA10', $this->num($t['iva_10']));
        $this->el($tot, 'dLiqTotIVA5', '0');
        $this->el($tot, 'dLiqTotIVA10', '0');
        $this->el($tot, 'dTotIVA', $this->num($t['tot_iva']));
        $this->el($tot, 'dBaseGrav5', $this->num($t['base_5']));
        $this->el($tot, 'dBaseGrav10', $this->num($t['base_10']));
        $this->el($tot, 'dTBasGraIVA', $this->num($t['tot_base']));

        return $doc;
    }

    /**
     * Agrega gCamFuFD (dCarQR) como último hijo de rDE: fuera de la firma digital,
     * a continuación de <Signature> (campo J001 del Manual Técnico).
     */
    public function agregar_qr(DOMDocument $doc, $qr_url)
    {
        $fufd = $this->el($doc->documentElement, 'gCamFuFD');
        $this->el($fufd, 'dCarQR', $qr_url);
    }

    /**
     * DigestValue del DE: SHA-256 (base64) de su canonicalización exclusiva,
     * igual al que va en la firma (XS17) y en el QR.
     */
    public function calcular_digest_de(DOMDocument $doc)
    {
        $de = $doc->getElementsByTagNameNS(self::NS_SIFEN, 'DE')->item(0);
        return $de ? base64_encode(hash('sha256', $de->C14N(true, false), true)) : '';
    }

    /**
     * Crea un elemento en el namespace de SIFEN (el texto se escapa automáticamente)
     */
    protected function el(DOMElement $parent, $name, $value = null)
    {
        $node = $parent->ownerDocument->createElementNS(self::NS_SIFEN, $name);
        if ($value !== null) {
            $node->appendChild($parent->ownerDocument->createTextNode((string)$value));
        }
        $parent->appendChild($node);
        return $node;
    }

    /**
     * Crea un elemento en el namespace de XMLDSig
     */
    protected function ds(DOMElement $parent, $name, $value = null)
    {
        $node = $parent->ownerDocument->createElementNS(self::NS_DSIG, $name);
        if ($value !== null) {
            $node->appendChild($parent->ownerDocument->createTextNode((string)$value));
        }
        $parent->appendChild($node);
        return $node;
    }

    /**
     * Firma el DE (XMLDSig enveloped) e inserta <Signature> en rDE, después de DE.
     * Devuelve true si se firmó.
     */
    public function firmar_documento(DOMDocument $doc, $cert_path, $cert_password)
    {
        $de = $doc->getElementsByTagNameNS(self::NS_SIFEN, 'DE')->item(0);
        return $de ? $this->firmar_nodo($doc, $de, $cert_path, $cert_password) : false;
    }

    /**
     * Firma XMLDSig enveloped (RSA-SHA256, digest SHA-256) del elemento $nodo, referenciado
     * por su atributo Id; <Signature> se inserta como hermano inmediatamente posterior.
     * SignedInfo usa C14N exclusiva para que la firma no dependa del contexto: es la misma
     * firmada suelta o dentro del sobre SOAP (así lo hacen las librerías en producción).
     */
    protected function firmar_nodo(DOMDocument $doc, DOMElement $nodo, $cert_path, $cert_password)
    {
        if (!file_exists($cert_path)) {
            return false;
        }
        $certs = [];
        if (!openssl_pkcs12_read(file_get_contents($cert_path), $certs, $cert_password)) {
            return false;
        }

        $padre = $nodo->parentNode;
        $sig = $doc->createElementNS(self::NS_DSIG, 'Signature');
        if ($nodo->nextSibling) {
            $padre->insertBefore($sig, $nodo->nextSibling);
        } else {
            $padre->appendChild($sig);
        }

        $si = $this->ds($sig, 'SignedInfo');
        $this->ds($si, 'CanonicalizationMethod')->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
        $this->ds($si, 'SignatureMethod')->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256');
        $ref = $this->ds($si, 'Reference');
        $ref->setAttribute('URI', '#' . $nodo->getAttribute('Id'));
        $transforms = $this->ds($ref, 'Transforms');
        $this->ds($transforms, 'Transform')->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
        $this->ds($transforms, 'Transform')->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
        $this->ds($ref, 'DigestMethod')->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
        $this->ds($ref, 'DigestValue', base64_encode(hash('sha256', $nodo->C14N(true, false), true)));

        $signature = '';
        if (!openssl_sign($si->C14N(true, false), $signature, $certs['pkey'], OPENSSL_ALGO_SHA256)) {
            $padre->removeChild($sig);
            return false;
        }
        $this->ds($sig, 'SignatureValue', base64_encode($signature));

        $key_info = $this->ds($sig, 'KeyInfo');
        $x509 = $this->ds($key_info, 'X509Data');
        $this->ds($x509, 'X509Certificate', preg_replace('/-----(BEGIN|END) CERTIFICATE-----|\s+/', '', $certs['cert']));

        return true;
    }

    /**
     * Firmar un XML de DE en texto (compatibilidad). Devuelve el XML firmado o false.
     */
    public function firmar_xml($xml_content, $cert_path, $cert_password)
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;
        if (!@$doc->loadXML($xml_content)) {
            return false;
        }
        return $this->firmar_documento($doc, $cert_path, $cert_password) ? $doc->saveXML() : false;
    }

    /* =====================================================================
     * Servicios web de SIFEN (Manual Técnico v150, capítulos 7 a 9)
     * SOAP 1.2 sobre TLS 1.2 con autenticación mutua por certificado.
     * ===================================================================== */

    protected $ws_rutas = [
        'recibe'        => '/de/ws/sync/recibe.wsdl',
        'recibe-lote'   => '/de/ws/async/recibe-lote.wsdl',
        'consulta'      => '/de/ws/consultas/consulta.wsdl',
        'consulta-lote' => '/de/ws/consultas/consulta-lote.wsdl',
        'consulta-ruc'  => '/de/ws/consultas/consulta-ruc.wsdl',
        'evento'        => '/de/ws/eventos/evento.wsdl'
    ];

    public function ws_url($servicio, $config = null)
    {
        $config = $config ?: $this->get_config();
        $host = (($config['ambiente'] ?? 'test') === 'prod') ? 'https://sifen.set.gov.py' : 'https://sifen-test.set.gov.py';
        return $host . $this->ws_rutas[$servicio];
    }

    /**
     * Ruta absoluta del certificado .p12 si está configurado (archivo y contraseña)
     */
    protected function ruta_certificado($config)
    {
        if (empty($config['cert_file']) || empty($config['cert_pass'])) {
            return null;
        }
        $path = FCPATH . $config['cert_file'];
        return file_exists($path) ? $path : null;
    }

    /**
     * Registra el inicio de una operación en geopos_sifen_log. Su id es el dId
     * (identificador de control de envío autoincremental que exige el manual).
     */
    protected function log_inicio($operacion, $config, $extra = [])
    {
        $this->CI->db->insert('geopos_sifen_log', $extra + [
            'operacion'  => $operacion,
            'ambiente'   => $config['ambiente'] ?? 'test',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $id = (int)$this->CI->db->insert_id();
        return $id ?: (int)date('ymdHis');
    }

    protected function log_fin($log_id, $r, $datos)
    {
        $this->CI->db->where('id', (int)$log_id)->update('geopos_sifen_log', $datos + [
            'http_code' => $r['http'] ?: null,
            'request'   => $r['request'],
            'response'  => mb_substr((string)$r['response'], 0, 1000000)
        ]);
    }

    /**
     * Llamada SOAP 1.2 a un servicio de SIFEN con TLS mutuo.
     * Devuelve ['ok', 'http', 'error', 'request', 'response', 'xpath'].
     */
    protected function ws_llamar($servicio, $body, $config, $timeout = 60)
    {
        $res = ['ok' => false, 'http' => 0, 'error' => '', 'request' => '', 'response' => '', 'xpath' => null];

        $cert = $this->ruta_certificado($config);
        if (!$cert) {
            $res['error'] = 'No hay certificado digital configurado (archivo .p12 y contraseña).';
            return $res;
        }
        $certs = [];
        if (!openssl_pkcs12_read(file_get_contents($cert), $certs, $config['cert_pass'])) {
            $res['error'] = 'No se pudo abrir el certificado .p12 con la contraseña configurada.';
            return $res;
        }

        $res['request'] = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope"><env:Header/><env:Body>'
            . $body . '</env:Body></env:Envelope>';

        // Certificado + cadena + clave en un PEM temporal (0600) solo durante la conexión
        $pem = tempnam(sys_get_temp_dir(), 'sifen');
        @chmod($pem, 0600);
        file_put_contents($pem, $certs['cert'] . implode('', $certs['extracerts'] ?? []) . $certs['pkey']);

        $ch = curl_init($this->ws_url($servicio, $config));
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $res['request'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/xml; charset=utf-8', 'User-Agent: GeeksPOS-SIFEN/1.0'],
            CURLOPT_CONNECTTIMEOUT => min(10, $timeout),
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_SSLVERSION     => CURL_SSLVERSION_TLSv1_2,
            CURLOPT_SSLCERT        => $pem,
            CURLOPT_SSLKEY         => $pem,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);
        $raw = curl_exec($ch);
        $res['http'] = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        @unlink($pem);

        $res['response'] = (string)$raw;
        if ($raw === false) {
            $res['error'] = 'Error de conexión con SIFEN: ' . $curl_error;
            return $res;
        }
        // El balanceador BIG-IP de SIFEN redirige cuando no acepta el certificado cliente
        if ($res['http'] === 302 || stripos(ltrim($raw), '<html') === 0 || stripos($raw, 'vdesk/hangup') !== false) {
            $res['error'] = 'SIFEN rechazó la conexión: el certificado no fue aceptado (debe ser cualificado, con el RUC y habilitado para el ambiente).';
            return $res;
        }

        $doc = new DOMDocument();
        if (!@$doc->loadXML($raw)) {
            $res['error'] = 'La respuesta de SIFEN no es un XML válido (HTTP ' . $res['http'] . ').';
            return $res;
        }
        $res['xpath'] = new DOMXPath($doc);
        $fault = $this->xp_txt($res['xpath'], 'Text') ?: $this->xp_txt($res['xpath'], 'faultstring');
        if ($res['xpath']->query('//*[local-name()="Fault"]')->length) {
            $res['error'] = 'SOAP Fault de SIFEN: ' . ($fault ?: 'sin detalle');
            return $res;
        }
        $res['ok'] = true;
        return $res;
    }

    /**
     * Primer texto de un elemento por nombre local (ignora prefijos ns2:, env:, etc.)
     */
    protected function xp_txt(DOMXPath $x, $nombre, $ctx = null)
    {
        $n = $x->query('.//*[local-name()="' . $nombre . '"]', $ctx ?: $x->document->documentElement)->item(0);
        return $n ? trim($n->textContent) : '';
    }

    /**
     * Mensajes gResProc (dCodRes / dMsgRes) dentro de un contexto
     */
    protected function xp_mensajes(DOMXPath $x, $ctx = null)
    {
        $out = [];
        foreach ($x->query('.//*[local-name()="gResProc"]', $ctx ?: $x->document->documentElement) as $g) {
            $out[] = ['codigo' => $this->xp_txt($x, 'dCodRes', $g), 'mensaje' => $this->xp_txt($x, 'dMsgRes', $g)];
        }
        return $out;
    }

    protected function texto_mensajes($mensajes)
    {
        $partes = [];
        foreach ($mensajes as $m) {
            $partes[] = trim($m['codigo'] . ' - ' . $m['mensaje'], ' -');
        }
        return mb_substr(implode(' | ', $partes), 0, 500);
    }

    /**
     * dEstRes de SIFEN -> valor de geopos_invoices.sifen_estado
     */
    protected function estado_factura($dEstRes)
    {
        $e = mb_strtolower(trim((string)$dEstRes));
        if ($e === 'aprobado') {
            return 'aprobado';
        }
        if (strpos($e, 'aprobado') === 0) {
            return 'aprobado_obs';
        }
        return $e === 'rechazado' ? 'rechazado' : '';
    }

    /**
     * XML firmado de una factura listo para enviar. Si el archivo no existe o no está
     * firmado (p. ej. se generó antes de cargar el certificado), lo regenera y firma.
     * Devuelve el DOMDocument o un mensaje de error.
     */
    protected function dte_para_envio($invoice)
    {
        $path = FCPATH . $invoice['sifen_xml'];
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        if (!empty($invoice['sifen_xml']) && is_file($path) && @$doc->load($path)
            && $doc->getElementsByTagNameNS(self::NS_DSIG, 'Signature')->length) {
            return $doc;
        }
        $re = $this->regenerar_xml($invoice['id']);
        if (!$re['status']) {
            return $re['message'];
        }
        return $re['firmado'] ? $re['doc'] : 'No se pudo firmar el DE: configure el certificado digital y su contraseña.';
    }

    /**
     * WS siRecepDE (síncrono): envía una factura y guarda el resultado.
     */
    public function enviar_de($invoice_id, $timeout = 30)
    {
        $config = $this->get_config();
        $invoice = $this->CI->db->get_where('geopos_invoices', ['id' => (int)$invoice_id])->row_array();
        if (!$invoice || strlen((string)$invoice['cdc']) !== 44) {
            return ['status' => false, 'message' => 'La factura no tiene CDC (no fue procesada para SIFEN).'];
        }
        if (in_array($invoice['sifen_estado'], ['aprobado', 'aprobado_obs', 'cancelado'], true)) {
            return ['status' => false, 'message' => 'La factura ya fue aprobada por SIFEN.'];
        }
        $doc = $this->dte_para_envio($invoice);
        if (is_string($doc)) {
            return ['status' => false, 'message' => $doc];
        }

        $log_id = $this->log_inicio('recibe', $config, ['invoice_id' => (int)$invoice_id, 'cdc' => $invoice['cdc']]);
        $body = '<rEnviDe xmlns="' . self::NS_SIFEN . '"><dId>' . $log_id . '</dId><xDE>'
            . $doc->saveXML($doc->documentElement) . '</xDE></rEnviDe>';
        $r = $this->ws_llamar('recibe', $body, $config, $timeout);

        if (!$r['ok']) {
            $this->log_fin($log_id, $r, ['estado' => 'error', 'mensaje' => mb_substr($r['error'], 0, 500)]);
            return ['status' => false, 'message' => $r['error'], 'log_id' => $log_id];
        }

        $x = $r['xpath'];
        $est = $this->xp_txt($x, 'dEstRes');
        $prot = $this->xp_txt($x, 'dProtAut');
        $msgs = $this->xp_mensajes($x);
        $this->log_fin($log_id, $r, [
            'estado'    => $est,
            'codigo'    => $msgs[0]['codigo'] ?? null,
            'mensaje'   => $this->texto_mensajes($msgs),
            'protocolo' => $prot ?: null
        ]);
        $estado = $this->estado_factura($est);
        if ($estado) {
            $this->CI->db->where('id', (int)$invoice_id)->update('geopos_invoices', ['sifen_estado' => $estado]);
        }

        return [
            'status'    => true,
            'aprobado'  => in_array($estado, ['aprobado', 'aprobado_obs'], true),
            'estado'    => $est,
            'protocolo' => $prot,
            'mensajes'  => $msgs,
            'log_id'    => $log_id
        ];
    }

    /**
     * Contenido base64 del lote: zip con un único xml_file.xml que contiene
     * <rLoteDE> con hasta 50 rDE firmados (formato usado por las librerías en producción).
     */
    public function armar_lote(array $docs)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><rLoteDE>';
        foreach ($docs as $doc) {
            $xml .= $doc->saveXML($doc->documentElement);
        }
        $xml .= '</rLoteDE>';

        $tmp = tempnam(sys_get_temp_dir(), 'lote');
        $zip = new ZipArchive();
        $zip->open($tmp, ZipArchive::OVERWRITE);
        $zip->addFromString('xml_file.xml', $xml);
        $zip->close();
        $b64 = base64_encode(file_get_contents($tmp));
        @unlink($tmp);
        return $b64;
    }

    /**
     * WS siRecepLoteDE (asíncrono): envía hasta 50 facturas en un lote.
     * Devuelve el número de lote (dProtConsLote) para consultar luego con consultar_lote().
     */
    public function enviar_lote(array $invoice_ids)
    {
        $config = $this->get_config();
        $invoice_ids = array_values(array_unique(array_map('intval', $invoice_ids)));
        if (count($invoice_ids) < 1 || count($invoice_ids) > 50) {
            return ['status' => false, 'message' => 'Un lote debe tener entre 1 y 50 facturas.'];
        }

        $docs = [];
        $cdcs = [];
        foreach ($invoice_ids as $id) {
            $inv = $this->CI->db->get_where('geopos_invoices', ['id' => $id])->row_array();
            if (!$inv || strlen((string)$inv['cdc']) !== 44) {
                return ['status' => false, 'message' => "La factura #$id no tiene CDC."];
            }
            if (in_array($inv['sifen_estado'], ['aprobado', 'aprobado_obs', 'cancelado'], true)) {
                return ['status' => false, 'message' => "La factura #$id ya fue aprobada por SIFEN."];
            }
            $doc = $this->dte_para_envio($inv);
            if (is_string($doc)) {
                return ['status' => false, 'message' => "Factura #$id: $doc"];
            }
            $docs[] = $doc;
            $cdcs[] = $inv['cdc'];
        }

        $log_id = $this->log_inicio('recibe-lote', $config, ['referencia' => count($docs) . ' DE']);
        $body = '<rEnvioLote xmlns="' . self::NS_SIFEN . '"><dId>' . $log_id . '</dId><xDE>'
            . $this->armar_lote($docs) . '</xDE></rEnvioLote>';
        $r = $this->ws_llamar('recibe-lote', $body, $config, 60);

        if (!$r['ok']) {
            $this->log_fin($log_id, $r, ['estado' => 'error', 'mensaje' => mb_substr($r['error'], 0, 500)]);
            return ['status' => false, 'message' => $r['error'], 'log_id' => $log_id];
        }

        $x = $r['xpath'];
        $codigo = $this->xp_txt($x, 'dCodRes');
        $mensaje = $this->xp_txt($x, 'dMsgRes');
        $lote = $this->xp_txt($x, 'dProtConsLote');
        $recibido = ($codigo === '0300');
        $this->log_fin($log_id, $r, [
            'estado'    => $recibido ? 'Lote recibido' : 'Lote rechazado',
            'codigo'    => $codigo,
            'mensaje'   => mb_substr($mensaje, 0, 500),
            'protocolo' => $lote ?: null
        ]);
        if ($recibido) {
            $this->CI->db->where_in('id', $invoice_ids)->update('geopos_invoices', ['sifen_estado' => 'en_lote']);
        }

        return [
            'status'   => true,
            'recibido' => $recibido,
            'codigo'   => $codigo,
            'mensaje'  => $mensaje,
            'lote'     => $lote,
            'tiempo'   => $this->xp_txt($x, 'dTpoProces'),
            'cdcs'     => $cdcs,
            'log_id'   => $log_id
        ];
    }

    /**
     * WS siResultLoteDE: resultado de cada DE de un lote. Actualiza el estado de las facturas.
     */
    public function consultar_lote($lote)
    {
        $config = $this->get_config();
        $lote = preg_replace('/[^0-9]/', '', (string)$lote);
        if ($lote === '') {
            return ['status' => false, 'message' => 'Indique el número de lote.'];
        }

        $log_id = $this->log_inicio('consulta-lote', $config, ['referencia' => $lote]);
        $body = '<rEnviConsLoteDe xmlns="' . self::NS_SIFEN . '"><dId>' . $log_id . '</dId><dProtConsLote>'
            . $lote . '</dProtConsLote></rEnviConsLoteDe>';
        $r = $this->ws_llamar('consulta-lote', $body, $config, 30);

        if (!$r['ok']) {
            $this->log_fin($log_id, $r, ['estado' => 'error', 'mensaje' => mb_substr($r['error'], 0, 500)]);
            return ['status' => false, 'message' => $r['error'], 'log_id' => $log_id];
        }

        $x = $r['xpath'];
        $codigo = $this->xp_txt($x, 'dCodResLot');
        $mensaje = $this->xp_txt($x, 'dMsgResLot');
        $resultados = [];
        foreach ($x->query('//*[local-name()="gResProcLote"]') as $g) {
            $cdc = $this->xp_txt($x, 'id', $g);
            $est = $this->xp_txt($x, 'dEstRes', $g);
            $msgs = $this->xp_mensajes($x, $g);
            $resultados[] = ['cdc' => $cdc, 'estado' => $est, 'protocolo' => $this->xp_txt($x, 'dProtAut', $g), 'mensajes' => $msgs];
            $estado = $this->estado_factura($est);
            if ($estado && $cdc) {
                $this->CI->db->where('cdc', $cdc)->update('geopos_invoices', ['sifen_estado' => $estado]);
            }
        }
        $this->log_fin($log_id, $r, ['estado' => $mensaje ?: $codigo, 'codigo' => $codigo, 'mensaje' => mb_substr($mensaje, 0, 500)]);

        return ['status' => true, 'codigo' => $codigo, 'mensaje' => $mensaje, 'concluido' => $codigo === '0362', 'resultados' => $resultados, 'log_id' => $log_id];
    }

    /**
     * WS siConsDE: consulta un DTE por su CDC.
     */
    public function consultar_de($cdc)
    {
        $config = $this->get_config();
        $cdc = preg_replace('/[^0-9]/', '', (string)$cdc);
        if (strlen($cdc) !== 44) {
            return ['status' => false, 'message' => 'El CDC debe tener 44 dígitos.'];
        }
        $invoice = $this->CI->db->get_where('geopos_invoices', ['cdc' => $cdc])->row_array();

        $log_id = $this->log_inicio('consulta', $config, ['cdc' => $cdc, 'invoice_id' => $invoice ? (int)$invoice['id'] : null]);
        $body = '<rEnviConsDeRequest xmlns="' . self::NS_SIFEN . '"><dId>' . $log_id . '</dId><dCDC>'
            . $cdc . '</dCDC></rEnviConsDeRequest>';
        $r = $this->ws_llamar('consulta', $body, $config, 30);

        if (!$r['ok']) {
            $this->log_fin($log_id, $r, ['estado' => 'error', 'mensaje' => mb_substr($r['error'], 0, 500)]);
            return ['status' => false, 'message' => $r['error'], 'log_id' => $log_id];
        }

        $x = $r['xpath'];
        $codigo = $this->xp_txt($x, 'dCodRes');
        $mensaje = $this->xp_txt($x, 'dMsgRes');
        $prot = $this->xp_txt($x, 'dProtAut');
        $this->log_fin($log_id, $r, ['estado' => $mensaje ?: $codigo, 'codigo' => $codigo, 'mensaje' => mb_substr($mensaje, 0, 500), 'protocolo' => $prot ?: null]);

        // 0422 = CDC encontrado: solo los DE aprobados se convierten en DTE
        if ($codigo === '0422' && $invoice && !in_array($invoice['sifen_estado'], ['aprobado', 'aprobado_obs', 'cancelado'], true)) {
            $this->CI->db->where('id', (int)$invoice['id'])->update('geopos_invoices', ['sifen_estado' => 'aprobado']);
        }

        return ['status' => true, 'encontrado' => $codigo === '0422', 'codigo' => $codigo, 'mensaje' => $mensaje, 'protocolo' => $prot, 'log_id' => $log_id];
    }

    /**
     * WS siConsRUC: datos y estado de un RUC en SIFEN (incluye si es facturador electrónico).
     */
    public function consultar_ruc_sifen($ruc)
    {
        $config = $this->get_config();
        $ruc = preg_replace('/[^0-9]/', '', explode('-', (string)$ruc)[0]);
        if (strlen($ruc) < 5 || strlen($ruc) > 8) {
            return ['status' => false, 'message' => 'El RUC (sin dígito verificador) debe tener entre 5 y 8 dígitos.'];
        }

        $log_id = $this->log_inicio('consulta-ruc', $config, ['referencia' => $ruc]);
        $body = '<rEnviConsRUC xmlns="' . self::NS_SIFEN . '"><dId>' . $log_id . '</dId><dRUCCons>'
            . $ruc . '</dRUCCons></rEnviConsRUC>';
        $r = $this->ws_llamar('consulta-ruc', $body, $config, 30);

        if (!$r['ok']) {
            $this->log_fin($log_id, $r, ['estado' => 'error', 'mensaje' => mb_substr($r['error'], 0, 500)]);
            return ['status' => false, 'message' => $r['error'], 'log_id' => $log_id];
        }

        $x = $r['xpath'];
        $codigo = $this->xp_txt($x, 'dCodRes');
        $mensaje = $this->xp_txt($x, 'dMsgRes');
        $this->log_fin($log_id, $r, ['estado' => $mensaje ?: $codigo, 'codigo' => $codigo, 'mensaje' => mb_substr($mensaje, 0, 500)]);

        return [
            'status'       => true,
            'encontrado'   => $codigo === '0502',
            'codigo'       => $codigo,
            'mensaje'      => $mensaje,
            'razon_social' => $this->xp_txt($x, 'dRazCons'),
            'estado'       => $this->xp_txt($x, 'dDesEstCons'),
            'facturador'   => $this->xp_txt($x, 'dRUCFactElec') === 'S',
            'log_id'       => $log_id
        ];
    }

    /**
     * Arma y firma un evento del emisor (gGroupGesEve > rGesEve > rEve + Signature).
     * $llenar recibe el nodo gGroupTiEvt y agrega rGeVeCan / rGeVeInu.
     * Devuelve el DOMDocument (raíz gGroupGesEve) o false si no se pudo firmar.
     */
    public function armar_evento($id_evento, callable $llenar, $cert_path, $cert_password)
    {
        $xsi = 'http://www.w3.org/2001/XMLSchema-instance';
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;

        $grupo = $doc->createElementNS(self::NS_SIFEN, 'gGroupGesEve');
        $grupo->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', $xsi);
        $grupo->setAttributeNS($xsi, 'xsi:schemaLocation', self::NS_SIFEN . ' siRecepEvento_v150.xsd');
        $doc->appendChild($grupo);

        $ges = $this->el($grupo, 'rGesEve');
        $eve = $this->el($ges, 'rEve');
        $eve->setAttribute('Id', (string)(int)$id_evento);
        $this->el($eve, 'dFecFirma', date('Y-m-d\TH:i:s'));
        $this->el($eve, 'dVerFor', '150');
        $llenar($this->el($eve, 'gGroupTiEvt'));

        return $this->firmar_nodo($doc, $eve, $cert_path, $cert_password) ? $doc : false;
    }

    /**
     * Envía un evento firmado al WS siRecepEvento y registra el resultado.
     */
    protected function enviar_evento($tipo, callable $llenar, $config, $log_extra)
    {
        $cert = $this->ruta_certificado($config);
        if (!$cert) {
            return ['status' => false, 'message' => 'No hay certificado digital configurado (archivo .p12 y contraseña).'];
        }
        $log_id = $this->log_inicio('evento', $config, $log_extra + ['referencia' => $tipo]);

        $doc = $this->armar_evento($log_id, $llenar, $cert, $config['cert_pass']);
        if (!$doc) {
            $this->CI->db->where('id', $log_id)->update('geopos_sifen_log', ['estado' => 'error', 'mensaje' => 'No se pudo firmar el evento.']);
            return ['status' => false, 'message' => 'No se pudo firmar el evento con el certificado configurado.'];
        }
        $body = '<rEnviEventoDe xmlns="' . self::NS_SIFEN . '"><dId>' . $log_id . '</dId><dEvReg>'
            . $doc->saveXML($doc->documentElement) . '</dEvReg></rEnviEventoDe>';
        $r = $this->ws_llamar('evento', $body, $config, 30);

        if (!$r['ok']) {
            $this->log_fin($log_id, $r, ['estado' => 'error', 'mensaje' => mb_substr($r['error'], 0, 500)]);
            return ['status' => false, 'message' => $r['error'], 'log_id' => $log_id];
        }

        $x = $r['xpath'];
        $est = $this->xp_txt($x, 'dEstRes');
        $prot = $this->xp_txt($x, 'dProtAut');
        $msgs = $this->xp_mensajes($x);
        $this->log_fin($log_id, $r, [
            'estado'    => $est,
            'codigo'    => $msgs[0]['codigo'] ?? null,
            'mensaje'   => $this->texto_mensajes($msgs),
            'protocolo' => $prot ?: null
        ]);

        return [
            'status'    => true,
            'aprobado'  => in_array($this->estado_factura($est), ['aprobado', 'aprobado_obs'], true),
            'estado'    => $est,
            'protocolo' => $prot,
            'mensajes'  => $msgs,
            'log_id'    => $log_id
        ];
    }

    /**
     * Evento de cancelación de un DTE aprobado (rGeVeCan).
     */
    public function evento_cancelacion($cdc, $motivo)
    {
        $config = $this->get_config();
        $cdc = preg_replace('/[^0-9]/', '', (string)$cdc);
        $motivo = trim((string)$motivo);
        if (strlen($cdc) !== 44) {
            return ['status' => false, 'message' => 'El CDC debe tener 44 dígitos.'];
        }
        if (mb_strlen($motivo) < 5 || mb_strlen($motivo) > 500) {
            return ['status' => false, 'message' => 'El motivo debe tener entre 5 y 500 caracteres.'];
        }
        $invoice = $this->CI->db->get_where('geopos_invoices', ['cdc' => $cdc])->row_array();

        $res = $this->enviar_evento('cancelacion', function ($grupo) use ($cdc, $motivo) {
            $can = $this->el($grupo, 'rGeVeCan');
            $this->el($can, 'Id', $cdc);
            $this->el($can, 'mOtEve', $motivo);
        }, $config, ['cdc' => $cdc, 'invoice_id' => $invoice ? (int)$invoice['id'] : null]);

        if (!empty($res['aprobado']) && $invoice) {
            $this->CI->db->where('id', (int)$invoice['id'])->update('geopos_invoices', ['sifen_estado' => 'cancelado']);
        }
        return $res;
    }

    /**
     * Evento de inutilización de un rango de numeración no usado (rGeVeInu), hasta 1000 números.
     * $tipo_de: 1 = Factura, 4 = Autofactura, 5 = Nota de crédito, 6 = Nota de débito, 7 = Nota de remisión.
     */
    public function evento_inutilizacion($desde, $hasta, $motivo, $tipo_de = 1)
    {
        $config = $this->get_config();
        $desde = (int)$desde;
        $hasta = (int)$hasta;
        $motivo = trim((string)$motivo);
        if ($desde < 1 || $hasta < $desde || ($hasta - $desde) >= 1000) {
            return ['status' => false, 'message' => 'Rango inválido: "desde" debe ser ≤ "hasta" y como máximo 1000 números.'];
        }
        if (mb_strlen($motivo) < 5 || mb_strlen($motivo) > 500) {
            return ['status' => false, 'message' => 'El motivo debe tener entre 5 y 500 caracteres.'];
        }
        if (!in_array((int)$tipo_de, [1, 4, 5, 6, 7], true)) {
            return ['status' => false, 'message' => 'Tipo de documento no válido para inutilización.'];
        }

        return $this->enviar_evento('inutilizacion', function ($grupo) use ($config, $desde, $hasta, $motivo, $tipo_de) {
            $inu = $this->el($grupo, 'rGeVeInu');
            $this->el($inu, 'dNumTim', sprintf('%08d', (int)$config['timbrado']));
            $this->el($inu, 'dEst', sprintf('%03d', (int)$config['establecimiento']));
            $this->el($inu, 'dPunExp', sprintf('%03d', (int)$config['punto_expedicion']));
            $this->el($inu, 'dNumIn', sprintf('%07d', $desde));
            $this->el($inu, 'dNumFin', sprintf('%07d', $hasta));
            $this->el($inu, 'iTiDE', (string)(int)$tipo_de);
            $this->el($inu, 'mOtEve', $motivo);
        }, $config, ['cdc' => null]);
    }

    /**
     * Validar e inspeccionar un certificado .p12 / .pfx cargado
     */
    public function validar_certificado($p12_path, $password)
    {
        if (!file_exists($p12_path)) {
            return ['status' => false, 'message' => 'Archivo de certificado no encontrado'];
        }

        $p12_content = file_get_contents($p12_path);
        $certs = [];
        if (!openssl_pkcs12_read($p12_content, $certs, $password)) {
            return ['status' => false, 'message' => 'Contraseña del certificado incorrecta o formato inválido'];
        }

        $cert_data = openssl_x509_parse($certs['cert']);
        if (!$cert_data) {
            return ['status' => false, 'message' => 'No se pudo leer la información X.509 del certificado'];
        }

        $valido_desde = date('Y-m-d H:i:s', $cert_data['validFrom_time_t']);
        $valido_hasta = date('Y-m-d H:i:s', $cert_data['validTo_time_t']);
        $vencido = ($cert_data['validTo_time_t'] < time());

        return [
            'status'       => true,
            'titular'      => $cert_data['subject']['CN'] ?? ($cert_data['subject']['O'] ?? 'Titular'),
            'emisor'       => $cert_data['issuer']['CN'] ?? ($cert_data['issuer']['O'] ?? 'Emisor'),
            'valido_desde' => $valido_desde,
            'valido_hasta' => $valido_hasta,
            'vencido'      => $vencido,
            'dias_restantes'=> max(0, round(($cert_data['validTo_time_t'] - time()) / 86400))
        ];
    }
}
