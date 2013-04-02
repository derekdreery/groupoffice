<?php
class GO_Customfields_Customfieldtype_Heading extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'Heading';
	}
	
	public function formatFormOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model){
		return '';
	}
	public function formatFormInput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		return '';
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		return '';
	}
	
	
	public function selectForGrid(){
		return false;
	}
}