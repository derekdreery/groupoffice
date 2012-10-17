<?php
/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base
 */

/**
 * A collection that holds all the installed modules.
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id: config.class.inc.php 7687 2011-06-23 12:00:34Z mschering $
 * @copyright Copyright Intermesh BV.
 * @package GO.base 
 */
class GO_Base_ModuleCollection extends GO_Base_Model_ModelCollection{
	
	private $_allowedModules;
	
	public function __construct($model='GO_Base_Model_Module'){

		parent::__construct($model);
	}
	
	private function _isAllowed($moduleid){
		
		if(!isset($this->_allowedModules))
			$this->_allowedModules=empty(GO::config()->allowed_modules) ? array() : explode(',', GO::config()->allowed_modules);
		
		return empty($this->_allowedModules) || in_array($moduleid, $this->_allowedModules);			
	}
	
	/**
	 * Returns an array of all module classes as string found in the modules folder.
	 * 
	 * @return array Module class names eg. GO_Calendar_Module
	 */
	public function getAvailableModules($returnInstalled=false){
		$folder = new GO_Base_Fs_Folder(GO::config()->root_path.'modules');
		
		$folders = $folder->ls();
		$modules = array();
		foreach($folders as $folder){
			$ucfirst = ucfirst($folder->name());
			$moduleClass = $folder->path().'/'.$ucfirst.'Module.php';
			if(file_exists($moduleClass) && $this->_isAllowed($folder->name()) && ($returnInstalled || !GO_Base_Model_Module::model()->findByPk($folder->name(), false, true))){
				$modules[]='GO_'.$ucfirst.'_'.$ucfirst.'Module';
			}
		}
		
		return $modules;		
	}
	

	/**
	 * Call a method of a module class. eg. GO_Notes_NotesModule::firstRun
	 * 
	 * @param string $method
	 * @param array $params 
	 */
	public function callModuleMethod($method, $params=array(), $ignoreAclPermissions=true){
		
		$oldIgnore = GO::setIgnoreAclPermissions($ignoreAclPermissions);
		$modules = $this->getAllModules();
		
		foreach($modules as $module)
		{	
			if($this->_isAllowed($module->id)){
				$file = $module->path.ucfirst($module->id).'Module.php';
				//todo load listeners
				if(file_exists($file)){
					//require_once($file);
					$class='GO_'.ucfirst($module->id).'_'.ucfirst($module->id).'Module';

					$object = new $class;
					if(method_exists($object, $method)){					
//						GO::debug('Calling '.$class.'::'.$method);
						call_user_func_array(array($object, $method), $params);
						//$object->$method($params);
					}
				}
			}
		}
		
		GO::setIgnoreAclPermissions($oldIgnore);
	}
	
	public function __get($name) {
		
		if(!$this->_isAllowed($name))
			return false;
		
		$model = parent::__get($name);
		
		if(!$model || !is_dir($model->path))
						return false;
		
		return $model;
	}
	
	/**
	 * Check if a module is installed.
	 * 
	 * @param string $moduleId
	 * @return GO_Base_Model_Module 
	 */
	public function isInstalled($moduleId){
		$model = $this->model->findByPk($moduleId, false, true);
		
		if(!$model || !$this->_isAllowed($model->id) || !$model->isAvailable())
				return false;
		
		return $model;
	}
	
	
	
	
	public function __isset($name){
		if(!$this->_isAllowed($name))
			return false;
		
		try{
			return $this->model->findByPk($name)!==false;
		}catch(GO_Base_Exception_AccessDenied $e){
			return false;
		}
	}
	/**
	 * Query all modules.
	 * 
	 * @return GO_Base_Model_Module[]
	 */
//	public function getAllModules($enabledOnly=true){
//		$findParams = GO_Base_Db_FindParams::newInstance();
//		if($enabledOnly)
//			$findParams->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('enabled', 1));
//			
//		$stmt = $this->model->find($findParams);
		
	public function getAllModules($ignoreAcl=false){
		
		$findParams = GO_Base_Db_FindParams::newInstance()->order("sort_order");
		
		if($ignoreAcl)
			$findParams->ignoreAcl ();
		
		$stmt = $this->model->find($findParams);
		$modules = array();
		while($module = $stmt->fetch()){
			if($this->_isAllowed($module->id) && $module->isAvailable())
				$modules[]=$module;
		}
		
		return $modules;
	}
}