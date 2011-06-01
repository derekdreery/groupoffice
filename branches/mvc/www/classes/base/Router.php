<?php

class GO_Router{
	
	public function despatch(){
		$r = explode('/', $_REQUEST['r']);
		
		$module=strtolower($r[0]);
		$controller=ucfirst($r[1]);
		$action=ucfirst($r[2]);
		
		//$module = GO::modules()->modules[$r[0]];
		
		//if($module)
		
		$controllerFile = GO::config()->root_path.'modules/'.$module.'/controllers/'.$controller.'.php';		
		require_once($controllerFile);
		
		$controller='GO_Controller_'.$controller;
		
		$controller = new $controller;
		$controller->init();
		$controller->run($action);	
	}	
}
