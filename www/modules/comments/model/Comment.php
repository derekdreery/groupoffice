<?php
class GO_Comments_Model_Comment extends GO_Base_Db_ActiveRecord{

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Comments_Model_Comment 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
//	public function linkType(){
//		return false;
//	}
//
//	public function aclField(){
//		return false;
//	}

	public function tableName(){
		return 'co_comments';
	}

//	public function hasFiles(){
//		return false;
//	}

}
