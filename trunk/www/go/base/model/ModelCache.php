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
 * Sometimes the same models are accessed lots of times in one script run. This
 * class helps GO_Base_Db_ActiveRecord->findByPk to return the object from 
 * memory if it has already been fetched from the database.
 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.model 
 */

class GO_Base_Model_ModelCache{
	
	private $_models;
	
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
		
		$cacheKey = $this->_formatCacheKey($cacheKey);		
		
		$this->_models[$modelClassName][$cacheKey]=$model;

	}
	
	private function _formatCacheKey($cacheKey){
		
		$cacheKey=md5(serialize($cacheKey));
		
		return $cacheKey;
	}
	
	/**
	 * Remove an item from the cache. 
	 * 
	 * @param string $modelClassName
	 */
	public function remove($modelClassName){		
		unset($this->_models[$modelClassName]);
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