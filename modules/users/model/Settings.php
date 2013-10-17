<?php

class GO_Users_Model_Settings extends GO_Base_Model_AbstractSettingsCollection{

	public $register_email_subject;
	public $register_email_body;
	
	public $globalsettings_show_tab_addresslist;	
	
	public function myPrefix() {
		return '';
	}
	
}