<?php
class GO_Notes_Model_CustomFieldsRecord extends GO_Customfields_Model_AbstractCustomFieldsRecord{
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Notes_Model_CustomFieldsRecord 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function linkType(){
		return 4;
	}
}