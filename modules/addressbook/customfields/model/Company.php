<?php
class GO_Addressbook_Customfields_Model_Company extends GO_Customfields_Model_AbstractCustomFieldsRecord{
	public function extendsModel() {		
		return "GO_Addressbook_Model_Company";
	}
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Addressbook_Model_CompanyCustomFieldsRecord 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
}