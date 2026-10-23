<?php
/**
 * GeeksPOS - SIFEN Paraguay Electronic Invoicing Library
 * Cumplimiento con la Dirección Nacional de Ingresos Tributarios (DNIT / SIFEN v150)
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Sifen
{
    protected $CI;
    protected $config_cache = null;

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
     * Desglose de IVA en Paraguay (precios con IVA incluido al consumidor)
     * - IVA 10%: IVA = Monto / 11; Gravada = Monto - IVA
     * - IVA 5%:  IVA = Monto / 21; Gravada = Monto - IVA
     * - Exenta:  IVA = 0; Exenta = Monto
     */
    public function desglosar_iva($monto, $tasa = 10)
    {
        $monto = (float)$monto;
        $tasa = (float)$tasa;

        if ($tasa == 10 || $tasa == 0) {
            // General 10% (por defecto en comida y comercio de Paraguay)
            $iva = round($monto / 11.0);
            $gravada = $monto - $iva;
            return [
                'tasa'        => 10,
                'total'       => $monto,
                'iva'         => $iva,
                'gravada_10'  => $gravada,
                'gravada_5'   => 0.0,
                'exenta'      => 0.0
            ];
        } elseif ($tasa == 5) {
            $iva = round($monto / 21.0);
            $gravada = $monto - $iva;
            return [
                'tasa'        => 5,
                'total'       => $monto,
                'iva'         => $iva,
                'gravada_10'  => 0.0,
                'gravada_5'   => $gravada,
                'exenta'      => 0.0
            ];
        } else {
            // Exentas
            return [
                'tasa'        => 0,
                'total'       => $monto,
                'iva'         => 0.0,
                'gravada_10'  => 0.0,
                'gravada_5'   => 0.0,
                'exenta'      => $monto
            ];
        }
    }

    /**
     * Generar URL del Código QR oficial para el ticket KuDE
     */
    public function generar_qr_url($cdc, $fecha_hora_iso, $ruc_receptor, $total, $total_iva, $items_count, $digest_value = '', $config = null)
    {
        if (!$config) {
            $config = $this->get_config();
        }

        $base_url_qr = ($config['ambiente'] === 'prod')
            ? 'https://sifen.set.gov.py/consultas-ku-de/qr?'
            : 'https://sifen-test.set.gov.py/consultas-ku-de/qr?';

        $nVersion = '150';
        $Id = $cdc;
        // Fecha en formato YYYY-MM-DDTHH:MM:SS
        $dFeEmiDE = date('Y-m-d\TH:i:s', strtotime($fecha_hora_iso));
        // En SIFEN la fecha en el QR se codifica en hexadecimal UTF-8
        $dFeEmiDE_hex = bin2hex($dFeEmiDE);

        $clean_rec = preg_replace('/[^0-9A-Za-z]/', '', (string)$ruc_receptor);
        if (empty($clean_rec)) {
            $clean_rec = 'SinNombre';
        }

        $dTotGralOpe = number_format((float)$total, 0, '', '');
        $dTotIVA = number_format((float)$total_iva, 0, '', '');
        $cItems = (int)$items_count;

        // Digest value del XML (en hex) o fallback si es borrador
        if (!empty($digest_value)) {
            $digest_hex = bin2hex($digest_value);
        } else {
            $digest_hex = bin2hex(sha1($cdc));
        }

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

        // 2. Liquidar IVA de todos los ítems
        $tot_iva10 = 0.0;
        $tot_iva5 = 0.0;
        $tot_exenta = 0.0;
        $tot_grav10 = 0.0;
        $tot_grav5 = 0.0;

        foreach ($items as $it) {
            $sub = (float)$it['subtotal'];
            $tasa = (float)$it['tax'];
            // Si no tiene tasa explícita, en gastronomía/retail Paraguay es 10%
            if ($tasa <= 0) $tasa = 10;
            $desg = $this->desglosar_iva($sub, $tasa);
            $tot_iva10 += $desg['iva'];
            $tot_grav10 += $desg['gravada_10'];
            $tot_iva5 += $desg['iva_5'];
            $tot_grav5 += $desg['gravada_5'];
            $tot_exenta += $desg['exenta'];
        }

        // Si hay delivery / shipping, liquidar con IVA 10%
        if (!empty($invoice['shipping']) && (float)$invoice['shipping'] > 0) {
            $desg_ship = $this->desglosar_iva((float)$invoice['shipping'], 10);
            $tot_iva10 += $desg_ship['iva'];
            $tot_grav10 += $desg_ship['gravada_10'];
        }

        $total_iva = $tot_iva10 + $tot_iva5;

        // 3. Generar CDC de 44 dígitos
        $cdc_data = $this->generar_cdc(
            1, // Factura Electrónica
            $config['ruc'],
            $config['dv'],
            $establecimiento,
            $punto_exp,
            $secuencia,
            2, // Persona Jurídica
            $invoice['invoicedate'] ?: date('Y-m-d'),
            1 // Normal
        );

        $cdc = $cdc_data['cdc'];

        // 4. Generar URL para el Código QR
        $ruc_receptor = !empty($customer['taxid']) ? $customer['taxid'] : (!empty($customer['phone']) ? $customer['phone'] : 'SinNombre');
        $fecha_hora = $invoice['invoicedate'] . ' ' . ($invoice['r_time'] ?: date('H:i:s'));
        $qr_url = $this->generar_qr_url(
            $cdc,
            $fecha_hora,
            $ruc_receptor,
            $invoice['total'],
            $total_iva,
            count($items),
            sha1($cdc),
            $config
        );

        // 5. Generar archivo XML DTE
        $xml_content = $this->generar_xml_dte($invoice, $items, $customer, $config, $cdc, $cdc_data['codigo_seguridad'], $sifen_numero, $tot_grav10, $tot_iva10, $tot_grav5, $tot_iva5, $tot_exenta, $qr_url);
        $xml_filename = 'DTE_' . $cdc . '.xml';
        $xml_path = 'userfiles/sifen/xml/' . $xml_filename;
        @file_put_contents(FCPATH . $xml_path, $xml_content);

        // 6. Firmar digitalmente si existe el certificado
        $cert_abs_path = FCPATH . $config['cert_file'];
        if (!empty($config['cert_file']) && file_exists($cert_abs_path) && !empty($config['cert_pass'])) {
            $signed_xml = $this->firmar_xml($xml_content, $cert_abs_path, $config['cert_pass']);
            if ($signed_xml) {
                @file_put_contents(FCPATH . $xml_path, $signed_xml);
            }
        }

        // 7. Actualizar factura en base de datos
        $update_data = [
            'cdc'          => $cdc,
            'sifen_numero' => $sifen_numero,
            'sifen_estado' => 'borrador',
            'sifen_qr'     => $qr_url,
            'sifen_xml'    => $xml_path,
            'iva_10'       => $tot_iva10,
            'iva_5'        => $tot_iva5,
            'iva_exenta'   => $tot_exenta,
            'gravada_10'   => $tot_grav10,
            'gravada_5'    => $tot_grav5
        ];

        $this->CI->db->where('id', (int)$invoice_id);
        $this->CI->db->update('geopos_invoices', $update_data);

        $this->CI->db->trans_complete();

        return [
            'status'       => true,
            'cdc'          => $cdc,
            'cdc_formato'  => $this->formatear_cdc($cdc),
            'sifen_numero' => $sifen_numero,
            'sifen_qr'     => $qr_url,
            'iva_10'       => $tot_iva10,
            'iva_5'        => $tot_iva5,
            'iva_total'    => $total_iva,
            'total'        => $invoice['total']
        ];
    }

    /**
     * Generar estructura XML DTE v150
     */
    public function generar_xml_dte($inv, $items, $cust, $cfg, $cdc, $cod_seg, $sifen_num, $grav10, $iva10, $grav5, $iva5, $exenta, $qr_url)
    {
        $num_parts = explode('-', $sifen_num);
        $est = $num_parts[0] ?? '001';
        $pun = $num_parts[1] ?? '001';
        $sec = $num_parts[2] ?? '0000001';

        $fecha_hora = date('Y-m-d\TH:i:s', strtotime($inv['invoicedate'] . ' ' . ($inv['r_time'] ?: date('H:i:s'))));

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rDE xmlns="http://ekuatia.set.gov.py/sifen/xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"></rDE>');
        $xml->addChild('dVerFor', '150');

        $de = $xml->addChild('DE');
        $de->addAttribute('Id', $cdc);

        $gOpeDE = $de->addChild('gOpeDE');
        $gOpeDE->addChild('iTipEmi', '1');
        $gOpeDE->addChild('dDesTipEmi', 'Normal');
        $gOpeDE->addChild('dCodSeg', $cod_seg);

        $gTimb = $de->addChild('gTimb');
        $gTimb->addChild('iTiDoc', '1');
        $gTimb->addChild('dDesTiDoc', 'Factura electrónica');
        $gTimb->addChild('dNumTim', $cfg['timbrado']);
        $gTimb->addChild('dEst', $est);
        $gTimb->addChild('dPunExp', $pun);
        $gTimb->addChild('dNumDoc', $sec);
        $gTimb->addChild('dFeIniT', $cfg['timbrado_fecha'] ?: date('Y-01-01'));

        $gDatGralOpe = $de->addChild('gDatGralOpe');
        $gDatGralOpe->addChild('dFeEmiDE', $fecha_hora);

        // Emisor
        $gEmis = $gDatGralOpe->addChild('gEmis');
        $gEmis->addChild('dRucEm', $cfg['ruc']);
        $gEmis->addChild('dDVEmi', $cfg['dv']);
        $gEmis->addChild('iTipCont', '2'); // Persona Jurídica
        $gEmis->addChild('dNomEmi', htmlspecialchars($cfg['razon_social'], ENT_XML1));
        $gEmis->addChild('dNomFanEmi', htmlspecialchars($cfg['nombre_fantasia'] ?: $cfg['razon_social'], ENT_XML1));
        $gEmis->addChild('dDirEmi', htmlspecialchars($cfg['direccion'] ?: 'Asuncion', ENT_XML1));
        $gEmis->addChild('dNumCas', '0');
        $gEmis->addChild('cDepEmi', $cfg['departamento'] ?: '1');
        $gEmis->addChild('dDesDepEmi', 'CAPITAL');
        $gEmis->addChild('cDisEmi', $cfg['distrito'] ?: '1');
        $gEmis->addChild('dDesDisEmi', 'ASUNCION');
        $gEmis->addChild('cCiuEmi', $cfg['ciudad'] ?: '1');
        $gEmis->addChild('dDesCiuEmi', 'ASUNCION (DISTRITO)');
        $gEmis->addChild('dTelEmi', $cfg['telefono'] ?: '021000000');
        $gEmis->addChild('dEmailE', $cfg['email'] ?: 'admin@empresa.com.py');

        $gActEco = $gEmis->addChild('gActEco');
        $gActEco->addChild('cActEco', $cfg['actividad_economica'] ?: '56101');
        $gActEco->addChild('dDesActEco', htmlspecialchars($cfg['actividad_descripcion'] ?: 'Restaurante', ENT_XML1));

        // Receptor
        $gDatRec = $gDatGralOpe->addChild('gDatRec');
        $ruc_rec = !empty($cust['taxid']) ? $cust['taxid'] : '';
        if (!empty($ruc_rec)) {
            $parts = explode('-', $ruc_rec);
            $gDatRec->addChild('iNatRec', '1'); // Contribuyente
            $gDatRec->addChild('iTiOpe', '1');
            $gDatRec->addChild('dRucRec', $parts[0]);
            $gDatRec->addChild('dDVRec', $parts[1] ?? '0');
        } else {
            $gDatRec->addChild('iNatRec', '2'); // No contribuyente
            $gDatRec->addChild('iTiOpe', '2'); // B2C
            $gDatRec->addChild('iTiContRec', '1');
        }
        $gDatRec->addChild('dNomRec', htmlspecialchars(!empty($cust['name']) ? $cust['name'] : 'Sin Nombre', ENT_XML1));

        // Detalles de factura
        $gDtipDE = $de->addChild('gDtipDE');
        $gCamFE = $gDtipDE->addChild('gCamFE');
        $gCamFE->addChild('iIndPres', '1'); // En mostrador / presencial
        $gCamFE->addChild('dDesIndPres', 'Operación presencial');

        $gCamCond = $gDtipDE->addChild('gCamCond');
        $gCamCond->addChild('iCondOpe', '1'); // Contado
        $gCamCond->addChild('dDesCondOpe', 'Contado');

        $gPaConEIni = $gCamCond->addChild('gPaConEIni');
        $gPaConEIni->addChild('iTiPago', '1'); // Efectivo
        $gPaConEIni->addChild('dDesTiPago', 'Efectivo');
        $gPaConEIni->addChild('dMonTiPago', number_format((float)$inv['total'], 0, '', ''));
        $gPaConEIni->addChild('cMoneTiPago', 'PYG');

        // Items
        $sec_item = 1;
        foreach ($items as $it) {
            $gCamItem = $gDtipDE->addChild('gCamItem');
            $gCamItem->addChild('dSecItem', (string)$sec_item);
            $gCamItem->addChild('dCodInt', $it['code'] ?: 'PROD' . $it['pid']);
            $gCamItem->addChild('dDesProSer', htmlspecialchars($it['product'], ENT_XML1));
            $gCamItem->addChild('cUniMed', '77'); // Unidad
            $gCamItem->addChild('dDesUniMed', 'Unidad');
            $gCamItem->addChild('dCantProSer', number_format((float)$it['qty'], 2, '.', ''));

            $gValorItem = $gCamItem->addChild('gValorItem');
            $gValorRestaItem = $gValorItem->addChild('gValorRestaItem');
            $gValorRestaItem->addChild('dPUniProSer', number_format((float)$it['price'], 0, '', ''));
            $gValorRestaItem->addChild('dTotBruOpeItem', number_format((float)$it['subtotal'], 0, '', ''));
            $gValorRestaItem->addChild('dTotOpeItem', number_format((float)$it['subtotal'], 0, '', ''));

            $gCamIVA = $gCamItem->addChild('gCamIVA');
            $gCamIVA->addChild('iAfecIVA', '1'); // Gravado
            $gCamIVA->addChild('dDesAfecIVA', 'Gravado IVA');
            $gCamIVA->addChild('dPropIVA', '100');
            $gCamIVA->addChild('dTasaIVA', '10');
            $desg = $this->desglosar_iva($it['subtotal'], 10);
            $gCamIVA->addChild('dBasGravIVA', number_format($desg['gravada_10'], 0, '', ''));
            $gCamIVA->addChild('dLiqIVAItem', number_format($desg['iva'], 0, '', ''));

            $sec_item++;
        }

        // Totales
        $gTotSub = $de->addChild('gTotSub');
        $gTotSub->addChild('dSub10', number_format($grav10, 0, '', ''));
        $gTotSub->addChild('dSub5', number_format($grav5, 0, '', ''));
        $gTotSub->addChild('dSubExo', number_format($exenta, 0, '', ''));
        $gTotSub->addChild('dTotOpe', number_format((float)$inv['total'], 0, '', ''));
        $gTotSub->addChild('dTotGralOpe', number_format((float)$inv['total'], 0, '', ''));
        $gTotSub->addChild('dIVA10', number_format($iva10, 0, '', ''));
        $gTotSub->addChild('dIVA5', number_format($iva5, 0, '', ''));
        $gTotSub->addChild('dTotIVA', number_format($iva10 + $iva5, 0, '', ''));
        $gTotSub->addChild('dBaseGrav10', number_format($grav10, 0, '', ''));
        $gTotSub->addChild('dBaseGrav5', number_format($grav5, 0, '', ''));
        $gTotSub->addChild('dTotBasGrav', number_format($grav10 + $grav5, 0, '', ''));

        // QR
        $gCamFuFD = $de->addChild('gCamFuFD');
        $gCamFuFD->addChild('dCarQR', htmlspecialchars($qr_url, ENT_XML1));

        return $xml->asXML();
    }

    /**
     * Firmar digitalmente el XML usando el certificado .p12 / .pfx (XMLDSig)
     */
    public function firmar_xml($xml_content, $cert_path, $cert_password)
    {
        if (!file_exists($cert_path)) {
            return false;
        }

        $p12_content = file_get_contents($cert_path);
        $certs = [];
        if (!openssl_pkcs12_read($p12_content, $certs, $cert_password)) {
            return false;
        }

        $private_key = $certs['pkey'];
        $certificate = $certs['cert'];

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;
        if (!@$doc->loadXML($xml_content)) {
            return false;
        }

        $root = $doc->documentElement;
        $de_element = $doc->getElementsByTagName('DE')->item(0);
        if (!$de_element) {
            return false;
        }

        $canonical_de = $de_element->C14N(true, false);
        $digest_value = base64_encode(hash('sha256', $canonical_de, true));

        // Construir nodo Signature
        $sig_element = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'Signature');
        $signed_info = $doc->createElement('SignedInfo');

        $c14n_method = $doc->createElement('CanonicalizationMethod');
        $c14n_method->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
        $signed_info->appendChild($c14n_method);

        $sig_method = $doc->createElement('SignatureMethod');
        $sig_method->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256');
        $signed_info->appendChild($sig_method);

        $reference = $doc->createElement('Reference');
        $reference->setAttribute('URI', '#' . $de_element->getAttribute('Id'));

        $transforms = $doc->createElement('Transforms');
        $transform1 = $doc->createElement('Transform');
        $transform1->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
        $transforms->appendChild($transform1);

        $transform2 = $doc->createElement('Transform');
        $transform2->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');
        $transforms->appendChild($transform2);
        $reference->appendChild($transforms);

        $digest_method = $doc->createElement('DigestMethod');
        $digest_method->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
        $reference->appendChild($digest_method);

        $digest_val_el = $doc->createElement('DigestValue', $digest_value);
        $reference->appendChild($digest_val_el);

        $signed_info->appendChild($reference);
        $sig_element->appendChild($signed_info);

        // Firmar SignedInfo
        $canonical_si = $signed_info->C14N(true, false);
        $signature_binary = '';
        if (!openssl_sign($canonical_si, $signature_binary, $private_key, OPENSSL_ALGO_SHA256)) {
            return false;
        }

        $sig_val_el = $doc->createElement('SignatureValue', base64_encode($signature_binary));
        $sig_element->appendChild($sig_val_el);

        $key_info = $doc->createElement('KeyInfo');
        $x509_data = $doc->createElement('X509Data');

        // Limpiar certificado de cabeceras PEM
        $cert_clean = preg_replace('/-----BEGIN CERTIFICATE-----|-----END CERTIFICATE-----|\r|\n/', '', $certificate);
        $x509_cert = $doc->createElement('X509Certificate', $cert_clean);
        $x509_data->appendChild($x509_cert);
        $key_info->appendChild($x509_data);
        $sig_element->appendChild($key_info);

        $root->appendChild($sig_element);

        return $doc->saveXML();
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
