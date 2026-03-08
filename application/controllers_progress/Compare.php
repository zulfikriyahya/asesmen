<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Compare extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->CHARACTER_SET = 'utf8 COLLATE utf8_general_ci';
        $this->DB1 = $this->load->database('main_garuda', true);
        $this->DB2 = $this->load->database('live', true);
    }

    public function index()
    {
        $sql_commands_to_run = [];
        $development_tables = $this->DB1->list_tables();
        $live_tables = $this->DB2->list_tables();
        $tables_to_create = array_diff($development_tables, $live_tables);
        $tables_to_drop = array_diff($live_tables, $development_tables);

        if (!empty($tables_to_create)) {
            $sql_commands_to_run = array_merge($sql_commands_to_run, $this->manage_tables($tables_to_create, 'create'));
        }
        if (!empty($tables_to_drop)) {
            $sql_commands_to_run = array_merge($sql_commands_to_run, $this->manage_tables($tables_to_drop, 'drop'));
        }

        $tables_to_update = array_diff($this->compare_table_structures($development_tables, $live_tables), $tables_to_create);
        if (!empty($tables_to_update)) {
            $sql_commands_to_run = array_merge($sql_commands_to_run, $this->update_existing_tables($tables_to_update));
        }

        if (!empty($sql_commands_to_run)) {
            echo '<h2>The database is out of Sync!</h2>';
            echo '<p>The following SQL commands need to be executed to bring the Live database tables up to date:</p>';
            echo '<pre style="padding: 20px; background-color: #FFFAF0;">';
            foreach ($sql_commands_to_run as $sql_command) {
                echo htmlspecialchars($sql_command) . "\n";
            }
            echo '</pre>';
        } else {
            echo '<h2>The database appears to be up to date</h2>';
        }
    }

    public function manage_tables($tables, $action)
    {
        $sql_commands_to_run = [];
        if ($action == 'create') {
            foreach ($tables as $table) {
                $query = $this->DB1->query("SHOW CREATE TABLE `{$table}`");
                $table_structure = $query->row_array();
                $sql_commands_to_run[] = $table_structure['Create Table'] . ';';
            }
        } elseif ($action == 'drop') {
            foreach ($tables as $table) {
                $sql_commands_to_run[] = "DROP TABLE `{$table}`;";
            }
        }
        return $sql_commands_to_run;
    }

    public function compare_table_structures($development_tables, $live_tables)
    {
        $tables_need_updating = [];
        $development_table_structures = [];
        $live_table_structures = [];

        foreach ($development_tables as $table) {
            $query = $this->DB1->query("SHOW CREATE TABLE `{$table}`");
            $development_table_structures[$table] = $query->row_array()['Create Table'];
        }
        foreach ($live_tables as $table) {
            $query = $this->DB2->query("SHOW CREATE TABLE `{$table}`");
            $live_table_structures[$table] = $query->row_array()['Create Table'];
        }
        foreach ($development_tables as $table) {
            $dev = $development_table_structures[$table];
            $live = $live_table_structures[$table] ?? '';
            if ($this->count_differences($dev, $live) > 0) {
                $tables_need_updating[] = $table;
            }
        }
        return $tables_need_updating;
    }

    public function count_differences($old, $new)
    {
        $old = trim(preg_replace('/\s+/', '', $old) ?? '');
        $new = trim(preg_replace('/\s+/', '', $new) ?? '');
        if ($old === $new) {
            return 0;
        }
        $old_parts = explode(' ', $old);
        $new_parts = explode(' ', $new);
        $length = max(count($old_parts), count($new_parts));
        $differences = 0;
        for ($i = 0; $i < $length; $i++) {
            $o = $old_parts[$i] ?? '';
            $n = $new_parts[$i] ?? '';
            if ($o !== $n) {
                $differences++;
            }
        }
        return $differences;
    }

    public function update_existing_tables($tables)
    {
        $sql_commands_to_run = [];
        if (empty($tables)) {
            return $sql_commands_to_run;
        }
        $table_structure_development = [];
        $table_structure_live = [];
        foreach ($tables as $table) {
            $table_structure_development[$table] = $this->table_field_data($this->DB1, $table);
            $table_structure_live[$table] = $this->table_field_data($this->DB2, $table);
        }
        return array_merge($sql_commands_to_run, $this->determine_field_changes($table_structure_development, $table_structure_live));
    }

    public function table_field_data($database, $table)
    {
        $query = $database->query("SHOW COLUMNS FROM `{$table}`");
        return $query->result_array();
    }

    public function determine_field_changes($source_field_structures, $destination_field_structures)
    {
        $sql_commands_to_run = [];
        foreach ($source_field_structures as $table => $fields) {
            $previous_field = '';
            foreach ($fields as $n => $field) {
                if ($this->in_array_recursive($field['Field'], $destination_field_structures[$table] ?? [])) {
                    $dest_field = $destination_field_structures[$table][$n] ?? null;
                    if ($dest_field === null) {
                        continue;
                    }
                    $differences = array_diff($fields[$n], $dest_field);
                    if (empty($differences)) {
                        $previous_field = $fields[$n]['Field'];
                        continue;
                    }
                    $modify_field = "ALTER TABLE `{$table}` MODIFY COLUMN `" . $fields[$n]['Field'] . '` ' . $fields[$n]['Type'] . ' CHARACTER SET ' . $this->CHARACTER_SET;
                    $modify_field .= !empty($fields[$n]['Default']) ? ' DEFAULT \'' . $fields[$n]['Default'] . '\'' : '';
                    $modify_field .= (isset($fields[$n]['Null']) && $fields[$n]['Null'] == 'YES') ? ' NULL' : ' NOT NULL';
                    $modify_field .= !empty($fields[$n]['Extra']) ? ' ' . $fields[$n]['Extra'] : '';
                    $modify_field .= $previous_field !== '' ? ' AFTER `' . $previous_field . '`' : '';
                    $modify_field .= ';';
                    $previous_field = $fields[$n]['Field'];
                    if ($modify_field !== ';' && !in_array($modify_field, $sql_commands_to_run)) {
                        $sql_commands_to_run[] = $modify_field;
                    }
                } else {
                    $add_field = "ALTER TABLE `{$table}` ADD COLUMN `" . $field['Field'] . '` ' . $field['Type'] . ' CHARACTER SET ' . $this->CHARACTER_SET;
                    $add_field .= (isset($field['Null']) && $field['Null'] == 'YES') ? ' NULL' : '';
                    $add_field .= ' DEFAULT \'' . ($field['Default'] ?? '') . '\'';
                    $add_field .= !empty($field['Extra']) ? ' ' . $field['Extra'] : '';
                    $add_field .= ';';
                    $sql_commands_to_run[] = $add_field;
                }
            }
        }
        return $sql_commands_to_run;
    }

    public function in_array_recursive($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $item) {
            $val = $item['Field'];
            if ($strict ? $val === $needle : $val == $needle) {
                return true;
            }
        }
        return false;
    }
}
