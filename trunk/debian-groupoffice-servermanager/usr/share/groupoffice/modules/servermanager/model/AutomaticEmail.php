<?php
class GO_ServerManager_Model_AutomaticEmail extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_ServerManager_Model_AutomaticEmail
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'sm_auto_email';
	}
	
	protected function init() {
		$this->columns['mime']['required']=true;		
		return parent::init();
	}
		
}
