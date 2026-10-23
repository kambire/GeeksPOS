<?php
/**
 * GeeksPOS - Delivery & WhatsApp Bot Operations Model
 * Handles configuration of Caja, Cocina, Gerente, Encargado, Admins, Delivery Group
 * and Round-Robin turn-based assignment for Deliveries (Slots 1 to 10).
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensure_tables_exist();
    }

    /**
     * Safety check to ensure required tables exist
     */
    private function ensure_tables_exist()
    {
        if (!$this->db->table_exists('geopos_whatsapp_config')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `geopos_whatsapp_config` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `loc_id` int(11) NOT NULL,
                `phone_caja` varchar(30) DEFAULT '',
                `phone_cocina` varchar(30) DEFAULT '',
                `phone_gerente` varchar(30) DEFAULT '',
                `phone_encargado` varchar(30) DEFAULT '',
                `phone_admins` text DEFAULT NULL,
                `delivery_group` varchar(100) DEFAULT '',
                `last_assigned_slot` int(11) DEFAULT 0,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `loc_id` (`loc_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        if (!$this->db->table_exists('geopos_deliveries')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `geopos_deliveries` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `loc_id` int(11) NOT NULL,
                `slot` int(3) NOT NULL,
                `nombre` varchar(100) DEFAULT '',
                `telefono` varchar(30) DEFAULT '',
                `activo` tinyint(1) DEFAULT 1,
                `ocupado` tinyint(1) DEFAULT 0,
                `pedidos_hoy` int(11) DEFAULT 0,
                `ultimo_pedido_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `loc_slot` (`loc_id`, `slot`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
    }

    /**
     * Clean phone number to digits only (e.g. +595 981 123-456 -> 595981123456)
     */
    public function clean_phone($phone)
    {
        if (empty($phone)) return '';
        $digits = preg_replace('/[^0-9]/', '', (string)$phone);
        // If starts with 09..., convert Paraguayan local format 0981... to 595981...
        if (strlen($digits) === 10 && substr($digits, 0, 2) === '09') {
            $digits = '595' . substr($digits, 1);
        } elseif (strlen($digits) === 9 && substr($digits, 0, 1) === '9') {
            $digits = '595' . $digits;
        }
        return $digits;
    }

    /**
     * Get or initialize WhatsApp numbers configuration for a specific location
     */
    public function get_config($loc_id)
    {
        $loc_id = (int)$loc_id;
        $row = $this->db->where('loc_id', $loc_id)->get('geopos_whatsapp_config')->row_array();

        if (!$row) {
            $default = [
                'loc_id'                 => $loc_id,
                'tipo_flujo'             => 'restaurante',
                'phone_caja'             => '',
                'phone_cocina'           => '',
                'phone_gerente'          => '',
                'phone_encargado'        => '',
                'phone_admins'           => '',
                'delivery_group'         => '',
                'horario_atencion'       => 'Lunes a Domingo de 18:00 a 23:59',
                'hora_apertura'          => '18:00',
                'hora_cierre'            => '23:59',
                'dias_atencion'          => 'Mon,Tue,Wed,Thu,Fri,Sat,Sun',
                'mensaje_cerrado'        => 'Hola, en este momento nuestro local se encuentra cerrado. Te esperamos en nuestro horario habitual.',
                'is_local_cerrado'       => 0,
                'is_delivery_cerrado'    => 0,
                'radio_max_km'           => 10.00,
                'costo_envio_base'       => 10000,
                'costo_por_km'           => 2000,
                'tiempo_entrega_min'     => '35 a 45 minutos',
                'tiempo_retiro_min'      => '15 a 20 minutos',
                'banco_nombre'           => '',
                'banco_titular'          => '',
                'banco_ruc_ci'           => '',
                'banco_cuenta'           => '',
                'banco_alias'            => '',
                'maps_direccion'         => '',
                'maps_link'              => '',
                'maps_resenas_link'      => '',
                'bot_modo'               => 'bot',
                'numeros_prueba'         => '',
                'ia_proveedor'           => 'gemini',
                'ia_api_key'             => '',
                'ia_modelo'              => 'gemini-1.5-flash',
                'ia_temperatura'         => 0.70,
                'ia_base_url'            => '',
                'ia_max_tokens'          => 1000,
                'ia_tono'                => 'Cálido, amigable, paraguayo informal con emojis moderados',
                'ia_instrucciones_extra' => '',
                'promos_semana'          => '',
                'last_assigned_slot'     => 0
            ];
            $this->db->insert('geopos_whatsapp_config', $default);
            $default['id'] = $this->db->insert_id();
            $default['updated_at'] = date('Y-m-d H:i:s');
            $row = $default;
        }

        // Parse admins string to array
        $admins_arr = [];
        if (!empty($row['phone_admins'])) {
            $admins_arr = array_values(array_filter(array_map('trim', explode(',', $row['phone_admins']))));
        }
        $row['phone_admins_list'] = $admins_arr;

        // Parse day-by-day business hours
        $row['horarios_semana_parsed'] = $this->parse_horarios_semana($row);

        return $row;
    }

    /**
     * Standard day catalog for day-by-day scheduling
     */
    public static function get_dias_semana_catalog()
    {
        return [
            'mon' => ['key' => 'mon', 'code' => 'Mon', 'nombre' => 'Lunes',     'corto' => 'Lun', 'icon' => 'fa fa-calendar-check-o'],
            'tue' => ['key' => 'tue', 'code' => 'Tue', 'nombre' => 'Martes',    'corto' => 'Mar', 'icon' => 'fa fa-calendar-check-o'],
            'wed' => ['key' => 'wed', 'code' => 'Wed', 'nombre' => 'Miércoles', 'corto' => 'Mié', 'icon' => 'fa fa-calendar-check-o'],
            'thu' => ['key' => 'thu', 'code' => 'Thu', 'nombre' => 'Jueves',    'corto' => 'Jue', 'icon' => 'fa fa-calendar-check-o'],
            'fri' => ['key' => 'fri', 'code' => 'Fri', 'nombre' => 'Viernes',   'corto' => 'Vie', 'icon' => 'fa fa-calendar-check-o'],
            'sat' => ['key' => 'sat', 'code' => 'Sat', 'nombre' => 'Sábado',    'corto' => 'Sáb', 'icon' => 'fa fa-glass'],
            'sun' => ['key' => 'sun', 'code' => 'Sun', 'nombre' => 'Domingo',   'corto' => 'Dom', 'icon' => 'fa fa-coffee']
        ];
    }

    /**
     * Parse horarios_semana JSON or construct defaults from legacy fields
     */
    public function parse_horarios_semana($row)
    {
        $catalog = self::get_dias_semana_catalog();
        $stored = [];
        if (!empty($row['horarios_semana'])) {
            $decoded = json_decode($row['horarios_semana'], true);
            if (is_array($decoded)) {
                $stored = $decoded;
            }
        }

        $dias_sel = array_filter(array_map('trim', explode(',', $row['dias_atencion'] ?? 'Mon,Tue,Wed,Thu,Fri,Sat,Sun')));
        $default_ini = !empty($row['hora_apertura']) ? $row['hora_apertura'] : '18:00';
        $default_fin = !empty($row['hora_cierre']) ? $row['hora_cierre'] : '23:59';

        $parsed = [];
        foreach ($catalog as $k => $info) {
            if (isset($stored[$k]) && is_array($stored[$k])) {
                $parsed[$k] = [
                    'key'      => $k,
                    'code'     => $info['code'],
                    'nombre'   => $info['nombre'],
                    'corto'    => $info['corto'],
                    'icon'     => $info['icon'],
                    'activo'   => !empty($stored[$k]['activo']) ? 1 : 0,
                    'apertura' => !empty($stored[$k]['apertura']) ? $stored[$k]['apertura'] : $default_ini,
                    'cierre'   => !empty($stored[$k]['cierre']) ? $stored[$k]['cierre'] : $default_fin
                ];
            } else {
                $is_active = in_array($info['code'], $dias_sel) ? 1 : 0;
                $parsed[$k] = [
                    'key'      => $k,
                    'code'     => $info['code'],
                    'nombre'   => $info['nombre'],
                    'corto'    => $info['corto'],
                    'icon'     => $info['icon'],
                    'activo'   => $is_active,
                    'apertura' => $default_ini,
                    'cierre'   => $default_fin
                ];
            }
        }

        return $parsed;
    }

    /**
     * Save WhatsApp numbers and all operational configuration for a location
     */
    public function save_config($loc_id, $data)
    {
        $loc_id = (int)$loc_id;
        $existing = $this->db->where('loc_id', $loc_id)->get('geopos_whatsapp_config')->row();

        $admins = '';
        if (isset($data['phone_admins'])) {
            if (is_array($data['phone_admins'])) {
                $cleaned = array_map([$this, 'clean_phone'], $data['phone_admins']);
                $admins = implode(',', array_filter($cleaned));
            } else {
                $raw = explode(',', $data['phone_admins']);
                $cleaned = array_map([$this, 'clean_phone'], $raw);
                $admins = implode(',', array_filter($cleaned));
            }
        }

        // Promos semana format handler
        $promos_semana_val = null;
        if (isset($data['promos_semana'])) {
            if (is_array($data['promos_semana'])) {
                $promos_semana_val = json_encode($data['promos_semana'], JSON_UNESCAPED_UNICODE);
            } else {
                $promos_semana_val = trim($data['promos_semana']);
            }
        }

        // Horarios semana day-by-day handler
        $horarios_semana_json = null;
        if (isset($data['horario']) && is_array($data['horario'])) {
            $catalog = self::get_dias_semana_catalog();
            $clean_horarios = [];
            $active_codes = [];
            $first_ini = null;
            $first_fin = null;

            foreach ($catalog as $k => $info) {
                $raw_day = $data['horario'][$k] ?? [];
                $is_active = !empty($raw_day['activo']) ? 1 : 0;
                $apertura = $this->valid_hhmm($raw_day['apertura'] ?? '', '18:00');
                $cierre = $this->valid_hhmm($raw_day['cierre'] ?? '', '23:59');

                $clean_horarios[$k] = [
                    'activo'   => $is_active,
                    'apertura' => $apertura,
                    'cierre'   => $cierre
                ];

                if ($is_active) {
                    $active_codes[] = $info['code'];
                    if ($first_ini === null) {
                        $first_ini = $apertura;
                        $first_fin = $cierre;
                    }
                }
            }

            $horarios_semana_json = json_encode($clean_horarios, JSON_UNESCAPED_UNICODE);
            $data['dias_atencion'] = implode(',', $active_codes);
            if ($first_ini !== null) {
                $data['hora_apertura'] = $first_ini;
                $data['hora_cierre'] = $first_fin;
            }
        } elseif (isset($data['horarios_semana'])) {
            $horarios_semana_json = $this->sanitize_horarios_semana($data['horarios_semana']);
        }

        $fields = [
            'phone_caja'             => isset($data['phone_caja']) ? $this->clean_phone($data['phone_caja']) : '',
            'phone_cocina'           => isset($data['phone_cocina']) ? $this->clean_phone($data['phone_cocina']) : '',
            'phone_gerente'          => isset($data['phone_gerente']) ? $this->clean_phone($data['phone_gerente']) : '',
            'phone_encargado'        => isset($data['phone_encargado']) ? $this->clean_phone($data['phone_encargado']) : '',
            'phone_admins'           => $admins,
            'delivery_group'         => isset($data['delivery_group']) ? trim($data['delivery_group']) : '',
            'horario_atencion'       => isset($data['horario_atencion']) ? trim($data['horario_atencion']) : '',
            'hora_apertura'          => $this->valid_hhmm($data['hora_apertura'] ?? '', '18:00'),
            'hora_cierre'            => $this->valid_hhmm($data['hora_cierre'] ?? '', '23:59'),
            'dias_atencion'          => isset($data['dias_atencion']) ? trim($data['dias_atencion']) : 'Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            'mensaje_cerrado'        => isset($data['mensaje_cerrado']) ? trim($data['mensaje_cerrado']) : '',
            'is_local_cerrado'       => !empty($data['is_local_cerrado']) ? 1 : 0,
            'is_delivery_cerrado'    => !empty($data['is_delivery_cerrado']) ? 1 : 0,
            'radio_max_km'           => isset($data['radio_max_km']) ? (float)$data['radio_max_km'] : 10.00,
            'costo_envio_base'       => isset($data['costo_envio_base']) ? (int)$data['costo_envio_base'] : 10000,
            'costo_por_km'           => isset($data['costo_por_km']) ? (int)$data['costo_por_km'] : 2000,
            'tiempo_entrega_min'     => isset($data['tiempo_entrega_min']) ? trim($data['tiempo_entrega_min']) : '35 a 45 minutos',
            'tiempo_retiro_min'      => isset($data['tiempo_retiro_min']) ? trim($data['tiempo_retiro_min']) : '15 a 20 minutos',
            'banco_nombre'           => isset($data['banco_nombre']) ? trim($data['banco_nombre']) : '',
            'banco_titular'          => isset($data['banco_titular']) ? trim($data['banco_titular']) : '',
            'banco_ruc_ci'           => isset($data['banco_ruc_ci']) ? trim($data['banco_ruc_ci']) : '',
            'banco_cuenta'           => isset($data['banco_cuenta']) ? trim($data['banco_cuenta']) : '',
            'banco_alias'            => isset($data['banco_alias']) ? trim($data['banco_alias']) : '',
            'maps_direccion'         => isset($data['maps_direccion']) ? trim($data['maps_direccion']) : '',
            'maps_link'              => isset($data['maps_link']) ? trim($data['maps_link']) : '',
            'maps_resenas_link'      => isset($data['maps_resenas_link']) ? trim($data['maps_resenas_link']) : '',
            'bot_modo'               => isset($data['bot_modo']) ? trim($data['bot_modo']) : 'bot',
            'numeros_prueba'         => isset($data['numeros_prueba']) ? trim($data['numeros_prueba']) : '',
            'ia_proveedor'           => isset($data['ia_proveedor']) ? substr(preg_replace('/[^a-z0-9_.-]/i', '', $data['ia_proveedor']), 0, 50) : 'gemini',
            'ia_modelo'              => isset($data['ia_modelo']) ? substr(trim($data['ia_modelo']), 0, 100) : 'gemini-1.5-flash',
            'ia_temperatura'         => isset($data['ia_temperatura']) ? max(0, min(2, (float)$data['ia_temperatura'])) : 0.70,
            'ia_base_url'            => isset($data['ia_base_url']) ? $this->valid_ia_base_url($data['ia_base_url']) : '',
            'ia_max_tokens'          => isset($data['ia_max_tokens']) ? max(50, min(8000, (int)$data['ia_max_tokens'])) : 1000,
            'ia_tono'                => isset($data['ia_tono']) ? mb_substr(trim($data['ia_tono']), 0, 150) : 'Cálido, amigable, paraguayo informal con emojis moderados',
            'ia_instrucciones_extra' => isset($data['ia_instrucciones_extra']) ? trim($data['ia_instrucciones_extra']) : '',
            'tipo_flujo'             => $this->valid_tipo_flujo($data['tipo_flujo'] ?? null, $existing->tipo_flujo ?? 'restaurante')
        ];

        // Clave de IA: se guarda cifrada (AES-256-GCM) y un campo vacío conserva la clave actual
        if (isset($data['ia_api_key']) && trim($data['ia_api_key']) !== '') {
            $fields['ia_api_key'] = $this->encrypt_ia_key($loc_id, trim($data['ia_api_key']));
        } elseif (!empty($data['ia_api_key_borrar'])) {
            $fields['ia_api_key'] = '';
        }

        if ($horarios_semana_json !== null) {
            $fields['horarios_semana'] = $horarios_semana_json;
        }

        if ($promos_semana_val !== null) {
            $fields['promos_semana'] = $promos_semana_val;
        }

        if ($existing) {
            $this->db->where('loc_id', $loc_id)->update('geopos_whatsapp_config', $fields);
        } else {
            $fields['loc_id'] = $loc_id;
            $fields['last_assigned_slot'] = 0;
            $this->db->insert('geopos_whatsapp_config', $fields);
        }

        return $this->get_config($loc_id);
    }

    /**
     * Actualización parcial desde la API REST: solo toca las claves presentes en el payload.
     * Datos bancarios, clave de IA y prompts NO se pueden escribir por API (solo desde el panel).
     */
    public function patch_config($loc_id, array $data)
    {
        $loc_id = (int)$loc_id;
        $this->get_config($loc_id); // asegura que exista la fila

        $writable = [
            'phone_caja' => 'phone', 'phone_cocina' => 'phone', 'phone_gerente' => 'phone', 'phone_encargado' => 'phone',
            'phone_admins' => 'phones', 'delivery_group' => 'text100',
            'horario_atencion' => 'text', 'hora_apertura' => 'hhmm', 'hora_cierre' => 'hhmm', 'dias_atencion' => 'dias',
            'mensaje_cerrado' => 'text', 'is_local_cerrado' => 'bool', 'is_delivery_cerrado' => 'bool',
            'radio_max_km' => 'float', 'costo_envio_base' => 'int', 'costo_por_km' => 'int',
            'tiempo_entrega_min' => 'text50', 'tiempo_retiro_min' => 'text50',
            'bot_modo' => 'bot_modo', 'numeros_prueba' => 'phones',
        ];

        $fields = [];
        foreach ($writable as $k => $type) {
            if (!array_key_exists($k, $data)) {
                continue; // ausente = no se modifica
            }
            $v = $data[$k];
            switch ($type) {
                case 'phone':   $fields[$k] = $this->clean_phone($v); break;
                case 'phones':  $fields[$k] = implode(',', array_filter(array_map([$this, 'clean_phone'], is_array($v) ? $v : explode(',', (string)$v)))); break;
                case 'bool':    $fields[$k] = !empty($v) ? 1 : 0; break;
                case 'int':     $fields[$k] = max(0, (int)$v); break;
                case 'float':   $fields[$k] = max(0, min(999, (float)$v)); break;
                case 'hhmm':    $fields[$k] = $this->valid_hhmm($v, null); if ($fields[$k] === null) unset($fields[$k]); break;
                case 'dias':    $fields[$k] = implode(',', array_intersect(array_map('trim', explode(',', (string)$v)), ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'])); break;
                case 'bot_modo': if (in_array($v, ['bot', 'humano', 'desarrollo'], true)) $fields[$k] = $v; break;
                case 'text50':  $fields[$k] = mb_substr(trim((string)$v), 0, 50); break;
                case 'text100': $fields[$k] = mb_substr(trim((string)$v), 0, 100); break;
                default:        $fields[$k] = mb_substr(trim((string)$v), 0, 2000);
            }
        }
        if (array_key_exists('horarios_semana', $data)) {
            $hs = $this->sanitize_horarios_semana($data['horarios_semana']);
            if ($hs !== null) {
                $fields['horarios_semana'] = $hs;
            }
        }
        if (array_key_exists('promos_semana', $data) && is_array($data['promos_semana'])) {
            $promos = [];
            foreach (['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'] as $d) {
                $promos[$d] = isset($data['promos_semana'][$d]) ? mb_substr(trim((string)$data['promos_semana'][$d]), 0, 500) : '';
            }
            $fields['promos_semana'] = json_encode($promos, JSON_UNESCAPED_UNICODE);
        }

        if ($fields) {
            $this->db->where('loc_id', $loc_id)->update('geopos_whatsapp_config', $fields);
        }
        return array_keys($fields);
    }

    /**
     * Hora HH:MM válida (00:00-23:59) o el valor por defecto
     */
    public function valid_hhmm($value, $default)
    {
        $value = trim((string)$value);
        if (preg_match('/^(\d{1,2}):([0-5]\d)$/', $value, $m) && (int)$m[1] <= 23) {
            return sprintf('%02d:%s', (int)$m[1], $m[2]);
        }
        return $default;
    }

    /**
     * Normaliza el JSON de horarios día por día; devuelve null si no es válido
     */
    protected function sanitize_horarios_semana($raw)
    {
        $decoded = is_array($raw) ? $raw : json_decode((string)$raw, true);
        if (!is_array($decoded)) {
            return null;
        }
        $clean = [];
        foreach (array_keys(self::get_dias_semana_catalog()) as $k) {
            $d = isset($decoded[$k]) && is_array($decoded[$k]) ? $decoded[$k] : [];
            $clean[$k] = [
                'activo'   => !empty($d['activo']) ? 1 : 0,
                'apertura' => $this->valid_hhmm($d['apertura'] ?? '', '18:00'),
                'cierre'   => $this->valid_hhmm($d['cierre'] ?? '', '23:59'),
            ];
        }
        return json_encode($clean, JSON_UNESCAPED_UNICODE);
    }

    /**
     * base_url de IA: solo https hacia hosts públicos (evita SSRF hacia la red interna desde n8n)
     */
    protected function valid_ia_base_url($url)
    {
        $url = trim((string)$url);
        if ($url === '') {
            return '';
        }
        $parts = parse_url($url);
        if (empty($parts['scheme']) || strtolower($parts['scheme']) !== 'https' || empty($parts['host'])) {
            return '';
        }
        $host = strtolower($parts['host']);
        if ($host === 'localhost' || strpos($host, '.') === false) {
            return ''; // nombres internos de Docker (evolution_api, n8n, db...)
        }
        if (filter_var($host, FILTER_VALIDATE_IP) && !filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return '';
        }
        return substr($url, 0, 255);
    }

    protected function valid_tipo_flujo($value, $fallback)
    {
        if ($value === null || $value === '') {
            return $fallback;
        }
        $this->load->model('workflow_templates_model');
        $key = strtolower(trim((string)$value));
        return array_key_exists($key, $this->workflow_templates_model->get_templates()) ? $key : $fallback;
    }

    protected function encrypt_ia_key($loc_id, $plain)
    {
        $this->load->library('secret_box');
        return $this->secret_box->encrypt($plain, 'loc:' . (int)$loc_id . ':ia_api_key');
    }

    /**
     * Clave de IA descifrada (solo para uso interno del servidor / clave de plataforma)
     */
    public function get_ia_key_plain($loc_id, $stored)
    {
        $this->load->library('secret_box');
        return $this->secret_box->decrypt($stored, 'loc:' . (int)$loc_id . ':ia_api_key');
    }

    /**
     * Últimos 4 caracteres de la clave de IA para mostrar en el panel
     */
    public function get_ia_key_hint($loc_id, $stored)
    {
        $this->load->library('secret_box');
        return $this->secret_box->hint($stored, 'loc:' . (int)$loc_id . ':ia_api_key');
    }

    /**
     * Slots sin datos sensibles (sin PIN ni token) para respuestas de API
     */
    public function get_deliveries_public($loc_id)
    {
        $public = ['slot', 'nombre', 'telefono', 'activo', 'ocupado', 'pedidos_hoy', 'ultimo_pedido_at'];
        return array_map(function ($d) use ($public) {
            return array_intersect_key($d, array_flip($public));
        }, $this->get_deliveries($loc_id));
    }

    /**
     * Get all 10 deliveries slots for a location. Auto-populates missing slots 1..10
     */
    public function get_deliveries($loc_id)
    {
        $loc_id = (int)$loc_id;
        $rows = $this->db->where('loc_id', $loc_id)
                         ->order_by('slot', 'ASC')
                         ->get('geopos_deliveries')
                         ->result_array();

        $indexed = [];
        foreach ($rows as $r) {
            $indexed[(int)$r['slot']] = $r;
        }

        $result = [];
        for ($slot = 1; $slot <= 10; $slot++) {
            if (isset($indexed[$slot])) {
                $result[] = $indexed[$slot];
            } else {
                // Insert missing slot
                $new_slot = [
                    'loc_id'           => $loc_id,
                    'slot'             => $slot,
                    'nombre'           => 'Repartidor ' . $slot,
                    'telefono'         => '',
                    'activo'           => 0,
                    'ocupado'          => 0,
                    'pedidos_hoy'      => 0,
                    'ultimo_pedido_at' => null,
                    'pin'              => (string)random_int(1000, 9999)
                ];
                $this->db->insert('geopos_deliveries', $new_slot);
                $new_slot['id'] = $this->db->insert_id();
                $result[] = $new_slot;
            }
        }

        return $result;
    }

    /**
     * Save deliveries slots 1 to 10 for a location
     */
    public function save_deliveries($loc_id, $deliveries_data)
    {
        $loc_id = (int)$loc_id;
        if (!is_array($deliveries_data)) {
            return false;
        }

        foreach ($deliveries_data as $item) {
            $slot = isset($item['slot']) ? (int)$item['slot'] : 0;
            if ($slot < 1 || $slot > 10) continue;

            $nombre   = isset($item['nombre']) ? trim($item['nombre']) : ('Repartidor ' . $slot);
            $telefono = isset($item['telefono']) ? $this->clean_phone($item['telefono']) : '';
            $activo   = !empty($item['activo']) ? 1 : 0;
            $ocupado  = !empty($item['ocupado']) ? 1 : 0;
            $pin      = !empty($item['pin']) ? substr(preg_replace('/[^0-9]/', '', $item['pin']), 0, 6) : '';
            if (strlen($pin) < 4) $pin = (string)random_int(1000, 9999);

            $existing = $this->db->where('loc_id', $loc_id)->where('slot', $slot)->get('geopos_deliveries')->row();

            $payload = [
                'nombre'   => $nombre,
                'telefono' => $telefono,
                'activo'   => $activo,
                'ocupado'  => $ocupado,
                'pin'      => $pin
            ];

            if ($existing) {
                // Cambiar teléfono o PIN, o desactivar el slot, cierra la sesión del repartidor en la app
                if ($existing->pin !== $pin || $existing->telefono !== $telefono || !$activo) {
                    $payload['auth_token'] = null;
                    $payload['auth_token_expires'] = null;
                }
                $this->db->where('id', $existing->id)->update('geopos_deliveries', $payload);
            } else {
                $payload['loc_id'] = $loc_id;
                $payload['slot'] = $slot;
                $payload['pedidos_hoy'] = 0;
                $this->db->insert('geopos_deliveries', $payload);
            }
        }

        return $this->get_deliveries($loc_id);
    }

    /**
     * Simulación de solo lectura: a quién le tocaría el próximo pedido (no marca ocupado ni crea pedidos)
     */
    public function preview_next_delivery($loc_id)
    {
        $loc_id = (int)$loc_id;
        $chosen = $this->db->where('loc_id', $loc_id)
                           ->where('activo', 1)
                           ->where('ocupado', 0)
                           ->where("telefono != '' AND telefono IS NOT NULL")
                           ->order_by('pedidos_hoy', 'ASC')
                           ->order_by('ultimo_pedido_at IS NULL', 'DESC', false)
                           ->order_by('ultimo_pedido_at', 'ASC')
                           ->order_by('slot', 'ASC')
                           ->limit(1)
                           ->get('geopos_deliveries')
                           ->row_array();

        if (!$chosen) {
            return ['assigned' => false, 'simulacion' => true, 'message' => 'No hay repartidores activos o disponibles en este momento', 'delivery' => null];
        }
        return [
            'assigned'   => true,
            'simulacion' => true,
            'message'    => 'Simulación: no se asignó ningún pedido real',
            'delivery'   => [
                'slot'             => (int)$chosen['slot'],
                'nombre'           => $chosen['nombre'],
                'telefono'         => $chosen['telefono'],
                'pedidos_hoy'      => (int)$chosen['pedidos_hoy'],
                'ultimo_pedido_at' => $chosen['ultimo_pedido_at']
            ]
        ];
    }

    /**
     * Asignar delivery por turno equitativo (Round-Robin & Menos pedidos hoy)
     * Matching the exact algorithmic requirements of n8n flujo restaurante.json:
     * 1. Solo repartidores activos (activo = 1) y disponibles (ocupado = 0)
     * 2. Ordenar por:
     *    a. Menos pedidos entregados hoy (pedidos_hoy ASC)
     *    b. Si empatan, el que hace más tiempo no recibe un pedido (ultimo_pedido_at ASC / NULL first)
     *    c. Slot ascendente (1..10)
     * 3. Asigna 1, incrementa su contador de pedidos_hoy, actualiza timestamp y registra en geopos_delivery_orders
     */
    public function assign_next_delivery($loc_id, $order_id = null, $order_details = [])
    {
        $loc_id = (int)$loc_id;

        // Ensure slots exist
        $this->get_deliveries($loc_id);

        // Query candidate
        $this->db->select('*');
        $this->db->from('geopos_deliveries');
        $this->db->where('loc_id', $loc_id);
        $this->db->where('activo', 1);
        $this->db->where('ocupado', 0);
        $this->db->where("telefono != '' AND telefono IS NOT NULL");

        // Order criteria:
        // 1. pedidos_hoy ASC
        // 2. ISNULL(ultimo_pedido_at) DESC (those with NULL go first)
        // 3. ultimo_pedido_at ASC
        // 4. slot ASC
        $this->db->order_by('pedidos_hoy', 'ASC');
        $this->db->order_by('ultimo_pedido_at IS NULL', 'DESC', false);
        $this->db->order_by('ultimo_pedido_at', 'ASC');
        $this->db->order_by('slot', 'ASC');
        $this->db->limit(1);

        $chosen = $this->db->get()->row_array();

        if (!$chosen) {
            return [
                'assigned' => false,
                'message'  => 'No hay repartidores activos o disponibles en este momento',
                'delivery' => null
            ];
        }

        // Increment counter & update timestamp
        $new_count = (int)$chosen['pedidos_hoy'] + 1;
        $now = date('Y-m-d H:i:s');

        $this->db->where('id', $chosen['id'])->update('geopos_deliveries', [
            'pedidos_hoy'      => $new_count,
            'ultimo_pedido_at' => $now,
            'ocupado'          => 1 // Marcar ocupado mientras entrega este pedido
        ]);

        // Record last assigned slot in config
        $this->db->where('loc_id', $loc_id)->update('geopos_whatsapp_config', [
            'last_assigned_slot' => $chosen['slot']
        ]);

        $chosen['pedidos_hoy'] = $new_count;
        $chosen['ultimo_pedido_at'] = $now;
        $chosen['ocupado'] = 1;

        // Generar hash de tracking en vivo y registrar en geopos_delivery_orders
        $tracking_hash = bin2hex(random_bytes(16));
        $order_num = !empty($order_details['order_number']) ? $order_details['order_number'] : ('#' . ($order_id ?: rand(1000, 9999)));

        $delivery_order_data = [
            'loc_id'            => $loc_id,
            'invoice_id'        => $order_id ? (int)$order_id : null,
            'order_number'      => $order_num,
            'delivery_slot'     => (int)$chosen['slot'],
            'delivery_nombre'   => $chosen['nombre'],
            'delivery_telefono' => $chosen['telefono'],
            'estado'            => 'asignado',
            'cliente_nombre'    => !empty($order_details['cliente_nombre']) ? $order_details['cliente_nombre'] : 'Cliente',
            'cliente_telefono'  => !empty($order_details['cliente_telefono']) ? $order_details['cliente_telefono'] : '',
            'direccion_entrega' => !empty($order_details['direccion_entrega']) ? $order_details['direccion_entrega'] : (!empty($order_details['direccion']) ? $order_details['direccion'] : 'A convenir'),
            'maps_link'         => !empty($order_details['maps_link']) ? $order_details['maps_link'] : '',
            'lat_destino'       => !empty($order_details['lat_destino']) ? $order_details['lat_destino'] : null,
            'lng_destino'       => !empty($order_details['lng_destino']) ? $order_details['lng_destino'] : null,
            'metodo_pago'       => !empty($order_details['metodo_pago']) ? $order_details['metodo_pago'] : 'efectivo',
            'monto_total'       => !empty($order_details['monto_total']) ? (float)$order_details['monto_total'] : 0,
            'paga_con'          => !empty($order_details['paga_con']) ? (float)$order_details['paga_con'] : null,
            'vuelto'            => !empty($order_details['vuelto']) ? (float)$order_details['vuelto'] : null,
            'comprobante_url'   => !empty($order_details['comprobante_url']) ? $order_details['comprobante_url'] : '',
            'items_json'        => !empty($order_details['items_json']) ? (is_array($order_details['items_json']) ? json_encode($order_details['items_json'], JSON_UNESCAPED_UNICODE) : $order_details['items_json']) : null,
            'notas_cocina'      => !empty($order_details['notas_cocina']) ? $order_details['notas_cocina'] : '',
            'tracking_hash'     => $tracking_hash,
            'assigned_at'       => $now
        ];

        $this->db->insert('geopos_delivery_orders', $delivery_order_data);
        $delivery_order_id = $this->db->insert_id();
        $tracking_url = base_url('tracking/' . $tracking_hash);

        return [
            'assigned'          => true,
            'order_id'          => $order_id ? (int)$order_id : null,
            'delivery_order_id' => $delivery_order_id,
            'tracking_hash'     => $tracking_hash,
            'tracking_url'      => $tracking_url,
            'delivery'          => [
                'slot'             => (int)$chosen['slot'],
                'nombre'           => $chosen['nombre'],
                'telefono'         => $chosen['telefono'],
                'pedidos_hoy'      => $new_count,
                'ultimo_pedido_at' => $now
            ]
        ];
    }

    /**
     * Update delivery availability status (e.g. cuando el repartidor escribe "ocupado" o "disponible")
     */
    public function update_delivery_status($loc_id, $phone_or_slot, $ocupado)
    {
        $loc_id = (int)$loc_id;
        $ocupado = (int)$ocupado ? 1 : 0;

        $this->db->where('loc_id', $loc_id);
        if (is_numeric($phone_or_slot) && (int)$phone_or_slot <= 10 && (int)$phone_or_slot >= 1) {
            $this->db->where('slot', (int)$phone_or_slot);
        } else {
            $clean = $this->clean_phone($phone_or_slot);
            $this->db->where('telefono', $clean);
        }

        $row = $this->db->get('geopos_deliveries')->row_array();
        if (!$row) {
            return ['status' => false, 'message' => 'Repartidor no encontrado'];
        }

        $this->db->where('id', $row['id'])->update('geopos_deliveries', ['ocupado' => $ocupado]);

        return [
            'status'     => true,
            'slot'       => (int)$row['slot'],
            'nombre'     => $row['nombre'],
            'telefono'   => $row['telefono'],
            'ocupado'    => $ocupado,
            'message'    => $ocupado ? 'Repartidor marcado como Ocupado' : 'Repartidor marcado como Disponible'
        ];
    }

    /**
     * Reset daily orders counter for all delivery drivers of a location
     */
    public function reset_daily_counters($loc_id)
    {
        // Siempre por local: nunca reiniciar los contadores de todos los tenants
        $this->db->where('loc_id', (int)$loc_id)->update('geopos_deliveries', ['pedidos_hoy' => 0]);
        return true;
    }

    /**
     * Randomize 4-digit PINs for all slots of a location
     */
    public function randomize_all_pins($loc_id)
    {
        $slots = $this->db->where('loc_id', (int)$loc_id)->get('geopos_deliveries')->result_array();
        $updated = [];
        foreach ($slots as $s) {
            $pin = (string)random_int(1000, 9999);
            // Un PIN nuevo invalida la sesión activa del repartidor en la app
            $this->db->where('id', $s['id'])->update('geopos_deliveries', ['pin' => $pin, 'auth_token' => null, 'auth_token_expires' => null]);
            $updated[(int)$s['slot']] = $pin;
        }
        return $updated;
    }

    /**
     * Get complete workflow config for n8n dynamically.
     * Nunca incluye PIN/tokens de repartidores ni API keys. La clave de IA descifrada solo
     * se incluye con $include_secrets (clave REST de plataforma desde la red interna).
     */
    public function get_full_workflow_config($loc_id, $include_secrets = false)
    {
        $loc_id = (int)$loc_id;
        $cfg = $this->get_config($loc_id);
        $deliveries = $this->get_deliveries_public($loc_id);
        $ia_key = $include_secrets ? $this->get_ia_key_plain($loc_id, $cfg['ia_api_key'] ?? '') : '';
        $ia_key_configurada = !empty($cfg['ia_api_key']);

        // Get tenant/location name and whatsapp_instance
        $location = $this->db->where('id', $loc_id)->get('geopos_locations')->row_array();
        if (!$location && $loc_id === 0) {
            $sys = $this->db->get('geopos_system')->row_array();
            $location = [
                'id' => 0,
                'cname' => $sys['cname'] ?? 'Principal',
                'whatsapp_instance' => 'pos_whatsapp',
                'address' => $sys['address'] ?? ''
            ];
        }

        // Active delivery numbers array
        $active_repartidores = [];
        foreach ($deliveries as $d) {
            if (!empty($d['activo']) && !empty($d['telefono'])) {
                $active_repartidores[] = $d['telefono'];
            }
        }

        // Staff silent: caja y cocina no son intervenidos por el bot
        $staff_silent = array_values(array_unique(array_filter([
            $cfg['phone_caja'],
            $cfg['phone_cocina']
        ])));

        // Super admins & internal lists
        $admins = $cfg['phone_admins_list'];
        $gerentes = array_values(array_unique(array_filter(array_merge(
            $admins,
            [$cfg['phone_gerente'], $cfg['phone_encargado']]
        ))));

        // Evaluation of business hours in Paraguay time (America/Asuncion)
        $tz = new DateTimeZone('America/Asuncion');
        $now = new DateTime('now', $tz);
        $current_weekday = $now->format('D'); // Mon, Tue, Wed, Thu, Fri, Sat, Sun
        $current_day_key = strtolower($current_weekday);
        $current_time = $now->format('H:i');

        $dias_map_es = [
            'Mon' => 'Lunes',
            'Tue' => 'Martes',
            'Wed' => 'Miércoles',
            'Thu' => 'Jueves',
            'Fri' => 'Viernes',
            'Sat' => 'Sábado',
            'Sun' => 'Domingo'
        ];
        $dia_nombre = $dias_map_es[$current_weekday] ?? $current_weekday;

        $horarios_semana = $this->parse_horarios_semana($cfg);
        $today_cfg = $horarios_semana[$current_day_key] ?? null;

        $abierto = false;
        $motivo_cierre = '';
        $hora_ini_hoy = $today_cfg ? $today_cfg['apertura'] : ($cfg['hora_apertura'] ?: '18:00');
        $hora_fin_hoy = $today_cfg ? $today_cfg['cierre'] : ($cfg['hora_cierre'] ?: '23:59');

        if ($today_cfg && !empty($today_cfg['activo'])) {
            $ini = $today_cfg['apertura'];
            $fin = $today_cfg['cierre'];
            if ($fin >= $ini) {
                // Turno habitual dentro del mismo día (ej. 11:00 a 23:00)
                if ($current_time >= $ini && $current_time <= $fin) {
                    $abierto = true;
                }
            } else {
                // Turno nocturno que cruza la medianoche (ej. 18:00 a 02:00)
                if ($current_time >= $ini || $current_time <= $fin) {
                    $abierto = true;
                }
            }
        }

        // Revisar si ayer hubo un turno nocturno de madrugada que sigue abierto hoy a esta hora
        if (!$abierto) {
            $yesterday = (clone $now)->modify('-1 day');
            $y_key = strtolower($yesterday->format('D'));
            $y_cfg = $horarios_semana[$y_key] ?? null;
            if ($y_cfg && !empty($y_cfg['activo'])) {
                $y_ini = $y_cfg['apertura'];
                $y_fin = $y_cfg['cierre'];
                // Si el horario de ayer cerraba de madrugada y aún no pasó la hora de cierre
                if ($y_fin < $y_ini && $current_time <= $y_fin) {
                    $abierto = true;
                    $hora_ini_hoy = $y_ini;
                    $hora_fin_hoy = $y_fin;
                }
            }
        }

        $is_override_cerrado = !empty($cfg['is_local_cerrado']);
        if ($is_override_cerrado) {
            $abierto = false;
            $motivo_cierre = 'Pausado por emergencia / feriado';
        } elseif (!$abierto) {
            if (!$today_cfg || empty($today_cfg['activo'])) {
                $motivo_cierre = 'Día ' . $dia_nombre . ' cerrado según horario habitual';
            } else {
                $motivo_cierre = 'Fuera del horario de atención (' . $hora_ini_hoy . ' a ' . $hora_fin_hoy . ')';
            }
        }

        $delivery_cerrado = !empty($cfg['is_delivery_cerrado']);
        $local_cerrado = $is_override_cerrado || !$abierto;

        // Parse promos semana
        $promos_semana = [];
        if (!empty($cfg['promos_semana'])) {
            $decoded = json_decode($cfg['promos_semana'], true);
            if (is_array($decoded)) {
                $promos_semana = $decoded;
            }
        }
        $dia_key_lower = strtolower($current_weekday);
        $promo_del_dia = $promos_semana[$dia_key_lower] ?? ($promos_semana[$current_weekday] ?? '');

        // Numeros de prueba array
        $numeros_prueba = [];
        if (!empty($cfg['numeros_prueba'])) {
            $numeros_prueba = array_values(array_filter(array_map('trim', explode(',', $cfg['numeros_prueba']))));
        }

        $mensaje_cerrado = $cfg['mensaje_cerrado'];
        if (empty($mensaje_cerrado)) {
            $mensaje_cerrado = "🤖 Hola, en este momento " . ($location['cname'] ?? 'el local') . " se encuentra cerrado ⛔\n"
                . "Horario de atención: " . ($cfg['horario_atencion'] ?: ($hora_ini_hoy . ' a ' . $hora_fin_hoy)) . ".\n"
                . "¡Escribinos en nuestro horario habitual y con gusto te tomamos el pedido!";
        }

        // Rubro y Tipo de Flujo GeeksIA
        $tipo_flujo_key = !empty($cfg['tipo_flujo']) ? $cfg['tipo_flujo'] : ($location['tipo_flujo'] ?? 'restaurante');
        $this->load->model('workflow_templates_model');
        $rubro_info = $this->workflow_templates_model->get_template($tipo_flujo_key);

        return [
            'status'                  => true,
            'location_id'             => $loc_id,
            'tenant_name'             => $location['cname'] ?? 'Local ' . $loc_id,
            'whatsapp_instance'       => $location['whatsapp_instance'] ?? '',
            'tipo_flujo'              => $tipo_flujo_key,
            'rubro'                   => [
                'key'         => $rubro_info['key'],
                'nombre'      => $rubro_info['name'],
                'badge'       => $rubro_info['badge'],
                'descripcion' => $rubro_info['short_desc'],
                'features'    => $rubro_info['features']
            ],

            // 1. Números Operativos
            'phone_caja'              => $cfg['phone_caja'],
            'phone_cocina'            => $cfg['phone_cocina'],
            'phone_gerente'           => $cfg['phone_gerente'],
            'phone_encargado'         => $cfg['phone_encargado'],
            'phone_admins'            => $admins,
            'gerentes'                => $gerentes,
            'delivery_group'          => $cfg['delivery_group'],
            'staff_silent'            => $staff_silent,
            'repartidores'            => $active_repartidores,
            'last_assigned_slot'      => (int)$cfg['last_assigned_slot'],
            'deliveries'              => $deliveries,
            'deliveries_count_active' => count($active_repartidores),

            // 2. Horarios y Estado en Vivo
            'abierto'                 => $abierto,
            'local_cerrado'           => $local_cerrado,
            'delivery_cerrado'        => $delivery_cerrado,
            'motivo_cierre'           => $motivo_cierre,
            'dia_nombre'              => $dia_nombre,
            'dia_codigo'              => $current_weekday,
            'hora_actual'             => $current_time,
            'horarios_semana'         => $horarios_semana,
            'horario_hoy'             => [
                'dia'           => $dia_nombre,
                'codigo'        => $current_weekday,
                'activo'        => !empty($today_cfg['activo']),
                'apertura'      => $hora_ini_hoy,
                'cierre'        => $hora_fin_hoy,
                'abierto_ahora' => $abierto
            ],
            'horario_atencion'        => $cfg['horario_atencion'] ?: ($hora_ini_hoy . ' a ' . $hora_fin_hoy),
            'hora_apertura'           => $hora_ini_hoy,
            'hora_cierre'             => $hora_fin_hoy,
            'dias_atencion'           => $cfg['dias_atencion'],
            'mensaje_cerrado'         => $mensaje_cerrado,

            // 3. Tarifas, Radio y Tiempos de Entrega
            'radio_km'                => (float)($cfg['radio_max_km'] ?: 10.0),
            'costo_envio_base'        => (int)($cfg['costo_envio_base'] ?: 10000),
            'costo_por_km'            => (int)($cfg['costo_por_km'] ?: 2000),
            'tiempo_entrega'          => !empty($cfg['tiempo_entrega_min']) ? $cfg['tiempo_entrega_min'] : '35 a 45 minutos',
            'tiempo_retiro'           => !empty($cfg['tiempo_retiro_min']) ? $cfg['tiempo_retiro_min'] : '15 a 20 minutos',

            // 4. Datos Bancarios para Transferencias
            'banco' => [
                'nombre'  => $cfg['banco_nombre'] ?? '',
                'titular' => $cfg['banco_titular'] ?? '',
                'ruc_ci'  => $cfg['banco_ruc_ci'] ?? '',
                'cuenta'  => $cfg['banco_cuenta'] ?? '',
                'alias'   => $cfg['banco_alias'] ?? ''
            ],

            // 5. Ubicación & Google Maps
            'ubicacion' => [
                'direccion'         => !empty($cfg['maps_direccion']) ? $cfg['maps_direccion'] : ($location['address'] ?? ''),
                'maps_link'         => $cfg['maps_link'] ?? '',
                'maps_resenas_link' => $cfg['maps_resenas_link'] ?? ''
            ],

            // 6. Bot IA & Configuración del Proveedor
            'bot_modo'                => !empty($cfg['bot_modo']) ? $cfg['bot_modo'] : 'bot',
            'numeros_prueba'          => $numeros_prueba,
            'ia' => [
                'proveedor'           => !empty($cfg['ia_proveedor']) ? $cfg['ia_proveedor'] : 'gemini',
                'api_key'             => $ia_key,
                'api_key_configurada' => $ia_key_configurada,
                'modelo'              => !empty($cfg['ia_modelo']) ? $cfg['ia_modelo'] : 'gemini-1.5-flash',
                'temperatura'         => (float)($cfg['ia_temperatura'] ?? 0.70),
                'base_url'            => $cfg['ia_base_url'] ?? '',
                'max_tokens'          => (int)($cfg['ia_max_tokens'] ?? 1000),
                'tono'                => !empty($cfg['ia_tono']) ? $cfg['ia_tono'] : 'Cálido, amigable, paraguayo informal con emojis moderados',
                'instrucciones_extra' => $cfg['ia_instrucciones_extra'] ?? ''
            ],
            // Compatibilidad directa en raíz
            'ia_proveedor'            => !empty($cfg['ia_proveedor']) ? $cfg['ia_proveedor'] : 'gemini',
            'ia_api_key'              => $ia_key,
            'ia_modelo'               => !empty($cfg['ia_modelo']) ? $cfg['ia_modelo'] : 'gemini-1.5-flash',
            'ia_temperatura'          => (float)($cfg['ia_temperatura'] ?? 0.70),
            'ia_base_url'             => $cfg['ia_base_url'] ?? '',
            'ia_max_tokens'           => (int)($cfg['ia_max_tokens'] ?? 1000),
            'ia_tono'                 => !empty($cfg['ia_tono']) ? $cfg['ia_tono'] : 'Cálido, amigable, paraguayo informal con emojis moderados',
            'ia_instrucciones_extra'  => $cfg['ia_instrucciones_extra'] ?? '',
            'promo_del_dia'           => $promo_del_dia,
            'promos_semana'           => $promos_semana,

            'updated_at'              => $cfg['updated_at'] ?? date('Y-m-d H:i:s')
        ];
    }

    // =========================================================================
    // MÉTODOS DE LA APP MÓVIL FLUTTER & SEGUIMIENTO GPS
    // =========================================================================

    /**
     * Autenticación del repartidor con Teléfono + PIN de 4 dígitos
     */
    const LOGIN_MAX_FAILS_PHONE = 5;   // intentos fallidos por teléfono...
    const LOGIN_MAX_FAILS_IP    = 20;  // ...y por IP
    const LOGIN_WINDOW_MIN      = 15;  // dentro de esta ventana (minutos)
    const DRIVER_TOKEN_DAYS     = 30;

    public function driver_login($telefono, $pin, $ip = '')
    {
        $raw_phone = $this->clean_phone($telefono);
        $clean_pin = preg_replace('/[^0-9]/', '', (string)$pin);
        $ip = substr((string)$ip, 0, 45);

        // Se exige el teléfono completo (mínimo 8 dígitos) y el PIN: ya no se acepta login solo por PIN o por número de slot
        if (strlen($raw_phone) < 8 || strlen($clean_pin) < 4) {
            return ['status' => false, 'message' => 'Ingresá tu número de teléfono completo y tu PIN'];
        }
        $tail8 = substr($raw_phone, -8);

        if ($this->login_blocked($tail8, $ip)) {
            return ['status' => false, 'message' => 'Demasiados intentos fallidos. Esperá ' . self::LOGIN_WINDOW_MIN . ' minutos o pedí un PIN nuevo al encargado.'];
        }

        // Candidatos por los últimos 8 dígitos (acepta 0981..., 981... o 595981...); comparación final en PHP
        $candidates = $this->db->like('telefono', $tail8, 'before')
                               ->where('telefono !=', '')
                               ->get('geopos_deliveries')
                               ->result_array();
        $driver = null;
        $matches = 0;
        foreach ($candidates as $c) {
            if (substr($this->clean_phone($c['telefono']), -8) === $tail8 && hash_equals((string)$c['pin'], $clean_pin)) {
                $driver = $c;
                $matches++;
            }
        }

        if ($matches !== 1) {
            $this->db->insert('geopos_delivery_login_attempts', ['ip' => $ip, 'phone_tail' => $tail8, 'success' => 0, 'created_at' => date('Y-m-d H:i:s')]);
            return ['status' => false, 'message' => $matches > 1
                ? 'Tu teléfono está registrado en más de un local con el mismo PIN. Pedí al encargado un PIN nuevo.'
                : 'PIN o Teléfono incorrecto. Verifica el PIN en el panel web'];
        }

        $this->db->insert('geopos_delivery_login_attempts', ['ip' => $ip, 'phone_tail' => $tail8, 'success' => 1, 'created_at' => date('Y-m-d H:i:s')]);

        // Al iniciar sesión exitosamente, activar el repartidor automáticamente.
        // Solo se guarda el hash del token, con vencimiento.
        $token = bin2hex(random_bytes(32));
        $this->db->where('id', $driver['id'])->update('geopos_deliveries', [
            'auth_token'         => hash('sha256', $token),
            'auth_token_expires' => date('Y-m-d H:i:s', strtotime('+' . self::DRIVER_TOKEN_DAYS . ' days')),
            'activo'             => 1
        ]);

        $location = $this->db->where('id', $driver['loc_id'])->get('geopos_locations')->row_array();

        return [
            'status' => true,
            'token'  => $token,
            'driver' => [
                'id'          => (int)$driver['id'],
                'slot'        => (int)$driver['slot'],
                'nombre'      => $driver['nombre'],
                'telefono'    => $driver['telefono'],
                'ocupado'     => (int)$driver['ocupado'],
                'pedidos_hoy' => (int)$driver['pedidos_hoy']
            ],
            'tenant' => [
                'id'                => (int)$driver['loc_id'],
                'name'              => $location['cname'] ?? 'Local ' . $driver['loc_id'],
                'address'           => $location['address'] ?? '',
                'whatsapp_instance' => $location['whatsapp_instance'] ?? 'pos_whatsapp'
            ]
        ];
    }

    /**
     * Validar token de sesión del repartidor
     */
    public function driver_get_by_token($token)
    {
        if (empty($token)) return false;
        $clean_tok = trim(preg_replace('/^Bearer\s+/i', '', (string)$token));
        if (!preg_match('/^[a-f0-9]{64}$/', $clean_tok)) return false;
        $driver = $this->db->where('auth_token', hash('sha256', $clean_tok))
                           ->where('activo', 1)
                           ->where('auth_token_expires >', date('Y-m-d H:i:s'))
                           ->get('geopos_deliveries')
                           ->row_array();
        return $driver ?: false;
    }

    /**
     * Bloqueo por fuerza bruta: demasiados fallos recientes para ese teléfono o esa IP
     */
    protected function login_blocked($phone_tail, $ip)
    {
        $since = date('Y-m-d H:i:s', strtotime('-' . self::LOGIN_WINDOW_MIN . ' minutes'));
        $by_phone = $this->db->where('phone_tail', $phone_tail)->where('success', 0)->where('created_at >=', $since)
                             ->count_all_results('geopos_delivery_login_attempts');
        if ($by_phone >= self::LOGIN_MAX_FAILS_PHONE) {
            return true;
        }
        if ($ip !== '') {
            $by_ip = $this->db->where('ip', $ip)->where('success', 0)->where('created_at >=', $since)
                              ->count_all_results('geopos_delivery_login_attempts');
            if ($by_ip >= self::LOGIN_MAX_FAILS_IP) {
                return true;
            }
        }
        return false;
    }

    /**
     * Actualizar coordenadas GPS en tiempo real
     */
    public function driver_update_gps($slot, $loc_id, $lat, $lng)
    {
        $lat = (float)$lat;
        $lng = (float)$lng;
        $now = date('Y-m-d H:i:s');

        $this->db->where('loc_id', (int)$loc_id)->where('slot', (int)$slot)->update('geopos_deliveries', [
            'lat'         => $lat,
            'lng'         => $lng,
            'last_gps_at' => $now
        ]);

        return true;
    }

    /**
     * Alternar estado Disponible (0) vs En Pausa (1)
     */
    public function driver_toggle_availability($slot, $loc_id, $ocupado)
    {
        $ocupado = (int)$ocupado ? 1 : 0;
        $this->db->where('loc_id', (int)$loc_id)->where('slot', (int)$slot)->update('geopos_deliveries', [
            'ocupado' => $ocupado
        ]);
        return ['status' => true, 'ocupado' => $ocupado];
    }

    /**
     * Obtener pedidos asignados al repartidor
     */
    public function driver_get_orders($slot, $loc_id, $history = false)
    {
        $slot = (int)$slot;
        $loc_id = (int)$loc_id;

        $this->db->where('loc_id', $loc_id);
        $this->db->where('delivery_slot', $slot);

        if ($history) {
            $this->db->where('estado', 'entregado');
            $this->db->where('DATE(delivered_at) = CURDATE()');
            $this->db->order_by('delivered_at', 'DESC');
        } else {
            $this->db->where_in('estado', ['asignado', 'en_camino', 'en_puerta']);
            $this->db->order_by('id', 'DESC');
        }

        $rows = $this->db->get('geopos_delivery_orders')->result_array();
        $orders = [];

        foreach ($rows as $r) {
            $items = [];
            if (!empty($r['items_json'])) {
                $decoded = json_decode($r['items_json'], true);
                if (is_array($decoded)) {
                    $items = $decoded;
                }
            }

            $orders[] = [
                'id'                => (int)$r['id'],
                'order_number'      => $r['order_number'],
                'estado'            => $r['estado'],
                'cliente_nombre'    => $r['cliente_nombre'],
                'cliente_telefono'  => $r['cliente_telefono'],
                'direccion_entrega' => $r['direccion_entrega'],
                'maps_link'         => $r['maps_link'],
                'lat_destino'       => $r['lat_destino'] ? (float)$r['lat_destino'] : null,
                'lng_destino'       => $r['lng_destino'] ? (float)$r['lng_destino'] : null,
                'metodo_pago'       => $r['metodo_pago'],
                'monto_total'       => (float)$r['monto_total'],
                'paga_con'          => $r['paga_con'] ? (float)$r['paga_con'] : null,
                'vuelto'            => $r['vuelto'] ? (float)$r['vuelto'] : null,
                'comprobante_url'   => $r['comprobante_url'],
                'notas_cocina'      => $r['notas_cocina'],
                'items'             => $items,
                'tracking_hash'     => $r['tracking_hash'],
                'tracking_url'      => base_url('tracking/' . $r['tracking_hash']),
                'assigned_at'       => $r['assigned_at'],
                'picked_at'         => $r['picked_at'],
                'at_door_at'        => $r['at_door_at'],
                'delivered_at'      => $r['delivered_at']
            ];
        }

        return $orders;
    }

    /**
     * Actualizar estado del pedido (en_camino, en_puerta, entregado) y disparar WhatsApp
     */
    public function driver_update_order_status($driver, $order_id, $new_status)
    {
        $order_id = (int)$order_id;
        $allowed = ['asignado', 'en_camino', 'en_puerta', 'entregado', 'cancelado'];

        if (!in_array($new_status, $allowed)) {
            return ['status' => false, 'message' => 'Estado no válido'];
        }

        $order = $this->db->where('id', $order_id)
                          ->where('loc_id', (int)$driver['loc_id'])
                          ->where('delivery_slot', (int)$driver['slot'])
                          ->get('geopos_delivery_orders')
                          ->row_array();

        if (!$order) {
            return ['status' => false, 'message' => 'Pedido no encontrado o no asignado a su turno'];
        }

        $now = date('Y-m-d H:i:s');
        $upd = ['estado' => $new_status];

        if ($new_status === 'en_camino' && empty($order['picked_at'])) {
            $upd['picked_at'] = $now;
        } elseif ($new_status === 'en_puerta' && empty($order['at_door_at'])) {
            $upd['at_door_at'] = $now;
        } elseif ($new_status === 'entregado' && empty($order['delivered_at'])) {
            $upd['delivered_at'] = $now;
        }

        $this->db->where('id', $order_id)->update('geopos_delivery_orders', $upd);

        // Si se entregó, verificar si quedan más pedidos en curso; si no, liberar disponibilidad
        if ($new_status === 'entregado') {
            $active_pending = $this->db->where('loc_id', (int)$driver['loc_id'])
                                       ->where('delivery_slot', (int)$driver['slot'])
                                       ->where_in('estado', ['asignado', 'en_camino', 'en_puerta'])
                                       ->count_all_results('geopos_delivery_orders');
            if ($active_pending === 0) {
                $this->db->where('id', $driver['id'])->update('geopos_deliveries', ['ocupado' => 0]);
            }
        }

        // Obtener datos del local para el mensaje de WhatsApp
        $loc = $this->db->where('id', $driver['loc_id'])->get('geopos_locations')->row_array();
        $instance = $loc['whatsapp_instance'] ?? 'pos_whatsapp';
        $store_name = $loc['cname'] ?? 'el local';
        $tracking_url = base_url('tracking/' . $order['tracking_hash']);

        // Notificaciones automáticas por WhatsApp al cliente
        if (!empty($order['cliente_telefono'])) {
            if ($new_status === 'en_camino') {
                $msg = "🛵 *¡Tu pedido va en camino!*\n\n"
                     . "Hola " . $order['cliente_nombre'] . ", tu pedido *" . $order['order_number'] . "* de *" . $store_name . "* ya salió con nuestro repartidor *" . $driver['nombre'] . "*.\n\n"
                     . "📍 *Sigue su recorrido en vivo aquí:*\n" . $tracking_url;
                $this->send_whatsapp_message($instance, $order['cliente_telefono'], $msg);
            } elseif ($new_status === 'en_puerta') {
                $msg = "📍 *¡Tu delivery está en la puerta!*\n\n"
                     . "Hola " . $order['cliente_nombre'] . ", tu repartidor *" . $driver['nombre'] . "* ya llegó a tu domicilio con tu pedido *" . $order['order_number'] . "* 🛎️";
                $this->send_whatsapp_message($instance, $order['cliente_telefono'], $msg);
            } elseif ($new_status === 'entregado') {
                $msg = "✔️ *¡Pedido Entregado con Éxito!*\n\n"
                     . "¡Muchas gracias por elegir *" . $store_name . "*! Esperamos que disfrutes tu pedido *" . $order['order_number'] . "*. ¡Hasta la próxima! ⭐";
                $this->send_whatsapp_message($instance, $order['cliente_telefono'], $msg);
            }
        }

        return [
            'status'     => true,
            'order_id'   => $order_id,
            'new_status' => $new_status,
            'message'    => "Estado del pedido actualizado a '{$new_status}'"
        ];
    }

    /**
     * Resumen de caja y cobranzas del día para rendición
     */
    public function driver_get_cash_summary($slot, $loc_id)
    {
        $slot = (int)$slot;
        $loc_id = (int)$loc_id;

        $q = $this->db->query("
            SELECT 
                COUNT(*) as total_pedidos,
                COALESCE(SUM(monto_total), 0) as total_cobrado,
                COALESCE(SUM(CASE WHEN metodo_pago = 'efectivo' THEN monto_total ELSE 0 END), 0) as total_efectivo,
                COALESCE(SUM(CASE WHEN metodo_pago = 'sipap' THEN monto_total ELSE 0 END), 0) as total_sipap,
                COALESCE(SUM(CASE WHEN metodo_pago = 'tarjeta' THEN monto_total ELSE 0 END), 0) as total_tarjeta
            FROM geopos_delivery_orders
            WHERE loc_id = ?
              AND delivery_slot = ?
              AND estado = 'entregado'
              AND DATE(delivered_at) = CURDATE()
        ", [$loc_id, $slot])->row_array();

        return [
            'total_pedidos'  => (int)($q['total_pedidos'] ?? 0),
            'total_cobrado'  => (float)($q['total_cobrado'] ?? 0),
            'total_efectivo' => (float)($q['total_efectivo'] ?? 0),
            'total_sipap'    => (float)($q['total_sipap'] ?? 0),
            'total_tarjeta'  => (float)($q['total_tarjeta'] ?? 0),
            'fecha'          => date('d/m/Y')
        ];
    }

    /**
     * Datos para el portal web de seguimiento GPS en vivo del cliente (/tracking/{hash})
     */
    public function get_tracking_data_by_hash($hash)
    {
        if (empty($hash)) return false;

        $hash = strtolower(trim((string)$hash));
        if (!preg_match('/^[a-f0-9]{32,64}$/', $hash)) return false;

        $order = $this->db->where('tracking_hash', $hash)->get('geopos_delivery_orders')->row_array();
        if (!$order) return false;

        // El enlace vence 3 horas después de entregado o cancelado
        $fin = $order['delivered_at'] ?: ($order['estado'] === 'cancelado' ? $order['updated_at'] : null);
        if ($fin && strtotime($fin) < strtotime('-3 hours')) return false;

        // La ubicación del repartidor solo se comparte mientras va en camino con este pedido
        $compartir_gps = in_array($order['estado'], ['en_camino', 'en_puerta'], true);

        $driver = $this->db->where('loc_id', $order['loc_id'])
                           ->where('slot', $order['delivery_slot'])
                           ->get('geopos_deliveries')
                           ->row_array();

        $location = $this->db->where('id', $order['loc_id'])->get('geopos_locations')->row_array();
        $wa_cfg = $this->db->where('loc_id', $order['loc_id'])->get('geopos_whatsapp_config')->row_array();

        $items = [];
        if (!empty($order['items_json'])) {
            $decoded = json_decode($order['items_json'], true);
            if (is_array($decoded)) $items = $decoded;
        }

        return [
            'status'           => true,
            'order'            => [
                'id'                => (int)$order['id'],
                'order_number'      => $order['order_number'],
                'estado'            => $order['estado'],
                'cliente_nombre'    => $order['cliente_nombre'],
                'direccion_entrega' => $order['direccion_entrega'],
                'lat_destino'       => $order['lat_destino'] ? (float)$order['lat_destino'] : null,
                'lng_destino'       => $order['lng_destino'] ? (float)$order['lng_destino'] : null,
                'metodo_pago'       => $order['metodo_pago'],
                'monto_total'       => (float)$order['monto_total'],
                'items'             => $items,
                'assigned_at'       => $order['assigned_at'],
                'picked_at'         => $order['picked_at'],
                'at_door_at'        => $order['at_door_at'],
                'delivered_at'      => $order['delivered_at']
            ],
            'delivery'         => [
                'nombre'           => $driver['nombre'] ?? 'Repartidor',
                // No se expone el teléfono personal del repartidor: se muestra el del local
                'telefono'         => $wa_cfg['phone_caja'] ?? ($location['phone'] ?? ''),
                'lat'              => ($compartir_gps && !empty($driver['lat'])) ? (float)$driver['lat'] : null,
                'lng'              => ($compartir_gps && !empty($driver['lng'])) ? (float)$driver['lng'] : null,
                'last_gps_at'      => $compartir_gps ? ($driver['last_gps_at'] ?? null) : null
            ],
            'local'            => [
                'nombre'           => $location['cname'] ?? 'Local',
                'direccion'        => $location['address'] ?? '',
                'phone_caja'       => $wa_cfg['phone_caja'] ?? ($location['phone'] ?? '')
            ]
        ];
    }

    /**
     * Enviar mensaje de WhatsApp usando Evolution API interna
     */
    public function send_whatsapp_message($instance, $number, $text)
    {
        $clean = $this->clean_phone($number);
        if (empty($clean) || empty($text)) return false;

        $instance_name = $instance ?: 'pos_whatsapp';
        $this->config->load('geeksia', true, true);
        $url = $this->config->item('evolution_api_url', 'geeksia') . '/message/sendText/' . rawurlencode($instance_name);

        $payload = json_encode([
            'number'       => $clean,
            'options'      => ['delay' => 1000, 'presence' => 'composing', 'linkPreview' => true],
            'textMessage'  => ['text' => $text]
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'apikey: ' . $this->config->item('evolution_api_key', 'geeksia')
        ]);

        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}
