<?php
class GO_Users_Model_CustomFieldsRecord extends GO_Customfields_Model_AbstractCustomFieldsRecord{
	
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
	const linkType = 8;
}