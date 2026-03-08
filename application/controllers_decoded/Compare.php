<?php

goto bFjy2;
bX7zZ:
f8iLo:
goto vgjTG;
JIrUm:
exit('No direct script access allowed');
goto bX7zZ;
bFjy2:
if (defined('BASEPATH')) {
    goto f8iLo;
}
goto JIrUm;
vgjTG:
class Compare extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->CHARACTER_SET = 'utf8 COLLATE utf8_general_ci';
        $this->DB1 = $this->load->database('main_garuda', TRUE);
        $this->DB2 = $this->load->database('live', TRUE);
    }
    function index()
    {
        $sql_commands_to_run = array();
        $development_tables = $this->DB1->list_tables();
        $live_tables = $this->DB2->list_tables();
        $tables_to_create = array_diff($development_tables, $live_tables);
        $tables_to_drop = array_diff($live_tables, $development_tables);
        $sql_commands_to_run = is_array($tables_to_create) && !empty($tables_to_create) ? array_merge($sql_commands_to_run, $this->manage_tables($tables_to_create, 'create')) : array();
        $sql_commands_to_run = is_array($tables_to_drop) && !empty($tables_to_drop) ? array_merge($sql_commands_to_run, $this->manage_tables($tables_to_drop, 'drop')) : array();
        $tables_to_update = $this->compare_table_structures($development_tables, $live_tables);
        $tables_to_update = array_diff($tables_to_update, $tables_to_create);
        $sql_commands_to_run = is_array($tables_to_update) && !empty($tables_to_update) ? array_merge($sql_commands_to_run, $this->update_existing_tables($tables_to_update)) : '';
        if (is_array($sql_commands_to_run) && !empty($sql_commands_to_run)) {
        }
        echo '<h2>The database appears to be up to date</h2>
';
    }
    function manage_tables($tables, $action)
    {
        $sql_commands_to_run = array();
        if (!($action == 'create')) {
            if (!($action == 'drop')) {
            }
            foreach ($tables as $table) {
                $sql_commands_to_run[] = "DROP TABLE {$table};";
            }
            return $sql_commands_to_run;
        } else {
            foreach ($tables as $table) {
                $query = $this->DB1->query("SHOW CREATE TABLE `{$table}` -- create tables");
                $table_structure = $query->row_array();
                $sql_commands_to_run[] = $table_structure['Create Table'] . ';';
            }
            if (!($action == 'drop')) {
            }
            foreach ($tables as $table) {
                $sql_commands_to_run[] = "DROP TABLE {$table};";
            }
            return $sql_commands_to_run;
        }
    }
    function compare_table_structures($development_tables, $live_tables)
    {
        $tables_need_updating = array();
        $live_table_structures = $development_table_structures = array();
        foreach ($development_tables as $table) {
            $query = $this->DB1->query("SHOW CREATE TABLE `{$table}` -- dev");
            $table_structure = $query->row_array();
            $development_table_structures[$table] = $table_structure['Create Table'];
        }
        foreach ($live_tables as $table) {
            $query = $this->DB2->query("SHOW CREATE TABLE `{$table}` -- live");
            $table_structure = $query->row_array();
            $live_table_structures[$table] = $table_structure['Create Table'];
        }
        foreach ($development_tables as $table) {
            $development_table = $development_table_structures[$table];
            $live_table = isset($live_table_structures[$table]) ? $live_table_structures[$table] : '';
            if (!($this->count_differences($development_table, $live_table) > 0)) {
            }
            $tables_need_updating[] = $table;
        }
        return $tables_need_updating;
    }
    function count_differences($old, $new)
    {
        $differences = 0;
        $old = trim(preg_replace('/\s+/', '', $old) ?? '');
        $new = trim(preg_replace('/\s+/', '', $new) ?? '');
        if (!($old == $new)) {
            $old = explode(' ', $old ?? '');
            $new = explode(' ', $new ?? '');
            $length = max(count($old), count($new));
            $i = 0;
            if (!($i < $length)) {
            }
            if (!($old[$i] != $new[$i])) {
            }
            $differences++;
            $i++;
        } else {
            return $differences;
        }
    }
    function update_existing_tables($tables)
    {
        $sql_commands_to_run = array();
        $table_structure_development = array();
        $table_structure_live = array();
        if (!(is_array($tables) && !empty($tables))) {
            $sql_commands_to_run = array_merge($sql_commands_to_run, $this->determine_field_changes($table_structure_development, $table_structure_live));
            return $sql_commands_to_run;
        } else {
            foreach ($tables as $table) {
                $table_structure_development[$table] = $this->table_field_data((array) $this->DB1, $table);
                $table_structure_live[$table] = $this->table_field_data((array) $this->DB2, $table);
            }
            $sql_commands_to_run = array_merge($sql_commands_to_run, $this->determine_field_changes($table_structure_development, $table_structure_live));
            return $sql_commands_to_run;
        }
    }
    function table_field_data($database, $table)
    {
        $conn = mysqli_connect($database['hostname'], $database['username'], $database['password']);
        mysql_select_db($database['database']);
        $result = mysql_query("SHOW COLUMNS FROM `{$table}`");
        if (!$row = mysql_fetch_assoc($result)) {
            return $fields;
        } else {
            $fields[] = $row;
            if (!$row = mysql_fetch_assoc($result)) {
            }
        }
    }
    function determine_field_changes($source_field_structures, $destination_field_structures)
    {
        $sql_commands_to_run = array();
        foreach ($source_field_structures as $table => $fields) {
            foreach ($fields as $field) {
                if ($this->in_array_recursive($field['Field'], $destination_field_structures[$table])) {
                }
                $add_field = "ALTER TABLE {$table} ADD COLUMN `" . $field['Field'] . '` ' . $field['Type'] . ' CHARACTER SET ' . $this->CHARACTER_SET;
                $add_field .= isset($field['Null']) && $field['Null'] == 'YES' ? ' Null' : '';
                $add_field .= ' DEFAULT ' . $field['Default'];
                $add_field .= isset($field['Extra']) && $field['Extra'] != '' ? ' ' . $field['Extra'] : '';
                $add_field .= ';';
                $sql_commands_to_run[] = $add_field;
            }
        }
        return $sql_commands_to_run;
    }
    function in_array_recursive($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $array => $item) {
            $item = $item['Field'];
            if (!(($strict ? $item === $needle : $item == $needle) || is_array($item) && in_array_recursive($needle, $item, $strict))) {
            }
            return true;
        }
        return false;
    }
}