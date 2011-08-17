<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */


/**
 * 
 * Sometimes the same models are accessed lots of times in one script run. This
 * class helps GO_Base_Db_ActiveRecord->findByPk to return the object from 
 * memory if it has already been fetched from the database.
 */

class GO_Base_Model_ModelCache{
	
	private $_models;
	
	public function __construct() {
		
		//GO::debug("Model cache construct");
		
		if(isset(GO::session()->values['modelCache'])){
			//GO::debug(array_keys(GO::session()->values['modelCache']));
			//$this->_models=GO::session()->values['modelCache'];
			
		}
	}
	
	/**
	 * Add a model to the memory cache.
	 * 
	 * @param String $modelClassName The GO_Base_Db_ActiveRecord derived class
	 * @param mixed $model 
	 */
	public function add($modelClassName, $model, $cacheKey=false){
		
		/**
		 * This cache mechanism can consume a lot of memory when running large
		 * batch scripts. That's why it can be disabled.
		 */
		if(GO::$disableModelCache)
			return;
		
		if(!$cacheKey)
			$cacheKey=$model->pk;
		
		//GO::debug("GO_Base_Model_ModelCache::add($modelClassName, $cacheKey)");
		
		$cacheKey = $this->_formatCacheKey($cacheKey);		
		
		$this->_models[$modelClassName][$cacheKey]=$model;
		
//		if($model->sessionCache()){
//			//GO::debug("Add to session");
//			//GO::session()->values['modelCache'][$modelClassName][$cacheKey]=$model;
//		}
	}
	
	private function _formatCacheKey($cacheKey){
		
		$cacheKey=md5(serialize($cacheKey));
		
		return $cacheKey;
	}
	
	/**
	 * Get a model from the memory cache
	 * 
	 * @param String $modelClassName The GO_Base_Db_ActiveRecord derived class
	 * @param mixed $primaryKey 
	 */
	public function get($modelClassName, $cacheKey){		
		
		$formatted=$this->_formatCacheKey($cacheKey);
		
		//GO::debug("GO_Base_Model_ModelCache::get($modelClassName, $cacheKey) ".$formatted);
		
		if(isset($this->_models[$modelClassName][$formatted]))
		{
			//GO::debug("Found in cache");
			return $this->_models[$modelClassName][$formatted];
		}else
			return false;
	}
	
}