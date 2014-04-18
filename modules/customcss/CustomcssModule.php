<?php

class GO_Customcss_CustomcssModule extends GO_Base_Module {

	public static function initListeners() {
		
		$c = new GO_Core_Controller_Auth();
		$c->addListener('head', 'GO_Customcss_CustomcssModule', 'head');
		
		return parent::initListeners();
	}
	public static function head() {

		if (file_exists(GO::config()->file_storage_path . 'customcss/style.css'))
			echo '<style>' . file_get_contents(GO::config()->file_storage_path . 'customcss/style.css') . '</style>' . "\n";
	}
	
	public function adminModule() {
		return true;
	}

}