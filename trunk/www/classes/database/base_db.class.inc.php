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
 * @package go.database
 * @access public
 */

class base_db{

	/**
	 * The database host
	 *
	 * @var string
	 */
	var $host = "";

	/**
	 * The database name
	 *
	 * @var string
	 */
	var $database = "";

	/**
	 * The database username
	 *
	 * @var string
	 */
	var $user = "";

	/**
	 * The database password
	 *
	 * @var string
	 */
	var $password = "";

	/**
	 * Set to true for debugging messages.
	 *
	 * @var bool
	 */
	var $debug = false;

	/**
	 * "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
	 *
	 * @var string
	 */
	var $halt_on_error = "yes";

	/**
	 * The sequence table to use for autoincrementing numbers.
	 *
	 * @var unknown_type
	 */
	var $seq_table = "go_db_sequence";

	/**
	 * The current record from a select query
	 *
	 * @var array
	 */
	var $record = array();

	/**
	 * The current row index when waling through a result set
	 *
	 * @var unknown_type
	 */
	var $row;

	/**
	 * Database error number
	 *
	 * @var int
	 */
	var $errno = 0;

	/**
	 * Database error message
	 *
	 * @var string
	 */
	var $error = "";

	/**
	 * Type of database connector
	 *
	 * @var string
	 */
	var $type = "mysqli";

	/**
	 * The database connection link identifier
	 *
	 * @var resource
	 */
	var $link = false;

	/**
	 * The result object from a query
	 *
	 * @var resource
	 */
	var $result = false;

	/**
	 * True when a table is locked
	 *
	 * @var bool
	 */
	var $locked = false;

	/**
	 * Constructor a config object with db_host, db_pass, db_user and db_name
	 * may be passed so it can connect to a different database then the default.
	 *
	 * @param unknown_type $config
	 * @return db
	 */
	public function __construct($config=null)
	{
		$this->set_config($config);
	}

	/**
	 * Set's the connection parameters. A config object with db_host, db_pass, db_user and db_name
	 * may be passed so it can connect to a different database then the default.
	 *
	 * @param object $config
	 */

	public function set_config($config=null)
	{
		global $GO_CONFIG;
			
		if(!isset($config) && isset($GO_CONFIG))
		{
			$config = $GO_CONFIG;
		}
			
		if(isset($config))
		{
			if (isset($config->db_host)) {
				$this->host = $config->db_host;
			}
			if (isset($config->db_name)) {
				$this->database = $config->db_name;
			}
			if (isset($config->db_user)) {
				$this->user = $config->db_user;
			}
			if (isset($config->db_pass)) {
				$this->password = $config->db_pass;
			}
		}
	}
	
	/**
	 * Set the connection parameters manually
	 *
	 * @param string $host
	 * @param string $database
	 * @param string $user
	 * @param string $pass
	 */
	
	public function set_parameters($host, $database, $user, $pass)
	{
		$this->host = $host;
		$this->database = $database;
		$this->user = $user;
		$this->password = $pass;
	}

	/**
	 * Connnects to the database
	 *
	 * @return resource The connection link identifier
	 */

	public function connect()
	{
	}

	/**
	 * Frees the memory associated with a result
	 * return void
	 */
	function free() {
	}

	/**
	 * Queries the database
	 *
	 * @param string $sql
	 * @param string $types The types of the parameters. possible values: i, d, s, b for integet, double, string and blob
	 * @param mixed $params If a single or an array of parameters are given in the statement will be prepared
	 *
	 * @return object The result object
	 */
	public function query($sql, $types='', $params=array())
	{
	}

	/**
	 * Returns the number of rows found when you have used
	 * SELECT SQL_CALC_FOUND_ROWS
	 *
	 * @return unknown
	 */

	public function found_rows(){
	}


	/**
	 * Walk the reseult set from a select query
	 *
	 * @param int $result_type DB_ASSOC, DB_BOTH or DB_NUM
	 * @return unknown
	 */
	public function next_record($result_type=DB_ASSOC) {
	}


	/**
	 * Lock a table
	 *
	 * @param string $table
	 * @param string $mode Modes are: "read", "read local", "write", "low priority write"
	 * @return unknown
	 */
	public function lock($table, $mode = "write") {
		$query = "lock tables ";
		if(is_array($table)) {
			while(list($key,$value) = each($table)) {
				if(is_int($key)) $key = $mode;
				if(strpos($value, ",")) {
					$query .= str_replace(",", " $key, ", $value) . " $key, ";
				} else {
					$query .= "$value $key, ";
				}
			}
			$query = substr($query, 0, -2);
		} elseif(strpos($table, ",")) {
			$query .= str_replace(",", " $mode, ", $table) . " $mode";
		} else {
			$query .= "$table $mode";
		}
		if(!$this->query($query)) {
			$this->halt("lock() failed.");
			return false;
		}
		$this->locked = true;
		return true;
	}

	/**
	 * Unlock tables
	 *
	 * @return bool True on success
	 */

	public function unlock() {
		// set before unlock to avoid potential loop
		$this->locked = false;

		if(!$this->query("unlock tables")) {
			$this->halt("unlock() failed.");
			return false;
		}
		return true;
	}

	/**
	 * Fetch a single field from a result record
	 *
	 * @param string $name Field name or index
	 * @return mixed the field value
	 */

	public function f($name) {
		if (isset($this->record[$name])) {
			return $this->record[$name];
		}
	}

	/**
	 * Print a single field from a result record.
	 *
	 * @param string $name Field name or index
	 * @return mixed the field value
	 */
	public function p($name) {
		if (isset($this->record[$name])) {
			print $this->record[$name];
		}
	}

	/**
	 * Get a next unique ID for a table. Used instead of auto increment
	 * so that we have better support for different database backends.
	 *
	 * @param string $seq_name
	 * @return int the next unique ID
	 */
	public function nextid($seq_name) {
		/* if no current lock, lock sequence table */
		if(!$this->locked) {
			if($this->lock($this->seq_table)) {
				$locked = true;
			} else {
				$this->halt("cannot lock ".$this->seq_table." - has it been created?");
				return 0;
			}
		}

		/* get sequence number and increment */
		$q = sprintf("select nextid from %s where seq_name = '%s'",
		$this->seq_table,
		$seq_name);
		if(!$this->query($q)) {
			$this->halt('query failed in nextid: '.$q);
			return 0;
		}

		/* No current value, make one */
		if(!$this->next_record()) {
			$currentid = 0;
			$q = sprintf("insert into %s values('%s', %s)",
			$this->seq_table,
			$seq_name,
			$currentid);
			if(!$this->query($q)) {
				$this->halt('query failed in nextid: '.$q);
				return 0;
			}
		} else {
			$currentid = $this->f("nextid");
		}
		$nextid = $currentid + 1;
		$q = sprintf("update %s set nextid = '%s' where seq_name = '%s'",
		$this->seq_table,
		$nextid,
		$seq_name);
		if(!$this->query($q)) {
			$this->halt('query failed in nextid: '.$q);
			return 0;
		}

		/* if nextid() locked the sequence table, unlock it */
		if($locked) {
			$this->unlock();
		}

		return $nextid;
	}

	/**
	 * Return the number of rows found in the last select statement
	 *
	 * @return int Number of rows
	 */

	public function num_rows() {
	}

	/**
	 * Gets the number of affected rows in a previous MySQL operatio
	 *
	 * @return int
	 */
	function affected_rows() {
	}


	/**
	 * Get the number of fields in a result
	 *
	 * @return int
	 */
	function num_fields() {
	}

	/**
	 * Updates a row from a table
	 *
	 * @param string $table The table name
	 * @param string $index The field name to select on ( WHERE '$index'=1). This field must be present in the $fields parameter
	 * @param array $fields An associative array with fieldname=>value
	 * @param string $types The types of the parameters. possible values: i, d, s, b for integet, double, string and blob
	 * @param bool $trim Trim values in the array
	 * @return bool
	 */

	public function update_row($table, $index, $fields, $types='', $trim=true)
	{
		if(!is_array($fields))
		{
			$this->halt('Invalid update row called');
			return false;
		}
		if(!is_array($index))
		{
			$index = array($index);
		}
		if(empty($types))
		{
			$types = str_repeat('s', count($fields));
		}

		$field_types='';
		$index_types='';
		$count=0;
		$indexes=array();

		foreach($fields as $key => $value)
		{
			if(!in_array($key, $index))
			{
				$updates[] = "`$key`=?";
				$field_values[] = $value;
				$field_types.=$types[$count];
			}else
			{
				$indexes[]="`$key`=?";
				$index_types.=$types[$count];
				$index_values[] = $value;
			}
			$count++;
		}
		if(isset($updates))
		{
			$sql = "UPDATE `$table` SET ".implode(',',$updates)." WHERE ".implode(' AND ', $indexes);
				
			foreach($index_values as $index_value)
			{
				$field_values[]=$index_value;
			}
			$field_types .= $index_types;

			if(!$this->query($sql, $field_types, $field_values))
			{
				if($this->halt_on_error=='yes')
				{
					throw new DatabaseUpdateException();
				}
			}else
			{
				return true;
			}
		}
			
		return false;
	}

	/**
	 * Inserts a row in a table
	 *
	 * @param string $table The table name
	 * @param array $fields An associative array with fieldname=>value
	 * @param string $types The types of the parameters. possible values: i, d, s, b for integet, double, string and blob
	 * @param bool $trim Trim values in the array
	 * @return bool
	 */

	public function insert_row($table, $fields, $types='', $trim=true, $replace=false)
	{
		if(!is_array($fields))
		{
			$this->halt('Invalid insert row called');
			return false;
		}

		foreach($fields as $key => $value)
		{
			$field_names[] = $key;
			$field_values[] = $value;
		}
		if(isset($field_names))
		{
			$sql = $replace ? 'REPLACE' : 'INSERT';
			$sql .= " INTO `$table` (`".implode('`,`', $field_names)."`) VALUES ".
  					"(".str_repeat('?,', count($field_values)-1)."?)";
				
			if(empty($types))
			{
				$types = str_repeat('s',count($field_values));
			}
				
			if(!$this->query($sql, $types, $field_values))
			{
				if($this->halt_on_error=='yes')
				{
					throw new DatabaseInsertException();
				}
			}else
			{
				return true;
			}
		}else
		{
			throw new DatabaseInsertException();
		}			
	}

	/**
	 * Replaces a row in a table
	 *
	 * @param string $table The table name
	 * @param string $index The field name to select on ( WHERE '$index'=1). This field must be present in the $fields parameter
	 * @param array $fields An associative array with fieldname=>value
	 * @param string $types The types of the parameters. possible values: i, d, s, b for integet, double, string and blob
	 * @param bool $trim Trim values in the array
	 * @return bool
	 */

	public function replace_row($table, $fields, $types, $trim=true)
	{
		return $this->insert_row($table, $fields, $types,$trim, true);
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
	}

	/**
	 * Returns the auto generated id used in the last query
	 *
	 * @return int
	 */
	public function insert_id()
	{
	}

	/**
	 * Sets the error and errno property
	 *
	 * @return void
	 */
	protected function set_error()
	{

	}

	/**
	 * Halts when an error occurs
	 *
	 * @param unknown_type $msg
	 */
	protected function halt($msg) {

		$this->set_error();

		if ($this->locked) {
			$this->unlock();
		}

		go_log(LOG_DEBUG, sprintf("<b>Database error:</b> %s<br>\n<b>MySQL Error</b>: %s (%s)<br>\n",
		$msg,
		$this->errno,
		$this->error));

		if($this->halt_on_error=='yes')
		{
			throw new Exception(sprintf("<b>Database error:</b> %s<br>\n<b>MySQL Error</b>: %s (%s)<br>\n",
			$msg,
			$this->errno,
			$this->error));
		}elseif($this->halt_on_error=='report')
		{
			echo sprintf("<b>Database error:</b> %s<br>\n<b>MySQL Error</b>: %s (%s)<br>\n",
			$msg,
			$this->errno,
			$this->error);
		}
	}

}
