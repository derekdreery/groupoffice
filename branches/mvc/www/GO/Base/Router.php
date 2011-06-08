<?php

class GO_Base_Router{
	
	public function despatch(){
		
		//router.php?r=notes/Note/get&note_id=1
		
		$r = explode('/', $_REQUEST['r']);
		
		$module=strtolower($r[0]);
		$controller=ucfirst($r[1]);
		$action=ucfirst($r[2]);
		
		//$module = GO::modules()->modules[$r[0]];
		
		//if($module)
		
		$controllerFile = GO::config()->root_path.'modules/'.$module.'/controller/'.$controller.'.php';		
		require_once($controllerFile);
		
		$controller='GO_Controller_'.$controller;
		
		$output=empty($_REQUEST['output']) ? 'json' : $_REQUEST['output'];
		
		$controller = new $controller;
		$controller->init($output);
		$controller->run($action);	
	}	
}
