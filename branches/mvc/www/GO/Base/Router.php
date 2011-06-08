<?php

class GO_Base_Router{
	
	/**
	 * Analyzes the request URL and finds the controller.
	 * 
	 * URL Should be like index.php?r=module/controller/method&param=value
	 */
	public function run(){
		
		$r = isset($_REQUEST['r']) ? explode('/', $_REQUEST['r']) :array();
		
		$module=isset($r[0]) ? strtolower($r[0]) : 'Base';;
		$controller=isset($r[1]) ? ucfirst($r[1]) : 'Main';
		$action=isset($r[2]) ? ucfirst($r[2]) : 'Index';
		
		//$module = GO::modules()->modules[$r[0]];
		
		//if($module)
		
		//$controllerFile = GO::config()->root_path.'modules/'.$module.'/controller/'.$controller.'.php';		
		//require_once($controllerFile);
		
		$controller='GO_'.ucfirst($module).'_Controller_'.$controller;
		
		$output=empty($_REQUEST['output']) ? 'json' : $_REQUEST['output'];
		
		$controller = new $controller;
		$controller->init($module,$output);
		$controller->run($action);	
	}	
}
