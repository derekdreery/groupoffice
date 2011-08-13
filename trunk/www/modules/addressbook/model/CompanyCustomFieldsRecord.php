<?php
class GO_Addressbook_Model_CompanyCustomFieldsRecord extends GO_Customfields_Model_AbstractCustomFieldsRecord{
	const linkType = 3;
	
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