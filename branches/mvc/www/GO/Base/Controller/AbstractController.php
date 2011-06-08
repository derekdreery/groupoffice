<?php

abstract class GO_Base_Controller_AbstractController {
	
	protected $outputStream;
	
	protected $module;

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
	public function init($module, $output){
		//header('Content-Type: text/plain');
		$this->module=$module;
		
		$outputClass = 'GO_Base_OutputStream_OutputStream'.ucfirst($output);
		$this->outputStream = new $outputClass;
	}
	
	/**
	 * Outputs data directly to the standard output
	 * 
	 * @param mixed $str 
	 */
	protected function output($str){
		$this->outputStream->write($str);
	}
	
	/**
	 * Includes the file from the views folder
	 * 
	 * @param string $viewName 
	 */
	protected function render($viewName){
		if($this->module=='Base'){
			require(GO::config()->root_path.'views/'.GO::view().'/'.$viewName.'.php');
		}else
		{
			require(GO::modules()->modules[$this->module].'views/'.GO::view().'/'.$viewName.'.php');
		}
	}
	
	/**
	 * Adds a permission check on an acl ID.
	 * 
	 * @param int $aclId
	 * @param int $requiredPermissionLevel See GO_SECURITY constants
	 * @param string $action 
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
			return GO::security()->hasPermission($this->requiredPermissionLevels[$action]['aclId'])>=$this->requiredPermissionLevels[$action]['requiredPermissionLevel'];
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
	public function run($action){
		
		if(!$this->checkPermissions($action)){
			throw new AccessDeniedException();
		}

		$methodName='action'.$action;

		$method=new ReflectionMethod($this, $methodName);
		if($method->getNumberOfParameters()>0)
			$this->runWithParams($method, $_REQUEST);
		else
			$this->$methodName();
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
	abstract protected function actionIndex();
}