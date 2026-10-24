<?php
/**
 * GeeksPOS - SaaS Multi-Tenant Manager
 * Panel central para dar de alta clientes, negocios, sucursales y bots de WhatsApp en 1 solo clic.
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Saas extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Aauth');
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        // Solo el Super-Administrador (loc = 0 y roleid = 5) puede administrar el SaaS.
        // Excepción: volver a su cuenta mientras está "impersonando" un local.
        $is_reverting = ($this->router->fetch_method() === 'revert' && $this->session->userdata('saas_impersonator_id') == $this->aauth->get_user()->id);
        if (!$is_reverting && ($this->aauth->get_user()->roleid < 5 || $this->aauth->get_user()->loc != 0)) {
            exit('<h3>Acceso restringido: Se requieren permisos de Super-Administrador SaaS</h3>');
        }
        $this->li_a = 'saas';
    }

    /**
     * Dashboard / Lista de Negocios
     */
    public function index()
    {
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'SaaS Multi-Sitio | Gestión de Negocios & Clientes';

        // Obtener todos los negocios (locaciones)
        $locations = $this->db->order_by('id', 'DESC')->get('geopos_locations')->result_array();

        $this->load->model('workflow_templates_model');
        foreach ($locations as &$loc) {
            $lid = $loc['id'];
            $wid = $loc['ware'];

            // Cantidad de productos en su almacén
            $loc['products_count'] = $this->db->where('warehouse', $wid)->count_all_results('geopos_products');

            // Cantidad de ventas / facturas realizadas
            $loc['invoices_count'] = $this->db->where('loc', $lid)->count_all_results('geopos_invoices');

            // Usuario Administrador asignado a este local
            $user = $this->db->where('loc', $lid)->where('roleid', 5)->get('geopos_users')->row_array();
            $loc['admin_user'] = $user['username'] ?? 'Sin asignar';
            $loc['admin_email'] = $user['email'] ?? $loc['email'];

            // Información de Rubro / Plantilla GeeksIA
            $wa_c = $this->db->where('loc_id', $lid)->get('geopos_whatsapp_config')->row_array();
            $tpl_key = !empty($wa_c['tipo_flujo']) ? $wa_c['tipo_flujo'] : ($loc['tipo_flujo'] ?? 'restaurante');
            $loc['rubro_info'] = $this->workflow_templates_model->get_template($tpl_key);
        }

        $data['locations'] = $locations;
        $data['main_company'] = $this->db->get('geopos_system')->row_array();

        $this->load->view('fixed/header', $head);
        $this->load->view('saas/index', $data);
        $this->load->view('fixed/footer');
    }

    /**
     * Crear un nuevo negocio / cliente en 1 clic
     */
    public function create()
    {
        if ($this->input->post()) {
            $cname = trim($this->input->post('cname', true));
            $raw_username = trim($this->input->post('username', true));
            $password = trim($this->input->post('password', true));
            $email = trim($this->input->post('email', true));
            $phone = trim($this->input->post('phone', true));
            $taxid = trim($this->input->post('taxid', true));
            $address = trim($this->input->post('address', true));
            $city = trim($this->input->post('city', true));
            $tipo_flujo = trim($this->input->post('tipo_flujo', true)) ?: 'restaurante';

            if (empty($cname) || empty($raw_username) || empty($password)) {
                echo json_encode(['status' => 'Error', 'message' => 'El nombre del negocio, usuario y contraseña son obligatorios.']);
                return;
            }

            // Normalizar y sanitizar nombre de usuario (sin espacios, caracteres alfanuméricos válidos)
            $username = strtolower(preg_replace('/[^a-zA-Z0-9_.-]/', '_', $raw_username));
            $username = trim(preg_replace('/_+/', '_', $username), '_');

            // Generar Slug para WhatsApp (alfanumérico en minúsculas)
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($cname)));
            $slug = trim($slug, '_');
            if (empty($slug)) {
                $slug = 'local_' . time();
            }

            // Si el correo viene vacío, generar uno único para este local
            if (empty($email)) {
                $email = 'admin@' . str_replace('_', '', $slug) . '.com';
            }

            // 1. Validar específicamente si el correo ya está registrado
            $email_exists = $this->db->where('email', $email)->get('geopos_users')->row();
            if ($email_exists) {
                echo json_encode([
                    'status' => 'Error',
                    'message' => "El correo electrónico '{$email}' ya está en uso por otra cuenta (ej: tu cuenta de Superadmin). Cada nuevo local necesita un correo único (ejemplo: admin@" . str_replace('_', '', $slug) . ".com)."
                ]);
                return;
            }

            // 2. Validar específicamente si el nombre de usuario ya está registrado
            $user_exists = $this->db->where('username', $username)->get('geopos_users')->row();
            if ($user_exists) {
                echo json_encode([
                    'status' => 'Error',
                    'message' => "El nombre de usuario '{$username}' ya está registrado. Por favor ingrese un nombre de usuario diferente (ej: {$username}_" . rand(10, 99) . ")."
                ]);
                return;
            }

            // 1. Crear Almacén Principal del Negocio
            $this->db->insert('geopos_warehouse', [
                'title' => 'Almacén - ' . $cname,
                'extra' => 'Depósito principal para ' . $cname,
                'loc' => 0
            ]);
            $wid = $this->db->insert_id();

            // 3. Generar API Key única (256 bits)
            $apikey = 'gk_' . bin2hex(random_bytes(30)); // 63 caracteres: entra en geopos_locations.api_key varchar(64)

            // 4. Crear Negocio / Locación
            $this->db->insert('geopos_locations', [
                'cname' => $cname,
                'tipo_flujo' => $tipo_flujo,
                'address' => $address,
                'city' => $city,
                'region' => 'Central',
                'country' => 'Paraguay',
                'postbox' => '1001',
                'phone' => $phone,
                'email' => $email,
                'taxid' => $taxid,
                'cur' => 1, // Moneda local
                'ware' => $wid,
                'whatsapp_instance' => $slug,
                'api_key' => $apikey
            ]);
            $loc_id = $this->db->insert_id();

            // Actualizar almacén con el ID de la locación
            $this->db->where('id', $wid)->update('geopos_warehouse', ['loc' => $loc_id]);

            // Auto-inicializar configuración de WhatsApp y repartidores 1 al 10 para este local
            $this->load->model('delivery_model');
            $this->delivery_model->get_config($loc_id);
            $this->delivery_model->get_deliveries($loc_id);

            // Aplicar plantilla del rubro seleccionado (prompts, tono, tipo_flujo)
            $this->load->model('workflow_templates_model');
            $this->workflow_templates_model->apply_template($loc_id, $tipo_flujo, true);

            // 5. Crear Usuario Administrador
            $uid = $this->aauth->create_user($email, $password, $username);
            if (!$uid) {
                // Creación directa con hash de Aauth
                $this->db->insert('geopos_users', [
                    'email' => $email,
                    'username' => $username,
                    'pass' => $this->aauth->hash_password($password, 0),
                    'roleid' => 5,
                    'loc' => $loc_id,
                    'date_created' => date('Y-m-d H:i:s'),
                    'picture' => 'example.png',
                    'banned' => 0
                ]);
                $uid = $this->db->insert_id();
                $this->db->where('id', $uid)->update('geopos_users', ['pass' => $this->aauth->hash_password($password, $uid)]);
            } else {
                $this->db->where('id', $uid)->update('geopos_users', [
                    'roleid' => 5,
                    'loc' => $loc_id,
                    'banned' => 0
                ]);
            }

            // Crear ficha de empleado
            $this->db->insert('geopos_employees', [
                'id' => $uid,
                'username' => $username,
                'name' => $cname . ' Admin',
                'address' => $address,
                'city' => $city,
                'phone' => $phone,
                'salary' => 0
            ]);

            // 6. Registrar API Key en geopos_restkeys: atada a este local y guardada solo como hash
            $this->db->insert('geopos_restkeys', [
                'user_id'       => $uid,
                'loc_id'        => $loc_id,
                'scope'         => 'tenant',
                'key'           => null,
                'key_hash'      => hash('sha256', $apikey),
                'key_hint'      => substr($apikey, -4),
                'level'         => 0,
                'ignore_limits' => 0,
                'date_created'  => date('Y-m-d')
            ]);

            // 7. Crear Instancia en Evolution API automáticamente
            $evo_res = $this->_create_evolution_instance($slug);

            echo json_encode([
                'status' => 'Success',
                'message' => '¡Negocio ' . $cname . ' creado y configurado con éxito!',
                'data' => [
                    'tenant_id' => $loc_id,
                    'business_name' => $cname,
                    'username' => $username,
                    'password' => $password,
                    'login_url' => base_url('user/login'),
                    'whatsapp_instance' => $slug,
                    'api_key' => $apikey,
                    'qr_code' => $evo_res['qrcode']['base64'] ?? null,
                    'menu_api_url' => base_url('api/menu?location_id=' . $loc_id)
                ]
            ]);
            return;
        }

        $this->load->model('workflow_templates_model');
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Crear Nuevo Negocio | SaaS';
        $data['templates'] = $this->workflow_templates_model->get_templates();
        $this->load->view('fixed/header', $head);
        $this->load->view('saas/create', $data);
        $this->load->view('fixed/footer');
    }

    /**
     * Editar perfil, datos comerciales y credenciales de acceso de un local
     */
    public function edit($loc_id = 0)
    {
        $loc_id = (int)$loc_id;
        $loc = $this->db->where('id', $loc_id)->get('geopos_locations')->row_array();
        if (!$loc) {
            $this->session->set_flashdata('error_msg', 'El negocio solicitado no existe.');
            redirect('saas');
            return;
        }

        // Obtener usuario administrador del local (roleid = 5)
        $admin_user = $this->db->where('loc', $loc_id)->where('roleid', 5)->order_by('id', 'ASC')->get('geopos_users')->row_array();
        if (!$admin_user) {
            // Fallback: cualquier usuario asignado a este local
            $admin_user = $this->db->where('loc', $loc_id)->order_by('id', 'ASC')->get('geopos_users')->row_array();
        }

        // Obtener configuración de WhatsApp / Rubro
        $wa_config = $this->db->where('loc_id', $loc_id)->get('geopos_whatsapp_config')->row_array();

        if ($this->input->post()) {
            $cname = trim($this->input->post('cname', true));
            $taxid = trim($this->input->post('taxid', true));
            $phone = trim($this->input->post('phone', true));
            $address = trim($this->input->post('address', true));
            $city = trim($this->input->post('city', true));
            $tipo_flujo = trim($this->input->post('tipo_flujo', true)) ?: ($loc['tipo_flujo'] ?? 'restaurante');
            $whatsapp_instance = trim($this->input->post('whatsapp_instance', true)) ?: ($loc['whatsapp_instance'] ?? '');

            // Datos de usuario administrador
            $raw_user = trim($this->input->post('username', true));
            $username = strtolower(preg_replace('/[^a-zA-Z0-9_.-]/', '_', $raw_user));
            $username = trim(preg_replace('/_+/', '_', $username), '_');
            $email = trim($this->input->post('email', true));
            $password = trim($this->input->post('password', true));
            $banned = (int)$this->input->post('banned');

            if (empty($cname) || empty($username) || empty($email)) {
                $err = 'El nombre comercial, usuario y correo son obligatorios.';
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'Error', 'message' => $err]);
                    return;
                }
                $this->session->set_flashdata('error_msg', $err);
                redirect('saas/edit/' . $loc_id);
                return;
            }

            // Validar que el username o email no estén en uso por otro usuario
            if ($admin_user) {
                $user_exists = $this->db->where('username', $username)->where('id !=', $admin_user['id'])->get('geopos_users')->row();
                if ($user_exists) {
                    $err = "El nombre de usuario '{$username}' ya está en uso por otra cuenta.";
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['status' => 'Error', 'message' => $err]);
                        return;
                    }
                    $this->session->set_flashdata('error_msg', $err);
                    redirect('saas/edit/' . $loc_id);
                    return;
                }

                $email_exists = $this->db->where('email', $email)->where('id !=', $admin_user['id'])->get('geopos_users')->row();
                if ($email_exists) {
                    $err = "El correo '{$email}' ya está en uso por otra cuenta.";
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['status' => 'Error', 'message' => $err]);
                        return;
                    }
                    $this->session->set_flashdata('error_msg', $err);
                    redirect('saas/edit/' . $loc_id);
                    return;
                }
            }

            // 1. Actualizar geopos_locations
            $loc_update = [
                'cname'             => $cname,
                'tipo_flujo'        => $tipo_flujo,
                'taxid'             => $taxid,
                'phone'             => $phone,
                'email'             => $email,
                'address'           => $address,
                'city'              => $city,
                'whatsapp_instance' => $whatsapp_instance
            ];
            $this->db->where('id', $loc_id)->update('geopos_locations', $loc_update);

            // 2. Actualizar también el almacén asociado
            if (!empty($loc['ware'])) {
                $this->db->where('id', $loc['ware'])->update('geopos_warehouse', [
                    'title' => 'Almacén - ' . $cname
                ]);
            }

            // 3. Actualizar tipo_flujo en geopos_whatsapp_config si cambió
            if ($tipo_flujo !== ($loc['tipo_flujo'] ?? '')) {
                $this->load->model('workflow_templates_model');
                $this->workflow_templates_model->apply_template($loc_id, $tipo_flujo, false);
            }

            // 4. Actualizar usuario administrador del local
            if ($admin_user) {
                $user_update = [
                    'username' => $username,
                    'email'    => $email,
                    'banned'   => $banned
                ];
                if (!empty($password)) {
                    $user_update['pass'] = $this->aauth->hash_password($password, $admin_user['id']);
                }
                $this->db->where('id', $admin_user['id'])->update('geopos_users', $user_update);

                // Actualizar ficha empleado
                $this->db->where('id', $admin_user['id'])->update('geopos_employees', [
                    'username' => $username,
                    'name'     => $cname . ' Admin',
                    'phone'    => $phone,
                    'address'  => $address,
                    'city'     => $city
                ]);
            } else {
                // Si el local no tenía usuario aún, crearlo
                $uid = $this->aauth->create_user($email, $password ?: 'geeks123', $username);
                if ($uid) {
                    $this->db->where('id', $uid)->update('geopos_users', [
                        'roleid' => 5,
                        'loc'    => $loc_id,
                        'banned' => $banned
                    ]);
                    $this->db->insert('geopos_employees', [
                        'id'       => $uid,
                        'username' => $username,
                        'name'     => $cname . ' Admin',
                        'address'  => $address,
                        'city'     => $city,
                        'phone'    => $phone,
                        'salary'   => 0
                    ]);
                }
            }

            $success_msg = '¡Perfil del negocio ' . htmlspecialchars($cname) . ' y credenciales actualizados con éxito!';
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status'  => 'Success',
                    'message' => $success_msg
                ]);
                return;
            }

            $this->session->set_flashdata('success_msg', $success_msg);
            redirect('saas');
            return;
        }

        // Cargar vista de edición
        $this->load->model('workflow_templates_model');
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title']  = 'Editar Perfil del Negocio: ' . $loc['cname'] . ' | SaaS';

        $data = [
            'loc'        => $loc,
            'loc_id'     => $loc_id,
            'admin_user' => $admin_user,
            'wa_config'  => $wa_config,
            'templates'  => $this->workflow_templates_model->get_templates()
        ];

        $this->load->view('fixed/header', $head);
        $this->load->view('saas/edit', $data);
        $this->load->view('fixed/footer');
    }

    /**
     * Llamada interna a Evolution API para crear la instancia del nuevo negocio
     */
    private function _create_evolution_instance($instance_name)
    {
        $this->config->load('geeksia', true, true);
        $headers = [
            'apikey: ' . $this->config->item('evolution_api_key', 'geeksia'),
            'Content-Type: application/json'
        ];
        $payload = json_encode([
            'instanceName' => $instance_name,
            'qrcode' => true,
            'integration' => 'WHATSAPP-BAILEYS'
        ]);

        $ch = curl_init($this->config->item('evolution_api_url', 'geeksia') . '/instance/create');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res, true) ?: [];
    }

    /**
     * Iniciar sesión como este Negocio (Impersonate / Switch Tenant)
     */
    public function impersonate($loc_id)
    {
        $loc_id = (int)$loc_id;
        // Enlace GET: exigir que venga del propio panel (evita que un sitio externo lo dispare)
        if (!$this->_same_origin_referer() || !$this->db->where('id', $loc_id)->count_all_results('geopos_locations')) {
            show_error('Solicitud no válida.', 403);
            return;
        }
        $uid = (int)$this->aauth->get_user()->id;
        $this->session->set_userdata(['saas_orig_loc' => 0, 'saas_impersonator_id' => $uid]);
        $this->db->where('id', $uid)->update('geopos_users', ['loc' => $loc_id]);
        log_message('info', "SaaS: usuario {$uid} entró como local {$loc_id}");
        redirect('/pos_invoices/create');
    }

    /**
     * Volver a la cuenta Super-Administrador
     */
    public function revert()
    {
        $uid = (int)$this->aauth->get_user()->id;
        if ($this->session->userdata('saas_impersonator_id') != $uid && (int)$this->aauth->get_user()->loc !== 0) {
            show_error('No hay una sesión de impersonación activa.', 403);
            return;
        }
        $this->db->where('id', $uid)->update('geopos_users', ['loc' => 0]);
        $this->session->unset_userdata(['saas_orig_loc', 'saas_impersonator_id']);
        log_message('info', "SaaS: usuario {$uid} volvió a la cuenta de plataforma");
        redirect('/saas');
    }

    private function _same_origin_referer()
    {
        $ref = (string)$this->input->server('HTTP_REFERER');
        $host = parse_url($ref, PHP_URL_HOST);
        $allowed = array_filter([
            $this->input->server('HTTP_HOST') ? parse_url('http://' . $this->input->server('HTTP_HOST'), PHP_URL_HOST) : null,
            $this->input->server('HTTP_X_FORWARDED_HOST'),
            parse_url(base_url(), PHP_URL_HOST),
        ]);
        return $host && in_array(strtolower($host), array_map('strtolower', $allowed), true);
    }

    /**
     * Helper privado: Llamar a la API REST de n8n
     */
    private function _call_n8n($endpoint, $method = 'GET', $body = null)
    {
        $this->config->load('geeksia', true, true);
        $api_key = $this->config->item('n8n_api_key', 'geeksia');
        $url = $this->config->item('n8n_api_url', 'geeksia') . ltrim($endpoint, '/');

        $ch = curl_init($url);
        $headers = [
            'X-N8N-API-KEY: ' . $api_key,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);

        $upperMethod = strtoupper($method);
        if ($upperMethod === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($body !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($body) ? $body : json_encode($body));
            }
        } elseif ($upperMethod === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($body !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($body) ? $body : json_encode($body));
            }
        } elseif ($upperMethod === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $res = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = json_decode($res, true);
        return [
            'code' => $http_code,
            'data' => $decoded !== null ? $decoded : $res
        ];
    }

    /**
     * Gestión del Flujo n8n por Local (Superadmin)
     */
    public function workflow($loc_id = 0)
    {
        $loc_id = (int)$loc_id;

        // Obtener datos del negocio
        if ($loc_id == 0) {
            $loc = $this->db->get('geopos_system')->row_array();
            $loc['id'] = 0;
            $loc['whatsapp_instance'] = 'pos_whatsapp';
        } else {
            $loc = $this->db->where('id', $loc_id)->get('geopos_locations')->row_array();
            if (!$loc) {
                $this->session->set_flashdata('error_msg', 'El negocio seleccionado no existe.');
                redirect('/saas');
                return;
            }
        }

        // Obtener configuración de WhatsApp y el ID de flujo asignado
        $wa_config = $this->db->where('loc_id', $loc_id)->get('geopos_whatsapp_config')->row_array();
        if (!$wa_config) {
            $this->db->insert('geopos_whatsapp_config', [
                'loc_id' => $loc_id,
                'n8n_workflow_id' => 'OlRZzvyTYUzRc6ec'
            ]);
            $wa_config = $this->db->where('loc_id', $loc_id)->get('geopos_whatsapp_config')->row_array();
        }

        $workflow_id = !empty($wa_config['n8n_workflow_id']) ? $wa_config['n8n_workflow_id'] : 'OlRZzvyTYUzRc6ec';

        // 1. Obtener detalles del flujo actual desde n8n
        $wf_res = $this->_call_n8n('workflows/' . $workflow_id);
        $current_wf = null;
        if ($wf_res['code'] == 200 && is_array($wf_res['data'])) {
            $current_wf = $wf_res['data'];
        }

        // 2. Obtener lista de todos los flujos en el servidor n8n
        $all_wf_res = $this->_call_n8n('workflows');
        $all_workflows = [];
        if ($all_wf_res['code'] == 200 && !empty($all_wf_res['data']['data'])) {
            $all_workflows = $all_wf_res['data']['data'];
        }

        // 3. Cargar plantillas de flujos GeeksIA por rubro
        $this->load->model('workflow_templates_model');
        $templates = $this->workflow_templates_model->get_templates();
        $current_tpl_key = !empty($wa_config['tipo_flujo']) ? $wa_config['tipo_flujo'] : ($loc['tipo_flujo'] ?? 'restaurante');
        $current_tpl = $this->workflow_templates_model->get_template($current_tpl_key);

        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Gestor de Flujo GeeksIA | ' . ($loc['cname'] ?? 'Local');

        $data = [
            'loc'                 => $loc,
            'loc_id'              => $loc_id,
            'wa_config'           => $wa_config,
            'workflow_id'         => $workflow_id,
            'current_wf'          => $current_wf,
            'all_workflows'       => $all_workflows,
            'templates'           => $templates,
            'current_tpl_key'     => $current_tpl_key,
            'current_tpl'         => $current_tpl
        ];

        $this->load->view('fixed/header', $head);
        $this->load->view('saas/workflow', $data);
        $this->load->view('fixed/footer');
    }

    /**
     * Iniciar, Detener o Reiniciar el Flujo en n8n
     */
    public function workflow_action($action = '', $loc_id = 0)
    {
        $loc_id = (int)$loc_id;
        $action = trim(strtolower($action));

        // Estas acciones llegan por enlaces GET: exigir que vengan del propio panel
        if (!$this->_same_origin_referer()) {
            show_error('Solicitud no válida.', 403);
            return;
        }

        $wa_config = $this->db->where('loc_id', $loc_id)->get('geopos_whatsapp_config')->row_array();
        $workflow_id = !empty($wa_config['n8n_workflow_id']) ? $wa_config['n8n_workflow_id'] : 'OlRZzvyTYUzRc6ec';

        $is_ajax = $this->input->is_ajax_request();

        if ($action === 'activate') {
            $res = $this->_call_n8n('workflows/' . $workflow_id . '/activate', 'POST');
            $msg = ($res['code'] == 200) ? '¡Flujo iniciado y activado correctamente!' : 'Error al activar flujo: ' . json_encode($res['data']);
        } elseif ($action === 'deactivate') {
            $res = $this->_call_n8n('workflows/' . $workflow_id . '/deactivate', 'POST');
            $msg = ($res['code'] == 200) ? '¡Flujo detenido y pausado correctamente!' : 'Error al detener flujo: ' . json_encode($res['data']);
        } elseif ($action === 'restart') {
            $this->_call_n8n('workflows/' . $workflow_id . '/deactivate', 'POST');
            usleep(200000); // 200ms
            $res = $this->_call_n8n('workflows/' . $workflow_id . '/activate', 'POST');
            $msg = ($res['code'] == 200) ? '¡Flujo reiniciado con éxito!' : 'Error al reiniciar flujo: ' . json_encode($res['data']);
        } else {
            $msg = 'Acción de flujo no válida.';
            $res = ['code' => 400];
        }

        if ($is_ajax) {
            echo json_encode([
                'status' => ($res['code'] == 200) ? 'Success' : 'Error',
                'message' => $msg
            ]);
            return;
        }

        if ($res['code'] == 200) {
            $this->session->set_flashdata('success_msg', $msg);
        } else {
            $this->session->set_flashdata('error_msg', $msg);
        }

        redirect('saas/workflow/' . $loc_id);
    }

    /**
     * Subir / Cargar un nuevo JSON para actualizar o reemplazar el flujo en n8n
     */
    public function workflow_upload($loc_id = 0)
    {
        $loc_id = (int)$loc_id;
        $wa_config = $this->db->where('loc_id', $loc_id)->get('geopos_whatsapp_config')->row_array();
        $current_wf_id = !empty($wa_config['n8n_workflow_id']) ? $wa_config['n8n_workflow_id'] : 'OlRZzvyTYUzRc6ec';

        $json_content = '';

        // 1. Verificar si subió archivo .json
        if (!empty($_FILES['workflow_file']['tmp_name']) && is_uploaded_file($_FILES['workflow_file']['tmp_name'])) {
            $json_content = file_get_contents($_FILES['workflow_file']['tmp_name']);
        }
        // 2. Si no, verificar si pegó JSON en texto
        if (empty($json_content)) {
            $json_content = trim($this->input->post('workflow_raw', false));
        }

        if (empty($json_content)) {
            $this->session->set_flashdata('error_msg', 'No se proporcionó ningún archivo ni contenido JSON.');
            redirect('saas/workflow/' . $loc_id);
            return;
        }

        // Decodificar JSON en modo Objeto (esencial para compatibilidad con esquemas n8n)
        $parsed = json_decode($json_content);
        if (!$parsed) {
            $this->session->set_flashdata('error_msg', 'El contenido no es un JSON válido o tiene errores de sintaxis.');
            redirect('saas/workflow/' . $loc_id);
            return;
        }

        // Si el JSON viene como un array de workflows (ej: export múltiple), tomar el primero
        if (is_array($parsed) && !empty($parsed[0])) {
            $parsed = $parsed[0];
        }

        if (empty($parsed->nodes)) {
            $this->session->set_flashdata('error_msg', 'El JSON proporcionado no contiene la estructura requerida de nodos ("nodes") de GeeksIA.');
            redirect('saas/workflow/' . $loc_id);
            return;
        }

        $flow_name = trim($this->input->post('workflow_name', true));
        if (empty($flow_name)) {
            $flow_name = !empty($parsed->name) ? $parsed->name : 'Flujo Restaurante WhatsApp';
        }

        $payload = [
            'name' => $flow_name,
            'nodes' => $parsed->nodes ?? [],
            'connections' => $parsed->connections ?? new stdClass(),
            'settings' => $parsed->settings ?? new stdClass()
        ];

        $mode = $this->input->post('upload_mode', true) ?: 'update';
        $target_id = $current_wf_id;

        if ($mode === 'create_new') {
            // Crear como nuevo flujo independiente
            $res = $this->_call_n8n('workflows', 'POST', $payload);
            if ($res['code'] == 200 || $res['code'] == 201) {
                $target_id = $res['data']['id'];
                // Asociar nuevo ID a este local
                $this->db->where('loc_id', $loc_id)->update('geopos_whatsapp_config', ['n8n_workflow_id' => $target_id]);
                $success_msg = "¡Nuevo flujo exclusivo creado con éxito en GeeksIA Restaurantes! (ID: {$target_id})";
            } else {
                $err = is_array($res['data']) ? ($res['data']['message'] ?? json_encode($res['data'])) : $res['data'];
                $this->session->set_flashdata('error_msg', 'Error al crear flujo en GeeksIA: ' . $err);
                redirect('saas/workflow/' . $loc_id);
                return;
            }
        } else {
            // Actualizar flujo existente
            $res = $this->_call_n8n('workflows/' . $target_id, 'PUT', $payload);
            if ($res['code'] == 200) {
                $success_msg = "¡Flujo de datos (ID: {$target_id}) actualizado exitosamente con " . count($payload['nodes']) . " nodos!";
            } else {
                $err = is_array($res['data']) ? ($res['data']['message'] ?? json_encode($res['data'])) : $res['data'];
                $this->session->set_flashdata('error_msg', 'Error al actualizar flujo en GeeksIA: ' . $err);
                redirect('saas/workflow/' . $loc_id);
                return;
            }
        }

        // Auto-activar si está marcado
        if ($this->input->post('auto_activate')) {
            $this->_call_n8n('workflows/' . $target_id . '/activate', 'POST');
            $success_msg .= ' y activado en vivo.';
        }

        $this->session->set_flashdata('success_msg', $success_msg);
        redirect('saas/workflow/' . $loc_id);
    }

    /**
     * Asociar un flujo existente de n8n a este local
     */
    public function workflow_select($loc_id = 0)
    {
        $loc_id = (int)$loc_id;
        $selected_id = trim($this->input->post('selected_workflow_id', true));

        // Los IDs de n8n son alfanuméricos; se valida porque el ID se usa en rutas de la API de n8n
        if ($selected_id !== '' && !preg_match('/^[A-Za-z0-9]{1,64}$/', $selected_id)) {
            $this->session->set_flashdata('error_msg', 'ID de flujo no válido.');
            redirect('saas/workflow/' . $loc_id);
            return;
        }

        if (!empty($selected_id)) {
            $this->db->where('loc_id', $loc_id)->update('geopos_whatsapp_config', ['n8n_workflow_id' => $selected_id]);
            $this->session->set_flashdata('success_msg', "Flujo GeeksIA vinculado exitosamente (ID: {$selected_id}).");
        }

        redirect('saas/workflow/' . $loc_id);
    }

    /**
     * Aplicar una plantilla de flujo por rubro (Restaurante, Óptica, Tienda de Ropa, Barbería, etc.)
     */
    public function apply_template($loc_id = 0)
    {
        $loc_id = (int)$loc_id;
        $template_key = trim($this->input->post('template_key', true)) ?: 'restaurante';
        $overwrite_prompts = (bool)$this->input->post('overwrite_prompts');
        $clone_flow = (bool)$this->input->post('clone_flow');

        $this->load->model('workflow_templates_model');
        $res = $this->workflow_templates_model->apply_template($loc_id, $template_key, $overwrite_prompts);

        // Si el admin pide crear un flujo exclusivo para este rubro en n8n
        if ($clone_flow) {
            $tpl = $this->workflow_templates_model->get_template($template_key);
            $loc = $this->db->where('id', $loc_id)->get('geopos_locations')->row_array();
            $loc_name = $loc ? $loc['cname'] : ('Local ' . $loc_id);

            // Obtener el flujo base existente en n8n para clonarlo
            $base_wf_res = $this->_call_n8n('workflows/OlRZzvyTYUzRc6ec');
            if ($base_wf_res['code'] == 200 && !empty($base_wf_res['data']['nodes'])) {
                $base_nodes = $base_wf_res['data']['nodes'];
                $base_conn = $base_wf_res['data']['connections'];

                $payload = [
                    'name' => 'GeeksIA - ' . $tpl['name'] . ' (' . $loc_name . ')',
                    'nodes' => $base_nodes,
                    'connections' => $base_conn,
                    'settings' => new stdClass()
                ];

                $create_res = $this->_call_n8n('workflows', 'POST', $payload);
                if ($create_res['code'] == 200 || $create_res['code'] == 201) {
                    $new_wf_id = $create_res['data']['id'];
                    $this->db->where('loc_id', $loc_id)->update('geopos_whatsapp_config', [
                        'n8n_workflow_id' => $new_wf_id
                    ]);
                    $this->_call_n8n('workflows/' . $new_wf_id . '/activate', 'POST');
                    $this->session->set_flashdata('success_msg', "¡Plantilla '{$tpl['name']}' aplicada y nuevo flujo exclusivo clonado en GeeksIA (ID: {$new_wf_id})!");
                    redirect('saas/workflow/' . $loc_id);
                    return;
                }
            }
        }

        $this->session->set_flashdata('success_msg', $res['message']);
        redirect('saas/workflow/' . $loc_id);
    }

    /**
     * Eliminar un local tenant y todos sus registros en cascada
     */
    public function delete($loc_id = 0)
    {
        $loc_id = (int)$loc_id;
        if ($loc_id <= 0) {
            $loc_id = (int)$this->input->post('loc_id');
        }

        // Solo Super-Administrador (loc = 0 y roleid = 5)
        if ($this->aauth->get_user()->roleid < 5 || $this->aauth->get_user()->loc != 0) {
            $resp = ['status' => 'Error', 'message' => 'Acceso denegado: Se requieren permisos de Super-Administrador.'];
            if ($this->input->is_ajax_request() || $this->input->post()) {
                echo json_encode($resp);
                return;
            }
            $this->session->set_flashdata('error_msg', $resp['message']);
            redirect('saas');
            return;
        }

        if ($loc_id <= 0) {
            $resp = ['status' => 'Error', 'message' => 'La Sede Matriz (ID 0) no puede ser eliminada.'];
            if ($this->input->is_ajax_request() || $this->input->post()) {
                echo json_encode($resp);
                return;
            }
            $this->session->set_flashdata('error_msg', $resp['message']);
            redirect('saas');
            return;
        }

        $this->load->model('locations_model', 'locations');
        $res = $this->locations->delete_cascade($loc_id);

        if ($this->input->is_ajax_request() || $this->input->post()) {
            echo json_encode($res);
            return;
        }

        if ($res['status'] === 'Success') {
            $this->session->set_flashdata('success_msg', $res['message']);
        } else {
            $this->session->set_flashdata('error_msg', $res['message']);
        }
        redirect('saas');
    }
}
