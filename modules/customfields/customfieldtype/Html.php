<?php
class GO_Customfields_Customfieldtype_Html extends GO_Customfields_Customfieldtype_Textarea{
	
	public function name(){
		return 'HTML';
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model){
		return $attributes[$key];
	}
	
	public function selectForGrid(){
		return false;
	}
}