<?php
class GO_Customfields_Customfieldtype_Datetime extends GO_Customfields_Customfieldtype_Date{
	
	public function name(){
		return 'Date time';
	}
	
	public function formatFormOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model){
		$unixtime = strtotime($attributes[$key]);
		return $attributes[$key]=GO_Base_Util_Date::get_timestamp($unixtime, true);
	}
	public function formatFormInput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {

//		if(empty($attributes[$key]))
//			return null;
//		
//		$time = $attributes[$key];
//		
//		if(isset($attributes[$key."_hour"]))
//			$time .= ' '.$attributes[$key."_hour"].':'.$attributes[$key."_min"];
		
		return GO_Base_Util_Date::to_db_date($attributes[$key], true);
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		return GO_Base_Util_Date::get_timestamp(strtotime($attributes[$key]), true);
	}
	
	public function fieldSql() {
		return 'DATETIME NULL';
	}
}