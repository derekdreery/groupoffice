<?php
class GO_Users_Customfields_Model_User extends GO_Customfields_Model_AbstractCustomFieldsRecord{
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Users_Model_CustomFieldsRecord 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	public function extendsModel() {
		return "GO_Base_Model_User";
	}
}