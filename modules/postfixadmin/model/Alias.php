<?php
class GO_Postfixadmin_Model_Alias extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Postfixadmin_Model_Alias 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function tableName() {
		return 'pa_aliases';
	}
	
	protected function init() {
		$this->columns['address']['unique']=true;
		$this->columns['address']['required']=true;
		
		return parent::init();
	}
}