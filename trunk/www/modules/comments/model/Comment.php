<?php
class GO_Comments_Model_Comment extends GO_Base_Db_ActiveRecord{

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

	public function relations(){
		return array(
			'user' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Base_Model_User', 'field'=>'user_id'),		);
	}

}
