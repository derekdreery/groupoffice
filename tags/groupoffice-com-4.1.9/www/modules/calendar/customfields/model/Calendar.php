<?php
class GO_Calendar_Customfields_Model_Calendar extends GO_Customfields_Model_AbstractCustomFieldsRecord{
	public function extendsModel() {		
		return "GO_Calendar_Model_Calendar";
	}
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Calendar_Model_CalendarCustomFieldsRecord 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
}