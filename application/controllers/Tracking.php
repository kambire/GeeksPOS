<?php
/**
 * GeeksPOS - Customer Live GPS Tracking Controller
 * Portal público para que el cliente vea en vivo el recorrido de su repartidor
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Tracking extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('delivery_model');
    }

    /**
     * Portal web público de seguimiento
     * URL: /tracking/{hash}
     */
    public function index($hash = '')
    {
        $hash = trim($hash ?: $this->uri->segment(2));

        if (empty($hash)) {
            show_404();
            return;
        }

        $data = $this->delivery_model->get_tracking_data_by_hash($hash);

        if (!$data || empty($data['status'])) {
            show_error('El enlace de seguimiento no es válido o ha expirado.', 404, 'Seguimiento no encontrado');
            return;
        }

        $data['tracking_hash'] = $hash;
        $this->load->view('delivery_app/tracking', $data);
    }

    /**
     * Endpoint AJAX para polling en vivo de coordenadas
     * URL: /tracking/live/{hash}
     */
    public function live($hash = '')
    {
        $hash = trim($hash ?: $this->uri->segment(3));
        $data = $this->delivery_model->get_tracking_data_by_hash($hash);

        if (!$data || empty($data['status'])) {
            $this->output
                 ->set_status_header(404)
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => false, 'message' => 'No encontrado']));
            return;
        }

        $this->output
             ->set_content_type('application/json', 'utf-8')
             ->set_output(json_encode([
                 'status'       => true,
                 'estado'       => $data['order']['estado'],
                 'lat'          => $data['delivery']['lat'],
                 'lng'          => $data['delivery']['lng'],
                 'last_gps_at'  => $data['delivery']['last_gps_at'],
                 'delivered_at' => $data['order']['delivered_at']
             ], JSON_UNESCAPED_UNICODE));
    }
}
