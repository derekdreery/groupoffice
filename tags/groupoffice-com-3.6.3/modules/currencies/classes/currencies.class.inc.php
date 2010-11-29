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
class currencies extends db {
	
	
	/**
	 * Update a Currency
	 *
	 * @param Array $currency Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function replace_currency($currency)
	{
		$r = $this->replace_row('cu_currencies',$currency);
		return $r;
	}
	/**
	 * Delete a Currency
	 *
	 * @param Int $currency_id ID of the currency
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_currency($code)
	{
		return $this->query("DELETE FROM cu_currencies WHERE code=?", 'i', $code);
	}
	/**
	 * Gets a Currency record
	 *
	 * @param Int $currency_id ID of the currency
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_currency($code)
	{
		$this->query("SELECT * FROM cu_currencies WHERE code=?", 's', $code);
		return $this->next_record();		
	}
	/**
	 * Gets all Currencies
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_currencies($query='', $sortfield='code', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT ";		
		if($offset>0)
		{
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}		
		$sql .= "* FROM cu_currencies ";
		$types='';
		$params=array();
		if(!empty($query))
 		{
 			$sql .= " WHERE code LIKE ?";
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

	function delete_other_currencies($codes)
	{
		$sql = "DELETE FROM cu_currencies";
		if(count($codes))
		{
			$sql .= " WHERE code NOT IN ('".implode("','", $codes)."')";
		}
		$this->query($sql);
	}

	function get_default_currency(){
		$sql = "SELECT * FROM cu_currencies WHERE value=1";
		$this->query($sql);
		return $this->next_record();
	}

}
