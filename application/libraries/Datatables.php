<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
  class Datatables
  {
    private $ci;
    private $table;
    private $distinct;
    private $group_by       = array();
    private $select         = array();
    private $joins          = array();
    private $columns        = array();
    private $where          = array();
    private $or_where       = array();
    private $where_in       = array();
    private $like           = array();
    private $or_like        = array();
    private $filter         = array();
    private $add_columns    = array();
    private $edit_columns   = array();
    private $unset_columns  = array();

    public function __construct()
    {
      $this->ci =& get_instance();
    }

    public function set_database($db_name)
    {
      $db_data = $this->ci->load->database($db_name, TRUE);
      $this->ci->db = $db_data;
    }

    public function select($columns, $backtick_protect = TRUE)
    {
      foreach($this->explode(',', $columns) as $val)
      {
        $column = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$2', $val));
        $column = preg_replace('/.*\.(.*)/i', '$1', $column); // get name after `.`
        $this->columns[] =  $column;
        $this->select[$column] =  trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$1', $val));
      }

      $this->ci->db->select($columns, $backtick_protect);
      return $this;
    }

    public function distinct($column)
    {
      $this->distinct = $column;
      $this->ci->db->distinct($column);
      return $this;
    }

    public function group_by($val)
    {
      $this->group_by[] = $val;
      $this->ci->db->group_by($val);
      return $this;
    }

    public function from($table)
    {
      $this->table = $table;
      return $this;
    }

    public function join($table, $fk, $type = NULL)
    {
      $this->joins[] = array($table, $fk, $type);
      $this->ci->db->join($table, $fk, $type);
      return $this;
    }

    public function where($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
      $this->where[] = array($key_condition, $val, $backtick_protect);
      $this->ci->db->where($key_condition, $val, $backtick_protect);
      return $this;
    }

    public function or_where($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
      $this->or_where[] = array($key_condition, $val, $backtick_protect);
      $this->ci->db->or_where($key_condition, $val, $backtick_protect);
      return $this;
    }
    
    public function where_in($key_condition, $val = NULL)
    {
      $this->where_in[] = array($key_condition, $val);
      $this->ci->db->where_in($key_condition, $val);
      return $this;
    }

    public function filter($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
      $this->filter[] = array($key_condition, $val, $backtick_protect);
      return $this;
    }

    public function like($key_condition, $val = NULL, $side = 'both')
    {
      $this->like[] = array($key_condition, $val, $side);
      $this->ci->db->like($key_condition, $val, $side);
      return $this;
    }

    public function or_like($key_condition, $val = NULL, $side = 'both')
    {
      $this->or_like[] = array($key_condition, $val, $side);
      $this->ci->db->or_like($key_condition, $val, $side);
      return $this;
    }

    public function add_column($column, $content, $match_replacement = NULL)
    {
      $this->add_columns[$column] = array('content' => $content, 'replacement' => $this->explode(',', $match_replacement));
      return $this;
    }

    public function edit_column($column, $content, $match_replacement)
    {
      $this->edit_columns[$column][] = array('content' => $content, 'replacement' => $this->explode(',', $match_replacement));
      return $this;
    }

    public function unset_column($column)
    {
      $column=explode(',',$column);
      $this->unset_columns=array_merge($this->unset_columns,$column);
      return $this;
    }

    public function generate($output = 'json', $charset = 'UTF-8')
    {
      if(strtolower($output) == 'json')
        $this->get_paging();

      $this->get_ordering();
      $this->get_filtering();
      return $this->produce_output(strtolower($output), strtolower($charset));
    }

    private function get_paging()
    {
      $iStart = $this->ci->input->post('start');
      $iLength = $this->ci->input->post('length');

      if($iLength != '' && $iLength != '-1')
        $this->ci->db->limit($iLength, ($iStart)? $iStart : 0);
    }

    private function get_ordering()
    {

      $Data = $this->ci->input->post('columns');


      if ($this->ci->input->post('order'))
        foreach ($this->ci->input->post('order') as $key)
          if($this->check_cType())
            $this->ci->db->order_by($Data[$key['column']]['data'], $key['dir']);
          else
            $this->ci->db->order_by($this->columns[$key['column']] , $key['dir']);

    }
    
    private function get_filtering()
    {

      $mColArray = $this->ci->input->post('columns');

      $sWhere = '';
      $search = $this->ci->input->post('search');
      $sSearch = $this->ci->db->escape_like_str(trim($search['value'] ?? ''));
      $columns = array_values(array_diff($this->columns, $this->unset_columns));

      if($sSearch != '')
        for($i = 0; $i < count($mColArray); $i++)
          if ($mColArray[$i]['searchable'] == 'true' && !array_key_exists($mColArray[$i]['data'], $this->add_columns))
            if($this->check_cType())
              $sWhere .= $this->select[$mColArray[$i]['data']] . " LIKE '%" . $sSearch . "%' OR ";
            else
              $sWhere .= $this->select[$this->columns[$i]] . " LIKE '%" . $sSearch . "%' OR ";
      $sWhere = substr_replace($sWhere, '', -3);

      if($sWhere != '')
        $this->ci->db->where('(' . $sWhere . ')');

      foreach($this->filter as $val)
        $this->ci->db->where($val[0], $val[1], $val[2]);
    }
    
    private function get_display_result()
    {
      return $this->ci->db->get($this->table);
    }

    private function produce_output($output, $charset)
    {
      $aaData = array();
      $rResult = $this->get_display_result();

      if($output == 'json')
      {
        $iTotal = $this->get_total_results();
        $iFilteredTotal = $this->get_total_results(TRUE);
      }

      foreach($rResult->result_array() as $row_key => $row_val)
      {
        $aaData[$row_key] =  ($this->check_cType())? $row_val : array_values($row_val);

        foreach($this->add_columns as $field => $val)
         if($this->check_cType())
            $aaData[$row_key][$field] = $this->exec_replace($val, $aaData[$row_key]);
          else
            $aaData[$row_key][] = $this->exec_replace($val, $aaData[$row_key]);

        foreach($this->edit_columns as $modkey => $modval)
          foreach($modval as $val)
            $aaData[$row_key][($this->check_cType())? $modkey : array_search($modkey, $this->columns)] = $this->exec_replace($val, $aaData[$row_key]);
        $aaData[$row_key] = array_diff_key($aaData[$row_key], ($this->check_cType())? $this->unset_columns : array_intersect($this->columns, $this->unset_columns));

        if(!$this->check_cType())
          $aaData[$row_key] = array_values($aaData[$row_key]);

      }

      if($output == 'json')
      {
        $sOutput = array
        (
          'draw'                => intval($this->ci->input->post('draw')),
          'recordsTotal'        => $iTotal,
          'recordsFiltered'     => $iFilteredTotal,
          'data'                => $aaData
        );

        if($charset == 'utf-8')
          return json_encode($sOutput);
        else
          return $this->jsonify($sOutput);
      }
      else
        return array('aaData' => $aaData);
    }

    private function get_total_results($filtering = FALSE)
    {
      if($filtering)
        $this->get_filtering();

      foreach($this->joins as $val)
        $this->ci->db->join($val[0], $val[1], $val[2]);

      foreach($this->where as $val)
        $this->ci->db->where($val[0], $val[1], $val[2]);

      foreach($this->or_where as $val)
        $this->ci->db->or_where($val[0], $val[1], $val[2]);
        
      foreach($this->where_in as $val)
        $this->ci->db->where_in($val[0], $val[1]);

      foreach($this->group_by as $val)
        $this->ci->db->group_by($val);

      foreach($this->like as $val)
        $this->ci->db->like($val[0], $val[1], $val[2]);

      foreach($this->or_like as $val)
        $this->ci->db->or_like($val[0], $val[1], $val[2]);

      if(strlen($this->distinct ?? '') > 0)
      {
        $this->ci->db->distinct($this->distinct);
        $this->ci->db->select($this->columns);
      }

      $query = $this->ci->db->get($this->table, NULL, NULL, FALSE);
      return $query->num_rows();
    }
    
    private function exec_replace($custom_val, $row_data)
    {
      $replace_string = '';
      
      $custom_val['replacement'] = array_reverse($custom_val['replacement'], true);

      if(isset($custom_val['replacement']) && is_array($custom_val['replacement']))
      {
        $custom_val['replacement'] = array_reverse($custom_val['replacement'], true);
        foreach($custom_val['replacement'] as $key => $val)
        {
          $sval = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($val));

      if(preg_match('/(\w+::\w+|\w+)\((.*)\)/i', $val, $matches) && is_callable($matches[1]))
          {
            $func = $matches[1];
            $args = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[,]+/", $matches[2], 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

            foreach($args as $args_key => $args_val)
            {
              $args_val = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($args_val));
              $args[$args_key] = (in_array($args_val, $this->columns))? ($row_data[($this->check_cType())? $args_val : array_search($args_val, $this->columns)]) : $args_val;
            }

            $replace_string = call_user_func_array($func, $args);
          }
          elseif(in_array($sval, $this->columns))
            $replace_string = $row_data[($this->check_cType())? $sval : array_search($sval, $this->columns)];
          else
            $replace_string = $sval;

          $custom_val['content'] = str_ireplace('$' . ($key + 1), $replace_string, $custom_val['content']);
        }
      }

      return $custom_val['content'];
    }

    private function check_cType()
    {
      $column = $this->ci->input->post('columns');
      if(is_numeric(isset($column[0]) && isset($column[0]['data']) ? $column[0]['data'] : ''))
        return FALSE;
      else
        return TRUE;
    }

    private function balanceChars($str, $open, $close)
    {
      $openCount = substr_count($str, $open);
      $closeCount = substr_count($str, $close);
      $retval = $openCount - $closeCount;
      return $retval;
    }

    private function explode($delimiter, $str, $open = '(', $close=')')
    {
      $retval = array();
      $hold = array();
      $balance = 0;
      $parts = explode($delimiter, $str);

      foreach($parts as $part)
      {
        $hold[] = $part;
        $balance += $this->balanceChars($part, $open, $close);

        if($balance < 1)
        {
          $retval[] = implode($delimiter, $hold);
          $hold = array();
          $balance = 0;
        }
      }

      if(count($hold) > 0)
        $retval[] = implode($delimiter, $hold);

      return $retval;
    }

    private function jsonify($result = FALSE)
    {
      if(is_null($result))
        return 'null';

      if($result === FALSE)
        return 'false';

      if($result === TRUE)
        return 'true';

      if(is_scalar($result))
      {
        if(is_float($result))
          return floatval(str_replace(',', '.', strval($result)));

        if(is_string($result))
        {
          static $jsonReplaces = array(array('\\', '/', '\n', '\t', '\r', '\b', '\f', '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
          return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $result) . '"';
        }
        else
          return $result;
      }

      $isList = TRUE;

      for($i = 0, reset($result); $i < count($result); $i++, next($result))
      {
        if(key($result) !== $i)
        {
          $isList = FALSE;
          break;
        }
      }

      $json = array();

      if($isList)
      {
        foreach($result as $value)
          $json[] = $this->jsonify($value);

        return '[' . join(',', $json) . ']';
      }
      else
      {
        foreach($result as $key => $value)
          $json[] = $this->jsonify($key) . ':' . $this->jsonify($value);

        return '{' . join(',', $json) . '}';
      }
    }

    public function last_query()
    {
      return  $this->ci->db->last_query();
    }
  }