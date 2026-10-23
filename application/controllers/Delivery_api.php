<?php
/**
 * GeeksPOS - Mobile Delivery API Controller
 * Provee endpoints REST para la App Móvil Flutter de Repartidores
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('delivery_model');

        // Permitir CORS para peticiones móviles
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

        if ($this->input->method() === 'options') {
            exit(0);
        }
    }

    /**
     * Helper para emitir respuestas JSON estándar
     */
    protected function json_response($data, $status_code = 200)
    {
        $this->output
             ->set_status_header($status_code)
             ->set_content_type('application/json', 'utf-8')
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Helper para obtener payload JSON o POST
     */
    protected function get_payload()
    {
        $json = json_decode($this->input->raw_input_stream, true);
        if (is_array($json)) {
            return $json;
        }
        return $this->input->post(null, true) ?: [];
    }

    /**
     * IP real del cliente. Detrás del túnel de Cloudflare REMOTE_ADDR es la del contenedor,
     * así que solo en ese caso se usa CF-Connecting-IP (un cliente directo no puede falsearla).
     */
    protected function client_ip()
    {
        $remote = (string)$this->input->server('REMOTE_ADDR');
        $cf = (string)$this->input->server('HTTP_CF_CONNECTING_IP');
        $is_internal = filter_var($remote, FILTER_VALIDATE_IP) && !filter_var($remote, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        if ($is_internal && filter_var($cf, FILTER_VALIDATE_IP)) {
            return $cf;
        }
        return $remote;
    }

    /**
     * Autenticar repartidor mediante Token Bearer (solo encabezado: el token no va en la URL)
     */
    protected function authenticate_driver()
    {
        $auth_header = $this->input->get_request_header('Authorization', true);

        if (empty($auth_header)) {
            $this->json_response(['status' => false, 'message' => 'Token de autorización requerido'], 401);
            $this->output->_display(); // sin esto el 401 salía como 200 vacío
            exit;
        }

        $driver = $this->delivery_model->driver_get_by_token($auth_header);
        if (!$driver) {
            $this->json_response(['status' => false, 'message' => 'Token inválido o sesión expirada'], 401);
            $this->output->_display();
            exit;
        }

        return $driver;
    }

    /**
     * GET /delivery_api/health
     */
    public function health()
    {
        $this->json_response([
            'status'     => true,
            'service'    => 'GeeksPOS Delivery Mobile API',
            'version'    => '1.0.0'
        ]);
    }

    /**
     * POST /delivery_api/login
     * Body: {telefono, pin}
     */
    public function login()
    {
        $payload = $this->get_payload();
        $telefono = $payload['phone'] ?? ($payload['telefono'] ?? '');
        $pin = $payload['pin'] ?? '';

        // Sin logs de depuración: el payload trae el PIN y la respuesta el token de sesión
        $result = $this->delivery_model->driver_login($telefono, $pin, $this->client_ip());
        if (!empty($result['status'])) {
            $result['success'] = true;
            $result['status'] = 'success';
        }

        $status_code = !empty($result['success']) ? 200 : 401;
        $this->json_response($result, $status_code);
    }

    /**
     * GET /delivery_api/orders
     * Headers: Authorization: Bearer <token>
     */
    public function orders()
    {
        $driver = $this->authenticate_driver();
        $orders = $this->delivery_model->driver_get_orders($driver['slot'], $driver['loc_id'], false);

        $this->json_response([
            'status' => true,
            'driver' => [
                'nombre'  => $driver['nombre'],
                'slot'    => (int)$driver['slot'],
                'ocupado' => (int)$driver['ocupado']
            ],
            'orders' => $orders
        ]);
    }

    /**
     * GET /delivery_api/orders_history
     * Headers: Authorization: Bearer <token>
     */
    public function orders_history()
    {
        $driver = $this->authenticate_driver();
        $history = $this->delivery_model->driver_get_orders($driver['slot'], $driver['loc_id'], true);

        $this->json_response([
            'status'  => true,
            'history' => $history
        ]);
    }

    /**
     * POST /delivery_api/update_order_status
     * Body: {order_id, status: 'en_camino' | 'en_puerta' | 'entregado'}
     */
    public function update_order_status()
    {
        $driver = $this->authenticate_driver();
        $payload = $this->get_payload();

        $order_id = $payload['order_id'] ?? 0;
        $status = $payload['status'] ?? '';

        $res = $this->delivery_model->driver_update_order_status($driver, $order_id, $status);
        $status_code = $res['status'] ? 200 : 400;
        $this->json_response($res, $status_code);
    }

    /**
     * POST /delivery_api/update_gps
     * Body: {lat, lng}
     */
    public function update_gps()
    {
        $driver = $this->authenticate_driver();
        $payload = $this->get_payload();

        $lat = $payload['lat'] ?? null;
        $lng = $payload['lng'] ?? null;

        if ($lat === null || $lng === null) {
            $this->json_response(['status' => false, 'message' => 'Coordenadas lat y lng requeridas'], 400);
            return;
        }

        $this->delivery_model->driver_update_gps($driver['slot'], $driver['loc_id'], $lat, $lng);

        $this->json_response([
            'status'      => true,
            'message'     => 'Coordenadas GPS actualizadas',
            'last_gps_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * POST /delivery_api/toggle_availability
     * Body: {ocupado: 0 | 1}
     */
    public function toggle_availability()
    {
        $driver = $this->authenticate_driver();
        $payload = $this->get_payload();

        $ocupado = isset($payload['ocupado']) ? (int)$payload['ocupado'] : ($driver['ocupado'] ? 0 : 1);
        $res = $this->delivery_model->driver_toggle_availability($driver['slot'], $driver['loc_id'], $ocupado);

        $this->json_response($res);
    }

    /**
     * GET /delivery_api/cash_summary
     */
    public function cash_summary()
    {
        $driver = $this->authenticate_driver();
        $summary = $this->delivery_model->driver_get_cash_summary($driver['slot'], $driver['loc_id']);

        $this->json_response([
            'status'  => true,
            'driver'  => $driver['nombre'],
            'summary' => $summary
        ]);
    }
}
