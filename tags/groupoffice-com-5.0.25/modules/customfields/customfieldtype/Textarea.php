<?php
class GO_Customfields_Customfieldtype_Textarea extends GO_Customfields_Customfieldtype_Text{
	
	public function name(){
		return 'Textarea';
	}
	
	public function fieldSql(){
		return "TEXT NULL";
	}
	
	public function selectForGrid(){
		return false;
	}
}