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
 * @property int $mtime
 */
class GO_Base_Model_Acl extends GO_Base_Db_ActiveRecord {
	
	/**
	 * Permission level constants.
	 */
	const READ_PERMISSION=10;
	const	CREATE_PERMISSION=20;
	const WRITE_PERMISSION=30;
	const DELETE_PERMISSION=40;
	const MANAGE_PERMISSION=50;
	
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
	

	/**
	 * Check for permissionlevel and return a boolean if it's OK or not
	 * 
	 * @param int $level The permissionlevel that is currently in use
	 * @param int $requiredLevel The minimal permissionlevel that is needed
	 * @return boolean Has permission or not 
	 */
	public static function hasPermission($level, $requiredLevel) {
		return $level>=$requiredLevel;
	}
	
	public function tableName(){
		return "go_acl_items";
	}
	
	/**
	 * Return the permission level that a user has for this ACL.
	 *  
	 * @param in $acl_id The ID of the acl to check
	 * @param int $userId If omitted then it will check the currently logged in user and return manage permission if GO::$ignoreAclPermissions is set.
	 * @param bool $checkGroupPermissionOnly
	 * @return int Permission level. See constants in GO_Base_Model_Acl for values. 
	 */
	public static function getUserPermissionLevel($aclId, $userId=false, $checkGroupPermissionOnly=false) {
		
		//only ignore when no explicit user is checked. Otherwise you can never check the real permissionlevel when GO::$ignoreAclPermissions is set to true.
		if(GO::$ignoreAclPermissions && $userId===false)
			return self::MANAGE_PERMISSION;
		
		if($userId===false){
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
			if($level>0){
				$usersGroup->level=$level;			
				return !$usersGroup->isModified() || $usersGroup->save();
			}else{
				return $usersGroup->delete();
			}
		}else
		{		
			if($level==0)
				return true;
			
			$usersGroup = new GO_Base_Model_AclUsersGroups();
			$usersGroup->acl_id = $this->id;
			$usersGroup->group_id = 0;
			$usersGroup->user_id = $userId;
			$usersGroup->level = $level;
			return $usersGroup->save();
		}

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
			if($level>0){
				$usersGroup->level=$level;			
				return !$usersGroup->isModified() || $usersGroup->save();
			}else{
				return $usersGroup->delete();
			}
		}else
		{	
			if($level==0)
				return true;
			
			$usersGroup = new GO_Base_Model_AclUsersGroups();
			$usersGroup->acl_id = $this->id;
			$usersGroup->group_id = $groupId;
			$usersGroup->user_id = 0;
			$usersGroup->level = $level;
			return $usersGroup->save();
		}
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
		
		if($groupId==GO::config()->group_root)
			return false;
		
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
	 * Copy the permissions of this acl to another.
	 * 
	 * @param GO_Base_Model_Acl $targetAcl
	 */
	public function copyPermissions(GO_Base_Model_Acl $targetAcl){
		//$this->duplicateRelation('records', $targetAcl);
		
		$stmt = $this->records;
		foreach($stmt as $r){
			if($r->group_id){
				$targetAcl->addGroup($r->group_id, $r->level);
			}elseif($r->user_id){
				$targetAcl->addUser($r->user_id, $r->level);
			}
		}
	}
		
	
	/**
	 * Get all users that have access to an acl.
	 * 
	 * @param int $aclId
	 * @param int $level 
	 * @params Array $callback Call a function with the user as argument. 
	 * This was added to save memory so that not all users have to be in memory. 
	 * If you pass this argument this function will return void.
	 * @return Array of GO_Base_Model_User 
	 */
	public static function getAuthorizedUsers($aclId, $level=GO_Base_Model_Acl::READ_PERMISSION, $callback=false, $callbackArguments=array()){
		
		//Very slow!:		
//		SELECT u.username from go_acl a
//left JOIN `go_users_groups` ug  ON (a.group_id=ug.group_id)
//inner join go_users u on (u.id=a.user_id OR u.id=ug.user_id)
//where a.acl_id=19260 and level>=1 group by u.id
		
		//VERy slow too
		//todo Sub query support with query builder
//		$stmt = GO::getDbConnection()->prepare("SELECT * 
//FROM go_users u
//where exists
//(select id from go_acl a LEFT JOIN `go_users_groups` ug  ON ( `a`.`group_id` = ug.group_id) where acl_id=:acl_id and level>=:level and (a.user_id=u.id or ug.user_id=u.id)
//)");
//		$stmt->bindParam("acl_id", $aclId, PDO::PARAM_INT);
//		$stmt->bindParam("level", $level, PDO::PARAM_INT);
//		$stmt->execute();
//		
//		$stmt->setFetchMode(PDO::FETCH_CLASS, "GO_Base_Model_User",array(false));
//		return $stmt;
		
		$joinCriteria = GO_Base_Db_FindCriteria::newInstance()
										->addModel(GO_Base_Model_AclUsersGroups::model(), 'a')
										->addModel(GO_Base_Model_User::model(), 't')
										->addCondition('id', 'a.user_id','=','t',true,true)
										->addCondition('acl_id', $aclId,'=','a');
		
		if($level > GO_Base_Model_Acl::READ_PERMISSION)
				$joinCriteria->addCondition('level', $level,'>=','a');
		
		$stmt =  GO_Base_Model_User::model()->find(GO_Base_Db_FindParams::newInstance()		
						->ignoreAcl()
						->join(GO_Base_Model_AclUsersGroups::model()->tableName(),$joinCriteria
										,'a')
						);
		
		$users=array();
		$ids = array();
		while($user=$stmt->fetch()){
			$ids[]=$user->id;
			if($callback){
				call_user_func_array($callback, array_merge(array($user), $callbackArguments));
			}else
			{
				$users[]=$user;
			}
		}
		
		$joinCriteria  = GO_Base_Db_FindCriteria::newInstance()
										->addModel(GO_Base_Model_AclUsersGroups::model(),'a')										
										->addCondition('group_id', 'ug.group_id','=','a',true,true)
										->addCondition('acl_id', $aclId,'=','a');
		
		if($level > GO_Base_Model_Acl::READ_PERMISSION)
			$joinCriteria->addCondition('level', $level,'>=','a');
		
		
		$stmt =  GO_Base_Model_User::model()->find(GO_Base_Db_FindParams::newInstance()				
						->ignoreAcl()
						->join(GO_Base_Model_UserGroup::model()->tableName(),  GO_Base_Db_FindCriteria::newInstance()		
										->addCondition('id', 'ug.user_id','=','t',true,true),
										'ug')
						->join(GO_Base_Model_AclUsersGroups::model()->tableName(),$joinCriteria
										,'a')
						->order('a.level')
						->group('t.id'));
		
		while($user = $stmt->fetch()){
			if(!in_array($user->id, $ids)){				
				if($callback){
					call_user_func_array($callback, array_merge(array($user), $callbackArguments));
				}else
				{
					$users[]=$user;
				}
			}
		}
		
		if(!$callback)
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
		
		$model = GO_Base_Model_AclUsersGroups::model()->find($findParams);
		
		return $model->count;		
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
	
	/**
	 * 
	 * /!\ Be careful when using this!
	 * 
	 * Makes sure that this ACL's permissions are only the manage permissions for
	 * the admin user and admin group. Other permissions will be deleted.
	 */
	public function clear(){
		
		if (!GO::user()->isAdmin())
			throw new AccessDeniedException();
		
		$adminUserRecordExists = false;
		$adminGroupRecordExists = false;
		
		foreach ($this->records as $aclRecord) {
			
			if ($aclRecord->user_id==1) {
				$adminUserRecordExists=true;
			} elseif ($aclRecord->group_id==GO::config()->group_root) {
				$adminGroupRecordExists=true;
			} else {
				$aclRecord->delete();
			}
			
		}

		if (!$adminUserRecordExists) {
			$aclRecord = new GO_Base_Model_AclUsersGroups();
			$aclRecord->acl_id = $this->id;
			$aclRecord->user_id = 1;
			$aclRecord->level = GO_Base_Model_Acl::MANAGE_PERMISSION;
			$aclRecord->save();
		}
		if (!$adminGroupRecordExists) {
			$aclRecord = new GO_Base_Model_AclUsersGroups();
			$aclRecord->acl_id = $this->id;
			$aclRecord->group_id = GO::config()->group_root;
			$aclRecord->level = GO_Base_Model_Acl::MANAGE_PERMISSION;
			$aclRecord->save();
		}
		
	}
}