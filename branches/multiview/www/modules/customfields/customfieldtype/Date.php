<?php
class GO_Customfields_Customfieldtype_Date extends \GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'Date';
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		return \GO\Base\Util\Date::format($attributes[$key], false);
	}
	
	public function formatFormOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model){
		return \GO\Base\Util\Date::format($attributes[$key], false);
	}
	public function formatFormInput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		return \GO\Base\Util\Date::to_db_date($attributes[$key]);
	}
	
	public function fieldSql() {
		return 'DATE NULL';
	}
}