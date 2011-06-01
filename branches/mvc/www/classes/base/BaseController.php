<?php
abstract class GO_BaseController {

	/**
	 *
	 * @var type 
	 */
	protected $requiredPermissionLevels=array(
			
	);
	
	protected function init(){
		
	}
	
	protected function addPermissionCheck($aclId, $requiredPermissionLevel, $action='*'){
		$this->requiredPermissionLevels[$action]=array('aclId'=>$aclId, 'requiredPermissionLevel'=>$requiredPermissionLevel);
	}
	
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
	
	public function run($action){
		
		if(!$this->checkPermissions($action)){
			throw new AccessDeniedException();
		}

		$methodName='action'.$action;

		$method=new ReflectionMethod($this, $methodName);
		if($method->getNumberOfParameters()>0)
			return $this->runWithParams($method, $_REQUEST);
		else
			return $this->$methodName();
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
}