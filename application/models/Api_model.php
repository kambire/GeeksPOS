<?php
/**
 * GeeksPOS - REST API Model for Multi-System Integration
 * Supporting n8n, WhatsApp Bots, Ecommerce, Delivery Apps & External POS
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Api_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Aauth');
        $this->load->library('Custom');
        $this->load->model('billing_model', 'billing');
        $this->config->load('api_config', false, true);
    }

    /**
     * Resolves the configured public base URL or falls back to CodeIgniter's base_url()
     */
    public function get_base_url()
    {
        $custom = $this->config->item('api_public_base_url');
        if (!empty($custom)) {
            return rtrim($custom, '/') . '/';
        }
        return base_url();
    }

    /**
     * List products with search, category filter, and pagination
     */
    public function get_products($id = null, $search = null, $category_id = null, $code = null, $limit = 50, $offset = 0)
    {
        $this->db->select('p.pid AS id, p.product_name AS name, p.product_code AS code, p.product_price AS price, p.fproduct_price, p.qty AS stock, p.unit, p.alert, p.image, p.pcat AS category_id, c.title AS category_name, p.product_des AS description');
        $this->db->from('geopos_products p');
        $this->db->join('geopos_product_cat c', 'c.id = p.pcat', 'left');

        if ($id !== null && $id !== '') {
            $this->db->where('p.pid', (int)$id);
            $query = $this->db->get();
            return $query->row_array();
        }

        if ($code !== null && $code !== '') {
            $this->db->where('p.product_code', $code);
            $this->db->or_where('p.barcode', $code);
            $query = $this->db->get();
            return $query->row_array();
        }

        if (!empty($category_id)) {
            $this->db->where('p.pcat', (int)$category_id);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.product_name', $search);
            $this->db->or_like('p.product_code', $search);
            $this->db->or_like('p.barcode', $search);
            $this->db->group_end();
        }

        $this->db->order_by('p.product_name', 'ASC');
        $this->db->limit((int)$limit, (int)$offset);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Search or get customer
     */
    public function get_customers($id = null, $phone = null, $search = null, $limit = 50, $offset = 0)
    {
        $this->db->select('id, name, phone, email, address, city, region, country, taxid, company, balance, reg_date');
        $this->db->from('geopos_customers');

        if ($id !== null && $id !== '') {
            $this->db->where('id', (int)$id);
            $query = $this->db->get();
            return $query->row_array();
        }

        if (!empty($phone)) {
            $clean_phone = preg_replace('/[^0-9]/', '', $phone);
            $this->db->group_start();
            $this->db->like('phone', $clean_phone);
            $this->db->or_like('phone', $phone);
            if (strlen($clean_phone) > 6) {
                $suffix = substr($clean_phone, -7);
                $this->db->or_like('phone', $suffix);
            }
            $this->db->group_end();
            $this->db->limit(1);
            $query = $this->db->get();
            return $query->row_array();
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('phone', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('taxid', $search);
            $this->db->group_end();
        }

        $this->db->order_by('id', 'DESC');
        $this->db->limit((int)$limit, (int)$offset);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Create or update customer
     */
    public function save_customer($data)
    {
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        $name = isset($data['name']) ? trim($data['name']) : '';
        $phone = isset($data['phone']) ? trim($data['phone']) : '';
        $email = isset($data['email']) ? trim($data['email']) : '';
        $address = isset($data['address']) ? trim($data['address']) : '';
        $city = isset($data['city']) ? trim($data['city']) : '';
        $taxid = isset($data['taxid']) ? trim($data['taxid']) : '';

        if (empty($name)) {
            $name = !empty($phone) ? "Cliente $phone" : "Cliente WhatsApp";
        }

        if ($id <= 0 && !empty($phone)) {
            $existing = $this->get_customers(null, $phone);
            if ($existing) {
                $id = (int)$existing['id'];
            }
        }

        $customer_data = [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'city' => $city,
            'taxid' => $taxid
        ];

        if ($id > 0) {
            $this->db->where('id', $id);
            $this->db->update('geopos_customers', $customer_data);
            return $id;
        } else {
            $customer_data['gid'] = 1;
            $customer_data['picture'] = 'example.png';
            $customer_data['balance'] = 0.00;
            $customer_data['loc'] = 0;
            $customer_data['reg_date'] = date('Y-m-d H:i:s');
            $this->db->insert('geopos_customers', $customer_data);
            return $this->db->insert_id();
        }
    }

    /**
     * Create an order & invoice from external systems (n8n, Ecommerce, Delivery App, etc.)
     */
    public function create_order($payload, $system = 'n8n_whatsapp')
    {
        $this->db->trans_start();

        // 0. Load System Defaults from Config
        $defaults = $this->config->item('api_defaults') ?: [];
        $systems = $this->config->item('api_systems') ?: [];
        $sys_cfg = isset($systems[$system]) ? $systems[$system] : null;

        // 1. Resolve Customer
        $customer_id = isset($payload['customer_id']) ? (int)$payload['customer_id'] : 0;
        $cust_data = isset($payload['customer']) && is_array($payload['customer']) ? $payload['customer'] : [];

        if ($customer_id <= 0 && !empty($cust_data)) {
            $customer_id = $this->save_customer($cust_data);
        } elseif ($customer_id <= 0 && !empty($payload['phone'])) {
            $customer_id = $this->save_customer([
                'name' => !empty($payload['customer_name']) ? $payload['customer_name'] : (!empty($payload['order_name']) ? $payload['order_name'] : 'Cliente WhatsApp'),
                'phone' => $payload['phone'],
                'address' => $payload['address'] ?? '',
                'taxid' => $payload['taxid'] ?? ''
            ]);
        }

        if ($customer_id <= 0) {
            $customer_id = $defaults['customer_id'] ?? 1; // Fallback to Walk-in client
        }

        $customer_info = $this->get_customers($customer_id);

        // 2. Order Metadata & Multi-System Tagging
        $order_name = isset($payload['order_name']) ? trim($payload['order_name']) : '';
        if (empty($order_name) && !empty($customer_info['name'])) {
            $order_name = $customer_info['name'];
        }

        $default_type = $sys_cfg['default_order_type'] ?? 'delivery';
        $order_type = isset($payload['order_type']) ? strtolower(trim($payload['order_type'])) : $default_type;
        $valid_types = $this->config->item('api_allowed_order_types') ?: ['delivery', 'pickup', 'dinein', 'local', 'pasa a buscar'];
        if (!in_array($order_type, $valid_types)) {
            $order_type = $default_type;
        }

        $notes = isset($payload['notes']) ? trim($payload['notes']) : '';
        $shipping = isset($payload['shipping']) ? (float)$payload['shipping'] : 0.00;
        $discount = isset($payload['discount']) ? (float)$payload['discount'] : 0.00;
        $pmethod = isset($payload['payment_method']) ? trim($payload['payment_method']) : ($defaults['payment_method'] ?? 'Cash');
        $payment_status = isset($payload['payment_status']) ? strtolower(trim($payload['payment_status'])) : ($defaults['payment_status'] ?? 'due');
        $card_txn = isset($payload['card_txn']) ? trim($payload['card_txn']) : '';
        $employee_id = isset($payload['employee_id']) ? (int)$payload['employee_id'] : ($defaults['employee_id'] ?? 11);
        $loc_id = isset($payload['location_id']) ? (int)$payload['location_id'] : ($defaults['location_id'] ?? 0);
        $system_tag = !empty($system) ? substr($system, 0, 20) : 'api';

        // 3. Sequential Invoice Number (tid)
        $this->db->select('tid');
        $this->db->from('geopos_invoices');
        $this->db->where('i_class', 1);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $last_inv = $this->db->get()->row();
        $invocieno = ($last_inv && $last_inv->tid) ? ((int)$last_inv->tid + 1) : 1;

        // 4. Process Items
        $items_raw = isset($payload['items']) && is_array($payload['items']) ? $payload['items'] : [];
        if (empty($items_raw)) {
            $this->db->trans_rollback();
            return ['status' => false, 'message' => 'Items array is required and cannot be empty'];
        }

        $subtotal = 0.00;
        $total_tax = 0.00;
        $total_items_count = 0;
        $batch_items = [];

        foreach ($items_raw as $item) {
            $pid = isset($item['product_id']) ? (int)$item['product_id'] : (isset($item['pid']) ? (int)$item['pid'] : 0);
            $code = isset($item['code']) ? trim($item['code']) : '';
            $qty = isset($item['qty']) ? (float)$item['qty'] : 1.0;
            if ($qty <= 0) $qty = 1.0;

            $p = null;
            if ($pid > 0) {
                $p = $this->get_products($pid);
            } elseif (!empty($code)) {
                $p = $this->get_products(null, null, null, $code);
            }

            if (!$p) {
                $this->db->trans_rollback();
                return ['status' => false, 'message' => "Product not found (ID: $pid, Code: $code)"];
            }

            $price = isset($item['price']) && (float)$item['price'] > 0 ? (float)$item['price'] : (float)$p['price'];
            $item_notes = isset($item['notes']) ? trim($item['notes']) : (isset($item['description']) ? trim($item['description']) : '');
            $line_subtotal = $price * $qty;

            $subtotal += $line_subtotal;
            $total_items_count += $qty;

            $batch_items[] = [
                'pid' => $p['id'],
                'product' => $p['name'],
                'code' => $p['code'],
                'qty' => $qty,
                'price' => $price,
                'tax' => 0.00,
                'discount' => 0.00,
                'subtotal' => $line_subtotal,
                'totaltax' => 0.00,
                'totaldiscount' => 0.00,
                'product_des' => $item_notes,
                'i_class' => 1,
                'unit' => $p['unit'] ?: 'Und',
                'serial' => null
            ];

            // Decrement Stock
            $this->db->set('qty', "qty - $qty", false);
            $this->db->where('pid', $p['id']);
            $this->db->update('geopos_products');
        }

        $total = ($subtotal + $shipping) - $discount;
        if ($total < 0) $total = 0.00;

        $status = ($payment_status === 'paid') ? 'paid' : 'due';
        $pamnt = ($status === 'paid') ? $total : (isset($payload['p_amount']) ? (float)$payload['p_amount'] : 0.00);

        // 5. Insert Invoice
        $bill_date = date('Y-m-d');
        $bill_time = date('H:i:s');
        $invoice_data = [
            'tid' => $invocieno,
            'invoicedate' => $bill_date,
            'invoiceduedate' => $bill_date,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'ship_tax' => 0.00,
            'ship_tax_type' => 'off',
            'discount' => $discount,
            'discount_rate' => 0.00,
            'tax' => 0.00,
            'total' => $total,
            'pmethod' => $pmethod,
            'notes' => $notes,
            'status' => $status,
            'csd' => $customer_id,
            'eid' => $employee_id,
            'pamnt' => $pamnt,
            'items' => $total_items_count,
            'taxstatus' => 'no',
            'discstatus' => 0,
            'format_discount' => '%',
            'refer' => $system_tag,
            'order_name' => $order_name,
            'order_type' => $order_type,
            'card_txn' => $card_txn,
            'term' => 1,
            'multi' => 0,
            'i_class' => 1,
            'loc' => $loc_id,
            'r_time' => $bill_time
        ];

        $this->db->insert('geopos_invoices', $invoice_data);
        $invoice_id = $this->db->insert_id();

        // 6. Insert Batch Items with generated invoice_id as tid
        foreach ($batch_items as &$item_row) {
            $item_row['tid'] = $invoice_id;
        }
        $this->db->insert_batch('geopos_invoice_items', $batch_items);

        // 7. Payment Registration if paid
        if ($status === 'paid' && $pamnt > 0) {
            $tnote = '#' . $invocieno . '-' . $pmethod;
            $this->billing->paynow($invoice_id, $pamnt, $tnote, $pmethod, $loc_id, $bill_date, 0);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['status' => false, 'message' => 'Failed to save order in database'];
        }

        // 7.1 SIFEN Paraguay Electronic Invoice Processing (DNIT v150)
        $sifen_data = null;
        try {
            $this->load->library('sifen');
            $sifen_cfg = $this->sifen->get_config();
            if (!empty($sifen_cfg['habilitado'])) {
                $sifen_res = $this->sifen->procesar_factura_sifen($invoice_id);
                if (!empty($sifen_res['status'])) {
                    $sifen_data = [
                        'cdc'            => $sifen_res['cdc'],
                        'cdc_formatted'  => $sifen_res['cdc_formato'] ?? $this->sifen->formatear_cdc($sifen_res['cdc']),
                        'invoice_number' => $sifen_res['sifen_numero'],
                        'qr_url'         => $sifen_res['sifen_qr'],
                        'vat_10'         => $sifen_res['iva_10'],
                        'vat_5'          => $sifen_res['iva_5'],
                        'vat_total'      => $sifen_res['iva_total']
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'SIFEN API Sale Error: ' . $e->getMessage());
        }

        // 8. Build URLs & Response using configured base_url
        $base = $this->get_base_url();
        $token = hash_hmac('ripemd160', $invoice_id, $this->config->item('encryption_key'));
        $paper_w = isset($payload['paper_width']) ? (int)$payload['paper_width'] : ($sys_cfg['paper_width'] ?? ($defaults['thermal_paper_width'] ?? 80));
        $ticket_url = $base . "pos_invoices/ticket?id=$invoice_id&token=$token&width=$paper_w";
        $pdf_url = $base . "pos_invoices/thermal_pdf?id=$invoice_id&token=$token";

        return [
            'status' => true,
            'message' => 'Order created successfully',
            'system' => $system_tag,
            'order' => [
                'id' => $invoice_id,
                'invoice_number' => $invocieno,
                'system' => $system_tag,
                'order_name' => $order_name,
                'order_type' => $order_type,
                'status' => $status,
                'customer_id' => $customer_id,
                'customer_name' => $customer_info['name'] ?? '',
                'customer_phone' => $customer_info['phone'] ?? '',
                'customer_address' => $customer_info['address'] ?? '',
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total,
                'pmethod' => $pmethod,
                'items_count' => count($batch_items),
                'created_at' => $bill_date . ' ' . $bill_time,
                'ticket_url' => $ticket_url,
                'pdf_url' => $pdf_url,
                'sifen' => $sifen_data
            ]
        ];
    }

    /**
     * Get order details
     */
    public function get_order_details($id)
    {
        $this->db->select('i.*, c.name AS customer_name, c.phone AS customer_phone, c.address AS customer_address, c.taxid AS customer_taxid');
        $this->db->from('geopos_invoices i');
        $this->db->join('geopos_customers c', 'c.id = i.csd', 'left');
        $this->db->where('i.id', (int)$id);
        $order = $this->db->get()->row_array();

        if (!$order) {
            return null;
        }

        $this->db->select('id, pid, product, code, qty, price, subtotal, product_des, unit');
        $this->db->from('geopos_invoice_items');
        $this->db->where('tid', (int)$id);
        $order['items'] = $this->db->get()->result_array();

        $base = $this->get_base_url();
        $token = hash_hmac('ripemd160', $id, $this->config->item('encryption_key'));
        $order['ticket_url'] = $base . "pos_invoices/ticket?id=$id&token=$token";
        $order['pdf_url'] = $base . "pos_invoices/thermal_pdf?id=$id&token=$token";

        return $order;
    }

    /**
     * List recent orders
     */
    public function get_orders($status = null, $order_type = null, $customer_id = null, $limit = 20, $offset = 0)
    {
        $this->db->select('i.id, i.tid AS invoice_number, i.invoicedate, i.r_time, i.subtotal, i.shipping, i.total, i.pmethod, i.status, i.order_name, i.order_type, i.notes, i.refer AS system, i.csd AS customer_id, i.cdc, i.sifen_numero, i.sifen_estado, c.name AS customer_name, c.phone AS customer_phone');
        $this->db->from('geopos_invoices i');
        $this->db->join('geopos_customers c', 'c.id = i.csd', 'left');
        $this->db->where('i.i_class', 1);

        if (!empty($status)) {
            $this->db->where('i.status', strtolower($status));
        }
        if (!empty($order_type)) {
            $this->db->where('i.order_type', strtolower($order_type));
        }
        if (!empty($customer_id)) {
            $this->db->where('i.csd', (int)$customer_id);
        }

        $this->db->order_by('i.id', 'DESC');
        $this->db->limit((int)$limit, (int)$offset);
        $list = $this->db->get()->result_array();

        $base = $this->get_base_url();
        foreach ($list as &$o) {
            $token = hash_hmac('ripemd160', $o['id'], $this->config->item('encryption_key'));
            $o['ticket_url'] = $base . "pos_invoices/ticket?id=" . $o['id'] . "&token=$token";
            $o['pdf_url'] = $base . "pos_invoices/thermal_pdf?id=" . $o['id'] . "&token=$token";
        }

        return $list;
    }

    /**
     * Update order status
     */
    public function update_order_status($id, $status, $notes = null)
    {
        $id = (int)$id;
        $status = strtolower(trim($status));
        $valid = $this->config->item('api_allowed_statuses') ?: ['paid', 'due', 'canceled', 'partial'];

        if (!in_array($status, $valid)) {
            return ['status' => false, 'message' => "Invalid status: $status. Allowed: " . implode(', ', $valid)];
        }

        $data = ['status' => $status];
        if ($notes !== null) {
            $data['notes'] = $notes;
        }

        $this->db->where('id', $id);
        $this->db->update('geopos_invoices', $data);

        return ['status' => true, 'message' => 'Status updated successfully', 'id' => $id, 'status_updated' => $status];
    }

    /**
     * Webhook Dispatcher: Sends POST to the external system (e.g. n8n) when status changes
     */
    public function dispatch_webhook($order_id, $status, $notes = null)
    {
        $webhooks_cfg = $this->config->item('api_webhooks');
        if (empty($webhooks_cfg['enabled'])) {
            return false;
        }

        $order = $this->get_order_details($order_id);
        if (!$order) {
            return false;
        }

        $refer = $order['refer'];
        $systems = $this->config->item('api_systems') ?: [];
        $webhook_url = !empty($systems[$refer]['webhook_url']) ? $systems[$refer]['webhook_url'] : null;

        if (!$webhook_url) {
            return false;
        }

        $token = hash_hmac('ripemd160', $order_id, $this->config->item('encryption_key'));
        $payload = [
            'event'          => 'order_status_updated',
            'order_id'       => (int)$order_id,
            'invoice_number' => (int)$order['tid'],
            'system'         => $refer,
            'order_name'     => $order['order_name'],
            'order_type'     => $order['order_type'],
            'status'         => $status,
            'notes'          => $notes ?: $order['notes'],
            'customer'       => [
                'name'  => $order['customer_name'],
                'phone' => $order['customer_phone']
            ],
            'total'          => (float)$order['total'],
            'ticket_url'     => $this->get_base_url() . "pos_invoices/ticket?id=$order_id&token=$token",
            'timestamp'      => date('Y-m-d H:i:s')
        ];

        $timeout = isset($webhooks_cfg['timeout']) ? (int)$webhooks_cfg['timeout'] : 3;
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'User-Agent: GeeksPOS-Webhook-Dispatcher/1.0'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        @curl_exec($ch);
        curl_close($ch);

        return true;
    }
}
