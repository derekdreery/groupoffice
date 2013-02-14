<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * The Module model
 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.model 
 * 
 * @property string $id The id of the module which is identical to the folder name inside the "modules" folder.
 * @property String $path The absolute filesystem path to module.
 * @property GO_Base_Module $moduleManager The module class to install, initialize etc the module.
 * @property int $acl_id
 * @property boolean $admin_menu
 * @property int $sort_order
 * @property int $version
 * @property int $acl_write
 * @property boolean $enabled
 */
class GO_Base_Model_Module extends GO_Base_Db_ActiveRecord {

	private $_moduleManager;
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
		if(!isset($this->_moduleManager))	
			$this->_moduleManager = GO_Base_Module::findByModuleId ($this->id);
		
		return $this->_moduleManager;
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
		
		if(!$this->admin_menu)
			$this->acl->addGroup(GO::config()->group_internal);
		
		if($wasNew){			
			if($this->moduleManager)
				$this->moduleManager->install();
		}		
		return parent::afterSave($wasNew);
	}
	
	protected function afterDelete() {
		if($this->moduleManager)
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