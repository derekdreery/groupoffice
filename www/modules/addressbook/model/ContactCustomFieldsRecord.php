<?php
class GO_Addressbook_Model_ContactCustomFieldsRecord extends GO_Customfields_Model_AbstractCustomFieldsRecord{
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Addressbook_Model_ContactCustomFieldsRecord 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	public function linkType(){
		return 2;
	}
}