<?php
class GO_Customfields_Customfieldtype_Date extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'Date';
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		return GO_Base_Util_Date::format($attributes[$key], false);
	}
	
	public function formatFormOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model){
		return GO_Base_Util_Date::format($attributes[$key], false);
	}
	public function formatFormInput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		return GO_Base_Util_Date::to_db_date($attributes[$key]);
	}
	
	public function fieldSql() {
		return 'DATE NULL';
	}
}