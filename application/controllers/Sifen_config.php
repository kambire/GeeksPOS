<?php
/**
 * GeeksPOS - SIFEN Paraguay Configuration Controller
 * Facturación Electrónica Nacional (DNIT / SIFEN - e-Kuatia)
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Sifen_config extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->li_a = 'sifen';

        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }

        if ($this->aauth->get_user()->roleid < 4) {
            exit('<h3>Acceso denegado. Se requieren permisos de administrador.</h3>');
        }

        $this->load->library('sifen');
    }

    /**
     * Pantalla de configuración principal de SIFEN
     */
    public function index()
    {
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title']  = 'Facturación Electrónica SIFEN (Paraguay)';

        $config = $this->sifen->get_config();

        // Validar certificado actual si existe
        $cert_info = null;
        if (!empty($config['cert_file'])) {
            $cert_abs = FCPATH . $config['cert_file'];
            if (file_exists($cert_abs) && !empty($config['cert_pass'])) {
                $cert_info = $this->sifen->validar_certificado($cert_abs, $config['cert_pass']);
            } elseif (!file_exists($cert_abs)) {
                $cert_info = ['status' => false, 'message' => 'El archivo del certificado no se encuentra en el servidor.'];
            }
        }

        $data['config']    = $config;
        $data['cert_info'] = $cert_info;

        $this->load->view('fixed/header', $head);
        $this->load->view('sifen/config', $data);
        $this->load->view('fixed/footer');
    }

    /**
     * Guardar configuración y subida de certificado digital
     */
    public function save()
    {
        if (!$this->input->post()) {
            redirect('sifen_config');
            return;
        }

        $current_config = $this->sifen->get_config();

        $habilitado            = (int)$this->input->post('habilitado');
        $ambiente              = $this->input->post('ambiente') === 'prod' ? 'prod' : 'test';
        $ruc                   = preg_replace('/[^0-9]/', '', $this->input->post('ruc', true));
        $dv                    = (string)$this->input->post('dv', true);
        if ($dv === '' || strlen($dv) === 0) {
            $dv = $this->sifen->calcular_dv_ruc($ruc);
        }
        $razon_social          = $this->input->post('razon_social', true);
        $nombre_fantasia       = $this->input->post('nombre_fantasia', true);
        $timbrado              = preg_replace('/[^0-9]/', '', $this->input->post('timbrado', true));
        $timbrado_fecha        = $this->input->post('timbrado_fecha', true);
        $establecimiento       = sprintf('%03d', (int)$this->input->post('establecimiento'));
        $punto_expedicion      = sprintf('%03d', (int)$this->input->post('punto_expedicion'));
        $secuencia_actual      = max(1, (int)$this->input->post('secuencia_actual'));
        $actividad_economica   = $this->input->post('actividad_economica', true);
        $actividad_descripcion = $this->input->post('actividad_descripcion', true);
        $direccion             = $this->input->post('direccion', true);
        $telefono              = $this->input->post('telefono', true);
        $email                 = $this->input->post('email', true);
        $departamento          = $this->input->post('departamento', true) ?: '1';
        $distrito              = $this->input->post('distrito', true) ?: '1';
        $ciudad                = $this->input->post('ciudad', true) ?: '1';
        $csc_id                = $this->input->post('csc_id', true) ?: '0001';
        $csc_val               = trim($this->input->post('csc_val', true));
        $cert_pass             = $this->input->post('cert_pass', true);

        // Si no se proporcionó contraseña nueva, conservar la anterior
        if (empty($cert_pass) && !empty($current_config['cert_pass'])) {
            $cert_pass = $current_config['cert_pass'];
        }

        $cert_file_path = $current_config['cert_file'];

        // Manejo de carga de archivo de Certificado Digital (.p12 / .pfx)
        if (isset($_FILES['cert_file']) && !empty($_FILES['cert_file']['name'])) {
            $file_name = $_FILES['cert_file']['name'];
            $file_tmp  = $_FILES['cert_file']['tmp_name'];
            $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($ext, ['p12', 'pfx'])) {
                $this->session->set_flashdata('error_msg', 'Formato de certificado no válido. Debe ser un archivo .p12 o .pfx');
                redirect('sifen_config');
                return;
            }

            $dest_dir = FCPATH . 'userfiles/sifen/certs/';
            if (!is_dir($dest_dir)) {
                @mkdir($dest_dir, 0755, true);
            }

            $stored_name = 'cert_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $dest_path   = $dest_dir . $stored_name;

            if (move_uploaded_file($file_tmp, $dest_path)) {
                // Si había uno previo, eliminarlo
                if (!empty($cert_file_path) && file_exists(FCPATH . $cert_file_path)) {
                    @unlink(FCPATH . $cert_file_path);
                }
                $cert_file_path = 'userfiles/sifen/certs/' . $stored_name;

                // Validar certificado inmediatamente
                $val = $this->sifen->validar_certificado($dest_path, $cert_pass);
                if (!$val['status']) {
                    $this->session->set_flashdata('warning_msg', 'Certificado subido, pero no se pudo abrir con la contraseña indicada: ' . $val['message']);
                }
            } else {
                $this->session->set_flashdata('error_msg', 'Error al mover el archivo de certificado al directorio protegido.');
                redirect('sifen_config');
                return;
            }
        }

        $update_data = [
            'habilitado'            => $habilitado,
            'ambiente'              => $ambiente,
            'ruc'                   => $ruc,
            'dv'                    => $dv,
            'razon_social'          => $razon_social,
            'nombre_fantasia'       => $nombre_fantasia,
            'timbrado'              => $timbrado,
            'timbrado_fecha'        => $timbrado_fecha,
            'establecimiento'       => $establecimiento,
            'punto_expedicion'      => $punto_expedicion,
            'secuencia_actual'      => $secuencia_actual,
            'actividad_economica'   => $actividad_economica,
            'actividad_descripcion' => $actividad_descripcion,
            'direccion'             => $direccion,
            'telefono'              => $telefono,
            'email'                 => $email,
            'departamento'          => $departamento,
            'distrito'              => $distrito,
            'ciudad'                => $ciudad,
            'csc_id'                => $csc_id,
            'csc_val'               => $csc_val,
            'cert_file'             => $cert_file_path,
            'cert_pass'             => $cert_pass,
            'updated_at'            => date('Y-m-d H:i:s')
        ];

        $this->sifen->save_config($update_data);

        $this->session->set_flashdata('success_msg', 'Configuración de SIFEN guardada exitosamente.');
        redirect('sifen_config');
    }

    /**
     * Endpoint AJAX para probar y validar el certificado digital
     */
    public function test_cert()
    {
        $config = $this->sifen->get_config();
        if (empty($config['cert_file'])) {
            echo json_encode(['status' => false, 'message' => 'No hay ningún certificado configurado aún.']);
            return;
        }

        $cert_abs = FCPATH . $config['cert_file'];
        $val = $this->sifen->validar_certificado($cert_abs, $config['cert_pass']);
        echo json_encode($val);
    }

    /**
     * Generar un CDC y URL QR de prueba para verificación
     */
    public function test_sample()
    {
        $config = $this->sifen->get_config();

        $cdc_data = $this->sifen->generar_cdc(
            1,
            $config['ruc'],
            $config['dv'],
            $config['establecimiento'],
            $config['punto_expedicion'],
            $config['secuencia_actual'],
            $this->sifen->tipo_contribuyente($config['ruc'], $config['tipo_contribuyente'] ?? null),
            date('Y-m-d')
        );

        $qr_url = $this->sifen->generar_qr_url(
            $cdc_data['cdc'],
            date('Y-m-d H:i:s'),
            '44444401-7',
            150000,
            13636,
            2,
            sha1($cdc_data['cdc']),
            $config
        );

        $qr_data_uri = $this->sifen->generar_qr_data_uri($qr_url, 180);

        echo json_encode([
            'status'       => true,
            'cdc'          => $cdc_data['cdc'],
            'cdc_formato'  => $this->sifen->formatear_cdc($cdc_data['cdc']),
            'numero'       => sprintf('%03d-%03d-%07d', $config['establecimiento'], $config['punto_expedicion'], $config['secuencia_actual']),
            'qr_url'       => $qr_url,
            'qr_preview'   => $qr_data_uri
        ]);
    }

    /**
     * Panel de pruebas de los servicios web (Guía de Pruebas e-kuatia)
     */
    public function pruebas()
    {
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title']  = 'Pruebas SIFEN';

        $data['config'] = $this->sifen->get_config();
        $data['facturas'] = $this->db->select('id, tid, invoicedate, total, cdc, sifen_numero, sifen_estado')
            ->from('geopos_invoices')->where('cdc IS NOT NULL')->where("cdc <> ''")
            ->order_by('id', 'DESC')->limit(50)->get()->result_array();
        $data['log'] = $this->db->select('id, operacion, ambiente, invoice_id, cdc, referencia, estado, codigo, mensaje, protocolo, http_code, created_at')
            ->from('geopos_sifen_log')->order_by('id', 'DESC')->limit(50)->get()->result_array();

        // Avance de las pruebas mínimas de la Guía de Pruebas (solo Factura Electrónica, ambiente test)
        $cuenta = function ($where) {
            return (int)$this->db->where('ambiente', 'test')->where($where)->count_all_results('geopos_sifen_log');
        };
        $data['avance'] = [
            ['Facturas aprobadas por WS síncrono', $cuenta("operacion = 'recibe' AND estado LIKE 'Aprobado%'"), 5],
            ['Facturas rechazadas por WS síncrono (datos con error)', $cuenta("operacion = 'recibe' AND estado = 'Rechazado'"), 5],
            ['Lotes recibidos por WS asíncrono', $cuenta("operacion = 'recibe-lote' AND codigo = '0300'"), 2],
            ['Consultas de lote concluidas', $cuenta("operacion = 'consulta-lote' AND codigo = '0362'"), 2],
            ['Consultas de DTE por CDC', $cuenta("operacion = 'consulta' AND codigo = '0422'"), 3],
            ['Consulta de RUC', $cuenta("operacion = 'consulta-ruc' AND codigo = '0502'"), 1],
            ['Cancelaciones aprobadas', $cuenta("operacion = 'evento' AND referencia = 'cancelacion' AND estado LIKE 'Aprobado%'"), 5],
            ['Inutilizaciones de facturas aprobadas', $cuenta("operacion = 'evento' AND referencia = 'inutilizacion' AND estado LIKE 'Aprobado%'"), 2]
        ];

        $this->load->view('fixed/header', $head);
        $this->load->view('sifen/pruebas', $data);
        $this->load->view('fixed/footer');
    }

    /**
     * Operaciones AJAX contra los servicios web de SIFEN
     */
    public function operacion()
    {
        $op = $this->input->post('op', true);
        switch ($op) {
            case 'enviar':
                $res = $this->sifen->enviar_de((int)$this->input->post('id'));
                break;
            case 'regenerar':
                $res = $this->sifen->regenerar_xml((int)$this->input->post('id'));
                unset($res['doc']);
                break;
            case 'consultar':
                $res = $this->sifen->consultar_de($this->input->post('cdc', true));
                break;
            case 'ruc':
                $res = $this->sifen->consultar_ruc_sifen($this->input->post('ruc', true));
                break;
            case 'lote':
                $res = $this->sifen->enviar_lote((array)$this->input->post('ids'));
                break;
            case 'consultar_lote':
                $res = $this->sifen->consultar_lote($this->input->post('lote', true));
                break;
            case 'cancelar':
                $res = $this->sifen->evento_cancelacion($this->input->post('cdc', true), $this->input->post('motivo', true));
                break;
            case 'inutilizar':
                $res = $this->sifen->evento_inutilizacion(
                    $this->input->post('desde'),
                    $this->input->post('hasta'),
                    $this->input->post('motivo', true),
                    (int)$this->input->post('tipo')
                );
                break;
            default:
                $res = ['status' => false, 'message' => 'Operación no válida.'];
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($res));
    }

    /**
     * Request y response XML de una operación registrada (para revisar mensajes de SIFEN)
     */
    public function log_detalle($id = 0)
    {
        $row = $this->db->select('id, operacion, request, response')
            ->get_where('geopos_sifen_log', ['id' => (int)$id])->row_array();
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($row ?: ['status' => false, 'message' => 'Registro no encontrado.']));
    }
}
