<?php
/**
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.model
 */

/**
 * The ACL model
 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.model
 * @property string $description
 * @property int $user_id
 * @property int $id
 */
class GO_Base_Model_Acl extends GO_Base_Db_ActiveRecord {
	
	/**
	 * Permission level constants.
	 */
	const READ_PERMISSION=1;
	const WRITE_PERMISSION=2;
	const DELETE_PERMISSION=3;
	const MANAGE_PERMISSION=4;
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Base_Model_Acl 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	protected function init() {
		
		$this->columns['user_id']['required']=true;
		$this->columns['description']['required']=true;
		
		return parent::init();
	}
	
	public function relations() {
		return array(
				'records' => array('type'=>self::HAS_MANY, 'model'=>'GO_Base_Model_AclUsersGroups', 'field'=>'acl_id', 'delete'=>true),
		);
	}

	public function tableName(){
		return "go_acl_items";
	}

	/**
	 * Return the permission level that a user has for this ACL.
	 * 
	 * @todo this query should be handled by GO_Base_Db_ActiveRecord::find()
	 * 
	 * @param int $userId
	 * @param bool $checkGroupPermissionOnly
	 * @return int Permission level. See constants in GO_Base_Model_Acl for values. 
	 */
	public static function getUserPermissionLevel($aclId, $userId=0, $checkGroupPermissionOnly=false) {
		
		//Scripts can set this variable to ignore permissions
		if(GO::$ignoreAclPermissions){
			return self::MANAGE_PERMISSION;
		}
		
		if($userId==0){
			if(GO::user())
				$userId=GO::user()->id;
			else
				return false;
		}
		
		$bindParams = array(':acl_id'=>$aclId, ':user_id1'=>$userId);
		$where = 't.acl_id=:acl_id AND (ug.user_id=:user_id1';
		if (!$checkGroupPermissionOnly){		
			$bindParams[':user_id2'] = $userId;		
			$where .= " OR t.user_id=:user_id2)";			
		}else
			$where .= ")";

		
		$findParams=array(
			'join'=>"LEFT JOIN go_users_groups ug ON t.group_id=ug.group_id",
			'where'=>$where,
			'order'=>'t.level',
			'orderDirection'=>'DESC',
			'bindParams'=>$bindParams
		);
		
		$model = GO_Base_Model_AclUsersGroups::model()->findSingle($findParams);
		if($model)
			return $model->level;
		else 
			return false;
	}

	/**
	 * Add a user to the ACL with a permission level.
	 *  
	 * @param int $userId
	 * @param int $level See constants in GO_Base_Model_Acl for values. 
	 * @return bool True on success
	 */
	public function addUser($userId, $level=GO_Base_Model_Acl::READ_PERMISSION) {
		
		if($userId<1)
			return false;
		
		$usersGroup = $this->hasUser($userId);
		
		if($usersGroup){
			$usersGroup->level=$level;			
		}else
		{		
			$usersGroup = new GO_Base_Model_AclUsersGroups();
			$usersGroup->acl_id = $this->id;
			$usersGroup->group_id = 0;
			$usersGroup->user_id = $userId;
			$usersGroup->level = $level;
		}
		
		return $usersGroup->save();

	}

	/**
	 * Add a group to the ACL with a permission level.
	 *  
	 * @param int $groupId
	 * @param int $level See constants in GO_Base_Model_Acl for values. 
	 * @return bool True on success
	 */
	public function addGroup($groupId, $level=GO_Base_Model_Acl::READ_PERMISSION) {
		
		if($groupId<1)
			return false;
		
		if($groupId==GO::config()->group_root)
			$level = GO_Base_Model_Acl::MANAGE_PERMISSION;
		
		$usersGroup = $this->hasGroup($groupId);
		
		if($usersGroup){
			$usersGroup->level=$level;			
		}else
		{	
			$usersGroup = new GO_Base_Model_AclUsersGroups();
			$usersGroup->acl_id = $this->id;
			$usersGroup->group_id = $groupId;
			$usersGroup->user_id = 0;
			$usersGroup->level = $level;
		}
		
		return $usersGroup->save();
	}
	
	/**
	 * Returns the links table model if the acl has the group
	 * 
	 * @param int $groupId
	 * @return GO_Base_Model_AclUsersGroups 
	 */
	public function hasGroup($groupId){
		return GO_Base_Model_AclUsersGroups::model()->findByPk(array(
				'acl_id'=>$this->id,
				'group_id'=>$groupId,
				'user_id'=>0
						));
	}
	
	/**
	 * Returns the links table model if the acl has the user
	 * 
	 * @param int $userId
	 * @return GO_Base_Model_AclUsersGroups 
	 */
	public function hasUser($userId){
		return GO_Base_Model_AclUsersGroups::model()->findByPk(array(
				'acl_id'=>$this->id,
				'group_id'=>0,
				'user_id'=>$userId
						));
	}


	/**
	 * Remove a user from the ACL
	 * 
	 * @param int $userId
	 * @return bool 
	 */
	public function removeUser($userId) {
		
		$model = $this->hasUser($userId);
		if($model)
			return $model->delete();
		else
			return true;
	}
	
	/**
	 * Remove a group from the ACL
	 * 
	 * @param int $groupId
	 * @return bool 
	 */
	public function removeGroup($groupId) {
		$model = $this->hasGroup($groupId);
		if($model)
			return $model->delete();
		else
			return true;
	}

	protected function afterSave($wasNew) {

		if($wasNew){
			$this->addGroup(GO::config()->group_root, GO_Base_Model_Acl::MANAGE_PERMISSION);
			$this->addUser($this->user_id, GO_Base_Model_Acl::MANAGE_PERMISSION);
		}elseif($this->isModified('user_id')){
			$this->addUser($this->user_id, GO_Base_Model_Acl::MANAGE_PERMISSION);
		}

		return parent::afterSave($wasNew);
	}
	
	
	/**
	 * Get all users that have access to an acl.
	 * 
	 * @param int $aclId
	 * @param int $level 
	 * @return GO_Base_Db_ActiveStatement 
	 */
	public static function getAuthorizedUsers($aclId, $level){
		$stmt =  GO_Base_Model_User::model()->find(GO_Base_Db_FindParams::newInstance()		
						->ignoreAcl()
						->join(GO_Base_Model_AclUsersGroups::model()->tableName(),GO_Base_Db_FindCriteria::newInstance()
										->addModel(GO_Base_Model_AclUsersGroups::model(), 'a')
										->addModel(GO_Base_Model_User::model(), 't')
										->addCondition('id', 'a.user_id','=','t',true,true)
										->addCondition('acl_id', $aclId,'=','a')
										->addCondition('level', $level,'>=','a')
										,'a')
						);
		
		$users = $stmt->fetchAll();
		$ids = array();
		foreach($users as $user)
			$ids[]=$user->id;
		
		$stmt =  GO_Base_Model_User::model()->find(GO_Base_Db_FindParams::newInstance()				
						->ignoreAcl()
						->join(GO_Base_Model_UserGroup::model()->tableName(),  GO_Base_Db_FindCriteria::newInstance()		
										->addCondition('id', 'ug.user_id','=','t',true,true),
										'ug')
						->join(GO_Base_Model_AclUsersGroups::model()->tableName(),GO_Base_Db_FindCriteria::newInstance()
										->addModel(GO_Base_Model_AclUsersGroups::model(),'a')										
										->addCondition('group_id', 'ug.group_id','=','a',true,true)
										->addCondition('acl_id', $aclId,'=','a')
										->addCondition('level', $level,'>=','a')
										,'a')
						->order('a.level')
						->group('t.id'));
		
		while($user = $stmt->fetch()){
			if(!in_array($user->id, $ids))
				$users[]=$user;
		}
		
		return $users;
	}
	
	public function checkDatabase() {
		
		if(empty($this->user_id))
			$this->user_id=1;
		
		if(empty($this->description))
			$this->description='unknown';
		
		$this->addGroup(GO::config()->group_root, GO_Base_Model_Acl::MANAGE_PERMISSION);
		$this->addUser($this->user_id, GO_Base_Model_Acl::MANAGE_PERMISSION);
		
		return parent::checkDatabase();
	}
	
	

}