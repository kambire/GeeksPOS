<?php
/**
 * GeeksPOS - Kitchen Display System (KDS) Controller
 * Real-time order preparation management screen for kitchen and cooks.
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Kitchen extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
            exit;
        }

        $this->load->model('kitchen_model', 'kitchen');
    }

    /**
     * Main Kitchen Screen (KDS)
     */
    public function index()
    {
        $loc_id = (int)$this->aauth->get_user()->loc;
        $data['title'] = 'Panel de Cocina en Vivo (KDS) - GeeksPOS';
        $data['usernm'] = $this->aauth->get_user()->username;
        $data['is_cocina_user'] = ($data['usernm'] === 'cocina');
        $data['stats'] = $this->kitchen->get_stats($loc_id);

        $this->load->view('kitchen/index', $data);
    }

    /**
     * AJAX: Get orders for real-time polling
     */
    public function api_orders()
    {
        $loc_id = (int)$this->aauth->get_user()->loc;
        $filter = $this->input->get('filter', true) ?: 'active';
        $orders = $this->kitchen->get_orders($loc_id, $filter);
        $stats  = $this->kitchen->get_stats($loc_id);

        header('Content-Type: application/json');
        echo json_encode(array(
            'status'       => true,
            'orders'       => $orders,
            'stats'        => $stats,
            'latest_id'    => $stats['latest_order_id'],
            'server_time'  => time(),
            'filter'       => $filter
        ));
    }

    /**
     * AJAX: Update kitchen order status
     */
    public function update_status()
    {
        $id = (int)$this->input->post('id');
        $new_status = trim($this->input->post('status', true));

        if ($id <= 0 || empty($new_status)) {
            echo json_encode(array('status' => false, 'message' => 'Parámetros inválidos.'));
            return;
        }

        $res = $this->kitchen->update_status($id, $new_status);
        if ($res) {
            echo json_encode(array(
                'status'  => true,
                'message' => 'Comanda actualizada con éxito.'
            ));
        } else {
            echo json_encode(array(
                'status'  => false,
                'message' => 'No se pudo actualizar el estado.'
            ));
        }
    }

    /**
     * AJAX: Mini-endpoint for Navbar badge count
     */
    public function badge_stats()
    {
        $loc_id = (int)$this->aauth->get_user()->loc;
        $stats = $this->kitchen->get_stats($loc_id);
        header('Content-Type: application/json');
        echo json_encode(array(
            'status'        => true,
            'active_count'  => $stats['total_activos'],
            'pendientes'    => $stats['pendientes'],
            'cooking'       => $stats['en_preparacion']
        ));
    }
}
