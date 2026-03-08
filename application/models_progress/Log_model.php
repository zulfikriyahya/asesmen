<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Log_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('user_agent');
    }

    public function saveLog($type, $desc)
    {
        $user_id = $this->ion_auth->user()->row()->id;
        $group   = $this->ion_auth->get_users_groups($user_id)->row();
        $agent   = $this->agent->is_browser()
            ? $this->agent->browser() . ' ' . $this->agent->version()
            : 'Data user gagal didapatkan';

        $this->insertLog(
            $user_id,
            $group->id,
            $group->name,
            $type,
            $desc,
            $agent,
            $this->agent->platform(),
            $this->input->ip_address()
        );
    }

    private function insertLog($id_user, $group_id, $group_name, $type, $desc, $agent, $os, $ip)
    {
        $this->db->insert('log', [
            'id_user'    => $id_user,
            'id_group'   => $group_id,
            'name_group' => $group_name,
            'log_desc'   => $desc,
            'address'    => $ip,
            'agent'      => $agent,
            'device'     => $os,
        ]);
    }

    public function loadAktifitas($limit = null)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.first_name, b.last_name, d.name');
        $this->db->from('log a');
        $this->db->join('users b', 'b.id=a.id_user', 'left');
        $this->db->join('groups d', 'd.id=a.id_group');
        $this->db->order_by('a.log_time', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    public function loadAktifitasSiswa($limit = null)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.first_name, b.last_name, d.name');
        $this->db->from('log a');
        $this->db->join('users b', 'b.id=a.id_user', 'left');
        $this->db->join('groups d', 'd.id=a.id_group');
        $this->db->where('a.id_group', 3);
        $this->db->order_by('a.log_time', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }
}
