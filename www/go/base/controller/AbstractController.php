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
 * 
 * Any function that starts with action will be publicly accessible by:
 * 
 * index.php?r=module/controllername/functionNameWithoutAction&security_token=1233456
 * 
 * This function will be called with one parameter which holds all request
 * variables.
 * 
 * A security token must be supplied in each request to prevent cross site 
 * request forgeries.
 * 
 * The functions must return a response object. In case of ajax controllers this
 * should be a an array that will be converted to Json or XMl by an OutputStream.
 * 
 * If you supply exportVariables in this response object the view will import
 * those variables for use in the view.
 * 
 */
abstract class GO_Base_Controller_AbstractController extends GO_Base_Observable {
	
	
	
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
		$this->module=strtolower($module);
		$this->moduleObject = GO::modules()->{$this->module};
	}	
	
	/**
	 * Includes the file from the views folder
	 * 
	 * @param string $viewName 
	 */
	protected function render($viewName){		
		//header('Content-Type: text/html; charset=UTF-8');
		
		if(empty($this->module)){
			require(GO::config()->root_path.'views/'.GO::view().'/'.$viewName.'.php');
		}else
		{
			require(GO::modules()->{$this->module}.'views/'.GO::view().'/'.$viewName.'.php');
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
			
			
			/**
			 * If this controller belongs to a module and it's the first request to
			 * a module we run the {Module}Module.php class firstRun function
			 * The response is added to the controller's action parameters.
			 */
			if($this->module != 'core' && !isset(GO::session()->values['firstRunDone'][$this->module])){
				$moduleClass = "GO_".ucfirst($this->module)."_".ucfirst($this->module)."Module";

				if(class_exists($moduleClass)){

					$_REQUEST['firstRun']=call_user_func(array($moduleClass,'firstRun'));
					GO::session()->values['firstRunDone'][$this->module]=true;
				}
			}

//			$method=new ReflectionMethod($this, $methodName);
//			if($method->getNumberOfParameters()>0)
//				$this->runWithParams($method, $_REQUEST);
//			else
				return $this->$methodName($_REQUEST);
		} catch (Exception $e) {
			$response['success'] = false;
			$response['feedback'] = !empty($response['feedback']) ? $response['feedback'] : '';
			$response['feedback'] .= "\r\n\r\n".$e->getMessage();
			
			if(GO::config()->debug)
				$response['trace']=$e->getTraceAsString();
			
			return $response;
			//exit();
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
//	protected function runWithParams($method, $params)
//	{
//		$ps=array();
//		foreach($method->getParameters() as $i=>$param)
//		{
//			$name=$param->getName();
//			if(isset($params[$name]))
//			{
//				if($param->isArray())
//					$ps[]=is_array($params[$name]) ? $params[$name] : array($params[$name]);
//				else if(!is_array($params[$name]))
//					$ps[]=$params[$name];
//				else
//					return false;
//			}
//			else if($param->isDefaultValueAvailable())
//				$ps[]=$param->getDefaultValue();
//			else
//				return false;
//		}
//		$method->invokeArgs($this,$ps);
//		return true;
//	}
	
	/**
	 * This default action should be overrriden
	 */
	public function actionIndex($params){
		
	}
	
	/**
	 * Redirect the browser.
	 * 
	 * @param string $path 
	 */
	protected function redirect($path=''){		
		header('Location: ' .$this->url($path));
		exit();
	}
	
	
	protected function url($path){
		$url = GO::config()->host;
		
		if($path!='')
			$url .= '?r='.$path;
		
		if(isset(GO::session()->values['security_token']))
			$url .= '&security_token='.GO::session()->values['security_token'];
		
		return $url;
	}
	
	/**
	 * Set a cookie
	 * 
	 * @param string $name 
	 * @param string $value [optional] 
	 * @param int $expire [optional] 
	 * @return type 
	 */
	protected function setCookie($name, $value, $expires=0){
		return SetCookie($name,$value,$expires,GO::config()->host,$_SERVER['HTTP_HOST'],!empty($_SERVER['HTTPS']),true);
	}
	
	
	protected function isAjax(){
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
	}
}