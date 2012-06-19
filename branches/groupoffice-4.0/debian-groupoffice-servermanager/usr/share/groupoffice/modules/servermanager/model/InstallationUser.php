<?php

class GO_ServerManager_Model_InstallationUser extends GO_Base_Db_ActiveRecord {

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
		return 'sm_installation_users';
	}
	
	public function relations() {
		return array(
				'modules' => array('type'=>self::HAS_MANY, 'model'=>'GO_ServerManager_Model_InstallationUserModule', 'field'=>'user_id', 'delete'=>true)
				);
	}

}
