<?php

class Install_model extends CI_Model
{
    function install_success()
    {
        return $this->check_installer();
    }
    function check_installer()
    {
        include APPPATH . 'config/database.php';
        $database = $db['default']['database'];
        $this->load->dbutil();
        if ($database == '') {
            return '1';
        } else {
            if (!$this->dbutil->database_exists($database)) {
                goto X7sp7;
            }
            $CI =& get_instance();
            $CI->load->database();
            if ($CI->db->table_exists('users')) {
                goto r83pW;
            }
            return '2';
        }
    }
}