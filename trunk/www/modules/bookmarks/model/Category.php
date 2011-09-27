<?php

class GO_Bookmarks_Model_Category extends GO_Base_Db_ActiveRecord {
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Presidents_Model_President 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName() {
		return 'bm_categories';
	}
	public function aclField() {
		return 'acl_id';
	}
	
	public function primaryKey() {
		return 'id';
	}
}