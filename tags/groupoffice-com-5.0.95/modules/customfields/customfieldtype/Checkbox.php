<?php
class GO_Customfields_Customfieldtype_Checkbox extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'Checkbox';
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		return !empty($attributes[$key]) ? GO::t('yes') : GO::t('no');
	}
	
	public function formatFormInput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		$attributes[$key]=empty($attributes[$key]) || $attributes[$key]=="false" ? 0 : 1;
		
		return $attributes[$key];
	}
	
	public function fieldSql() {
		return "BOOLEAN NOT NULL DEFAULT '0'";
	}
}