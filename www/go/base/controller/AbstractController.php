<?php
/*
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
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
 * should be a an array that will be converted to Json or XMl by an Exporter.
 * 
 * If you supply exportVariables in this response object the view will import
 * those variables for use in the view.
 * 
 * @package GO.base.controller
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl> 
 * @abstract
 */
abstract class GO_Base_Controller_AbstractController extends GO_Base_Observable {
	
	
	
	/**
	 *
	 * @var string The module the controller belongs too. 
	 */
	private $_module;
	
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
	
	public function __construct() {
		$this->init();
	}
	
	protected function init(){
		
	}
	
	/**
	 * Return array with actions (in lowercase and without "action" prefix!) that 
	 * may be accessed by a guest that is not logged in.
	 * 
	 * WARNING: All permissions will be ignored within this controller action.
	 * GO::$ignoreAclPermissions is set to true.
	 * 
	 * @return array
	 */
	protected function allowGuests(){
		return array();
	}
	
	/**
	 * Return array with actions (in lowercase and without "action" prefix!) that may be accessed without the user having access to the module.
	 * 
	 * @return array
	 */
	protected function allowWithoutModuleAccess(){
		return array();
	}
	
	/**
	 * Checks a token that is generated for each session.
	 */
	private function _checkSecurityToken(){
		
		//only check token when we are:
		// 1. Not in debug mode
		// 2. There's a logged in user.
		// 3. A route to a controller has been given. Because we don't want to block the default page when entered manually.
		
		if(
						!GO::config()->debug && 
						!GO::config()->disable_security_token_check && 
//						GO::user() && No longer needed. We only check token when action requires a logged in user
						!empty($_REQUEST['r']) && 
						$_REQUEST['security_token']!=GO::session()->values['security_token']
			){
			//GO::session()->logout();			
			trigger_error('Fatal error: Security token mismatch. Possible cross site request forgery attack!', E_USER_ERROR);
		}
	}	
	
	/**
	 * Get the module object to which this controller belongs.
	 * Returns false if it's a core controller.
	 * 
	 * @return GO_Base_Model_Module 
	 */
	public function getModule(){
		if(!isset($this->_module)){
			$classParts = explode('_',get_class($this));
			
			$moduleId = strtolower($classParts[1]);
			
			$this->_module = GO_Base_Model_Module::model()->findByPk($moduleId, false, true);			
		}
		
		return $this->_module;
	}
	
	/**
	 * Includes the file from the views folder
	 * 
	 * @param string $viewName 
	 * The view will be searched in modules/<moduleid>/views/<view>/<viewName>.php
	 * of /views/<view>/<viewName>.php
	 * 
	 * If it's not found it will fall back on Default.php
	 * 
	 * @param array $data 
	 * An associative array of which the keys become available variables in the view file.
	 */
	protected function render($viewName, $data=array()){				
		$module = $this->getModule();
		
		if(!$module){
			$file = GO::config()->root_path.'views/'.GO::view().'/'.$viewName.'.php';
		}else
		{
			$file = $module->path.'views/'.GO::view().'/'.$viewName.'.php';
		}
		
		if(file_exists($file)){
			require($file);
		}elseif(($file = GO::config()->root_path.'views/'.GO::view().'/'.$viewName.'.php') && file_exists($file))
		{
			require($file);
		}else
		{			
			$file = GO::config()->root_path.'views/'.GO::view().'/Default.php';			
			require($file);
		}
	}
	
//	protected function renderPartial($data=array()) {
//		
//	}
//	
//	
	/**
	 * Adds a permission check on an acl ID for specific controller actions.
	 * 
	 * Note: In most cases this is not necessary because model's have ACL's in 
	 * most cases which are checked automatically.
	 * 
	 * @param int $aclId
	 * @param int $requiredPermissionLevel See GO_SECURITY constants
	 * @param string $action By default it applies to all actions but you may specify a specific action here.
	 */
	protected function addPermissionCheck($aclId, $requiredPermissionLevel, $action='*'){
		if(!is_array($action))
			$action = array($action);
		
		foreach($action as $a)
			$this->requiredPermissionLevels[$a]=array('aclId'=>$aclId, 'requiredPermissionLevel'=>$requiredPermissionLevel);
	}
	/**
	 * Checks if a user is logged in, if the user has permission to the module and if the user has access to a specific action.
	 * 
	 * @param string $action
	 * @return boolean boolean
	 */
	private function _checkPermission($action){
		if(!in_array($action, $this->allowGuests())){			
			//check for logged in user
			if(!GO::user())
				return false;			
			
			$this->_checkSecurityToken();
			
			//check module permission
			if(!in_array($action, $this->allowWithoutModuleAccess()))
			{
				$module = $this->getModule();		
				if($module && !$module->permissionLevel)
					return false;
			}
		}
		
		return $this->_checkRequiredPermissionLevels($action);
		
	}
	
	/**
	 * Check the ACL permission levels manually added by addRequiredPermissionLevel();
	 * 
	 * @param string $action
	 * @return boolean 
	 */
	private function _checkRequiredPermissionLevels($action){
		//check action permission
		if(isset($this->requiredPermissionLevels[$action])){
			return GO_Base_Model_Acl::getUserPermissionLevel($this->requiredPermissionLevels[$action]['aclId'])>=$this->requiredPermissionLevels[$action]['requiredPermissionLevel'];
		}elseif($action!='*'){
			return $this->_checkRequiredPermissionLevels('*');
		}else
		{
			return true;
		}
	}
	
	/**
	 * Runs a method of this controller. If $action is save then it will run
	 * actionSave of your extended class.
	 * 
	 * @param string $action Without "action" prefix.
	 */
	public function run($action='', $params){

		try {
			if(empty($action))
				$action=strtolower($this->defaultAction);
			else
				$action=strtolower($action);
			
			if(!$this->_checkPermission($action)){
				throw new GO_Base_Exception_AccessDenied();
			}
			
			$allowGuests = in_array($action, $this->allowGuests());
//			if($allowGuests){		
//				$oldIgnore = GO::$ignoreAclPermissions;
//				//ignore all permissions when guest access is allowed
//				GO::$ignoreAclPermissions=true;
//			}
			

			$methodName='action'.$action;
			
			$module = $this->getModule();
			
			/**
			 * If this controller belongs to a module and it's the first request to
			 * a module we run the {Module}Module.php class firstRun function
			 * The response is added to the controller's action parameters.
			 */
			if($module && !isset(GO::session()->values['firstRunDone'][$module->id])){
				$moduleClass = "GO_".ucfirst($module->id)."_".ucfirst($module->id)."Module";

				if(class_exists($moduleClass)){

					$_REQUEST['firstRun']=call_user_func(array($moduleClass,'firstRun'));
					GO::session()->values['firstRunDone'][$module->id]=true;
				}
			}
			
			//Unset some system parameters not intended for the controller action.
			unset($params['security_token'], $params['r']);

			$response =  $this->$methodName($params);

			if(isset($response))
				$this->render($action, $response);
			
			//restore old value for acl permissions if this method was allowed for guests.
//			if(isset($oldIgnore))
//				GO::$ignoreAclPermissions=$oldIgnore;

			return $response;
			
		} catch (Exception $e) {
			
			GO::debug("EXCEPTION: ".$e->getMessage());
			
					
			$response['success'] = false;
			$response['feedback'] = !empty($response['feedback']) ? $response['feedback'] : '';
			$response['feedback'] .= "\r\n\r\n".$e->getMessage();

			if(GO::config()->debug)
				$response['trace']=$e->getTraceAsString();

			$this->render('Exception', $response);

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
	protected function redirect($path='', $params=array()){		
		header('Location: ' .GO::url($path, $params));
		exit();
	}
	
	/**
	 * Get the route to this controller. Eg.
	 * 
	 * route = addressbook/contact
	 * 
	 * @return string 
	 */
	public function getRoute(){
		$arr = explode('_',get_class($this));
				
		if($arr[1]!='Core')
			return strtolower($arr[1].'/'.$arr[3]);				
		else 
			return strtolower($arr[3]);				
	}	
	
	protected function isAjax(){
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
	}
}