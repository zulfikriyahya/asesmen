<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Install_model extends CI_Model
{
    public function install_success()
    {
        return $this->check_installer();
    }

    public function check_installer()
    {
        include APPPATH . 'config/database.php';
        $database = $db['default']['database'];

        $this->load->dbutil();

        if ($database == '') {
            return '1';
        }

        if (!$this->dbutil->database_exists($database)) {
            return '1';
        }

        $CI = &get_instance();
        $CI->load->database();

        if ($CI->db->table_exists('users')) {
            return '3';
        }

        return '2';
    }
}
