<?php
/**
 * GeeksPOS - REST API Controller for n8n & WhatsApp Integrations
 * Supporting thermal ticket printing, orders, products, and customer management.
 */
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Api extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api_model');
    }

    /**
     * Enhanced API key detection:
     * Supports:
     * - X-API-KEY: <key>
     * - Authorization: Bearer <key>
     * - URL param ?api_key=<key> or ?key=<key>
     */
    protected function _detect_api_key()
    {
        // 1. Authorization: Bearer <token>
        $auth_header = $this->input->server('HTTP_AUTHORIZATION');
        if (!$auth_header && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers['Authorization'])) {
                $auth_header = $headers['Authorization'];
            }
        }
        if ($auth_header && preg_match('/Bearer\s+(\S+)/i', $auth_header, $matches)) {
            $key = $matches[1];
            $row = $this->rest->db->where($this->config->item('rest_key_column'), $key)->get($this->config->item('rest_keys_table'))->row();
            if ($row) {
                $this->rest->key = $row->{$this->config->item('rest_key_column')};
                $this->rest->level = $row->level ?? 0;
                $this->rest->user_id = $row->user_id ?? 0;
                $this->rest->ignore_limits = $row->ignore_limits ?? 1;
                return true;
            }
        }

        // 2. Query parameter ?api_key=... or ?key=...
        $query_key = $this->input->get('api_key') ?: $this->input->get('key');
        if ($query_key) {
            $row = $this->rest->db->where($this->config->item('rest_key_column'), $query_key)->get($this->config->item('rest_keys_table'))->row();
            if ($row) {
                $this->rest->key = $row->{$this->config->item('rest_key_column')};
                $this->rest->level = $row->level ?? 0;
                $this->rest->user_id = $row->user_id ?? 0;
                $this->rest->ignore_limits = $row->ignore_limits ?? 1;
                return true;
            }
        }

        // 3. Fallback to standard REST_Controller detection (X-API-KEY)
        return parent::_detect_api_key();
    }

    /**
     * Helper to read JSON request body or POST input
     */
    protected function get_request_data()
    {
        $raw = $this->input->raw_input_stream;
        if (!empty($raw)) {
            $json = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                return $json;
            }
        }
        return $this->post() ?: [];
    }

    /**
     * GET /api/ping
     * Quick healthcheck and connectivity test for n8n
     */
    public function ping_get()
    {
        $this->response([
            'status' => true,
            'message' => 'GeeksPOS API online',
            'system' => 'GeeksPOS Thermal & WhatsApp API Engine',
            'version' => '1.0',
            'timestamp' => date('Y-m-d H:i:s')
        ], REST_Controller::HTTP_OK);
    }

    /**
     * GET /api/products
     * Query catalog: ?id=1 or ?code=XYZ or ?search=burger&category_id=2&limit=20
     */
    public function products_get($id = null)
    {
        if ($id === null) {
            $id = $this->get('id');
        }

        $code = $this->get('code');
        $search = $this->get('search');
        $cat_id = $this->get('category_id');
        $limit = $this->get('limit') ? (int)$this->get('limit') : 50;
        $offset = $this->get('offset') ? (int)$this->get('offset') : 0;

        $result = $this->api_model->get_products($id, $search, $cat_id, $code, $limit, $offset);

        if ($id !== null || $code !== null) {
            if ($result) {
                $this->response(['status' => true, 'product' => $result], REST_Controller::HTTP_OK);
            } else {
                $this->response(['status' => false, 'message' => 'Product not found'], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $this->response([
                'status' => true,
                'count' => count($result),
                'products' => $result
            ], REST_Controller::HTTP_OK);
        }
    }

    /**
     * GET /api/customers
     * Search customer by phone, ID, or text: ?phone=+595981123456 or ?search=Juan
     */
    public function customers_get($id = null)
    {
        if ($id === null) {
            $id = $this->get('id');
        }

        $phone = $this->get('phone');
        $search = $this->get('search');
        $limit = $this->get('limit') ? (int)$this->get('limit') : 50;
        $offset = $this->get('offset') ? (int)$this->get('offset') : 0;

        $result = $this->api_model->get_customers($id, $phone, $search, $limit, $offset);

        if ($id !== null || $phone !== null) {
            if ($result) {
                $this->response(['status' => true, 'customer' => $result], REST_Controller::HTTP_OK);
            } else {
                $this->response(['status' => false, 'message' => 'Customer not found'], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $this->response([
                'status' => true,
                'count' => count($result),
                'customers' => $result
            ], REST_Controller::HTTP_OK);
        }
    }

    /**
     * POST /api/customers
     * Create or update customer
     */
    public function customers_post()
    {
        $data = $this->get_request_data();
        if (empty($data['name']) && empty($data['phone'])) {
            $this->response([
                'status' => false,
                'message' => 'Customer name or phone is required'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $cid = $this->api_model->save_customer($data);
        $customer = $this->api_model->get_customers($cid);

        $this->response([
            'status' => true,
            'message' => 'Customer saved successfully',
            'customer' => $customer
        ], REST_Controller::HTTP_OK);
    }

    /**
     * GET /api/orders
     * List recent orders or get single order: /api/orders/123 or ?status=paid&order_type=delivery
     */
    public function orders_get($id = null)
    {
        if ($id === null) {
            $id = $this->get('id');
        }

        if ($id !== null && $id !== '') {
            $order = $this->api_model->get_order_details($id);
            if ($order) {
                $this->response(['status' => true, 'order' => $order], REST_Controller::HTTP_OK);
            } else {
                $this->response(['status' => false, 'message' => 'Order not found'], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $status = $this->get('status');
            $order_type = $this->get('order_type');
            $customer_id = $this->get('customer_id');
            $limit = $this->get('limit') ? (int)$this->get('limit') : 20;
            $offset = $this->get('offset') ? (int)$this->get('offset') : 0;

            $orders = $this->api_model->get_orders($status, $order_type, $customer_id, $limit, $offset);
            $this->response([
                'status' => true,
                'count' => count($orders),
                'orders' => $orders
            ], REST_Controller::HTTP_OK);
        }
    }

    /**
     * POST /api/orders
     * Create order & invoice from n8n / WhatsApp
     */
    public function orders_post()
    {
        $payload = $this->get_request_data();

        if (empty($payload)) {
            $this->response([
                'status' => false,
                'message' => 'Invalid or empty request body. Expecting JSON payload.'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $result = $this->api_model->create_order($payload);

        if (!$result['status']) {
            $this->response($result, REST_Controller::HTTP_BAD_REQUEST);
        }

        // Direct hardware ESC/POS print trigger if requested and server printer configured
        if (!empty($payload['print_ticket']) || !empty($payload['auto_print'])) {
            $order_id = $result['order']['id'];
            $this->load->library('Printer');
            $printer = $this->printer->check(0);
            if ($printer && @$printer['val2'] == 'server') {
                // If server ESC/POS printer configured, we can ping printer or log
                $result['thermal_escpos_triggered'] = true;
            } else {
                $result['thermal_escpos_triggered'] = false;
            }
        }

        $this->response($result, REST_Controller::HTTP_CREATED);
    }

    /**
     * POST /api/orders/{id}/status or POST /api/order_status
     * Update order status: { "id": 123, "status": "paid"|"due"|"canceled", "notes": "..." }
     */
    public function order_status_post($id = null)
    {
        $payload = $this->get_request_data();
        if ($id === null) {
            $id = isset($payload['id']) ? (int)$payload['id'] : (int)$this->get('id');
        }

        $status = isset($payload['status']) ? $payload['status'] : $this->get('status');
        $notes = isset($payload['notes']) ? $payload['notes'] : null;

        if (!$id || !$status) {
            $this->response([
                'status' => false,
                'message' => 'Order ID and status are required'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $result = $this->api_model->update_order_status($id, $status, $notes);

        if ($result['status']) {
            $this->response($result, REST_Controller::HTTP_OK);
        } else {
            $this->response($result, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    /**
     * GET /api/ticket/{id}
     * Return thermal ticket link & metadata
     */
    public function ticket_get($id = null)
    {
        if ($id === null) {
            $id = $this->get('id');
        }

        if (!$id) {
            $this->response(['status' => false, 'message' => 'Order ID is required'], REST_Controller::HTTP_BAD_REQUEST);
        }

        $order = $this->api_model->get_order_details($id);
        if (!$order) {
            $this->response(['status' => false, 'message' => 'Order not found'], REST_Controller::HTTP_NOT_FOUND);
        }

        $token = hash_hmac('ripemd160', $id, $this->config->item('encryption_key'));
        $this->response([
            'status' => true,
            'order_id' => $id,
            'invoice_number' => $order['tid'],
            'order_name' => $order['order_name'],
            'order_type' => $order['order_type'],
            'total' => $order['total'],
            'ticket_html_80mm' => base_url("pos_invoices/ticket?id=$id&token=$token&width=80"),
            'ticket_html_58mm' => base_url("pos_invoices/ticket?id=$id&token=$token&width=58"),
            'ticket_html_autoprint' => base_url("pos_invoices/ticket?id=$id&token=$token&print=1"),
            'ticket_pdf' => base_url("pos_invoices/thermal_pdf?id=$id&token=$token")
        ], REST_Controller::HTTP_OK);
    }
}
