<?php
class db{

	/* public: connection parameters */
	var $Host     = "";
	var $Database = "";
	var $User     = "";
	var $Password = "";

	/* public: configuration parameters */
	var $Debug         = 0;     ## Set to 1 for debugging messages.
	var $Halt_On_Error = "yes"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
	var $Seq_Table     = "go_db_sequence";

	/* public: result array and current row number */
	var $Record   = array();
	var $Row;

	/* public: current error number and error text */
	var $Errno    = 0;
	var $Error    = "";

	/* public: this is an api revision, not a CVS revision. */
	var $type     = "mysqli";

	/* private: link and query handles */
	var $Link_ID  = 0;
	var $Query_ID = 0;

	var $locked   = false;      ## set to true while we have a lock

	/* public: constructor 
	function __construct($config=null) {
		$this->set_config($config);
	}*/
	
	function db($config=null)
	{
		$this->set_config($config);
	}

	function set_config($config=null)
	{
		global $GO_CONFIG;
		 
		if(!isset($config) && isset($GO_CONFIG))
		{
			$config = $GO_CONFIG;
		}
		 
		if(isset($config))
		{
			if (isset($config->db_host)) {
				$this->Host = $config->db_host;
		  }
		  if (isset($config->db_name)) {
		  	$this->Database = $config->db_name;
		  }
		  if (isset($config->db_user)) {
		  	$this->User = $config->db_user;
		  }
		  if (isset($config->db_pass)) {
		  	$this->Password = $config->db_pass;
		  }
		}		
		//$this->Link_ID->query('SET NAMES UTF-8');
	}
	
	function connect()
	{
		if(!$this->Link_ID)
		{
			$this->Link_ID = new mysqli($this->Host, $this->User, $this->Password, $this->Database);
			$this->Link_ID->set_charset("utf8");
		}
		return $this->Link_ID;
	}
	
	function query($sql)
	{
		$this->connect();
		
		$this->Query_ID = $this->Link_ID->query($sql);
		return $this->Query_ID; 
	}

	function query_id() {
		return $this->Query_ID;
	}

	function found_rows(){
		$this->query("SELECT FOUND_ROWS() as found;");
		$this->next_record();
		return $this->f('found');
	}


	/* public: walk result set */
	function next_record($result_type=MYSQL_ASSOC) {
		if (!$this->Query_ID) {
			$this->halt("next_record called with no query pending.");
			return 0;
		}

		$this->Record = $this->Query_ID->fetch_assoc();
		$this->Row   += 1;
	
		return $this->Record;
	}


	/* public: table locking */
	function lock($table, $mode = "write") {
		$query = "lock tables ";
		if(is_array($table)) {
			while(list($key,$value) = each($table)) {
				// text keys are "read", "read local", "write", "low priority write"
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

	function unlock() {
		// set before unlock to avoid potential loop
		$this->locked = false;

		if(!$this->query("unlock tables")) {
			$this->halt("unlock() failed.");
			return false;
		}
		return true;
	}

	function f($Name) {
		if (isset($this->Record[$Name])) {
			return $this->Record[$Name];
		}
	}

	function p($Name) {
		if (isset($this->Record[$Name])) {
			print $this->Record[$Name];
		}
	}

	/* public: sequence numbers */
	function nextid($seq_name) {
		/* if no current lock, lock sequence table */
		if(!$this->locked) {
			if($this->lock($this->Seq_Table)) {
				$locked = true;
			} else {
				$this->halt("cannot lock ".$this->Seq_Table." - has it been created?");
				return 0;
			}
		}

		/* get sequence number and increment */
		$q = sprintf("select nextid from %s where seq_name = '%s'",
		$this->Seq_Table,
		$seq_name);
		if(!$this->query($q)) {
			$this->halt('query failed in nextid: '.$q);
			return 0;
		}

		/* No current value, make one */
		if(!$this->next_record()) {
			$currentid = 0;
			$q = sprintf("insert into %s values('%s', %s)",
			$this->Seq_Table,
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
		$this->Seq_Table,
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
	
	function num_rows() {
		return $this->Query_ID->num_rows;
	}

	/* private: error handling */
	function halt($msg) {
		$this->Error = @mysql_error($this->Link_ID);
		$this->Errno = @mysql_errno($this->Link_ID);

		if ($this->locked) {
			$this->unlock();
		}
		
		go_log(LOG_DEBUG, sprintf("<b>Database error:</b> %s<br>\n<b>MySQL Error</b>: %s (%s)<br>\n",
		 $msg,
		 $this->Errno,
		 $this->Error));

		if($this->Halt_On_Error=='yes')
		{
			throw new Exception(sprintf("<b>Database error:</b> %s<br>\n<b>MySQL Error</b>: %s (%s)<br>\n",
				$msg,
				$this->Errno,
				$this->Error));
		}else
		{
			echo sprintf("<b>Database error:</b> %s<br>\n<b>MySQL Error</b>: %s (%s)<br>\n",		
				$msg,
				$this->Errno,
				$this->Error);
		}
	}

	function haltmsg($msg) {
		go_log(LOG_DEBUG, sprintf("<b>Database error:</b> %s<br>\n<b>MySQL Error</b>: %s (%s)<br>\n",
		 $msg,
		 $this->Errno,
		 $this->Error));
		 
		if($this->Halt_On_Error=='yes')
		{
			throw new Exception(sprintf("<b>Database error:</b> %s<br>\n<b>MySQL Error</b>: %s (%s)<br>\n",		
				$msg,
				$this->Errno,
				$this->Error));
		}else
		{
			echo sprintf("<b>Database error:</b> %s<br>\n<b>MySQL Error</b>: %s (%s)<br>\n",		
				$msg,
				$this->Errno,
				$this->Error);
		}

	}

	function update_row($table, $index, $fields, $trim=true)
	{
		if(!is_array($fields))
		{
			var_dump($fields);
			exit('Invalid update row');
		}
		if(!is_array($index))
		{
			$index = array($index);
		}
		foreach($fields as $key => $value)
		{
			if(!in_array($key, $index))
			{
				$updates[] = "`$key`='".$this->escape($value, $trim)."'";
			}
		}
		if(isset($updates))
		{
			$sql = "UPDATE `$table` SET ".implode(',',$updates)." WHERE ";

			$indexes=array();
			foreach($index as $subindex)
			{
				$indexes[]="`$subindex`='".$fields[$subindex]."'";
			}
			$sql .= implode(' AND ', $indexes);

			if(!$this->query($sql))
			{
				if($this->Halt_On_Error=='yes')
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

	function insert_row($table, $fields, $trim=true)
	{
		if(!is_array($fields))
		{
			var_dump($fields);
			exit('Invalid insert row');
		}
		
		foreach($fields as $key => $value)
		{
			$field_names[] = $key;
			$field_values[] = $this->escape($value, $trim);
		}
		if(isset($field_names))
		{
			$sql = "INSERT INTO `$table` (`".implode('`,`', $field_names)."`) VALUES ".
  					"('".implode("','", $field_values)."')";

			if(!$this->query($sql))
			{
				if($this->Halt_On_Error=='yes')
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

	function replace_row($table, $fields, $trim=true)
	{
		foreach($fields as $key => $value)
		{
			$field_names[] = $key;
			$field_values[] = $this->escape($value, $trim);
		}
		if(isset($field_names))
		{
			$sql = "REPLACE INTO `$table` (`".implode('`,`', $field_names)."`) VALUES ".
  					"('".implode("','", $field_values)."')";

			if(!$this->query($sql))
			{
				if($this->Halt_On_Error=='yes')
				{
					throw new DatabaseReplaceException();
				}
			}else
			{
				return true;
			}
		}
		return false;
	}
	
	function escape($value, $trim=true)
	{
		$this->connect();
		
		if($trim)
			$value = trim($value);
			
		return $this->Link_ID->real_escape_string($value);
	}
}
