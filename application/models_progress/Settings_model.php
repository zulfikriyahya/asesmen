<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings_model extends CI_Model
{
    public function not_admin()
    {
        $this->db->select('a.id');
        $this->db->from('users a');
        $this->db->join('users_groups b', 'a.id=b.user_id');
        $this->db->where_not_in('b.group_id', ['1']);
        return $this->db->get()->result();
    }

    public function truncate($table)
    {
        $this->load->helper('file');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($table as $tb) {
            $this->db->truncate($tb);
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        delete_files('./uploads/bank_soal/');

        foreach ($this->not_admin() as $user) {
            $this->db->delete('users', ['id' => $user->id]);
        }
    }

    public function getSetting()
    {
        return $this->db->get('setting')->row();
    }

    public function toJSON($table)
    {
        return json_encode($this->db->get($table)->result(), JSON_PRETTY_PRINT);
    }

    public function rowSize($table)
    {
        return $this->db->get($table)->num_rows();
    }
}
