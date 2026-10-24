<?php
/**
 * GeeksPOS - Delivery & WhatsApp Configuration Controller
 * Allows merchants and administrators to configure:
 * - Phone numbers (Caja, Cocina, Gerente, Encargado, Super Admins, Delivery Group JID)
 * - Turn-based delivery drivers (Slots 1 to 10) with real-time status and equitable Round-Robin
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_config extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Aauth');
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }

        // Roles permitidos: Manager (4), Admin (5)
        if ($this->aauth->get_user()->roleid < 4) {
            exit('<h3>Acceso restringido: Se requieren permisos administrativos</h3>');
        }

        $this->load->model('delivery_model');
        $this->li_a = 'delivery_config';
    }

    /**
     * Resolve target location based on logged in user's scope
     */
    protected function is_platform_admin()
    {
        $u = $this->aauth->get_user();
        return (int)$u->loc === 0 && (int)$u->roleid === 5;
    }

    /**
     * ¿El usuario puede administrar este local? El de un local solo el suyo; solo el admin de plataforma, cualquiera.
     */
    protected function can_manage($loc_id)
    {
        return $this->is_platform_admin() || (int)$this->aauth->get_user()->loc === (int)$loc_id;
    }

    protected function resolve_target_location($requested_loc_id = null)
    {
        $user_loc = (int)$this->aauth->get_user()->loc;

        // If merchant has dedicated location, always bind to their location
        // (un gerente sin local asignado solo ve el local principal 0)
        if ($user_loc > 0 || !$this->is_platform_admin()) {
            return $user_loc;
        }

        // Superadmin (loc = 0) can specify location or defaults to 1 or 0
        if ($requested_loc_id !== null && $requested_loc_id !== '') {
            return (int)$requested_loc_id;
        }

        $get_loc = $this->input->get('loc_id');
        if ($get_loc !== null && $get_loc !== '') {
            return (int)$get_loc;
        }

        // Default to first active location or 0
        $first_loc = $this->db->order_by('id', 'ASC')->get('geopos_locations')->row_array();
        return $first_loc ? (int)$first_loc['id'] : 0;
    }

    /**
     * Main configuration panel view
     */
    public function index($loc_id = null)
    {
        $loc_id = $this->resolve_target_location($loc_id);

        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title']  = 'Configuración Operativa de WhatsApp & Deliveries';

        $is_superadmin = $this->is_platform_admin();

        // Fetch location info
        $location = null;
        if ($loc_id > 0) {
            $location = $this->db->where('id', $loc_id)->get('geopos_locations')->row_array();
        } else {
            $sys = $this->db->get('geopos_system')->row_array();
            $location = [
                'id' => 0,
                'cname' => $sys['cname'] ?? 'Empresa Principal',
                'phone' => $sys['phone'] ?? '',
                'whatsapp_instance' => 'pos_whatsapp',
                'api_key' => ''
            ];
        }

        // All locations for superadmin selector
        $all_locations = [];
        if ($is_superadmin) {
            $all_locations = $this->db->order_by('id', 'ASC')->get('geopos_locations')->result_array();
        }

        $config = $this->delivery_model->get_config($loc_id);
        $deliveries = $this->delivery_model->get_deliveries($loc_id);

        // La clave de IA nunca vuelve al navegador: solo sus últimos 4 caracteres
        $config['ia_api_key_hint'] = $this->delivery_model->get_ia_key_hint($loc_id, $config['ia_api_key'] ?? '');
        $config['ia_api_key'] = '';

        // Decode promos semana for dynamic promo cards and legacy day inputs
        $config['promos_list'] = $this->delivery_model->normalize_promos($config['promos_semana'] ?? '');
        $promos_decoded = [];
        foreach ($config['promos_list'] as $p) {
            foreach ($p['dias'] as $d) {
                if (empty($promos_decoded[$d])) {
                    $promos_decoded[$d] = $p['titulo'];
                }
            }
        }
        $config['promos_decoded'] = $promos_decoded;

        // Days of week array
        $config['dias_seleccionados'] = array_filter(array_map('trim', explode(',', $config['dias_atencion'] ?: 'Mon,Tue,Wed,Thu,Fri,Sat,Sun')));

        // Plantillas y Rubros GeeksIA
        $this->load->model('workflow_templates_model');
        $templates = $this->workflow_templates_model->get_templates();
        $tipo_flujo = !empty($config['tipo_flujo']) ? $config['tipo_flujo'] : ($location['tipo_flujo'] ?? 'restaurante');
        $rubro_info = $this->workflow_templates_model->get_template($tipo_flujo);

        $horarios_semana = $config['horarios_semana_parsed'] ?? $this->delivery_model->parse_horarios_semana($config);

        $data = [
            'loc_id'          => $loc_id,
            'is_superadmin'   => $is_superadmin,
            'location'        => $location,
            'all_locations'   => $all_locations,
            'config'          => $config,
            'horarios_semana' => $horarios_semana,
            'deliveries'      => $deliveries,
            'templates'       => $templates,
            'tipo_flujo'      => $tipo_flujo,
            'rubro_info'      => $rubro_info,
            'api_base_url'    => base_url('api/')
        ];

        $this->load->view('fixed/header', $head);
        $this->load->view('delivery_config/index', $data);
        $this->load->view('fixed/footer');
    }

    /**
     * AJAX Save Handler
     */
    public function save()
    {
        if ($this->input->method() !== 'post') {
            redirect('delivery_config');
            return;
        }

        $loc_id = (int)$this->input->post('loc_id', true);

        // Security check: tenant cannot edit another tenant's settings
        if (!$this->can_manage($loc_id)) {
            echo json_encode(['status' => 'Error', 'message' => 'No tiene permisos para modificar este local.']);
            return;
        }

        // 1. Save general WhatsApp & Operational configuration
        $dias_arr = $this->input->post('dias_atencion', true);
        $dias_str = is_array($dias_arr) ? implode(',', $dias_arr) : ($dias_arr ?: 'Mon,Tue,Wed,Thu,Fri,Sat,Sun');

        // Package promos semana (Soporta lista ilimitada de promociones con fotos y fallback legado)
        $promos_json_raw = $this->input->post('promos_json');
        $promos_list = [];
        if (!empty($promos_json_raw)) {
            $decoded_promos = json_decode($promos_json_raw, true);
            if (is_array($decoded_promos)) {
                $promos_list = $this->delivery_model->normalize_promos($decoded_promos);
            }
        }

        if (empty($promos_list)) {
            $legacy_map = [];
            foreach (['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'] as $d_k) {
                $val = $this->input->post('promo_' . $d_k, true);
                if (!empty($val)) {
                    $legacy_map[$d_k] = $val;
                }
            }
            if (!empty($legacy_map)) {
                $promos_list = $this->delivery_model->normalize_promos($legacy_map);
            }
        }

        // Procesar tramos de tarifas por distancia escalonada
        $tramos = [];
        $tramos_desde = $this->input->post('tramos_desde');
        $tramos_hasta = $this->input->post('tramos_hasta');
        $tramos_costo = $this->input->post('tramos_costo');
        $tramos_etiq  = $this->input->post('tramos_etiqueta');

        if (is_array($tramos_desde) && is_array($tramos_hasta) && is_array($tramos_costo)) {
            for ($i = 0; $i < count($tramos_desde); $i++) {
                $d = isset($tramos_desde[$i]) && $tramos_desde[$i] !== '' ? (float)$tramos_desde[$i] : null;
                $h = isset($tramos_hasta[$i]) && $tramos_hasta[$i] !== '' ? (float)$tramos_hasta[$i] : null;
                $c = isset($tramos_costo[$i]) && $tramos_costo[$i] !== '' ? (int)$tramos_costo[$i] : null;
                $e = isset($tramos_etiq[$i]) ? trim($tramos_etiq[$i]) : '';
                if ($d !== null && $h !== null && $c !== null && $h > $d && $c >= 0) {
                    $tramos[] = [
                        'desde'    => $d,
                        'hasta'    => $h,
                        'costo'    => $c,
                        'etiqueta' => $e
                    ];
                }
            }
            usort($tramos, function($a, $b) {
                return $a['desde'] <=> $b['desde'];
            });
        }

        $config_data = [
            'phone_caja'             => $this->input->post('phone_caja', true),
            'phone_cocina'           => $this->input->post('phone_cocina', true),
            'phone_gerente'          => $this->input->post('phone_gerente', true),
            'phone_encargado'        => $this->input->post('phone_encargado', true),
            'phone_admins'           => $this->input->post('phone_admins', true),
            'delivery_group'         => $this->input->post('delivery_group', true),
            'horario_atencion'       => $this->input->post('horario_atencion', true),
            'hora_apertura'          => $this->input->post('hora_apertura', true),
            'hora_cierre'            => $this->input->post('hora_cierre', true),
            'dias_atencion'          => $dias_str,
            'mensaje_cerrado'        => $this->input->post('mensaje_cerrado', true),
            'is_local_cerrado'       => $this->input->post('is_local_cerrado') ? 1 : 0,
            'is_delivery_cerrado'    => $this->input->post('is_delivery_cerrado') ? 1 : 0,
            'tarifa_tipo'            => $this->input->post('tarifa_tipo', true) ?: 'tramos',
            'radio_max_km'           => $this->input->post('radio_max_km', true),
            'costo_envio_base'       => $this->input->post('costo_envio_base', true),
            'km_base'                => $this->input->post('km_base', true) ?: 3.0,
            'costo_por_km'           => $this->input->post('costo_por_km', true),
            'tarifas_tramos'         => $tramos,
            'tiempo_entrega_min'     => $this->input->post('tiempo_entrega_min', true),
            'tiempo_retiro_min'      => $this->input->post('tiempo_retiro_min', true),
            'banco_nombre'           => $this->input->post('banco_nombre', true),
            'banco_titular'          => $this->input->post('banco_titular', true),
            'banco_ruc_ci'           => $this->input->post('banco_ruc_ci', true),
            'banco_cuenta'           => $this->input->post('banco_cuenta', true),
            'banco_alias'            => $this->input->post('banco_alias', true),
            'maps_direccion'         => $this->input->post('maps_direccion', true),
            'maps_link'              => $this->input->post('maps_link', true),
            'maps_resenas_link'      => $this->input->post('maps_resenas_link', true),
            'bot_modo'               => $this->input->post('bot_modo', true),
            'numeros_prueba'         => $this->input->post('numeros_prueba', true),
            'ia_proveedor'           => $this->input->post('ia_proveedor', true) ?: 'gemini',
            'ia_api_key'             => $this->input->post('ia_api_key', true),
            'ia_modelo'              => $this->input->post('ia_modelo', true) ?: 'gemini-1.5-flash',
            'ia_temperatura'         => $this->input->post('ia_temperatura', true) ?: 0.70,
            'ia_base_url'            => $this->input->post('ia_base_url', true),
            'ia_max_tokens'          => (int)($this->input->post('ia_max_tokens', true) ?: 1000),
            'ia_tono'                => $this->input->post('ia_tono', true),
            'ia_instrucciones_extra' => $this->input->post('ia_instrucciones_extra', true),
            'tipo_flujo'             => $this->input->post('tipo_flujo', true) ?: 'restaurante',
            'promos_semana'          => $promos_list,
            'horario'                => $this->input->post('horario')
        ];
        $this->delivery_model->save_config($loc_id, $config_data);

        // Actualizar también en geopos_locations si viene tipo_flujo
        $tf = strtolower(trim((string)$this->input->post('tipo_flujo', true)));
        $this->load->model('workflow_templates_model');
        if ($tf && $loc_id > 0 && array_key_exists($tf, $this->workflow_templates_model->get_templates())) {
            $this->db->where('id', $loc_id)->update('geopos_locations', ['tipo_flujo' => $tf]);
        }

        // 2. Save delivery slots 1 to 10
        $slots_data = [];
        for ($s = 1; $s <= 10; $s++) {
            $slots_data[] = [
                'slot'     => $s,
                'nombre'   => $this->input->post("slot_{$s}_nombre", true) ?: ('Repartidor ' . $s),
                'usuario'  => $this->input->post("slot_{$s}_usuario", true) ?: ('delivery' . $s),
                'email'    => $this->input->post("slot_{$s}_email", true) ?: ('delivery' . $s . '@geekspos.com'),
                'password' => $this->input->post("slot_{$s}_password", true),
                'telefono' => $this->input->post("slot_{$s}_telefono", true),
                'pin'      => $this->input->post("slot_{$s}_pin", true),
                'activo'   => $this->input->post("slot_{$s}_activo") ? 1 : 0,
                'ocupado'  => $this->input->post("slot_{$s}_ocupado") ? 1 : 0
            ];
        }
        $this->delivery_model->save_deliveries($loc_id, $slots_data);

        echo json_encode([
            'status'  => 'Success',
            'message' => '¡Configuración de WhatsApp y Deliveries guardada correctamente!'
        ]);
    }

    /**
     * AJAX Reset daily counters to 0
     */
    public function reset_counters()
    {
        $loc_id = (int)$this->input->post('loc_id', true);
        if (!$this->can_manage($loc_id)) {
            echo json_encode(['status' => 'Error', 'message' => 'No autorizado']);
            return;
        }

        $this->delivery_model->reset_daily_counters($loc_id);
        echo json_encode([
            'status'  => 'Success',
            'message' => 'Contadores de pedidos del día reiniciados a 0 correctamente.'
        ]);
    }

    /**
     * AJAX Promo Image Uploader
     * Subida asíncrona de flyers, fotos y banners promocionales
     */
    public function upload_promo_image()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($this->input->method() !== 'post') {
            echo json_encode(['status' => 'Error', 'message' => 'Método no permitido.']);
            return;
        }

        $loc_id = (int)$this->input->post('loc_id', true);
        if ($loc_id && !$this->can_manage($loc_id)) {
            echo json_encode(['status' => 'Error', 'message' => 'No autorizado para subir archivos en este local.']);
            return;
        }

        if (empty($_FILES['promo_image']['name'])) {
            echo json_encode(['status' => 'Error', 'message' => 'No se seleccionó ningún archivo de imagen.']);
            return;
        }

        $upload_dir = FCPATH . 'userfiles/promos/';
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0777, true);
        }

        $file = $_FILES['promo_image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($ext, $allowed_exts)) {
            echo json_encode(['status' => 'Error', 'message' => 'Formato no permitido. Utilice imágenes JPG, PNG o WEBP.']);
            return;
        }

        if ($file['size'] > 8 * 1024 * 1024) {
            echo json_encode(['status' => 'Error', 'message' => 'La imagen supera el límite permitido de 8 MB.']);
            return;
        }

        $check = @getimagesize($file['tmp_name']);
        if ($check === false) {
            echo json_encode(['status' => 'Error', 'message' => 'El archivo subido no es una imagen válida.']);
            return;
        }

        $filename = 'promo_' . time() . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 8) . '.' . $ext;
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            @chmod($target_path, 0666);
            $rel_path = 'userfiles/promos/' . $filename;
            echo json_encode([
                'status'    => 'Success',
                'message'   => '¡Flyer promocional subido con éxito!',
                'file_path' => $rel_path,
                'file_url'  => base_url($rel_path),
                'filename'  => $filename
            ]);
        } else {
            echo json_encode(['status' => 'Error', 'message' => 'No se pudo guardar la imagen en el servidor. Verifique permisos.']);
        }
    }

    /**
     * AJAX Randomize 4-digit PINs for all slots
     */
    public function randomize_pins()
    {
        $loc_id = (int)$this->input->post('loc_id', true);
        if (!$this->can_manage($loc_id)) {
            echo json_encode(['status' => 'Error', 'message' => 'No autorizado']);
            return;
        }

        $pins = $this->delivery_model->randomize_all_pins($loc_id);
        echo json_encode([
            'status'  => 'Success',
            'message' => '¡Nuevos PINs de 4 dígitos generados aleatoriamente!',
            'pins'    => $pins
        ]);
    }

    /**
     * AJAX Simulación de asignación: solo lectura (no marca ocupado ni crea pedidos)
     */
    public function test_assignment()
    {
        $loc_id = (int)$this->input->post('loc_id', true);
        if (!$this->can_manage($loc_id)) {
            echo json_encode(['assigned' => false, 'message' => 'No autorizado']);
            return;
        }
        echo json_encode($this->delivery_model->preview_next_delivery($loc_id));
    }

    /**
     * AJAX Vista previa del JSON que recibe el bot (sin secretos), con la sesión del panel
     */
    public function live_json()
    {
        $loc_id = $this->resolve_target_location($this->input->get('loc_id'));
        $this->output->set_content_type('application/json', 'utf-8')
                     ->set_output(json_encode($this->delivery_model->get_full_workflow_config($loc_id, false), JSON_UNESCAPED_UNICODE));
    }

    /**
     * AJAX Rotar la API key del local: invalida la anterior y muestra la nueva una sola vez
     */
    public function rotate_api_key()
    {
        $loc_id = (int)$this->input->post('loc_id', true);
        if ($loc_id <= 0 || !$this->can_manage($loc_id)) {
            echo json_encode(['status' => 'Error', 'message' => 'No autorizado']);
            return;
        }

        $new_key = 'gk_' . bin2hex(random_bytes(30));
        $this->db->trans_start();
        $this->db->where('loc_id', $loc_id)->where('scope', 'tenant')->where('revoked_at IS NULL', null, false)
                 ->update('geopos_restkeys', ['revoked_at' => date('Y-m-d H:i:s')]);
        $this->db->insert('geopos_restkeys', [
            'user_id'       => (int)$this->aauth->get_user()->id,
            'loc_id'        => $loc_id,
            'scope'         => 'tenant',
            'key'           => null,
            'key_hash'      => hash('sha256', $new_key),
            'key_hint'      => substr($new_key, -4),
            'level'         => 0,
            'ignore_limits' => 0,
            'date_created'  => date('Y-m-d')
        ]);
        $this->db->where('id', $loc_id)->update('geopos_locations', ['api_key' => $new_key]);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            echo json_encode(['status' => 'Error', 'message' => 'No se pudo rotar la clave. Intentá de nuevo.']);
            return;
        }
        echo json_encode([
            'status'  => 'Success',
            'message' => 'Nueva API key generada. La anterior dejó de funcionar: actualizala en tus integraciones.',
            'api_key' => $new_key
        ]);
    }

    /**
     * Vista de Monitoreo de Pedidos Asignados y Reasignación de Repartidores
     */
    public function orders($loc_id = null)
    {
        $loc_id = $this->resolve_target_location($loc_id);

        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title']  = 'Monitor de Deliveries & Reasignación de Pedidos';

        $is_superadmin = $this->is_platform_admin();

        // Location data
        $location = null;
        if ($loc_id > 0) {
            $location = $this->db->where('id', $loc_id)->get('geopos_locations')->row_array();
        } else {
            $sys = $this->db->get('geopos_system')->row_array();
            $location = ['id' => 0, 'cname' => $sys['cname'] ?? 'Empresa Principal'];
        }

        $all_locations = [];
        if ($is_superadmin) {
            $all_locations = $this->db->order_by('id', 'ASC')->get('geopos_locations')->result_array();
        }

        $orders = $this->delivery_model->get_assigned_orders_admin($loc_id);
        $drivers = $this->delivery_model->get_drivers_for_reassign($loc_id);

        $data = [
            'loc_id'        => $loc_id,
            'is_superadmin' => $is_superadmin,
            'location'      => $location,
            'all_locations' => $all_locations,
            'orders'        => $orders,
            'drivers'       => $drivers
        ];

        $this->load->view('fixed/header', $head);
        $this->load->view('delivery_config/orders', $data);
        $this->load->view('fixed/footer');
    }

    /**
     * AJAX endpoint para refrescar en vivo la tabla de pedidos
     */
    public function ajax_orders()
    {
        $loc_id = $this->resolve_target_location($this->input->get('loc_id'));
        $estado = $this->input->get('estado');

        $orders = $this->delivery_model->get_assigned_orders_admin($loc_id, $estado);
        $drivers = $this->delivery_model->get_drivers_for_reassign($loc_id);

        header('Content-Type: application/json');
        echo json_encode([
            'status'  => true,
            'orders'  => $orders,
            'drivers' => $drivers,
            'time'    => date('H:i:s')
        ]);
    }

    /**
     * AJAX Reasignar Pedido
     */
    public function ajax_reassign()
    {
        $order_id = (int)$this->input->post('order_id');
        $new_slot = (int)$this->input->post('new_slot');
        $motivo   = $this->input->post('motivo');
        $loc_id   = $this->resolve_target_location($this->input->post('loc_id'));

        if (!$this->can_manage($loc_id)) {
            echo json_encode(['status' => false, 'message' => 'No tiene permisos para modificar pedidos en este local']);
            return;
        }

        $result = $this->delivery_model->reassign_order($order_id, $new_slot, $loc_id, $motivo);

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * AJAX: Simular cálculo de costo de envío en tiempo real según distancia
     */
    public function simulate_fee()
    {
        $loc_id = $this->resolve_target_location($this->input->get_post('loc_id'));
        $km     = (float)$this->input->get_post('km');

        $result = $this->delivery_model->calculate_delivery_cost($loc_id, $km);

        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
