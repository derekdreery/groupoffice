<?php

class GO_Base_Export_Settings extends GO_Base_Model_AbstractSettingsCollection{

	public $export_include_headers;
	public $export_human_headers;
	public $export_include_hidden;
	
	public function myPrefix() {
		return '';
	}
			
}