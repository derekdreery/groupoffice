<?php
class GO_Base_Model_Setting extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Base_Model_Group 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName() {
		return 'go_settings';
	}
	
	public function defaultAttributes() {
		return array('user_id'=>0);
	}
}

