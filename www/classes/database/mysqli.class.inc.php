<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: about.php 1088 2008-10-07 13:02:06Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * Class that connects to MySQL using the MySQLi extension
 * 
 * @version $Id: imap.class.inc 1201 2008-10-22 18:23:34Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package go.mail
 * @access public
 */

define('DB_NUM', MYSQLI_NUM);
define('DB_BOTH', MYSQLI_BOTH);
define('DB_ASSOC', MYSQLI_ASSOC);

class db extends base_db{

	/**
	 * Type of database connector
	 *
	 * @var unknown_type
	 */
	var $type     = "mysqli";
	
	/**
	 * Connnects to the database
	 *
	 * @return resource The connection link identifier
	 */
	
	public function connect()
	{
		if(!$this->link)
		{
			$this->link = new MySQLi($this->host, $this->user, $this->password, $this->database);
			if(!$this->link)
			{
				$this->halt('Could not connect to MySQL database');
			}
			$this->link->set_charset("utf8");
		}
		return $this->link;
	}
	
	/**
	 * Frees the memory associated with a result
	 * return void
	 */
	function free() {
		$this->result->free();
		$this->result = 0;
	}
	
	/**
	 * Queries the database
	 *
	 * @param string $sql
	 * @return object The result object 
	 */
	public function query($sql)
	{
		if(empty($sql))
			return 0;
			
		$this->connect();
		
		# New query, discard previous result.
		if ($this->result) {
			$this->free();
		}
		
		if ($this->debug)
			printf("Debug: query = %s<br>\n", $sql);
		
		$this->result = $this->link->query($sql);
		if(!$this->result)
		{
			$this->halt("Invalid SQL: ".$sql);
		}
		return $this->result; 
	}
	
	/**
	 * Returns the number of rows found when you have used 
	 * SELECT SQL_CALC_FOUND_ROWS
	 *
	 * @return unknown
	 */

	public function found_rows(){
		$this->query("SELECT FOUND_ROWS() as found;");
		$this->next_record();
		return $this->f('found');
	}


	/**
	 * Walk the result set from a select query
	 *
	 * @param int $result_type DB_ASSOC, DB_BOTH or DB_NUM
	 * @return unknown
	 */
	public function next_record($result_type=DB_ASSOC) {
		if (!$this->result) {
			$this->halt("next_record called with no query pending.");
			return 0;
		}

		$this->record = $this->result->fetch_assoc();
		$this->row   += 1;
	
		return $this->record;
	}
	
	/**
	 * Return the number of rows found in the last select statement
	 *
	 * @return int Number of rows
	 */
	
	public function num_rows() {
		return $this->result->num_rows;
	}
	
	/**
	 * Gets the number of affected rows in a previous MySQL operatio
	 *
	 * @return int
	 */
	function affected_rows() {
		return $this->link->affected_row();
	}


	/**
	 * Get the number of fields in a result
	 *
	 * @return int
	 */
	function num_fields() {
		return $this->result->field_count;
	}
	
	/**
	 * Sets the error and errno property
	 *
	 * @return void
	 */
	protected function set_error()
	{
		$this->error = $this->link->error;
		$this->errno = $this->link->errno;
	}
	
	/**
	 * Escapes a value to make it safe to send to MySQL
	 *
	 * @param mixed $value
	 * @param bool $trim Trim the value
	 * @return mixed the escaped value.
	 */
	public function escape($value, $trim=true)
	{
		$this->connect();
		
		if($trim)
			$value = trim($value);
			
		return $this->link->real_escape_string($value);
	}
	
	/**
	 * Returns the auto generated id used in the last query
	 *
	 * @return int
	 */
	function insert_id()
	{
		return $this->link->insert_id();
	}
}
