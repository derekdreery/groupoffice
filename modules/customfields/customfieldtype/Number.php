<?php

namespace GO\Customfields\Customfieldtype;


class Number extends AbstractCustomfieldtype{
	
	public function name(){
		return 'Number';
	}
	
	public function formatDisplay($key, &$attributes, \GO\Customfields\Model\AbstractCustomFieldsRecord $model) {
		return \GO\Base\Util\Number::localize($attributes[$key],$this->field->number_decimals);
	}
	
	public function formatFormOutput($key, &$attributes, \GO\Customfields\Model\AbstractCustomFieldsRecord $model) {		
		if (empty($attributes[$key]) && $attributes[$key]!=0)
			return null;
		else {
			return \GO\Base\Util\Number::localize($attributes[$key],$this->field->number_decimals);
		}
	}
	
	public function formatFormInput($key, &$attributes, \GO\Customfields\Model\AbstractCustomFieldsRecord $model) {
		if (empty($attributes[$key]) && $attributes[$key]!=0)
			return null;
		else
			return \GO\Base\Util\Number::unlocalize($attributes[$key]);
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
		return \GO::t('numberValidationError','customfields');
	}
}