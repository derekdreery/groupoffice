<?php

class GO_Base_Router{
	
	/**
	 * Analyzes the request URL and finds the controller.
	 * 
	 * URL Should be like index.php?r=module/controller/method&param=value
	 */
	public function run(){
		
		$arr = isset($_REQUEST['r']) ? explode('/', $_REQUEST['r']) :array();
		
		if(count($arr)==3)
			$module = strtolower(array_shift($arr));
		else
			$module=false;
		
		$controller = array_shift($arr);
		if($controller)
			$controller=ucfirst($controller);
		else
			$controller='Default';
		
		$action = array_shift($arr);
		
		$controllerClass='GO_';
		
		if(!empty($module))
			$controllerClass.=ucfirst($module).'_';
		
		$controllerClass.='Controller_'.$controller;
	
		//$output=empty($_REQUEST['output']) ? 'json' : $_REQUEST['output'];
		
		$controller = new $controllerClass;
		$controller->init($module);
		$controller->run($action);	
	}	
}
