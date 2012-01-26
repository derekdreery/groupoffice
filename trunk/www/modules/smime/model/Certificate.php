<?php
class GO_Smime_Model_Certificate extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Smime_Model_Certificate
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function primaryKey() {
		return 'account_id';
	}


	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'smi_pkcs12';
	}
}