<?php
/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base
 */


/**
 * Router
 * 
 * The Router class which looks up the controller by the request URL. 
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
	 */
	
	private $_controller;
	
	private $_action;
	
	private $_r;
	
	/**
	 * Get the controller route. eg. email/message/view
	 * 
	 * @return string 
	 */
	public function getControllerRoute(){
		return $this->_r;
	}
	
	/**
	 * Get the currently active controller for this request.
	 * 
	 * @return GO_Base_Controller_AbstractController 
	 */
	public function getController(){
		return $this->_controller;
	}
	
	/**
	 * Get the currently processing controller action in lowercase and without the
	 * action prefix.
	 * 
	 * @return string
	 */
	public function getControllerAction(){
		return $this->_action;
	}	
	
	/**
	 * Runs a controller action with the given params
	 * 
	 * @param array $params 
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
		$this->_r=isset($params['r']) ? $params['r'] : "";
					
		if(GO::config()->debug || GO::config()->debug_log){
			$log = 'Controller route r=';
			if(isset($params['r']))
				$log .= $params['r'];
			else 
				$log = 'No r parameter given';				

			GO::debug($log);
		}
	
		$first = isset($r[0]) ? ucfirst($r[0]) : 'Auth';

		if(empty($r[2]) && file_exists(GO::config()->root_path.'controller/'.$first.'Controller.php')){
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
		
		$action = strtolower($action);
				
		$controllerClass='GO_';
		
		if(!empty($module))
			$controllerClass.=ucfirst($module).'_';
		
		$controllerClass.='Controller_'.$controller;
		
		$this->_action=$action;		
		
		if(!class_exists($controllerClass)){
			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not Found");
			
			if(empty($_SERVER['QUERY_STRING']))
				$_SERVER['QUERY_STRING']="[EMPTY QUERY_STRING]";

			
			$errorMsg = "Controller('".$controllerClass."') not found: ".$_SERVER['QUERY_STRING']." ".var_export($_REQUEST, true);

			echo '<h1>404 Not found</h1>';
			echo '<p>'.$errorMsg.'</p>';
			
			if(GO::config()->debug)
				trigger_error($errorMsg, E_USER_ERROR);
		}
		
		try{
			$this->_controller = new $controllerClass;
			$this->_controller->run($action, $params);		
		}catch(GO_Base_Exception_NotFound $e){
			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not Found");
			
			if(empty($_SERVER['QUERY_STRING']))
				$_SERVER['QUERY_STRING']="[EMPTY QUERY_STRING]";
			
			$errorMsg ="Controller action '".$action." not found in controller class '".$controllerClass."': ".$_SERVER['QUERY_STRING']." ".var_export($_REQUEST, true);

			echo '<h1>404 Not found</h1>';
			echo '<p>'.$errorMsg.'</p>';

			if(GO::config()->debug)
				trigger_error($errorMsg, E_USER_ERROR);
		}
	}
	

}
