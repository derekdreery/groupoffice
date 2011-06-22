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
		
		$findParams['orderField']='sort_order';
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
				
				$this->modules[$module->id]['humanName']=$module->humanName;
				$this->modules[$module->id]['description']=$module->description;
				
				$this->modules[$module->id]['read_permission']=$module->permissionLevel>=GO_SECURITY::READ_PERMISSION;
				$this->modules[$module->id]['write_permission']=$module->permissionLevel>GO_SECURITY::READ_PERMISSION;
			}
		}		
	}
	
	public function get_module( $module_id ) {
		return $this->modules[$module_id];
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
	
	
	/**
	 * Checks if the currently active user is permissioned for a module
	 *
	 * TODO long description
	 *
	 * @deprecated
	 *
	 * @param string $module_id The name of the module
	 * @param bool $admin Admin permissions required
	 *
	 * @return bool
	 */
	function authenticate( $module_id, $admin = false ) {
		global $GO_CONFIG, $GO_SECURITY;
		if ( isset( $this->modules[$module_id] ) ) {
			$module = $this->modules[$module_id];
			$_SESSION['GO_SESSION']['active_module'] = $module_id;
			$this->path = GO::config()->root_path.'modules/'.$module_id.'/';
			$this->class_path = $this->path.'classes/';
			$this->read_permission = $module['read_permission'];
			$this->write_permission = $module['write_permission'];
			$this->id = $module_id;
			$this->full_url = GO::config()->full_url.'modules/'.$module_id.'/';
			$this->url = GO::config()->host.'modules/'.$module_id.'/';

			if ( $this->read_permission || $this->write_permission ) {
				if ( $admin ) {
					if ( $this->write_permission ) {
						return true;
					}
				} else {
					return true;
				}
			}
			header( 'Location: '.GO::config()->host);
			exit();
		} else {
			exit( 'Invalid module specified' );
		}
	}
}