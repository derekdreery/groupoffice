<?php
class GO_Log_LogModule extends GO_Base_Module{
	/**
	 * Initialize the listeners for the ActiveRecords
	 */
	public static function initListeners(){	
		$c = new GO_Core_Controller_Maintenance();
		$c->addListener('servermanagerReport', 'GO_Log_LogModule', 'rotateLog');
		
	}	
	
	public function adminModule() {
		return true;
	}
	
	public static function rotateLog(){
		
		echo "Running log rotate for ".GO::config()->id."\n";		
		$controller = new GO_Log_Controller_Log();
		$controller->run("rotate");			
	}
}