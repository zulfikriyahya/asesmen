<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Update extends CI_Controller
{
    public function __construct()
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
            $this->output->set_content_type('application/json')->set_output($data);
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

    public function index()
    {
        $json        = file_get_contents('./assets/app/db/database.json');
        $data['json'] = (array) json_decode($json);
        $this->load->view('install/header', $data);
        $this->load->view('install/update');
        $this->load->view('install/footer');
    }

    public function object_to_array($data)
    {
        if (!is_array($data) && !is_object($data)) return $data;
        $result = [];
        foreach ($data as $key => $value) {
            $result[$key] = $this->object_to_array($value);
        }
        return $result;
    }

    public function checkDatabase()
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = false;
        $tabless    = $this->db->list_tables();
        $fields     = [];
        $currentDb  = [];

        foreach ($tabless as $table) {
            $datafld = $this->db->field_data($table);
            $sql     = 'SELECT `column_name`, `numeric_precision`, `extra`, `is_nullable`'
                . ' FROM `information_schema`.`columns` WHERE table_schema = "' . $this->db->database . '" AND table_name = "' . $table . '"';
            $query = $this->db->query($sql);
            if ($query === false) {
                $currentDb = false;
                continue;
            }
            $query  = $query->result_object();
            $retval = [];
            for ($i = 0, $c = count($query); $i < $c; $i++) {
                $retval[$i]        = new stdClass();
                $retval[$i]->name  = $query[$i]->column_name;
                $retval[$i]->extra = $query[$i]->extra;
                if (isset($datafld[$i]) && $datafld[$i]->name == $query[$i]->column_name && $query[$i]->extra != '') {
                    $datafld[$i]->extra = $query[$i]->extra;
                }
            }
            $fields[$table] = $retval;
        }

        $json = (array) json_decode(file_get_contents('./assets/app/db/database.json'));

        $full_tables = array_unique(array_merge(array_keys($json), array_keys($fields)));
        sort($full_tables);

        $create_tables = [];
        $add_columns   = [];
        $edit_columns  = [];

        foreach ($full_tables as $table) {
            if ($this->db->table_exists($table)) {
                if (!isset($json[$table])) continue;
                foreach ($json[$table] as $jtbl) {
                    if ($this->db->field_exists($jtbl->name, $table)) {
                        foreach ($fields[$table] as $ftbl) {
                            if ($jtbl->name != $ftbl->name) continue;
                            if ($jtbl->default != $ftbl->default || $jtbl->max_length != $ftbl->max_length || $jtbl->type != $ftbl->type) {
                                $edit_columns[$table][] = $jtbl;
                            }
                        }
                    } else {
                        $add_columns[$table][] = $jtbl;
                    }
                }
            } else {
                $create_tables[$table] = $json[$table];
            }
        }

        $this->db->db_debug = $db_debug;
        $this->output_json([
            'db'      => $fields,
            'create'  => $create_tables,
            'modify'  => $edit_columns,
            'add'     => $add_columns,
            'counts'  => count($create_tables) + count($add_columns) + count($edit_columns),
            'json'    => $json,
            'current' => $currentDb,
        ]);
    }

    public function updateDatabase()
    {
        $tabless = $this->db->list_tables();
        $fields  = [];
        foreach ($tabless as $table) {
            $fields[$table] = $this->db->field_data($table);
        }

        $json        = (array) json_decode(file_get_contents('./assets/app/db/database.json'));
        $full_tables = array_unique(array_merge(array_keys($json), array_keys($fields)));
        sort($full_tables);

        foreach ($full_tables as $table) {
            if ($this->db->table_exists($table)) {
                if (!isset($json[$table])) continue;
                foreach ($json[$table] as $jtbl) {
                    if ($this->db->field_exists($jtbl->name, $table)) {
                        foreach ($fields[$table] as $ftbl) {
                            if ($jtbl->name != $ftbl->name) continue;
                            if ($jtbl->default != $ftbl->default || $jtbl->max_length != $ftbl->max_length || $jtbl->type != $ftbl->type) {
                                if ($jtbl->primary_key != 0) {
                                    $this->dbforge->add_key($jtbl->name, true);
                                }
                                if ($jtbl->auto_increment != true) {
                                    $field = [$jtbl->name => ['type' => $jtbl->type, 'constraint' => $jtbl->max_length, 'null' => false]];
                                    $this->dbforge->modify_column($table, $field);
                                }
                            }
                        }
                    } else {
                        if ($jtbl->primary_key != 0) {
                            $this->dbforge->add_key($jtbl->name, true);
                        }
                        $field = [$jtbl->name => ['type' => $jtbl->type, 'constraint' => $jtbl->max_length, 'null' => false]];
                        $this->dbforge->add_column($table, $field);
                    }
                }
            } else {
                if (!isset($json[$table])) continue;
                foreach ($json[$table] as $jtbl) {
                    $field = [$jtbl->name => ['type' => $jtbl->type, 'constraint' => $jtbl->max_length, 'null' => $jtbl->primary_key == 0]];
                    $this->dbforge->add_field($field);
                    if ($jtbl->primary_key == 1) {
                        $this->dbforge->add_key($jtbl->name, true);
                    }
                }
                $this->dbforge->create_table($table, true);
                $this->db->query('ALTER TABLE `' . $table . '` ENGINE = InnoDB');
            }
        }
        echo true;
    }

    public function checkDb()
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = false;
        $tabless = $this->db->list_tables();
        $fields  = [];

        foreach ($tabless as $table) {
            $sql = 'SELECT `column_name`, `column_type`, `collation_name`, `data_type`, `character_maximum_length`, `numeric_precision`,'
                . ' `column_default`, `column_key`, `column_comment`, `extra`, `is_nullable`'
                . ' FROM `information_schema`.`columns` WHERE table_schema = "' . $this->db->database . '" AND table_name = "' . $table . '"';
            $query = $this->db->query($sql);
            if ($query === false) {
                $fields = false;
                continue;
            }
            $query  = $query->result_object();
            $retval = [];
            for ($i = 0, $c = count($query); $i < $c; $i++) {
                $retval[$i]             = new stdClass();
                $retval[$i]->name       = $query[$i]->column_name;
                $retval[$i]->col_type   = $query[$i]->column_type;
                $retval[$i]->type       = $query[$i]->data_type;
                $retval[$i]->collation  = $query[$i]->collation_name;
                $retval[$i]->max_length = $query[$i]->character_maximum_length > 0 ? $query[$i]->character_maximum_length : $query[$i]->numeric_precision;
                $retval[$i]->default    = $query[$i]->column_default;
                $retval[$i]->comment    = $query[$i]->column_comment;
                $retval[$i]->extra      = $query[$i]->extra;
                $retval[$i]->nullable   = $query[$i]->is_nullable;
                $retval[$i]->primary    = $query[$i]->column_key;
            }
            $fields[$table] = (object) ['columns' => $retval];
        }

        $json        = (array) json_decode(file_get_contents('./assets/app/db/database.json'));
        $full_tables = array_unique(array_merge(array_keys($json), array_keys($fields)));
        sort($full_tables);

        $create_tables        = [];
        $script_create_table  = [];
        $add_columns          = [];
        $script_create_column = [];
        $edit_columns         = [];
        $script_edit_column   = [];

        $no_length_types = ['longtext', 'mediumtext', 'text'];

        foreach ($full_tables as $table) {
            if (!$this->db->table_exists($table)) {
                $create_tables[] = $json[$table];
                $script = 'CREATE TABLE `' . $table . '` (';
                $pri    = '';
                foreach ($json[$table]->columns as $column) {
                    $length  = ($column->max_length != null && !in_array($column->type, $no_length_types)) ? '(' . $column->max_length . ')' : '';
                    $nullable = $column->nullable == 'NO' ? ' NOT NULL' : '';
                    $default  = $column->default == null ? '' : ' DEFAULT ' . $column->default;
                    $extra    = $column->extra == '' ? '' : ' ' . strtoupper($column->extra ?? '');
                    $comment  = $column->comment == '' ? '' : ' COMMENT \'' . $column->comment . '\'';
                    $script  .= '`' . $column->name . '` ' . $column->type . $length . $nullable . $default . $extra . $comment . ', ';
                    if ($column->primary != '') $pri = 'PRIMARY KEY (`' . $column->name . '`)';
                }
                $script .= $pri . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
                $script_create_table[$table] = $script;
                continue;
            }

            if (!isset($json[$table])) continue;

            $add_column   = [];
            $modif_column = [];

            foreach ($json[$table]->columns as $jtbl) {
                $this->_normalizeDefault($jtbl);
                $this->_normalizeExtra($jtbl);

                if ($this->db->field_exists($jtbl->name, $table)) {
                    foreach ($fields[$table]->columns as $ftbl) {
                        if ($jtbl->name != $ftbl->name) continue;
                        $this->_normalizeDefault($ftbl);
                        $this->_normalizeExtra($ftbl);

                        $this->_recordEditColumn($edit_columns, $table, $jtbl, $ftbl);

                        if ($jtbl->col_type != $ftbl->col_type || $jtbl->nullable != $ftbl->nullable || $jtbl->default != $ftbl->default || $jtbl->extra != $ftbl->extra || $jtbl->comment != $ftbl->comment) {
                            $nullable = $jtbl->nullable == 'NO' ? ' NOT NULL' : '';
                            $default  = $jtbl->default == null ? '' : ' DEFAULT ' . $jtbl->default;
                            $extra    = $jtbl->extra == '' ? '' : ' ' . strtoupper($jtbl->extra ?? '');
                            $comment  = $jtbl->comment == '' ? '' : ' COMMENT \'' . $jtbl->comment . '\'';
                            $modif_column[] = 'MODIFY `' . $jtbl->name . '` ' . $jtbl->col_type . $nullable . $default . $extra . $comment;
                        }
                    }
                } else {
                    $add_columns[$table][] = $jtbl;
                    $length   = ($jtbl->max_length != null && !in_array($jtbl->type, $no_length_types)) ? '(' . $jtbl->max_length . ')' : '';
                    $nullable = $jtbl->nullable == 'NO' ? ' NOT NULL' : '';
                    $default  = $jtbl->default == null ? '' : ' DEFAULT ' . $jtbl->default;
                    $extra    = $jtbl->extra == '' ? '' : ' ' . strtoupper($jtbl->extra ?? '');
                    if (strtoupper($extra ?? '') == ' AUTO_INCREMENT') $extra .= ' PRIMARY KEY';
                    $comment  = $jtbl->comment == '' ? '' : ' COMMENT \'' . $jtbl->comment . '\'';
                    $add_column[] = 'ADD `' . $jtbl->name . '` ' . $jtbl->type . $length . $nullable . $default . $extra . $comment;
                }
            }

            if (count($add_column) > 0) {
                $script_create_column[$table] = 'ALTER TABLE `' . $table . '` ' . implode(', ', $add_column) . ';';
            }
            if (count($modif_column) > 0) {
                $script_edit_column[$table] = 'ALTER TABLE `' . $table . '` ' . implode(', ', $modif_column) . ';';
            }
        }

        $this->db->db_debug = $db_debug;
        $this->output_json([
            'fields'                  => $fields,
            'create_tables'           => $create_tables,
            'count_tbl'               => count($create_tables),
            'add_columns_to_table'    => $add_columns,
            'count_col'               => count($add_columns),
            'edit_columns'            => $edit_columns,
            'count_mod'               => count($edit_columns),
            'add_tbl'                 => $this->encryption->encrypt(json_encode($script_create_table)),
            'add_col'                 => $this->encryption->encrypt(json_encode($script_create_column)),
            'mod_col'                 => $this->encryption->encrypt(json_encode($script_edit_column)),
        ]);
    }

    private function _normalizeDefault(&$col)
    {
        if ($col->default != null) {
            $col->default = strtoupper(str_replace('()', '', $col->default ?? ''));
        }
    }

    private function _normalizeExtra(&$col)
    {
        if ($col->extra != null) {
            $col->extra = strtoupper(str_replace('()', '', $col->extra ?? ''));
        }
    }

    private function _recordEditColumn(&$edit_columns, $table, $jtbl, $ftbl)
    {
        if ($jtbl->col_type != $ftbl->col_type)   $edit_columns[$table][$jtbl->name]['col_type']  = $jtbl->col_type;
        if ($jtbl->nullable != $ftbl->nullable)    $edit_columns[$table][$jtbl->name]['nullable']  = $jtbl->nullable;
        if ($jtbl->default != $ftbl->default)      $edit_columns[$table][$jtbl->name]['default']   = $jtbl->default;
        if ($jtbl->extra != $ftbl->extra)          $edit_columns[$table][$jtbl->name]['extra']     = $jtbl->extra;
        if ($jtbl->comment != $ftbl->comment)      $edit_columns[$table][$jtbl->name]['comment']   = $jtbl->comment;
        if ($jtbl->primary != $ftbl->primary)      $edit_columns[$table][$jtbl->name]['primary']   = $jtbl->primary;
    }

    public function createTable()
    {
        $scripts = $this->_decryptScripts($this->input->post('data', true));
        $this->output_json(['success' => $this->runQuery(implode('', (array) $scripts)), 'message' => 'Update kolom']);
    }

    public function createColumn()
    {
        $scripts = $this->_decryptScripts($this->input->post('data', true));
        $queries = implode('', (array) $scripts);
        if (strpos($queries, '`uid`') !== false) {
            $this->updateUID();
        }
        $this->output_json(['success' => $this->runQuery($queries), 'message' => 'Modify kolom']);
    }

    public function editColumn()
    {
        $scripts = $this->_decryptScripts($this->input->post('data', true));
        $this->output_json(['success' => $this->runQuery(implode('', (array) $scripts)), 'message' => 'Update selesai']);
    }

    private function _decryptScripts($raw)
    {
        str_replace('%2B', '+', $raw ?? '');
        sleep(1);
        return json_decode($this->encryption->decrypt($raw));
    }

    public function runQuery($script)
    {
        $mysqli = new mysqli($this->db->hostname, $this->db->username, $this->db->password, $this->db->database);
        if (mysqli_connect_errno()) return mysqli_connect_errno();
        $mysqli->multi_query($script);
        $mysqli->close();
        return true;
    }

    public function updateUID()
    {
        $this->load->library('Uuid', 'uuid');
        $siswas = $this->db->get('master_siswa')->result();
        $input  = [];
        foreach ($siswas as $siswa) {
            $input[] = ['id_siswa' => $siswa->id_siswa, 'uid' => $this->uuid->v4()];
        }
        return $this->db->update_batch('master_siswa', $input, 'id_siswa');
    }

    public function make_base() {}
}
