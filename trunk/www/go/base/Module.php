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
 * This class is used to manage a module. It performs tasks such as
 * installing, uninstalling and initializing.
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id: config.class.inc.php 7687 2011-06-23 12:00:34Z mschering $
 * @copyright Copyright Intermesh BV.
 * @package go.base 
 */
class GO_Base_Module extends GO_Base_Observable {

	/**
	 * Get the id of the module which is identical to 
	 * the folder name in the modules folder.
	 * 
	 * eg. notes, calendar  etc.
	 * @return string 
	 */
	public function id() {
		
		$className = get_class($this);
		
		$arr = explode('_', $className);
		return strtolower($arr[1]);
	}
	
	/**
	 * Get the absolute filesystem path to the module.
	 * 
	 * @return string 
	 */
	public function path(){
		return GO::config()->root_path . 'modules/' . $this->id() . '/';
	}

	/**
	 * Return the localized name
	 * 
	 * @return String 
	 */
	public function name() {
		return GO::t('name', $this->id());// isset($lang[$this->id]['name']) ? $lang[$this->id]['name'] : $this->id;
	}

	/**
	 * Return the localized description
	 * 
	 * @return String 
	 */
	public function description() {
		return GO::t('description', $this->id());
	}
	
	/**
	 * Return the name of the author.
	 * 
	 * @return String 
	 */
	public function author(){
		return '';
	}
	
	/**
	 * Return the e-mail address of the author.
	 * 
	 * @return String 
	 */
	public function authorEmail(){
		return 'info@intermesh.nl';
	}
	
	/**
	 * Return copyright information
	 * 
	 * @return String 
	 */
	public function copyright(){
		return 'Copyright Intermesh BV';
	}
	
	/**
	 * Return true if this module belongs in the admin menu.
	 * 
	 * @return boolean 
	 */
	public function adminModule(){
		return false;
	}
	
	/**
	 * Return the number of update queries.
	 * 
	 * @return integer 
	 */
	public function databaseVersion(){
		$updatesFile = $this->path() . 'install/updates.php';
		if(file_exists($updatesFile))
		{
			require($updatesFile);
			if(isset($updates))
				return count($updates);
		}else
		{
			return 0;
		}
	}

	/**
	 * Installs the module's tables etc
	 * 
	 * @return boolean
	 */
	public function install() {
		
		$sqlFile = $this->path().'install/install.sql';
		
		if(file_exists($sqlFile))
		{
			$queries = GO_Base_Util_SQL::getSqlQueries($sqlFile);
			foreach($queries as $query)
				GO::getDbConnection ()->query($query);
		}
		
		GO::clearCache();
		
		
		//call saveUser for each user
		$stmt = GO_Base_Model_User::model()->find(array('ignoreAcl'=>true));		
		while($user = $stmt->fetch()){
			call_user_func(array(get_class($this),'saveUser'), $user, true);
		}
		
		return true;
	}

	/**
	 * Delete's the module's tables etc.
	 * 
	 * @return boolean
	 */
	public function uninstall() {
		
		
		//call deleteUser for each user
		$stmt = GO_Base_Model_User::model()->find(array('ignoreAcl'=>true));		
		while($user = $stmt->fetch()){
			call_user_func(array(get_class($this),'deleteUser'), $user);
		}
		
		$sqlFile = $this->path().'install/uninstall.sql';
		
		if(file_exists($sqlFile))
		{
			$queries = GO_Base_Util_SQL::getSqlQueries($sqlFile);
			foreach($queries as $query)
				GO::getDbConnection ()->query($query);
		}
		
		GO::clearCache();
		
		
		return true;
	}

	/**
	 * This class can be overriden by a module class to add listeners to objects
	 * that extend the GO_Base_Observable class.
	 * 	 
	 */
	public static function initListeners() {
		
	}
	
	/**
	 * This function is called when the first request is made to the module.
	 * Useful to check for a default calendar, tasklist etc.
	 * 
	 * The response is added to the controller action parameters with index
	 * 'firstRun'.
	 */
	public static function firstRun(){
		return '';
	}
	
	/**
	 * This function is called when the search index needs to be rebuilt.
	 * 
	 * You want to use MyModel::model()->rebuildSearchCache();
	 * 
	 * @param array $response Array of output lines
	 */
	public function buildSearchCache(&$response){		
		
		$response[]  = "Building search cache for ".$this->id()."\n";		
				
		$models=$this->getModels();

		foreach($models as $model){
			echo $response[] = "Processing ".$model->getName()."\n";
			$stmt = call_user_func(array($model->getName(),'model'))->rebuildSearchCache();
			//$stmt->callOnEach('rebuildSearchCache');
		}
	}
	
	/**
	 * This function is called when a database check is performed
	 * 
	 * @param array $response Array of output lines
	 */
	public function checkDatabase(&$response){				
		
		//echo "<pre>";
		
		echo "Checking database for ".$this->id()."\n";		
				
		$models=$this->getModels();
		
		
		foreach($models as $model){			
			echo "Processing ".$model->getName()."\n";
			flush();
			
			$m = GO::getModel($model->getName());
			
			$stmt = $m->find(array(
					'ignoreAcl'=>true
			));
			$stmt->callOnEach('save');
		}
	}
	
	/**
	 * Get all model class names.
	 * 
	 * @return Array Names of all model classes 
	 */
	public function getModels(){		
	
		$models=array();
		$classes=$this->findClasses('model');
		foreach($classes as $class){
//			$class = new ReflectionClass($className);
//			$class->is
				if(!$class->isAbstract()){					
					$models[] = $class;
				}
		}		
		return $models;
	}
	
	public function findClasses($subfolder){
		
		$classes=array();
		$folder = new GO_Base_Fs_Folder($this->path().$subfolder);
		if($folder->exists()){
			
			$items = $folder->ls();
			
			foreach($items as $item){
				if($item instanceof GO_Base_Fs_File){
					$className = 'GO_'.ucfirst($this->id()).'_'.ucfirst($subfolder).'_'.$item->nameWithoutExtension();					
					$classes[] = new ReflectionClass($className);					
				}
			}
		}
		
		return $classes;
	}
	
	
	/**
	 * Called when the main settings are loaded.
	 * 
	 * @param GO_Core_Controller_Settings $settingsController
	 * @param array $params Request params
	 * @param array $response 
	 */
	public static function loadSettings(&$settingsController, &$params, &$response){		
	}
	
	/**
	 * Called when the main settings are submitted.
	 * 
	 * @param GO_Core_Controller_Settings $settingsController
	 * @param array $params Request params
	 * @param array $response 
	 */
	public static function submitSettings(&$settingsController, &$params, &$response){		
	}
	
	/**
	 * Called when a user is deleted
	 *
	 * @param GO_Base_Model_User $user
	 */
	public static function deleteUser($user){
		
	}
	
	/**
	 * Called when a user is saved
	 *
	 * @param GO_Base_Model_User $user
	 * @param boolean $wasNew 
	 */
	public static function saveUser($user, $wasNew){
		
	}
}