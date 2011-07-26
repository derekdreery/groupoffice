<?php
/**
 * This class is solely for backwards compatibility
 */
class GO_Base_ModuleCollection extends GO_Base_Model_ModelCollection{
	
	
	public function __construct($model='GO_Base_Model_Module'){

		parent::__construct($model);
	}
	

	
	public function callModuleMethod($method, $params=array()){
		foreach($this->getAll() as $module)
		{	
			$file = $module->path.ucfirst($module->id).'Module.php';
			//todo load listeners
			if(file_exists($file)){
				require_once($file);
				$class='GO_'.ucfirst($module->id).'_'.ucfirst($module->id).'Module';
				$object = new $class;
				if(method_exists($object, $method)){					
					GO::debug('Calling '.$class.'::'.$method);
					call_user_func_array(array($object, $method), $params);
					//$object->$method();
				}
			}
		}
	}
}