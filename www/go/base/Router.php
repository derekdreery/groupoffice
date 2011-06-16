<?php

class GO_Base_Router{
	
	/**
	 * Analyzes the request URL and finds the controller.
	 * 
	 * URL Should be like index.php?r=module/controller/method&param=value
	 * 
	 * If a controller consist of two words then the second word should start with
	 * a capital letter.
	 */
	public function run(){
		
		$r = isset($_REQUEST['r']) ? explode('/', $_REQUEST['r']) :array();
		
		$first = isset($r[0]) ? ucfirst($r[0]) : 'Core';
		
		if(file_exists(GO::config()->root_path.'controller/'.$first.'.php')){
			//this is a controller name that belongs to the Group-Office framework
			$module='';
			$controller=$first;
			$action = isset($r[1]) ? $r[1] : '';
			
		}else
		{
			//it must be pointing to a module
			$module=strtolower($r[0]);
			$controller=isset($r[1]) ? $r[1] : 'Default';
			$action = isset($r[2]) ? $r[2] : '';
		}
				
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
