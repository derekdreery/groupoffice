<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
class log extends db {
	
	public function __on_load_listeners($events){
		$events->add_listener('login', __FILE__, 'log', 'login');
	}
	
	public static function login($username, $password, $user, $count_login)
	{
		if($count_login){
			$log = new log();
			$sql = "DELETE FROM go_log WHERE time<".Date::date_add(time(),0,-1);
			$log->query($sql);

			$log->add('Logged in');
		}
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
		$sql .= "gl.* FROM go_log gl, go_users gu WHERE gl.user_id = gu.id";
 		if(!empty($query))
 		{
 			$sql .= " AND (text LIKE '".$this->escape($query)."' OR first_name LIKE '".$this->escape($query)."' OR middle_name LIKE '".$this->escape($query)."' OR last_name LIKE '".$this->escape($query)."' "
 					. "OR CONCAT(first_name, middle_name, last_name) LIKE '".$this->escape($query)."') ";
 		}		
		$sql .= " ORDER BY ".$this->escape("gl.".$sortfield.' '.$sortorder);	
		
		$_SESSION['GO_SESSION']['export_queries']['log']=array(
			'query'=>$sql,
			'method'=>'format_log_entry',
			'class'=>'log',
			'require'=>__FILE__);
		
		if($offset>0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
		}
		$this->query($sql);
		return $offset>0 ? $this->found_rows() : $this->num_rows();
	}
	
	function format_log_entry(&$entry)
	{
		global $lang, $GO_USERS;

		if(!isset($GO_USERS)){
			global $GO_CONFIG;
			require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();
		}
		
		if(!isset($lang['link_type']))
		{
			global $GO_LANGUAGE;
			$GO_LANGUAGE->get_all();
		}
		
		$entry['user_name']=$GO_USERS->get_user_realname($entry['user_id']);
		$entry['time']=Date::get_timestamp($entry['time']);
		$entry['link_type']=isset($lang['link_type'][$entry['link_type']]) ? $lang['link_type'][$entry['link_type']] : '-';
	}

	function add($text, $link_id=0, $link_type=0){
		if(!defined('NOLOG'))
		{
			$log['link_id']=$link_id;
			$log['link_type']=$link_type;
			$log['time']=time();
			$log['text']=$text;
			$log['user_id']=$GLOBALS['GO_SECURITY']->user_id;
			$log['id']=$this->nextid('go_log');

			return $this->insert_row('go_log', $log);
		}
	}
}
