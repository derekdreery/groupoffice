<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */


/**
 * The Router class which looks up the controller by the request URL.
 * 
 * URL Should be like index.php?r=module/controller/method&param=value
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id: config.class.inc.php 7687 2011-06-23 12:00:34Z mschering $
 * @copyright Copyright Intermesh BV.
 * @package GO.base 
 */
class GO_Base_Router{
	
	/**
	 * Analyzes the request URL and finds the controller.
	 * 
	 * URL Should be like index.php?r=module/controller/method&param=value
	 * 
	 * If a controller consist of two words then the second word should start with
	 * a capital letter.
	 * 
	 * @todo Handle multi request with:
	 * 
	 * ?r=notes/note/grid|notes/category/grid
	 * 
	 * array(
	 * 'notes'=>array(
	 * r=notes/note/grid
	 * id=1
	 * ),
	 * 'categories'=>array(
	 * r=>notes/category/grid
	 * param=>
	 * )
	 * )
	 */
	
	
	public function runController($params=false){
		
		if(!$params){
			if(PHP_SAPI=='cli'){
				$params = GO_Base_Util_Cli::parseArgs();
			}else
			{
				$params=$_REQUEST;
			}
		}
		
		$r = !empty($params['r']) ?  explode('/', $params['r']): array();		
		
		$first = isset($r[0]) ? ucfirst($r[0]) : 'Core';
		
		if(file_exists(GO::config()->root_path.'controller/'.$first.'Controller.php')){
			//this is a controller name that belongs to the Group-Office framework
			$module='Core';
			$controller=$first;
			$action = isset($r[1]) ? $r[1] : '';
			
		}else
		{
			//it must be pointing to a module
			$module=strtolower($r[0]);
			$controller=isset($r[1]) ? ucfirst($r[1]) : 'Default';
			$action = isset($r[2]) ? $r[2] : '';
		}
				
		$controllerClass='GO_';
		
		if(!empty($module))
			$controllerClass.=ucfirst($module).'_';
		
		$controllerClass.='Controller_'.$controller;
		
		$controller = new $controllerClass;
		$controller->run($action, $params);		
	}
}
