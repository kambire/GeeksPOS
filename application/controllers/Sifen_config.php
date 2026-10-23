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
            2,
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
}
