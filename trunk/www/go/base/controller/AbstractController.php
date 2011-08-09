<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */


/**
 * Abstract class for al Group-Office controllers.
 */
abstract class GO_Base_Controller_AbstractController extends GO_Base_Observable {
	
	/**
	 *
	 * @var mixed A JSON outputstream for example. 
	 */
	protected $outputStream;
	
	/**
	 *
	 * @var string The module the controller belongs too. 
	 */
	protected $module;
	
	/**
	 *
	 * @var string The default action when none is specified. 
	 */
	protected $defaultAction='Index';

	/**
	 *
	 * @var array See method addPermissionLevel
	 */
	protected $requiredPermissionLevels=array(
			
	);
	
	/**
	 * Inititalizes the controller
	 * 
	 * @param string $module Name of the module this controller belongs too
	 * @param string $output Type of output eg. json or html
	 */
	public function init($module){
		//header('Content-Type: text/plain');
		$this->module=$module;
		
		
	}
	
	/**
	 * Outputs data directly to the standard output
	 * 
	 * @param mixed $str 
	 */
	protected function output($str){
		
		if(!isset($this->outputStream)){
			$output=empty($_REQUEST['output']) ? 'Json' : ucfirst($_REQUEST['output']);
			$outputClass = 'GO_Base_OutputStream_OutputStream'.$output;
			$this->outputStream = new $outputClass;
		}
		$this->outputStream->write($str);
	}
	
	/**
	 * Includes the file from the views folder
	 * 
	 * @param string $viewName 
	 */
	protected function render($viewName){
		global $lang;
		
		header('Content-Type: text/html; charset=UTF-8');
		
		if(empty($this->module)){
			require(GO::config()->root_path.'themes/'.GO::view().'/'.$viewName.'.php');
		}else
		{
			require(GO::modules()->modules[$this->module].'themes/'.GO::view().'/'.$viewName.'.php');
		}
	}
	
	/**
	 * Adds a permission check on an acl ID.
	 * 
	 * @param int $aclId
	 * @param int $requiredPermissionLevel See GO_SECURITY constants
	 * @param string $action By default it applies to all actions but you may specify a specific action here.
	 */
	protected function addPermissionCheck($aclId, $requiredPermissionLevel, $action='*'){
		$this->requiredPermissionLevels[$action]=array('aclId'=>$aclId, 'requiredPermissionLevel'=>$requiredPermissionLevel);
	}
	/**
	 * Checks 
	 * 
	 * @param type $action
	 * @return type boolean
	 */
	public function checkPermissions($action){
		if(isset($this->requiredPermissionLevels[$action])){
			return true;// $GLOBALS['GO_SECURITY']->hasPermission($this->requiredPermissionLevels[$action]['aclId'])>=$this->requiredPermissionLevels[$action]['requiredPermissionLevel'];
		}elseif($action!='*'){
			return $this->checkPermissions('*');
		}else
		{
			return true;
		}
	}
	
	/**
	 * Runs a method of this controller. If $action is save then it will run
	 * actionSave of your extended class.
	 * 
	 * @param string $action 
	 */
	public function run($action=''){

		try {
			if(!$this->checkPermissions($action)){
				throw new AccessDeniedException();
			}

			if(empty($action))
				$action=$this->defaultAction;
			else
				$action=ucfirst($action);

			$methodName='action'.$action;

			$method=new ReflectionMethod($this, $methodName);
			if($method->getNumberOfParameters()>0)
				$this->runWithParams($method, $_REQUEST);
			else
				$this->$methodName();
		} catch (Exception $e) {
			$response['success'] = false;
			$response['feedback'] = !empty($response['feedback']) ? $response['feedback'] : '';
			$response['feedback'] .= "\r\n\r\n".$e->getMessage();
			
			if(GO::config()->debug)
				$response['trace']=$e->getTraceAsString();
			
			echo json_encode($response);exit();
		}
	}
	
	/**
	 * Executes a method of an object with the supplied named parameters.
	 * This method is internally used.
	 * @param ReflectionMethod $method the method reflection
	 * @param array $params the named parameters
	 * @return boolean whether the named parameters are valid
	 * @since 4.0
	 */
	protected function runWithParams($method, $params)
	{
		$ps=array();
		foreach($method->getParameters() as $i=>$param)
		{
			$name=$param->getName();
			if(isset($params[$name]))
			{
				if($param->isArray())
					$ps[]=is_array($params[$name]) ? $params[$name] : array($params[$name]);
				else if(!is_array($params[$name]))
					$ps[]=$params[$name];
				else
					return false;
			}
			else if($param->isDefaultValueAvailable())
				$ps[]=$param->getDefaultValue();
			else
				return false;
		}
		$method->invokeArgs($this,$ps);
		return true;
	}
	
	/**
	 * This default action should be overrriden
	 */
	protected function actionIndex(){
		
	}
}