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
 * The searchcache model
 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.model 
 */

class GO_Base_Model_SearchCacheRecord extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Base_Model_SearchCacheRecord 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	public function aclField() {
		return 'acl_id';
	}

	public function tableName() {
		return 'go_search_cache';
	}

	public function primaryKey(){
		return array('model_id', 'model_type_id');					
	}
	
	
	/**
	 * Find all links of this model type to a given model. 
	 * 
	 * @param type $model
	 * @param type $findParams
	 * @return type 
	 */
	public function findLinks($model, $findParams=array()){
		
		$params = GO_Base_Db_FindParams::newInstance()
						->select("t.*,l.description AS link_description")
						->join("INNER JOIN `go_links_{$model->tableName()}` l ON (l.id=".intval($model->id)." AND t.model_id=l.model_id AND t.model_type_id=l.model_type_id)")
						->mergeWith($findParams);

		return $this->find($params);
	}
	
	
	/**
	 * Set this to true so it won't be deleted.
	 * @var type 
	 */
	public $joinAclField = true;

}
