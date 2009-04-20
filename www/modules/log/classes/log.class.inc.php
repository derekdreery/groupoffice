<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: class.tpl 2255 2008-07-02 11:47:50Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
class log extends db {
	
	public function __on_load_listeners($events){
		$events->add_listener('login', __FILE__, 'log', 'login');
	}
	
	public static function login()
	{
		$log = new log();
		$sql = "DELETE FROM go_log WHERE time<".Date::date_add(time(),0,-3);
		$log->query($sql);	
	}
	
	/**
	 * Gets all Entries
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_entries($query, $sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT ";		
		if($offset>0)
		{
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}		
		$sql .= "* FROM go_log ";
		$types='';
		$params=array();
		if(!empty($query))
 		{
 			$sql .= " WHERE text LIKE ?";
 			$types .= 's';
 			$params[]=$query;
 		} 		
		$sql .= " ORDER BY ".$this->escape($sortfield.' '.$sortorder);	
		
		$_SESSION['GO_SESSION']['export_queries']['log']=$sql;
		
		if($offset>0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
		}
		return $this->query($sql, $types, $params);
	}
}
