<?php
class GO_Base_Model_SearchCacheRecord extends GO_Base_Db_ActiveRecord{
	
	public $aclField='acl_id';

	public $tableName="go_search_cache";
	
	public $primaryKey=array('id','link_type');
	
	/**
	 * Set this to true so it won't be deleted.
	 * @var type 
	 */
	public $joinAclField=true;

}
