<?php
class GO_Customfields_Customfieldtype_Number extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'Number';
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		return GO_Base_Util_Number::localize($attributes[$key],$this->field->number_decimals);
	}
	
	public function formatFormOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {		
		if (empty($attributes[$key]) && $attributes[$key]!=0)
			return null;
		else {
			return GO_Base_Util_Number::localize($attributes[$key],$this->field->number_decimals);
		}
	}
	
	public function formatFormInput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		if (empty($attributes[$key]) && $attributes[$key]!=0)
			return null;
		else
			return GO_Base_Util_Number::unlocalize($attributes[$key]);
	}
	
	public function fieldSql() {
		return 'DOUBLE NULL';
	}
	
	public function validate($value) {
		if($value===false || (!empty($value) && !is_numeric($value)))
			return false;
		
		return parent::validate($value);
	}
	
	public function getValidationError(){
		return GO::t('numberValidationError','customfields');
	}
}