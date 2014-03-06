<?php

namespace GO\Base\Db;
use GO;

class PDO extends \PDO{
	public function __construct($dsn, $username, $passwd, $options=null) {
		parent::__construct($dsn, $username, $passwd, $options);
		
		$this->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->setAttribute(\PDO::ATTR_PERSISTENT, true);
//		$this->setAttribute( PDO::ATTR_STATEMENT_CLASS, array( 'GO\Base\Db\ActiveStatement', array() ) );

		//todo needed for foundRows
		$this->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true); 

		$this->query("SET NAMES utf8");

		if(GO::config()->debug){
			//\GO::debug("Setting MySQL sql_mode to TRADITIONAL");
			$this->query("SET sql_mode='TRADITIONAL'");
		}
	}
}