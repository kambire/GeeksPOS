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


class Restapi_model extends CI_Model
{
    var $table = 'geopos_accounts';

    public function __construct()
    {
        parent::__construct();
    }

    public function keylist()
    {
        $this->db->select('id, user_id, loc_id, scope, key_hint, level, date_created, revoked_at');
        $this->db->from('geopos_restkeys');
        $this->db->where('revoked_at IS NULL', null, false);
        $query = $this->db->get();
        $rows = $query->result_array();
        // Nunca se muestra la key completa: solo alcance y últimos 4 caracteres
        foreach ($rows as &$r) {
            $alcance = $r['scope'] === 'platform' ? 'Plataforma' : ('Local #' . (int)$r['loc_id']);
            $r['key'] = $alcance . ' · ••••' . htmlspecialchars((string)$r['key_hint'], ENT_QUOTES);
        }
        return $rows;
    }

    /**
     * Crea una key de plataforma (legacy GeoPOS / integraciones internas) y la devuelve una sola vez
     */
    public function addnew()
    {
        // Prefijo + 20 bytes aleatorios = 40 caracteres (entra en la columna legacy `key` varchar(40))
        $random = 'gkp_' . substr(bin2hex(random_bytes(20)), 0, 36);
        $data = array(
            'user_id' => 0,
            'scope' => 'platform',
            // La impresión remota y el REST original de GeoPOS leen esta columna en claro
            'key' => $random,
            'key_hash' => hash('sha256', $random),
            'key_hint' => substr($random, -4),
            'level' => 0,
            'date_created' => date('Y-m-d')
        );

        return $this->db->insert('geopos_restkeys', $data) ? $random : false;
    }


}
