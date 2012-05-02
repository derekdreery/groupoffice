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
 * @package GO.modules.servermanager.model
 * @version $Id: example.php 7607 20120101Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
 
/**
 * The GO_ServerManager_Model_Installation model
 *

 */

class GO_ServerManager_Model_NewTrial extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_ServerManager_Model_NewTrial
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'sm_new_trials';
	}
	
	protected function init() {
		
		$this->columns['name']['required']=true;
		$this->columns['name']['unique']=true;
		$this->columns['name']['regex']='/^[a-z0-9-_]*$/';
		
		return parent::init();
	}
	
	protected function beforeSave() {
		
		$this->password = GO_Base_Util_String::randomPassword(6);
		$this->key = md5($this->password.$this->name);
		
		return parent::beforeSave();
	}
	
	public function validate() {
		$installation = new GO_ServerManager_Model_Installation();
		$installation->name = $this->name.'.'.GO::config()->servermanager_wildcard_domain;
		
		if(!$installation->validate()){
			$this->setValidationError('name', implode("\n", $installation->getValidationErrors()));
		}
							
		return parent::validate();
	}


	
}
