<?php
class GO_Calendar_Model_Settings extends GO_Base_Db_ActiveRecord{
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Calendar_Model_Settings 
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'cal_settings';
	}
	
	public function primaryKey() {
		return 'user_id';
	}
	
}