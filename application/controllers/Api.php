<?php
/**
 * GeeksPOS - REST API Controller for Multi-System Integration
 * Supporting n8n, WhatsApp Bots, Ecommerce, Delivery Apps, and Thermal Printing.
 */
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Api extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api_model');
        $this->config->load('api_config', false, true);
        $this->handle_cors();
    }

    /**
     * Cross-Origin Resource Sharing (CORS) handler for web applications
     */
    protected function handle_cors()
    {
        $cors = $this->config->item('api_cors');
        if (!empty($cors['enabled'])) {
            $origin = $this->input->server('HTTP_ORIGIN') ?: '*';
            if (in_array('*', $cors['allowed_origins']) || in_array($origin, $cors['allowed_origins'])) {
                header('Access-Control-Allow-Origin: ' . $origin);
                header('Access-Control-Allow-Methods: ' . implode(', ', $cors['allowed_methods']));
                header('Access-Control-Allow-Headers: ' . implode(', ', $cors['allowed_headers']));
                header('Access-Control-Max-Age: ' . ($cors['max_age'] ?? 86400));
            }
            if (strtolower($this->input->server('REQUEST_METHOD')) === 'options') {
                exit(0);
            }
        }
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
        if (!empty($this->request->body) && is_array($this->request->body)) {
            return $this->request->body;
        }
        if (!empty($this->_post_args) && is_array($this->_post_args)) {
            return $this->_post_args;
        }
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $json = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                return $json;
            }
        }
        return $_POST ?: [];
    }

    /**
     * GET /api/ping
     * Quick healthcheck and connectivity test for n8n and other systems
     */
    public function ping_get()
    {
        $base = $this->api_model->get_base_url();
        $this->response([
            'status' => true,
            'message' => 'GeeksPOS API online',
            'system' => $this->config->item('api_name') ?: 'GeeksPOS API Engine',
            'version' => $this->config->item('api_version') ?: '1.1',
            'base_url' => $base,
            'timestamp' => date('Y-m-d H:i:s')
        ], REST_Controller::HTTP_OK);
    }

    /**
     * GET /api/config or GET /api/info
     * Full discovery endpoint: returns addresses, endpoints, registered systems, and defaults
     */
    public function config_get()
    {
        $base = $this->api_model->get_base_url();
        $systems = $this->config->item('api_systems') ?: [];

        // Sanitize systems list (hide private keys for security)
        $clean_systems = [];
        foreach ($systems as $sys_id => $sys) {
            $clean_systems[$sys_id] = [
                'name'               => $sys['name'] ?? $sys_id,
                'description'        => $sys['description'] ?? '',
                'default_order_type' => $sys['default_order_type'] ?? 'delivery',
                'auto_print'         => !empty($sys['auto_print']),
                'paper_width'        => $sys['paper_width'] ?? 80,
                'has_webhook'        => !empty($sys['webhook_url'])
            ];
        }

        $this->response([
            'status' => true,
            'api_info' => [
                'name'        => $this->config->item('api_name') ?: 'GeeksPOS Multi-System REST API Engine',
                'version'     => $this->config->item('api_version') ?: '1.1',
                'environment' => $this->config->item('api_environment') ?: 'production',
                'base_url'    => $base,
                'api_url'     => $base . 'api/'
            ],
            'registered_systems' => $clean_systems,
            'defaults' => $this->config->item('api_defaults') ?: [
                'employee_id' => 11,
                'location_id' => 0,
                'customer_id' => 1,
                'payment_method' => 'Cash',
                'payment_status' => 'due',
                'thermal_paper_width' => 80,
                'ticket_auto_print' => true
            ],
            'allowed_order_types' => $this->config->item('api_allowed_order_types') ?: ['delivery', 'pickup', 'dinein'],
            'allowed_statuses'    => $this->config->item('api_allowed_statuses') ?: ['paid', 'due', 'canceled', 'partial'],
            'endpoints' => [
                'ping'             => ['method' => 'GET',  'url' => $base . 'api/ping', 'description' => 'Test de conexión'],
                'config'           => ['method' => 'GET',  'url' => $base . 'api/config', 'description' => 'Configuración y descubrimiento'],
                'products_list'    => ['method' => 'GET',  'url' => $base . 'api/products?search={term}&limit=50', 'description' => 'Consulta de catálogo y stock'],
                'product_detail'   => ['method' => 'GET',  'url' => $base . 'api/products/{id}', 'description' => 'Detalle de producto por ID'],
                'customers_search' => ['method' => 'GET',  'url' => $base . 'api/customers?phone={phone}', 'description' => 'Búsqueda por teléfono de WhatsApp'],
                'customer_create'  => ['method' => 'POST', 'url' => $base . 'api/customers', 'description' => 'Crear o actualizar cliente'],
                'orders_create'    => ['method' => 'POST', 'url' => $base . 'api/orders', 'description' => 'Crear pedido/factura desde WhatsApp'],
                'orders_list'      => ['method' => 'GET',  'url' => $base . 'api/orders?status={status}&limit=20', 'description' => 'Listar pedidos recientes'],
                'order_detail'     => ['method' => 'GET',  'url' => $base . 'api/orders/{id}', 'description' => 'Detalle completo del pedido'],
                'order_status'     => ['method' => 'POST', 'url' => $base . 'api/orders/{id}/status', 'description' => 'Actualizar estado del pedido'],
                'ticket'           => ['method' => 'GET',  'url' => $base . 'api/ticket/{id}', 'description' => 'Obtener enlaces del ticket térmico']
            ]
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
     * Create order & invoice from external systems (n8n WhatsApp, Ecommerce, etc.)
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

        // Detect originating external system
        $system = $this->input->server('HTTP_X_SYSTEM') ?: (isset($payload['system']) ? $payload['system'] : 'n8n_whatsapp');

        $result = $this->api_model->create_order($payload, $system);

        if (!$result['status']) {
            $this->response($result, REST_Controller::HTTP_BAD_REQUEST);
        }

        // Direct hardware ESC/POS print trigger if requested and server printer configured
        if (!empty($payload['print_ticket']) || !empty($payload['auto_print'])) {
            $order_id = $result['order']['id'];
            $this->load->library('Printer');
            $printer = $this->printer->check(0);
            if ($printer && @$printer['val2'] == 'server') {
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
     * Dispatches webhook to external system if configured!
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
            // Dispatch webhook notification to external system (e.g. n8n)
            $this->api_model->dispatch_webhook($id, $status, $notes);
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

        $base = $this->api_model->get_base_url();
        $token = hash_hmac('ripemd160', $id, $this->config->item('encryption_key'));
        $this->response([
            'status' => true,
            'order_id' => $id,
            'invoice_number' => $order['tid'],
            'order_name' => $order['order_name'],
            'order_type' => $order['order_type'],
            'total' => $order['total'],
            'ticket_html_80mm' => $base . "pos_invoices/ticket?id=$id&token=$token&width=80",
            'ticket_html_58mm' => $base . "pos_invoices/ticket?id=$id&token=$token&width=58",
            'ticket_html_autoprint' => $base . "pos_invoices/ticket?id=$id&token=$token&print=1",
            'ticket_pdf' => $base . "pos_invoices/thermal_pdf?id=$id&token=$token"
        ], REST_Controller::HTTP_OK);
    }
}
