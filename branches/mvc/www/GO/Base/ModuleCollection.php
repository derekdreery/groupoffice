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
			$this->modules[$module->id]=$module->getAttributes();
			$this->modules[$module->id]['path']=$module->path;
			$this->modules[$module->id]['class_path']=$module->class_path;
			$this->modules[$module->id]['url']=$module->url;
			$this->modules[$module->id]['full_url']=$module->full_url;

		}
	}
}