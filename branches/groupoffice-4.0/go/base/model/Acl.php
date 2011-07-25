<?php

class GO_Base_Model_Acl extends GO_Base_Db_ActiveRecord {
	public $tableName="go_acl_items";

	protected $_columns=array(
		'id'=>array('type'=>PDO::PARAM_INT, 'required'=>true),
		'user_id'=>array('type'=>PDO::PARAM_INT, 'required'=>true),
		'relation'=>array('type'=>PDO::PARAM_INT, 'required'=>true),
	);
	
	public function checkPermission($userId){}
	
	public function addUser($userId){}
	
	public function addGroup($groupId){}
	
	public function removeUser($userId){}
	
	public function deleteUser(){}
	
	protected function afterDelete() {	
		
		return parent::afterDelete();		
	}
}