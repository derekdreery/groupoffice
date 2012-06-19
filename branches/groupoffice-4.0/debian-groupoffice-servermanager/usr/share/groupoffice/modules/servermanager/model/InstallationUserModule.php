<?php

class GO_ServerManager_Model_InstallationUserModule extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_ServerManager_Model_Installation
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'sm_installation_user_modules';
	}
	
	public function primaryKey() {
		return array('user_id','module_id');
	}

}
