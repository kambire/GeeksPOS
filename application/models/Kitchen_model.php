<?php
/**
 * GeeksPOS - Kitchen Display System (KDS) Model
 * Handles kitchen orders, preparation lifecycle, items and status transitions.
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Kitchen_model extends CI_Model
{
    protected $table = 'geopos_kitchen_orders';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get orders for the kitchen screen
     * @param int $loc_id Location/branch id (0 = all or user loc)
     * @param string $filter 'active', 'ready', 'all', 'history'
     * @return array
     */
    public function get_orders($loc_id = 0, $filter = 'active')
    {
        $this->db->select("k.*, 
            TIMESTAMPDIFF(MINUTE, k.created_at, NOW()) AS elapsed_minutes,
            TIMESTAMPDIFF(SECOND, k.created_at, NOW()) AS elapsed_seconds", FALSE);
        $this->db->from($this->table . ' k');

        if ($loc_id > 0) {
            $this->db->where('k.loc_id', $loc_id);
        }

        switch ($filter) {
            case 'pending':
                $this->db->where('k.status', 'pendiente');
                break;
            case 'cooking':
                $this->db->where('k.status', 'en_preparacion');
                break;
            case 'ready':
                $this->db->where('k.status', 'listo');
                break;
            case 'history':
                $this->db->where('k.status', 'entregado');
                $this->db->where('DATE(k.created_at)', date('Y-m-d'));
                break;
            case 'all':
                $this->db->where_in('k.status', array('pendiente', 'en_preparacion', 'listo'));
                break;
            case 'active':
            default:
                $this->db->where_in('k.status', array('pendiente', 'en_preparacion'));
                break;
        }

        // FIFO: oldest active orders first so kitchen serves in order of arrival
        if ($filter == 'history') {
            $this->db->order_by('k.completed_at', 'DESC');
            $this->db->limit(50);
        } else {
            $this->db->order_by("CASE WHEN k.status = 'en_preparacion' THEN 1 WHEN k.status = 'pendiente' THEN 2 ELSE 3 END", 'ASC', FALSE);
            $this->db->order_by('k.created_at', 'ASC');
        }

        $query = $this->db->get();
        $orders = $query->result_array();

        foreach ($orders as &$order) {
            // Parse items json
            $items = json_decode($order['items_json'], true);
            if (!is_array($items)) {
                $items = array();
            }
            $order['items'] = $items;

            // Compute elapsed time text and urgency level
            $min = (int)$order['elapsed_minutes'];
            $sec = (int)$order['elapsed_seconds'] % 60;
            
            if ($min < 1) {
                $order['elapsed_text'] = 'Hace instantes';
            } elseif ($min < 60) {
                $order['elapsed_text'] = $min . ' min';
            } else {
                $hours = floor($min / 60);
                $rem_min = $min % 60;
                $order['elapsed_text'] = $hours . 'h ' . $rem_min . 'm';
            }

            // Urgency: normal (<20m), warning (20-35m), critical (>=35m)
            if ($min >= 35) {
                $order['urgency'] = 'critical';
                $order['urgency_badge'] = 'danger';
            } elseif ($min >= 20) {
                $order['urgency'] = 'warning';
                $order['urgency_badge'] = 'warning';
            } else {
                $order['urgency'] = 'normal';
                $order['urgency_badge'] = 'success';
            }

            // Human friendly order type label
            switch ($order['order_type']) {
                case 'delivery':
                    $slot_txt = !empty($order['delivery_slot']) ? ' • Slot ' . $order['delivery_slot'] : '';
                    $order['type_label'] = 'Delivery' . $slot_txt;
                    $order['type_icon']  = 'fa-motorcycle';
                    $order['type_class'] = 'badge-delivery';
                    break;
                case 'takeaway':
                    $order['type_label'] = 'Para Retirar';
                    $order['type_icon']  = 'fa-shopping-bag';
                    $order['type_class'] = 'badge-takeaway';
                    break;
                case 'mesa':
                    $tbl = !empty($order['table_number']) ? 'Mesa ' . $order['table_number'] . ' • ' : '';
                    $order['type_label'] = $tbl . 'Consumir en el Local';
                    $order['type_icon']  = 'fa-cutlery';
                    $order['type_class'] = 'badge-mesa';
                    break;
                case 'local':
                default:
                    $order['type_label'] = 'Consumir en el Local';
                    $order['type_icon']  = 'fa-cutlery';
                    $order['type_class'] = 'badge-local';
                    break;
            }
        }

        return $orders;
    }

    /**
     * Update kitchen order status
     */
    public function update_status($id, $new_status)
    {
        $valid_statuses = array('pendiente', 'en_preparacion', 'listo', 'entregado', 'cancelado');
        if (!in_array($new_status, $valid_statuses)) {
            return false;
        }

        $order = $this->get_order($id);
        if (!$order) {
            return false;
        }

        $data = array('status' => $new_status);
        $now = date('Y-m-d H:i:s');

        if ($new_status == 'en_preparacion' && empty($order['started_at'])) {
            $data['started_at'] = $now;
        } elseif ($new_status == 'listo' && empty($order['ready_at'])) {
            $data['ready_at'] = $now;
        } elseif ($new_status == 'entregado' && empty($order['completed_at'])) {
            $data['completed_at'] = $now;
        }

        $this->db->where('id', $id);
        $res = $this->db->update($this->table, $data);

        // If this order is linked to a delivery order, notify/sync delivery state if ready or dispatched
        if ($res && !empty($order['delivery_order_id'])) {
            if ($new_status == 'listo') {
                $this->db->where('id', $order['delivery_order_id']);
                $this->db->where('estado', 'asignado');
                $this->db->update('geopos_delivery_orders', array('estado' => 'en_camino', 'picked_at' => $now));
            } elseif ($new_status == 'entregado') {
                // Si cocina marca entregado en KDS, para delivery significa que cocina ya lo despachó
                $this->db->where('id', $order['delivery_order_id']);
                $this->db->where_in('estado', array('asignado', 'pendiente'));
                $this->db->update('geopos_delivery_orders', array('estado' => 'en_camino', 'picked_at' => $now));
            }
        }

        return $res;
    }

    /**
     * Get single order by ID
     */
    public function get_order($id)
    {
        $this->db->where('id', $id);
        $q = $this->db->get($this->table);
        return $q->row_array();
    }

    /**
     * Live stats summary for kitchen header & dashboard
     */
    public function get_stats($loc_id = 0)
    {
        $stats = array(
            'pendientes'       => 0,
            'en_preparacion'   => 0,
            'listos'           => 0,
            'total_activos'    => 0,
            'entregados_hoy'   => 0,
            'avg_prep_minutes' => 0,
            'latest_order_id'  => 0,
        );

        $this->db->select("
            COUNT(CASE WHEN status = 'pendiente' THEN 1 END) AS pendientes,
            COUNT(CASE WHEN status = 'en_preparacion' THEN 1 END) AS en_preparacion,
            COUNT(CASE WHEN status = 'listo' THEN 1 END) AS listos,
            COUNT(CASE WHEN status IN ('pendiente', 'en_preparacion') THEN 1 END) AS total_activos,
            COUNT(CASE WHEN status = 'entregado' AND DATE(created_at) = CURDATE() THEN 1 END) AS entregados_hoy,
            ROUND(AVG(CASE WHEN status = 'entregado' AND started_at IS NOT NULL AND completed_at IS NOT NULL AND DATE(created_at) = CURDATE() THEN TIMESTAMPDIFF(MINUTE, started_at, completed_at) END), 0) AS avg_prep_minutes,
            MAX(id) AS latest_order_id
        ", FALSE);
        $this->db->from($this->table);
        if ($loc_id > 0) {
            $this->db->where('loc_id', $loc_id);
        }

        $q = $this->db->get();
        if ($row = $q->row_array()) {
            $stats['pendientes']       = (int)$row['pendientes'];
            $stats['en_preparacion']   = (int)$row['en_preparacion'];
            $stats['listos']           = (int)$row['listos'];
            $stats['total_activos']    = (int)$row['total_activos'];
            $stats['entregados_hoy']   = (int)$row['entregados_hoy'];
            $stats['avg_prep_minutes'] = (int)($row['avg_prep_minutes'] ?: 12);
            $stats['latest_order_id']  = (int)($row['latest_order_id'] ?: 0);
        }

        return $stats;
    }

    /**
     * Sync POS invoice to kitchen order
     */
    public function sync_invoice_to_kitchen($invoice_id)
    {
        // Check if already in kitchen
        $this->db->where('invoice_id', $invoice_id);
        $exist = $this->db->get($this->table)->row();
        if ($exist) {
            return $exist->id;
        }

        // Get invoice details
        $this->db->where('id', $invoice_id);
        $inv = $this->db->get('geopos_invoices')->row_array();
        if (!$inv) {
            return false;
        }

        // Get invoice items
        $this->db->where('tid', $invoice_id);
        $items_raw = $this->db->get('geopos_invoice_items')->result_array();
        $items = array();

        foreach ($items_raw as $it) {
            $items[] = array(
                'name'  => $it['product'],
                'qty'   => (float)$it['qty'],
                'notes' => $it['product_des'] ?: ''
            );
        }

        // Determine order type
        $raw_type = strtolower($inv['order_type'] ?: '');
        if (strpos($raw_type, 'delivery') !== false) {
            $type = 'delivery';
        } elseif (strpos($raw_type, 'pasa') !== false || strpos($raw_type, 'retir') !== false || strpos($raw_type, 'llevar') !== false) {
            $type = 'takeaway';
        } elseif (strpos($raw_type, 'mesa') !== false) {
            $type = 'mesa';
        } else {
            $type = 'local';
        }

        $customer_name = !empty($inv['order_name']) ? $inv['order_name'] : 'Ticket #' . $inv['tid'];

        $kitchen_data = array(
            'order_number'  => '#' . $inv['tid'],
            'invoice_id'    => $invoice_id,
            'loc_id'        => (int)($inv['loc'] ?: 1),
            'order_type'    => $type,
            'customer_name' => $customer_name,
            'notes'         => $inv['notes'] ?: '',
            'items_json'    => json_encode($items, JSON_UNESCAPED_UNICODE),
            'status'        => 'pendiente',
            'created_at'    => date('Y-m-d H:i:s')
        );

        $this->db->insert($this->table, $kitchen_data);
        return $this->db->insert_id();
    }

    /**
     * Sync Delivery order to kitchen
     */
    public function sync_delivery_to_kitchen($delivery_order_id)
    {
        $this->db->where('delivery_order_id', $delivery_order_id);
        $exist = $this->db->get($this->table)->row();
        if ($exist) {
            return $exist->id;
        }

        $this->db->where('id', $delivery_order_id);
        $d = $this->db->get('geopos_delivery_orders')->row_array();
        if (!$d) {
            return false;
        }

        $kitchen_data = array(
            'order_number'      => $d['order_number'],
            'delivery_order_id' => $delivery_order_id,
            'invoice_id'        => $d['invoice_id'],
            'loc_id'            => (int)($d['loc_id'] ?: 1),
            'order_type'        => 'delivery',
            'customer_name'     => $d['cliente_nombre'],
            'customer_phone'    => $d['cliente_telefono'],
            'delivery_slot'     => $d['delivery_slot'],
            'delivery_name'     => $d['delivery_nombre'],
            'notes'             => $d['notas_cocina'],
            'items_json'        => $d['items_json'] ?: '[]',
            'status'            => 'pendiente',
            'created_at'        => $d['created_at'] ?: date('Y-m-d H:i:s')
        );

        $this->db->insert($this->table, $kitchen_data);
        return $this->db->insert_id();
    }
}
