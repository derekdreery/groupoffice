<?php

class calllog extends db {
		
	function create_call($call)
	{
		$call['id']=$this->nextid('cl_calls');		
		$call_id = ($this->insert_row('cl_calls', $call)) ? $call['id'] : false;
		
		return $call_id;
	}

	function update_call($call)
	{
		$r = $this->update_row('cl_calls', 'id', $call);
		return $r;
	}
	
	function delete_call($id)
	{
		return $this->query("DELETE FROM cl_calls WHERE id=?", 'i', $id);
	}
	
	function get_call($id)
	{
		$this->query("SELECT * FROM cl_calls WHERE id=?", 'i', $id);
		return $this->next_record();		
	}

	function get_calls($query='', $sortfield='ip', $sortorder='ASC', $start=0, $offset=0)
	{
		global $GO_MODULES;
		
		$sql = "SELECT ";		
		if($offset > 0)
		{
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}
		
		$sql .= "* FROM cl_calls c";
		$types='';
		$params=array();

		if ($GO_MODULES->has_module('customfields'))
		{
			$sql .= " LEFT JOIN cf_18 ON cf_18.link_id=c.id ";
		}
	
		if(!empty($query)) {		
			$query = $this->escape($query);
			$sql .= " WHERE (name LIKE '" . $query . "' " .
				"OR company LIKE '" . $query . "' OR phone LIKE '" . $query . "' " .
				"OR email LIKE '" . $query . "' OR description LIKE '" . $query . "')";
		}
		
		$sql .= " ORDER BY ".$this->escape($sortfield.' '.$sortorder);		
		if($offset > 0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
		}

		$this->query($sql, $types, $params);
		
		return ($offset > 0) ? $this->found_rows() : $this->num_rows();
	}

}
