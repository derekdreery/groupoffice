<?php
/**
 * @copyright Intermesh 2006
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.00 $ $Date: 2006/12/05 11:37:30 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

class search extends db {

	function search() {
		$this->db();
	}
	
	function global_search($user_id, $query, $start, $offset, $sort_index='name', $sort_order='ASC')
	{
		global $GO_MODULES;
		
		
				
		foreach($GO_MODULES->modules as $module)
		{
			if($module['id'] != 'search' && file_exists($module['class_path'].$module['id'].'.class.inc'))
			{
				require($module['class_path'].$module['id'].'.class.inc');
				$class = new $module['id'];
				
				if(method_exists($class, 'global_search_sync'))
				{
					$last_sync_time = $this->get_last_sync_time($user_id, $module['id']);
					$class->global_search_sync($user_id, $last_sync_time);
					$this->update_last_sync_time($user_id, $module['id'], get_gmt_time());
				}				
			}			
		}
		
		
		$sql = "SELECT * FROM se_cache WHERE user_id=$user_id AND";
		
		$keywords = explode(' ', $query);
			
		if(count($keywords)>1)
		{
			foreach($keywords as $keyword)
			{				
				$sql_keywords[] = "keywords LIKE '%$keyword%'";
			}
					
			
			$sql .= ' ('.implode(' AND ', $sql_keywords).') ';
		}else {
			$sql .= " keywords LIKE '%$query%'";
		}
		
		$sql .="ORDER BY $sort_index $sort_order";

		$this->query($sql);
		$count =$this->num_rows();
		
		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		
		
		
		return $count;
		
	}
	
	function reset($user_id=0)
	{
		if($user_id>0)
		{
			$sql = "DELETE FROM se_cache WHERE user_id=$user_id";
			$this->query($sql);
			
			$sql = "DELETE FROM se_last_sync_times WHERE user_id='$user_id";
			$this->query($sql);
		}else {
			$sql = "TRUNCATE TABLE se_cache";
			$this->query($sql);
			
			$sql = "TRUNCATE TABLE se_last_sync_times";
			$this->query($sql);
		}
		
	}
	
	function get_search_result($user_id, $link_id)
	{
		$sql = "SELECT * FROM se_cache WHERE user_id=$user_id AND link_id=$link_id";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}
	
	function get_last_sync_time($user_id, $module)
	{
		$sql = "SELECT last_sync_time FROM se_last_sync_times WHERE user_id=$user_id AND module='$module'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->f('last_sync_time');
		}else {
			$lst['user_id']=$user_id;
			$lst['module']=$module;
			$lst['last_sync_time']=0;
			$this->insert_row('se_last_sync_times',$lst);
			
			return 0;
		}
	}
	
	function update_last_sync_time($user_id, $module)
	{
		$lst['user_id']=$user_id;
		$lst['module']=$module;
		$lst['last_sync_time']=get_gmt_time();
		
		$this->update_row('se_last_sync_times',array('user_id','module'),$lst);
	}
}
?>