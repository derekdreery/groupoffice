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
	
	
	public function __construct($model='GO_Base_Model_Module'){

		parent::__construct($model);
	}
	
	/**
	 * Returns an array of all module classes found in the modules folder.
	 * 
	 * @return array 
	 */
	public function getAvailableModules(){
		$folder = new GO_Base_Fs_Folder(GO::config()->root_path.'modules');
		
		$folders = $folder->ls();
		$modules = array();
		foreach($folders as $folder){
			$ucfirst = ucfirst($folder->name());
			$moduleClass = $folder->path().'/'.$ucfirst.'Module.php';
			if(file_exists($moduleClass) && !GO_Base_Model_Module::model()->findByPk($folder->name(), false, true)){
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
	public function callModuleMethod($method, $params=array()){
		
		$oldIgnore = GO::setIgnoreAclPermissions();
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
		
		GO::setIgnoreAclPermissions($oldIgnore);
	}
	
	public function __get($name) {
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
		
		if(!$model || !is_dir($model->path))
				return false;
		
		return $model;
	}
}