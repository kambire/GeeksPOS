<?php
/**
 * GeeksPOS - REST API Controller for Multi-System Integration
 * Supporting n8n, WhatsApp Bots, Ecommerce, Delivery Apps, and Thermal Printing.
 */
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Api extends REST_Controller
{
    /**
     * Local (tenant) y alcance de la API key autenticada:
     * ['key_id' => int, 'loc_id' => int|null, 'scope' => 'tenant'|'platform']
     */
    protected $tenant = null;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('api_model');
        $this->config->load('api_config', false, true);
        $this->config->load('geeksia', true, true);
        $this->handle_cors();

        // Las consultas de catálogo, pedidos y clientes de una key de local quedan limitadas a ese local
        if ($this->tenant && $this->tenant['scope'] === 'tenant') {
            $this->api_model->scope_loc = (int)$this->tenant['loc_id'];
        }
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
     * Detección de API key. Solo por encabezado (nunca en la URL, para que no quede en logs):
     * - X-API-KEY: <key>
     * - Authorization: Bearer <key>
     * La key se busca por su hash SHA-256 y define el local (tenant) de toda la petición.
     */
    protected function _detect_api_key()
    {
        $this->rest->key = null;
        $this->rest->level = null;
        $this->rest->user_id = null;
        $this->rest->ignore_limits = false;
        $this->tenant = null;

        $raw = $this->input->server('HTTP_X_API_KEY');
        if (!$raw) {
            $auth_header = $this->input->server('HTTP_AUTHORIZATION') ?: $this->input->server('REDIRECT_HTTP_AUTHORIZATION');
            if (!$auth_header && function_exists('apache_request_headers')) {
                $headers = array_change_key_case(apache_request_headers(), CASE_LOWER);
                $auth_header = $headers['authorization'] ?? null;
            }
            if ($auth_header && preg_match('/^Bearer\s+(\S+)$/i', trim($auth_header), $m)) {
                $raw = $m[1];
            }
        }
        $raw = trim((string)$raw);
        if ($raw === '' || strlen($raw) > 128) {
            return false;
        }

        $row = $this->rest->db->where('key_hash', hash('sha256', $raw))
                              ->where('revoked_at IS NULL', null, false)
                              ->get($this->config->item('rest_keys_table'))
                              ->row();
        if (!$row) {
            return false;
        }

        $scope = ($row->scope === 'platform') ? 'platform' : 'tenant';
        if ($scope === 'tenant' && empty($row->loc_id)) {
            return false; // key de local sin local asignado: no sirve
        }
        if ($scope === 'platform' && !$this->config->item('platform_key_allow_public', 'geeksia') && !$this->_is_private_ip($this->input->ip_address())) {
            return false; // las keys de plataforma solo desde la red interna (n8n en Docker)
        }

        $this->rest->key = $row->key_hash;
        $this->rest->level = (int)$row->level;
        $this->rest->user_id = (int)$row->user_id;
        $this->rest->ignore_limits = false;
        $this->tenant = ['key_id' => (int)$row->id, 'loc_id' => $row->loc_id !== null ? (int)$row->loc_id : null, 'scope' => $scope];
        return true;
    }

    protected function _is_private_ip($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }
        // Lo que llega por el túnel de Cloudflare o un proxy viene de internet aunque REMOTE_ADDR sea interno
        if ($this->input->server('HTTP_CF_CONNECTING_IP') || $this->input->server('HTTP_X_FORWARDED_FOR') || $this->input->server('HTTP_CF_RAY')) {
            return false;
        }
        return $ip === '127.0.0.1' || $ip === '::1'
            || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    protected function _is_platform()
    {
        return $this->tenant && $this->tenant['scope'] === 'platform';
    }

    protected function _deny($message, $code = REST_Controller::HTTP_FORBIDDEN)
    {
        $this->response(['status' => false, 'message' => $message], $code);
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
     * Resolve warehouse ID for a tenant / location to ensure 100% catalog isolation
     */
    protected function _resolve_warehouse_id()
    {
        // Key de local: siempre el almacén de su propio local, ignorando parámetros del request
        if (!$this->_is_platform()) {
            $loc = $this->db->where('id', (int)$this->tenant['loc_id'])->get('geopos_locations')->row();
            return ($loc && !empty($loc->ware)) ? (int)$loc->ware : -1;
        }

        $warehouse_id = $this->get('warehouse_id');
        if (!empty($warehouse_id)) {
            return (int)$warehouse_id;
        }

        $loc_id = $this->get('location_id') ?: $this->get('loc_id');
        $instance = $this->get('instance') ?: $this->get('whatsapp_instance');

        if (!empty($instance)) {
            $loc = $this->db->where('whatsapp_instance', $instance)->get('geopos_locations')->row();
            if ($loc && !empty($loc->ware)) {
                return (int)$loc->ware;
            }
        }

        if ($loc_id !== null && $loc_id !== '') {
            $loc = $this->db->where('id', (int)$loc_id)->get('geopos_locations')->row();
            if ($loc && !empty($loc->ware)) {
                return (int)$loc->ware;
            }
            if ((int)$loc_id === 0) {
                return 1; // Master warehouse
            }
        }

        return null;
    }

    /**
     * GET /api/products
     * Query catalog: ?id=1 or ?code=XYZ or ?search=burger&category_id=2&limit=20&location_id=1
     */
    public function products_get($id = null)
    {
        if ($id === null) {
            $id = $this->get('id');
        }

        $code = $this->get('code');
        $search = $this->get('search');
        $cat_id = $this->get('category_id');
        $warehouse_id = $this->_resolve_warehouse_id();
        $limit = $this->get('limit') ? (int)$this->get('limit') : 50;
        $offset = $this->get('offset') ? (int)$this->get('offset') : 0;

        $result = $this->api_model->get_products($id, $search, $cat_id, $code, $limit, $offset, $warehouse_id);

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
     * GET /api/menu
     * Complete categorized menu with images, descriptions and prices for WhatsApp Bots
     * Supports multi-tenant/location: ?location_id=1 or ?instance=pepito_burger
     */
    public function menu_get()
    {
        $warehouse_id = $this->_resolve_warehouse_id();
        $menu = $this->api_model->get_menu($warehouse_id);
        $this->response([
            'status' => true,
            'categories_count' => count($menu),
            'menu' => $menu
        ], REST_Controller::HTTP_OK);
    }

    /**
     * GET /api/promos or GET /api/promotions
     * Active promotions and coupons for WhatsApp Bot
     * Supports multi-location: ?location_id=1
     */
    public function promos_get()
    {
        $location_id = $this->_is_platform() ? ($this->get('location_id') ?: $this->get('warehouse_id')) : (int)$this->tenant['loc_id'];
        $promos = $this->api_model->get_promotions($location_id);
        $this->response([
            'status' => true,
            'count' => count($promos),
            'promotions' => $promos
        ], REST_Controller::HTTP_OK);
    }

    /**
     * GET /api/tenant?instance={instance_name} or GET /api/tenant?id={location_id}
     * Dynamic Tenant Lookup for Multi-Tenant WhatsApp & n8n
     */
    public function tenant_get()
    {
        if ($this->_is_platform()) {
            $instance = $this->get('instance') ?: $this->get('whatsapp_instance');
            $id = $this->get('id') ?: $this->get('location_id');
        } else {
            $instance = null;
            $id = (int)$this->tenant['loc_id'];
        }

        $tenant = $this->api_model->get_tenant($instance, $id);
        if ($tenant) {
            unset($tenant['api_key']); // nunca devolver API keys

            $this->response([
                'status' => true,
                'tenant' => $tenant
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Tenant not found'
            ], REST_Controller::HTTP_NOT_FOUND);
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

        // Una key de local solo crea pedidos en su propio local
        if (!$this->_is_platform()) {
            $payload['location_id'] = (int)$this->tenant['loc_id'];
            unset($payload['instance']);
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

    /**
     * Resolve location_id from parameters or WhatsApp instance slug
     */
    /**
     * Local de la petición. Con una key de local SIEMPRE es el de la key: si el request pide
     * otro local se responde 403. Solo la key de plataforma (n8n) elige el local por
     * location_id o por instance.
     */
    protected function _resolve_location_id()
    {
        $payload = $this->get_request_data();
        $req_loc = isset($payload['location_id']) ? $payload['location_id'] : ($this->get('location_id') ?? $this->get('loc_id'));
        $instance = isset($payload['instance']) ? $payload['instance'] : ($this->get('instance') ?: $this->get('whatsapp_instance'));

        if (!$this->_is_platform()) {
            $own = (int)$this->tenant['loc_id'];
            if ($req_loc !== null && $req_loc !== '' && (int)$req_loc !== $own) {
                $this->_deny('Esta API key no tiene acceso a ese local');
            }
            if ($instance) {
                $t = $this->api_model->get_tenant($instance);
                if (!$t || (int)$t['location_id'] !== $own) {
                    $this->_deny('Esta API key no tiene acceso a esa instancia');
                }
            }
            return $own;
        }

        if ($req_loc !== null && $req_loc !== '') {
            $loc_id = (int)$req_loc;
            if ($loc_id !== 0 && !$this->db->where('id', $loc_id)->count_all_results('geopos_locations')) {
                $this->_deny('Local inexistente', REST_Controller::HTTP_NOT_FOUND);
            }
            return $loc_id;
        }
        if ($instance) {
            $t = $this->api_model->get_tenant($instance);
            if ($t) {
                return (int)$t['location_id'];
            }
        }
        $this->_deny('Indicá location_id o instance', REST_Controller::HTTP_BAD_REQUEST);
    }

    /**
     * GET /api/workflow_config?instance=pepito_burger (key de plataforma) o sin parámetros (key del local)
     * Configuración operativa para el bot. La clave de IA descifrada solo va a la key de plataforma.
     */
    public function workflow_config_get()
    {
        $loc_id = $this->_resolve_location_id();
        $this->load->model('delivery_model');
        $config = $this->delivery_model->get_full_workflow_config($loc_id, $this->_is_platform());

        $this->response($config, REST_Controller::HTTP_OK);
    }

    /**
     * POST /api/workflow_config
     * Actualización PARCIAL: solo cambia los campos enviados. Datos bancarios, clave de IA y PINs
     * no se pueden modificar por API (solo desde el panel).
     */
    public function workflow_config_post()
    {
        $payload = $this->get_request_data();
        $loc_id = $this->_resolve_location_id();

        $this->load->model('delivery_model');
        $updated = $this->delivery_model->patch_config($loc_id, is_array($payload) ? $payload : []);

        // Solo disponibilidad de repartidores (activo/ocupado); nombre, teléfono y PIN se editan en el panel
        if (!empty($payload['deliveries']) && is_array($payload['deliveries'])) {
            foreach ($payload['deliveries'] as $d) {
                $slot = isset($d['slot']) ? (int)$d['slot'] : 0;
                if ($slot < 1 || $slot > 10) continue;
                $upd = [];
                if (isset($d['activo']))  $upd['activo'] = !empty($d['activo']) ? 1 : 0;
                if (isset($d['ocupado'])) $upd['ocupado'] = !empty($d['ocupado']) ? 1 : 0;
                if ($upd) {
                    $this->db->where('loc_id', $loc_id)->where('slot', $slot)->update('geopos_deliveries', $upd);
                    $updated[] = 'deliveries.' . $slot;
                }
            }
        }

        $config = $this->delivery_model->get_full_workflow_config($loc_id, $this->_is_platform());
        $config['updated_fields'] = $updated;
        $this->response($config, REST_Controller::HTTP_OK);
    }

    /**
     * POST /api/assign_delivery
     * Elige el próximo repartidor disponible (menos pedidos hoy y, si empatan, el que espera hace más).
     * Solo POST: un GET (p. ej. la vista previa de un enlace) no debe asignar repartidores.
     */
    public function assign_delivery_post()
    {
        $payload = $this->get_request_data();
        $loc_id = $this->_resolve_location_id();
        $order_id = isset($payload['order_id']) ? (int)$payload['order_id'] : 0;

        $this->load->model('delivery_model');
        $result = $this->delivery_model->assign_next_delivery($loc_id, $order_id, is_array($payload) ? $payload : []);

        $this->response($result, REST_Controller::HTTP_OK);
    }

    /**
     * POST /api/delivery_status
     * Marks driver as busy/free (e.g. from WhatsApp commands "ocupado", "disponible", "llegue", "entregado")
     */
    public function delivery_status_post()
    {
        $payload = $this->get_request_data();
        $loc_id = $this->_resolve_location_id();
        $phone_or_slot = isset($payload['telefono']) ? $payload['telefono'] : (isset($payload['phone']) ? $payload['phone'] : ($payload['slot'] ?? null));
        $ocupado = isset($payload['ocupado']) ? (int)$payload['ocupado'] : 0;

        if (!$phone_or_slot) {
            $this->response(['status' => false, 'message' => 'telefono or slot is required'], REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->load->model('delivery_model');
        $result = $this->delivery_model->update_delivery_status($loc_id, $phone_or_slot, $ocupado);

        $this->response($result, REST_Controller::HTTP_OK);
    }

    /**
     * POST /api/reset_delivery_counters
     * Resets today's order counts for deliveries
     */
    public function reset_delivery_counters_post()
    {
        $loc_id = $this->_resolve_location_id();

        $this->load->model('delivery_model');
        $this->delivery_model->reset_daily_counters($loc_id);

        $this->response([
            'status' => true,
            'message' => 'Contadores diarios de repartidores reiniciados exitosamente',
            'location_id' => $loc_id
        ], REST_Controller::HTTP_OK);
    }
}
