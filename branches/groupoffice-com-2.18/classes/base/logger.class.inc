<?php

class logger extends db
{
	var $enabled=false;
	function logger()
	{
		global $GO_CONFIG;

		if($GO_CONFIG->dblog)
		{
			$this->db();
			$this->enabled=true;
		}
			
	}


	function log($module, $text, $link_id='')
	{
		if($this->enabled)
		{
			global $GO_SECURITY;

			$log['user_id']=$GO_SECURITY->user_id;
			$log['time']=get_gmt_time();
			$log['module']=$module;
			$log['text']=addslashes($text);
			$log['link_id']=$link_id;
			$log['id']=$this->nextid('log');

			if($log['id'])
			{
				$this->insert_row('log', $log);
					
				return $log['id'];
			}else
			{
				return false;
			}
		}
	}


	function get_log($module='', $link_id='', $user_id='', $start=0, $offset=0, $sort_field='mtime', $sort_order='DESC')
	{
		$sql = "SELECT * FROM log";

		$where=false;
		if(!empty($module))
		{
			$where=true;
			$sql .=" WHERE module='$module'";
		}

		if(!empty($module))
		{
			if($where)
			{
				$sql .= ' AND ';
			}else
			{
				$sql .= ' WHERE ';
			}
				
			$sql .="module='$module'";
			$where=true;
		}

		if(!empty($link_id))
		{
			if($where)
			{
				$sql .= ' AND ';
			}else
			{
				$sql .= ' WHERE ';
			}
				
			$sql .="link_id=$link_id";
			$where=true;
		}

		if(!empty($user_id))
		{
			if($user_id)
			{
				$sql .= ' AND ';
			}else
			{
				$sql .= ' WHERE ';
			}
				
			$sql .="user_id=$user_id";
			$where=true;
		}
		
		$sql .= " ORDER BY $sort_field $sort_order";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0 && $count> $start+$offset)
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
		}
		return $count;
	}
}
