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
class blacklist extends db {
	public function __on_load_listeners($events){
		$events->add_listener('bad_login', __FILE__, 'blacklist', 'bad_login');
		$events->add_listener('before_login', __FILE__, 'blacklist', 'before_login');
		$events->add_listener('login', __FILE__, 'blacklist', 'login');
	}

	/**
	 * Check's if the IP is blacklisted
	 */
	public static function before_login(){

		$bl = new blacklist();
		
		$ip = $bl->get_ip($_SERVER['REMOTE_ADDR']);
		if($ip && $ip['count']>4){
			global $GO_LANGUAGE, $lang;

			$GO_LANGUAGE->require_language_file('blacklist');
			throw new Exception(sprintf($lang['blacklist']['blacklisted'], $_SERVER['REMOTE_ADDR']));
		}
	}

	/**
	 * Increases the bad logins count
	 */

	public static function bad_login(){
		$bl = new blacklist();

		$ip = $bl->get_ip($_SERVER['REMOTE_ADDR']);
		if($ip){
			$ip['count']++;
			$bl->update_ip($ip);
		}else
		{
			$ip['ip']=$_SERVER['REMOTE_ADDR'];
			$ip['count']=1;
			$bl->add_ip($ip);
		}
	}

	/**
	 * Delete's an IP when login is succesful
	 */

	public static function login(){
		$bl = new blacklist();

		$bl->delete_ip($_SERVER['REMOTE_ADDR']);
	}
	/**
	 * Add a Ip
	 *
	 * @param Array $ip Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_ip($ip)
	{
		$ip['mtime']=time();
		return $this->insert_row('bl_ips', $ip);
	}
	/**
	 * Update a Ip
	 *
	 * @param Array $ip Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_ip($ip)
	{
		$ip['mtime']=time();
		$r = $this->update_row('bl_ips', 'ip', $ip);
		return $r;
	}
	/**
	 * Delete a Ip
	 *
	 * @param Int $ip_id ID of the ip
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_ip($ip){
		return $this->query("DELETE FROM bl_ips WHERE ip=?", 's', $ip);
	}
	/**
	 * Gets a Ip record
	 *
	 * @param Int $ip_id ID of the ip
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_ip($ip)
	{
		$this->query("SELECT * FROM bl_ips WHERE ip=?", 's', $ip);
		return $this->next_record();		
	}

	/**
	 * Gets all Ips
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_ips($query='', $sortfield='ip', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT ";		
		if($offset>0)
		{
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}		
		$sql .= "* FROM bl_ips ";
		$types='';
		$params=array();
		if(!empty($query))
 		{
 			$sql .= " WHERE name LIKE ?";
 			$types .= 's';
 			$params[]=$query;
 		} 		
		$sql .= " ORDER BY ".$this->escape($sortfield.' '.$sortorder);	
		if($offset>0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
		}
		$this->query($sql, $types, $params);
		return $offset>0 ? $this->found_rows() : $this->num_rows();
	}
/* {CLASSFUNCTIONS} */

}
