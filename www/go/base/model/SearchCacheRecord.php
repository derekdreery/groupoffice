<?php
class GO_Base_Model_SearchCacheRecord extends GO_Base_Db_ActiveRecord {

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
