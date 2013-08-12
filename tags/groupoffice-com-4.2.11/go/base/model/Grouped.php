<?php
/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.db
 */

/**
 * Model to perform grouped SQL queries. For example SUM, AVG, COUNT etc.
 *
 * @package GO.base.db
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl> 
 * 
 */

class GO_Base_Model_Grouped extends GO_Base_Model {

	
	private $_attributes=array();
	/**
	 * 
	 * @param string $className
	 * @return GO_Base_Model_Grouped
	 */
	public static function model($className = null) {
		return parent::model($className);
	}
	
	public function __set($name, $value) {
		$this->_attributes[$name]=$value;
	}
	
	public function __get($name) {
		return $this->_attributes[$name];
	}
	
	/**
	 * Execute a grouped query and return the statement. This class may be extended
	 * so you can document loaded properties or implement additional functions.
	 * 
	 * @param string $modelName
	 * @param string $selectFields
	 * @param GO_Base_Db_FindParams $findParams
	 * @return GO_Base_Db_ActiveStatement
	 */
	public function load($modelName, $selectFields, GO_Base_Db_FindParams $findParams=null){
		
		if(!isset($findParams))
			$findParams = GO_Base_Db_FindParams::newInstance ();
		
		$findParams->ignoreAcl()
						->select($selectFields)
						->fetchClass(get_class($this));

		$stmt = GO::getModel($modelName)->find($findParams);
		
		return $stmt;
	}

}