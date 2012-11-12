<?php
class GO_Base_Db_PDO extends PDO{
//	private $_dsn;
	public function __construct($dsn, $username, $passwd, $options=null) {
		
//		GO::debug("Connect: $dsn, $username, ***");
		
//		GO::debugCalledFrom(2);
		
//		$this->_dsn = $dsn;
		
		parent::__construct($dsn, $username, $passwd, $options);
		
		$this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$this->setAttribute( PDO::ATTR_STATEMENT_CLASS, array( 'GO_Base_Db_ActiveStatement', array() ) );

		//todo needed for foundRows
		$this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true); 

		$this->query("SET NAMES utf8");

		if(GO::config()->debug){
			//GO::debug("Setting MySQL sql_mode to TRADITIONAL");
			$this->query("SET sql_mode='TRADITIONAL'");
		}
	}
	
//	public function __destruct() {
//		GO::debug("Disconnect. ".$this->_dsn);
//		//parent::__destruct();
//	}
}