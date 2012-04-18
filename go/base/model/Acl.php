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
 * 
 * Add group to ACL:
 * 
 * replace into go_acl (acl_id, group_id, level) select acl_id,<group_id>,4 from go_users;
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
	 * @param int $userId If omitted then it will check the currently logged in user and return manage permission if GO::$ignoreAclPermissions is set.
	 * @param bool $checkGroupPermissionOnly
	 * @return int Permission level. See constants in GO_Base_Model_Acl for values. 
	 */
	public static function getUserPermissionLevel($aclId, $userId=false, $checkGroupPermissionOnly=false) {
		
		//only ignore when no explicit user is checked.
		if(GO::$ignoreAclPermissions && $userId===false)
			return self::MANAGE_PERMISSION;
		
		if(!$userId){
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
			return intval($model->level);
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
			if($level>0)
				$usersGroup->level=$level;			
			else
				return $usersGroup->delete();
		}else
		{		
			if($level==0)
				return true;
			
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
			if($level>0)
				$usersGroup->level=$level;			
			else
				return $usersGroup->delete();
		}else
		{	
			if($level==0)
				return true;
			
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
	 * Get all users in this acl. The user models will contain an extra
	 * permission_level property.
	 * 
	 * @return GO_Base_Db_ActiveStatement 
	 */
	public function getUsers(){
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->select('t.*,a.level as permission_level')
						->joinModel(array(
								'model'=>"GO_Base_Model_AclUsersGroups",
								'foreignField'=>'user_id',
								'tableAlias'=>'a'								
						));
		
		$findParams->getCriteria()->addCondition('acl_id', $this->id, '=','a');
		
		return GO_Base_Model_User::model()->find($findParams);
	}
	
	/**
	 * Get all groups in this acl. The group models will contain an extra
	 * permission_level property.
	 * 
	 * @return GO_Base_Db_ActiveStatement 
	 */
	public function getGroups(){
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->select('t.*,a.level as permission_level')
						->joinModel(array(
								'model'=>"GO_Base_Model_AclUsersGroups",
								'foreignField'=>'group_id',
								'tableAlias'=>'a'								
						));
		
		$findParams->getCriteria()->addCondition('acl_id', $this->id, '=','a');
		
		return GO_Base_Model_Group::model()->find($findParams);
	}
	
		
	
	/**
	 * Get all users that have access to an acl.
	 * 
	 * @param int $aclId
	 * @param int $level 
	 * @return Array of GO_Base_Model_User 
	 */
	public static function getAuthorizedUsers($aclId, $level){
		
		//todo change into ???:
		
//		SELECT u.username from go_acl a
//left JOIN `go_users_groups` ug  ON (a.group_id=ug.group_id)
//inner join go_users u on (u.id=a.user_id OR u.id=ug.user_id)
//where a.acl_id=19260 and level>=1 group by u.id
		
		
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
	
	/**
	 * Count the number of users that have access to this acl
	 * 
	 * @param int $level
	 * @return int 
	 */
	public function countUsers($level=  GO_Base_Model_Acl::READ_PERMISSION){
		
		//Either user_id in go_acl is 0 or user_id in go_users_groups is NULL.
		//We can add them up to get a distinct count.
		
//		SELECT count(distinct a.user_id+IFNULL(ug.user_id,0)) from go_acl a
//left JOIN `go_users_groups` ug  ON (a.group_id=ug.group_id)
//where acl_id=19260 and level>1
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->single()
						//->debugSql()
						->select('count(distinct t.user_id+IFNULL(ug.user_id,0)) AS count')
						->join(GO_Base_Model_UserGroup::model()->tableName(),  GO_Base_Db_FindCriteria::newInstance()		
										->addCondition('group_id', 'ug.group_id','=','t',true,true),
										'ug','LEFT');
		
		$findParams->getCriteria()->addCondition('acl_id', $this->id)->addCondition('level', $level,'>=');
		
		$record = GO_Base_Model_AclUsersGroups::model()->find($findParams);
		
		return $record['count'];		
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