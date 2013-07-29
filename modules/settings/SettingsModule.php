<?php
class GO_Settings_SettingsModule extends GO_Base_Module{
	public static function initListeners() {
		
		$c = new GO_Core_Controller_Auth();
		$c->addListener('inlinescripts', 'GO_Settings_SettingsModule', 'inlinescripts');
		
		return parent::initListeners();
	}
	
	public static function inlinescripts(){

		
		
		$t = GO::config()->get_setting('login_screen_text_enabled');
		if(!empty($t)){
			$login_screen_text = GO::config()->get_setting('login_screen_text');
			$login_screen_text_title = GO::config()->get_setting('login_screen_text_title');
			
			echo 'GO.mainLayout.on("login", function(mainLayout){mainLayout.msg("'.GO_Base_Util_String::escape_javascript ($login_screen_text_title).'", "'.GO_Base_Util_String::escape_javascript ($login_screen_text).'", 3600, 400);});';
					
		}

	}
	
	public function adminModule() {
		return true;
	}
}