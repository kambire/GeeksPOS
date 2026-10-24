<?php
/**
 * GeeksPOS -  Accounting,  Invoicing  and CRM Application
 * Copyright (c) UltimateKode. All Rights Reserved
 * ***********************************************************************
 *
 *  Email: support@ultimatekode.com
 *  Website: https://www.ultimatekode.com
 *
 *  ************************************************************************
 *  * This software is furnished under a license and may be used and copied
 *  * only  in  accordance  with  the  terms  of such  license and with the
 *  * inclusion of the above copyright notice.
 *  * If you Purchased from Codecanyon, Please read the full License from
 *  * here- http://codecanyon.net/licenses/standard/
 * ***********************************************************************
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Locations_model extends CI_Model
{


    public function locations_list()
    {
        $query = $this->db->query("SELECT * FROM geopos_locations ORDER BY id DESC");
        return $query->result_array();
    }

    public function locations_list2()
    {
        $where = '';
        if ($this->aauth->get_user()->loc) $where = 'WHERE id=' . $this->aauth->get_user()->loc . '';
        $query = $this->db->query("SELECT * FROM geopos_locations $where ORDER BY id DESC");
        return $query->result_array();
    }


    public function view($id)
    {

        $this->db->from('geopos_locations');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function create($name, $address, $city, $region, $country, $postbox, $phone, $email, $taxid, $image, $cur_id, $ac_id, $wid)
    {
        $data = array(
            'cname' => $name,
            'address' => $address,
            'city' => $city,
            'region' => $region,
            'country' => $country,
            'postbox' => $postbox,
            'phone' => $phone,
            'email' => $email,
            'taxid' => $taxid,
            'logo' => $image,
            'ext' => $ac_id,
            'cur' => $cur_id,
            'ware' => $wid
        );

        if ($this->db->insert('geopos_locations', $data)) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('ADDED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function edit($id, $name, $address, $city, $region, $country, $postbox, $phone, $email, $taxid, $image, $cur_id, $ac_id, $wid)
    {
        $data = array(
            'cname' => $name,
            'address' => $address,
            'city' => $city,
            'region' => $region,
            'country' => $country,
            'postbox' => $postbox,
            'phone' => $phone,
            'email' => $email,
            'taxid' => $taxid,
            'logo' => $image,
            'ext' => $ac_id,
            'cur' => $cur_id,
            'ware' => $wid
        );

        $this->db->set($data);
        $this->db->where('id', $id);

        if ($this->db->update('geopos_locations')) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function currencies()
    {

        $this->db->select('*');
        $this->db->from('geopos_currencies');

        $query = $this->db->get();
        return $query->result_array();

    }

    public function currency_d($id)
    {
        $this->db->select('*');
        $this->db->from('geopos_currencies');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function accountslist()
    {
        $this->db->select('*');
        $this->db->from('geopos_accounts');

        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
            $this->db->or_where('loc', 0);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    public function online_pay_settings($id)
    {

        $this->db->select('geopos_accounts.id,geopos_accounts.holder,');
        $this->db->from('geopos_locations');
        $this->db->where('geopos_locations.id', $id);
        $this->db->join('geopos_accounts', 'geopos_locations.ext = geopos_accounts.id', 'left');
        $query = $this->db->get();
        return $query->row_array();

    }

    public function warehouses()
    {
        $this->db->select('*');
        $this->db->from('geopos_warehouse');
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Eliminación en cascada de un local tenant y todos sus registros asociados
     * Protege siempre la Sede Matriz (loc_id = 0) y los usuarios de la matriz.
     */
    public function delete_cascade($loc_id)
    {
        $loc_id = (int)$loc_id;
        if ($loc_id <= 0) {
            return array('status' => 'Error', 'message' => 'No está permitido eliminar la Sede Matriz (ID 0) o un ID no válido.');
        }

        $location = $this->db->where('id', $loc_id)->get('geopos_locations')->row_array();
        if (!$location) {
            return array('status' => 'Error', 'message' => 'El local seleccionado no existe o ya fue eliminado.');
        }

        $loc_name = $location['cname'] ?? ('Local #' . $loc_id);

        $this->db->trans_start();

        // 1. Almacenes vinculados a este local
        $warehouses = $this->db->select('id')->where('loc', $loc_id)->get('geopos_warehouse')->result_array();
        $ware_ids = array_column($warehouses, 'id');
        if (!empty($location['ware'])) {
            $ware_ids[] = (int)$location['ware'];
        }
        $ware_ids = array_unique(array_filter($ware_ids));

        // 2. Productos y movimientos de stock en esos almacenes
        if (!empty($ware_ids)) {
            $products = $this->db->select('pid')->where_in('warehouse', $ware_ids)->get('geopos_products')->result_array();
            $pids = array_column($products, 'pid');
            if (!empty($pids)) {
                $this->db->where_in('pid', $pids)->delete('geopos_products');
            }
            $this->db->where_in('id', $ware_ids)->delete('geopos_warehouse');
        }
        $this->db->where('loc', $loc_id)->delete('geopos_warehouse');

        // 3. Usuarios y Empleados vinculados a este local
        $current_user_id = ($this->load->is_loaded('aauth') && $this->aauth->get_user()) ? (int)$this->aauth->get_user()->id : 0;
        $users = $this->db->select('id')->where('loc', $loc_id)->get('geopos_users')->result_array();
        $uids = array_column($users, 'id');
        // Salvaguarda: nunca eliminar el usuario actualmente logueado ni superadministradores de la matriz
        $uids = array_diff($uids, array($current_user_id));
        if (!empty($uids)) {
            $this->db->where_in('id', $uids)->delete('geopos_employees');
            $this->db->where_in('id', $uids)->delete('geopos_users');
        }

        // 4. Módulos Operativos (WhatsApp bot, Deliveries, Pedidos, Cocina)
        $this->db->where('loc_id', $loc_id)->delete('geopos_whatsapp_config');
        $this->db->where('loc_id', $loc_id)->delete('geopos_deliveries');
        $this->db->where('loc_id', $loc_id)->delete('geopos_delivery_orders');
        $this->db->where('loc_id', $loc_id)->delete('geopos_kitchen_orders');
        if ($this->db->table_exists('geopos_restkeys')) {
            $this->db->where('loc_id', $loc_id)->delete('geopos_restkeys');
        }

        // 5. Facturas, líneas y transacciones vinculadas a este local
        $invoices = $this->db->select('id')->where('loc', $loc_id)->get('geopos_invoices')->result_array();
        $inv_ids = array_column($invoices, 'id');
        if (!empty($inv_ids)) {
            $this->db->where_in('tid', $inv_ids)->delete('geopos_invoice_items');
            $this->db->where_in('id', $inv_ids)->delete('geopos_invoices');
        }
        $this->db->where('loc', $loc_id)->delete('geopos_transactions');
        $this->db->where('loc', $loc_id)->delete('geopos_draft');
        $this->db->where('loc', $loc_id)->delete('geopos_quotes');
        $this->db->where('loc', $loc_id)->delete('geopos_stock_r');
        $this->db->where('loc', $loc_id)->delete('geopos_purchase');
        if ($this->db->field_exists('location', 'geopos_promo')) {
            $this->db->where('location', $loc_id)->delete('geopos_promo');
        }

        // 6. Eliminar el registro del local
        $this->db->where('id', $loc_id)->delete('geopos_locations');

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return array('status' => 'Error', 'message' => 'Ocurrió un error en la base de datos al intentar eliminar el local.');
        }

        return array('status' => 'Success', 'message' => 'El local "' . $loc_name . '" y todos sus registros asociados fueron eliminados correctamente.');
    }

}
