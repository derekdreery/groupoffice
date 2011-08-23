<?php

/**
 * @property String $path The absolute filesystem path to module.
 * @property GO_Base_Module $moduleManager The module class to install, initialize etc the module.
 */
class GO_Base_Model_Module extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Base_Model_Module 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function aclField() {
		return 'acl_id';
	}

	public function tableName() {
		return 'go_modules';
	}
	
	protected function getPath(){
		return GO::config()->root_path . 'modules/' . $this->id . '/';
	}
	
	protected function getModuleManager(){
		$className = 'GO_'.ucfirst($this->id).'_'.ucfirst($this->id).'Module';
		
		if(class_exists($className))
			return new $className;
		else
			return false;
	}
	
	protected function beforeSave() {
		if($this->isNew){
			$this->version = $this->moduleManager->databaseVersion();		
			$this->sort_order = $this->count()+1;
			$this->admin_menu = $this->moduleManager->adminModule();
		}		
		return parent::beforeSave();
	}
	
	protected function afterSave($wasNew) {
		
		if($wasNew){			
			$this->moduleManager->install();
		}		
		return parent::afterSave($wasNew);
	}
	
	protected function afterDelete() {
		$this->moduleManager->uninstall();
		
		return parent::afterDelete();
	}
	
	/**
	 * Check if the module is available on disk.
	 * 
	 * @return boolean 
	 */
	public function isAvailable(){
		return is_dir($this->path);
	}

//	protected function getName() {
//		return GO::t('name', $this->id);// isset($lang[$this->id]['name']) ? $lang[$this->id]['name'] : $this->id;
//	}
//
//	protected function getDescription() {
//		return GO::t('description', $this->id);
//	}
}