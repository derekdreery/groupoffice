<?php
class GO_Customfields_Customfieldtype_Text extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'Text';
	}
	
	public function includeInSearches() {
		return true;
	}
}