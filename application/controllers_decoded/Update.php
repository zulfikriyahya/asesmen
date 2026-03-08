<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Update extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        include APPPATH . 'config/database.php';
        $this->load->dbforge();
        $this->load->database();
        $this->load->library('encryption');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function index()
    {
        $json = file_get_contents('./assets/app/db/database.json');
        $json = json_decode($json);
        $json = (array) $json;
        $data['json'] = $json;
        $this->load->view('install/header', $data);
        $this->load->view('install/update');
        $this->load->view('install/footer');
    }
    function object_to_array($data)
    {
        if (!(is_array($data) || is_object($data))) {
            return $data;
        } else {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = is_array($data) || is_object($data) ? $this->object_to_array($value) : $value;
                OLxj1:
            }
            return $result;
        }
    }
    public function checkDatabase()
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $tabless = $this->db->list_tables();
        $fields = [];
        $currentDb = [];
        foreach ($tabless as $table) {
            $datafld = $this->db->field_data($table);
            $sql = 'SELECT `column_name`, `numeric_precision`, `extra`, `is_nullable`' . ' FROM `information_schema`.`columns` WHERE table_schema = "' . $this->db->database . '" AND table_name = "' . $table . '"';
            if (!(($query = $this->db->query($sql)) === FALSE)) {
                $query = $query->result_object();
                $retval = array();
                $i = 0;
                $c = count($query);
                if (!($i < $c)) {
                    goto AFwrI;
                }
                if (!($datafld[$i]->name == $query[$i]->column_name)) {
                    goto WreuG;
                }
                if (!($query[$i]->extra != '')) {
                    goto Mc836;
                }
                if ($query[$i]->extra == 'auto_increment') {
                    goto i2x3g;
                }
                $datafld[$i]->extra = $query[$i]->extra;
                $retval[$i] = new stdClass();
                $retval[$i]->name = $query[$i]->column_name;
                $retval[$i]->extra = $query[$i]->extra;
                $i++;
            } else {
                $currentDb = FALSE;
                $query = $query->result_object();
                $retval = array();
                $i = 0;
                $c = count($query);
                if (!($i < $c)) {
                    goto AFwrI;
                }
                if (!($datafld[$i]->name == $query[$i]->column_name)) {
                    goto WreuG;
                }
                if (!($query[$i]->extra != '')) {
                    goto Mc836;
                }
                if ($query[$i]->extra == 'auto_increment') {
                    goto i2x3g;
                }
                $datafld[$i]->extra = $query[$i]->extra;
                $retval[$i] = new stdClass();
                $retval[$i]->name = $query[$i]->column_name;
                $retval[$i]->extra = $query[$i]->extra;
                $i++;
            }
        }
        $json = file_get_contents('./assets/app/db/database.json');
        $json = json_decode($json);
        $json = (array) $json;
        $tbl_baru = array_keys($json);
        $tbl_ada = array_keys($fields);
        $full_tables = array_merge($tbl_baru, $tbl_ada);
        $full_tables = array_unique($full_tables);
        sort($full_tables);
        $create_tables = [];
        $add_columns = [];
        $edit_columns = [];
        foreach ($full_tables as $table) {
            if ($this->db->table_exists($table)) {
            }
            $create_tables[$table] = $json[$table];
        }
        $counts = count($create_tables) + count($add_columns) + count($edit_columns);
        $data = ['db' => $fields, 'create' => $create_tables, 'modify' => $edit_columns, 'add' => $add_columns, 'counts' => $counts, 'json' => $json, 'current' => $currentDb];
        $this->output_json($data);
    }
    public function updateDatabase()
    {
        $tabless = $this->db->list_tables();
        $fields = [];
        foreach ($tabless as $table) {
            $fields[$table] = $this->db->field_data($table);
        }
        $json = file_get_contents('./assets/app/db/database.json');
        $json = json_decode($json);
        $json = (array) $json;
        $tbl_baru = array_keys($json);
        $tbl_ada = array_keys($fields);
        $full_tables = array_merge($tbl_baru, $tbl_ada);
        $full_tables = array_unique($full_tables);
        sort($full_tables);
        foreach ($full_tables as $table) {
            if ($this->db->table_exists($table)) {
            }
            if (!isset($json[$table])) {
            }
            foreach ($json[$table] as $tbl => $jtbl) {
                $field = [$jtbl->name => ['type' => $jtbl->type, 'constraint' => $jtbl->max_length, 'null' => $jtbl->primary_key == 0]];
                $this->dbforge->add_field($field);
                if (!($jtbl->primary_key == 1)) {
                }
                $this->dbforge->add_key($jtbl->name, true);
            }
            $this->dbforge->create_table($table, TRUE);
            $this->db->query('ALTER TABLE  `' . $table . '` ENGINE = InnoDB');
        }
        echo true;
    }
    public function checkDb()
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $tabless = $this->db->list_tables();
        $fields = [];
        foreach ($tabless as $table) {
            $sql = 'SELECT `column_name`, `column_type`, `collation_name`, `data_type`, `character_maximum_length`, `numeric_precision`,' . ' `column_default`, `column_key`, `column_comment`, `extra`, `is_nullable`
			FROM `information_schema`.`columns` WHERE table_schema = "' . $this->db->database . '" AND table_name = "' . $table . '"';
            if (!(($query = $this->db->query($sql)) === FALSE)) {
                $query = $query->result_object();
                $retval = array();
                $i = 0;
                $c = count($query);
                if (!($i < $c)) {
                    goto nRUCA;
                }
                $retval[$i] = new stdClass();
                $retval[$i]->name = $query[$i]->column_name;
                $retval[$i]->col_type = $query[$i]->column_type;
                $retval[$i]->type = $query[$i]->data_type;
                $retval[$i]->collation = $query[$i]->collation_name;
                $retval[$i]->max_length = $query[$i]->character_maximum_length > 0 ? $query[$i]->character_maximum_length : $query[$i]->numeric_precision;
                $retval[$i]->default = $query[$i]->column_default;
                $retval[$i]->comment = $query[$i]->column_comment;
                $retval[$i]->extra = $query[$i]->extra;
                $retval[$i]->nullable = $query[$i]->is_nullable;
                $retval[$i]->primary = $query[$i]->column_key;
                $i++;
            } else {
                $fields = FALSE;
                $query = $query->result_object();
                $retval = array();
                $i = 0;
                $c = count($query);
                if (!($i < $c)) {
                    goto nRUCA;
                }
                $retval[$i] = new stdClass();
                $retval[$i]->name = $query[$i]->column_name;
                $retval[$i]->col_type = $query[$i]->column_type;
                $retval[$i]->type = $query[$i]->data_type;
                $retval[$i]->collation = $query[$i]->collation_name;
                $retval[$i]->max_length = $query[$i]->character_maximum_length > 0 ? $query[$i]->character_maximum_length : $query[$i]->numeric_precision;
                $retval[$i]->default = $query[$i]->column_default;
                $retval[$i]->comment = $query[$i]->column_comment;
                $retval[$i]->extra = $query[$i]->extra;
                $retval[$i]->nullable = $query[$i]->is_nullable;
                $retval[$i]->primary = $query[$i]->column_key;
                $i++;
            }
        }
        $json = file_get_contents('./assets/app/db/database.json');
        $json = json_decode($json);
        $json = (array) $json;
        $tbl_seharusnya = array_keys($json);
        $tbl_ada = array_keys($fields);
        $full_tables = array_merge($tbl_seharusnya, $tbl_ada);
        $full_tables = array_unique($full_tables);
        sort($full_tables);
        $create_tables = [];
        $script_create_table = [];
        $add_columns = [];
        $script_create_column = [];
        $edit_columns = [];
        $script_edit_column = [];
        foreach ($full_tables as $table) {
            if (!$this->db->table_exists($table)) {
            }
            if (!isset($json[$table])) {
            }
            $add_column = [];
            $modif_column = [];
            foreach ($json[$table]->columns as $jtbl) {
                if ($this->db->field_exists($jtbl->name, $table)) {
                }
                $add_columns[$table][] = $jtbl;
                if ($jtbl->max_length == null) {
                }
                if ($jtbl->type != 'longtext' && $jtbl->type != 'mediumtext' && $jtbl->type != 'text') {
                }
                $length = '';
                $nullable = $jtbl->nullable == 'NO' ? ' NOT NULL' : '';
                $default = $jtbl->default == null ? '' : ' DEFAULT ' . $jtbl->default;
                $extra = $jtbl->extra == '' ? '' : ' ' . strtoupper($jtbl->extra ?? '');
                if (!(strtoupper($extra ?? '') == ' AUTO_INCREMENT')) {
                }
                $extra .= ' PRIMARY KEY';
                $comment = $jtbl->comment == '' ? '' : ' COMMENT \'' . $jtbl->comment . '\'';
                array_push($add_column, 'ADD `' . $jtbl->name . '` ' . $jtbl->type . $length . $nullable . $default . $extra . $comment);
                foreach ($fields[$table]->columns as $ftbl) {
                    if (!($jtbl->name == $ftbl->name)) {
                    }
                    if (!($jtbl->col_type != $ftbl->col_type)) {
                    }
                    $edit_columns[$table][$jtbl->name]['col_type'] = $jtbl->col_type;
                    if (!($jtbl->nullable != $ftbl->nullable)) {
                    }
                    $edit_columns[$table][$jtbl->name]['nullable'] = $jtbl->nullable;
                    if (!($jtbl->default != null)) {
                    }
                    $jtbl->default = str_replace('()', '', $jtbl->default ?? '');
                    $jtbl->default = strtoupper($jtbl->default ?? '');
                    if (!($ftbl->default != null)) {
                    }
                    $ftbl->default = str_replace('()', '', $ftbl->default ?? '');
                    $ftbl->default = strtoupper($ftbl->default ?? '');
                    if (!($jtbl->default != $ftbl->default)) {
                    }
                    $edit_columns[$table][$jtbl->name]['default'] = $jtbl->default;
                    if (!($jtbl->extra != null)) {
                    }
                    $jtbl->extra = str_replace('()', '', $jtbl->extra ?? '');
                    $jtbl->extra = strtoupper($jtbl->extra ?? '');
                    if (!($ftbl->extra != null)) {
                    }
                    $ftbl->extra = str_replace('()', '', $ftbl->extra ?? '');
                    $ftbl->extra = strtoupper($ftbl->extra ?? '');
                    if (!($jtbl->extra != $ftbl->extra)) {
                    }
                    $edit_columns[$table][$jtbl->name]['extra'] = $jtbl->extra;
                    if (!($jtbl->comment != $ftbl->comment)) {
                    }
                    $edit_columns[$table][$jtbl->name]['comment'] = $jtbl->comment;
                    if (!($jtbl->primary != $ftbl->primary)) {
                    }
                    $edit_columns[$table][$jtbl->name]['primary'] = $jtbl->primary;
                    if (strtolower($jtbl->primary ?? '') == 'pri') {
                    }
                    if (strtolower($jtbl->primary ?? '') == 'uni') {
                    }
                    if (!($jtbl->col_type != $ftbl->col_type || $jtbl->nullable != $ftbl->nullable || $jtbl->default != $ftbl->default || $jtbl->extra != $ftbl->extra || $jtbl->comment != $ftbl->comment)) {
                    }
                    $nullable = $jtbl->nullable == 'NO' ? ' NOT NULL' : '';
                    $default = $jtbl->default == null ? '' : ' DEFAULT ' . $jtbl->default;
                    $extra = $jtbl->extra == '' ? '' : ' ' . strtoupper($jtbl->extra ?? '');
                    $comment = $jtbl->comment == '' ? '' : ' COMMENT \'' . $jtbl->comment . '\'';
                    array_push($modif_column, 'MODIFY `' . $jtbl->name . '` ' . $jtbl->col_type . $nullable . $default . $extra . $comment);
                }
            }
            if (!(count($add_column) > 0)) {
            }
            $script_create_column[$table] = 'ALTER TABLE `' . $table . '` ' . implode(', ', $add_column) . ';';
            if (!(count($modif_column) > 0)) {
            }
            $script_edit_column[$table] = 'ALTER TABLE `' . $table . '` ' . implode(', ', $modif_column) . ';';
        }
        $this->db->db_debug = $db_debug;
        $data = ['fields' => $fields, 'create_tables' => $create_tables, 'count_tbl' => count($create_tables), 'add_columns_to_table' => $add_columns, 'count_col' => count($add_columns), 'edit_columns' => $edit_columns, 'count_mod' => count($edit_columns), 'add_tbl' => $this->encryption->encrypt(json_encode($script_create_table)), 'add_col' => $this->encryption->encrypt(json_encode($script_create_column)), 'mod_col' => $this->encryption->encrypt(json_encode($script_edit_column))];
        $this->output_json($data);
    }
    public function createTable()
    {
        $scripts = $this->input->post('data', true);
        str_replace('%2B', '+', $scripts ?? '');
        sleep(1);
        $scripts = json_decode($this->encryption->decrypt($scripts));
        $queries = '';
        foreach ($scripts as $script) {
            $queries .= $script;
        }
        $data['success'] = $this->runQuery($queries);
        $data['message'] = 'Update kolom';
        $this->output_json($data);
    }
    public function createColumn()
    {
        $scripts = $this->input->post('data', true);
        str_replace('%2B', '+', $scripts ?? '');
        sleep(1);
        $scripts = json_decode($this->encryption->decrypt($scripts));
        $queries = '';
        foreach ($scripts as $script) {
            $queries .= $script;
        }
        if (!(strpos('`uid`', $queries) !== false)) {
        }
        $this->updateUID();
        $data['success'] = $this->runQuery($queries);
        $data['message'] = 'Modify kolom';
        $this->output_json($data);
    }
    public function editColumn()
    {
        $scripts = $this->input->post('data', true);
        str_replace('%2B', '+', $scripts ?? '');
        sleep(1);
        $scripts = json_decode($this->encryption->decrypt($scripts));
        $queries = '';
        foreach ($scripts as $script) {
            $queries .= $script;
        }
        $data['success'] = $this->runQuery($queries);
        $data['message'] = 'Update selesai';
        $this->output_json($data);
    }
    function runQuery($script)
    {
        $hostname = $this->db->hostname;
        $hostuser = $this->db->username;
        $hostpass = $this->db->password;
        $database = $this->db->database;
        $mysqli = new mysqli($hostname, $hostuser, $hostpass, $database);
        if (!mysqli_connect_errno()) {
            $mysqli->multi_query($script);
            $mysqli->close();
            return true;
        } else {
            return mysqli_connect_errno();
        }
    }
    function updateUID()
    {
        $this->load->library('Uuid', 'uuid');
        $siswas = $this->db->get('master_siswa')->result();
        $input = array();
        foreach ($siswas as $siswa) {
            $input[] = array('id_siswa' => $siswa->id_siswa, 'uid' => $this->uuid->v4());
        }
        return $this->db->update_batch('master_siswa', $input, 'id_siswa');
    }
    function make_base()
    {
    }
}