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
	
	/**
	 * Add a model to the memory cache.
	 * 
	 * @param String $modelClassName The GO_Base_Db_ActiveRecord derived class
	 * @param mixed $model 
	 */
	public function add($modelClassName, $model){
		$this->_models[$modelClassName][$model->pk]=$model;
	}
	
	/**
	 * Get a model from the memory cache
	 * 
	 * @param String $modelClassName The GO_Base_Db_ActiveRecord derived class
	 * @param mixed $primaryKey 
	 */
	public function get($modelClassName, $primaryKey){
		return isset($this->_models[$modelClassName][$primaryKey]) ? $this->_models[$modelClassName][$primaryKey] : false;
	}
	
}