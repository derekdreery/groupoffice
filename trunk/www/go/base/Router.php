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
	
	/**
	 * Get the currently active controller for this request.
	 * 
	 * @return GO_Base_Controller_AbstractController 
	 */
	public function getController(){
		return $this->_controller;
	}
	
	/**
	 * Get the currently processing controller action.
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
				if(empty($params['r'])){
					echo "\nGroup-Office CLI - Copyright Intermesh BV.\n\n".
						"You must pass a controller route to use the command line script.\n".
						"eg.:\n\n".
						"sudo -u www-data php index.php -c=/path/to/config.php -r=maintenance/upgrade --param=value\n\n";
					exit();
					
					
				}elseif(isset($params['u']) && isset($params['p']))
				{
					$user = GO::session()->login($params['u'], $params['p']);
					if(!$user){
						die("Login failed for user ".$params['u']."\n");
					}
					unset($params['u'],$params['p']);
				}
			}else
			{
				$params=$_REQUEST;				
			}
		}
		
		$r = !empty($params['r']) ?  explode('/', $params['r']): array();		
		
		$first = isset($r[0]) ? ucfirst($r[0]) : 'Auth';
		
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
		
		$this->_action=$action;		
		$this->_controller = new $controllerClass;
		$this->_controller->run($action, $params);		
	}
}
