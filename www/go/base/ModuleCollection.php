<?php
/**
 * This class is solely for backwards compatibility
 */
class GO_Base_ModuleCollection extends GO_Base_Model_ModelCollection{
	
	
	public function __construct($model='GO_Base_Model_Module'){

		parent::__construct($model);
	}
	
	
	public function getAvailableModules(){
		$folder = new GO_Base_Fs_Folder(GO::config()->root_path.'modules');
		
		$folders = $folder->ls();
		$modules = array();
		foreach($folders as $folder){
			$ucfirst = ucfirst($folder->name());
			$moduleClass = $folder->path().'/'.$ucfirst.'Module.php';
			if(file_exists($moduleClass) && !GO_Base_Model_Module::model()->findByPk($folder->name())){
				$modules[]='GO_'.$ucfirst.'_'.$ucfirst.'Module';
			}
		}
		
		return $modules;		
	}
	

	
	public function callModuleMethod($method, $params=array()){
		
		$stmt = $this->getAll();
		$modules = $stmt->fetchAll();
		
		foreach($modules as $module)
		{	
			$file = $module->path.ucfirst($module->id).'Module.php';
			//todo load listeners
			if(file_exists($file)){
				//require_once($file);
				$class='GO_'.ucfirst($module->id).'_'.ucfirst($module->id).'Module';
				
				$object = new $class;
				if(method_exists($object, $method)){					
					GO::debug('Calling '.$class.'::'.$method);
					call_user_func_array(array($object, $method), $params);
					//$object->$method($params);
				}
			}
		}
	}
}