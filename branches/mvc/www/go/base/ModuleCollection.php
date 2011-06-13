<?php
/**
 * This class is solely for backwards compatibility
 */
class GO_Base_ModuleCollection extends GO_Base_Model_ModelCollection{
	
	/**
	 * Array with modules for backwards compativility
	 * 
	 * @deprecated
	 * @var array with modules.
	 */
	public $modules;
	
	public function __construct($model='GO_Base_Model_Module', $findParams=array()){
		parent::__construct($model, $findParams);
		
		foreach($this->_models as $module){
			
			if(!is_dir($module->path)){
				unset($this->_models[$module->id]);
			}else
			{
			
				$this->modules[$module->id]=$module->getAttributes();
				$this->modules[$module->id]['path']=$module->path;
				$this->modules[$module->id]['class_path']=$module->class_path;
				$this->modules[$module->id]['url']=$module->url;
				$this->modules[$module->id]['full_url']=$module->full_url;
				
				$this->modules[$module->id]['read_permission']=$module->permissionLevel>=GO_SECURITY::READ_PERMISSION;
				$this->modules[$module->id]['write_permission']=$module->permissionLevel>GO_SECURITY::READ_PERMISSION;
			}

		}
		
	}
	
	
	/**
	 * Checks if the current logged in user has access to a module
	 * 
	 * @param $module
	 * @return bool
	 */
	
	public function has_module($module)
	{
		return isset($this->modules[$module]) && ($this->modules[$module]['read_permission'] || $this->modules[$module]['write_permission']);
	}


	public function module_is_allowed($module){

		if(!$this->allowed_modules)
		{
			global $GO_CONFIG;
			$this->allowed_modules=empty(GO::config()->allowed_modules) ? array() : explode(',', GO::config()->allowed_modules);
		}
		return !count($this->allowed_modules) || in_array($module, $this->allowed_modules);
	}
	
	/**
	 * @deprecated
	 */
	public function load_modules(){
		
	}
}