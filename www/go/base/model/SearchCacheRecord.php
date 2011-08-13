<?php
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
		return array('id', 'link_type');					
	}
	
	/**
	 * Set this to true so it won't be deleted.
	 * @var type 
	 */
	public $joinAclField = true;

}
