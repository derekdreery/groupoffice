<?php

class GO_Base_Model_Acl extends GO_Base_Db_ActiveRecord {
	
	/**
	 * Permission level constants.
	 */
	const READ_PERMISSION=1;
	const WRITE_PERMISSION=2;
	const DELETE_PERMISSION=3;
	const MANAGE_PERMISSION=4;

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
	public function getUserPermissionLevel($userId=0, $checkGroupPermissionOnly=false) {
		
		//Scripts can set this variable to ignore permissions
		if(GO::$ignoreAclPerissions){
			return self::MANAGE_PERMISSION;
		}
		
		if($userId==0){
			if(GO::user())
				$userId=GO::user()->id;
			else
				return false;
		}
		
		if ($userId > 0 && $this->id > 0) {
			$sql = "SELECT a.acl_id, a.level FROM go_acl a " .
							"LEFT JOIN go_users_groups ug ON a.group_id=ug.group_id " .
							"WHERE a.acl_id=" . intval($this->id) . " AND " .
							"(ug.user_id=" . intval($userId);

			if (!$checkGroupPermissionOnly)
				$sql .= " OR a.user_id=" . intval($userId) . ") ORDER BY a.level DESC";
			else
				$sql .= ")";

			$stmt = $this->getDbConnection()->query($sql);
			if ($r = $stmt->fetch()) {
				return intval($r['level']);
			}
		}
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
			$this->addUser(GO::user() ? GO::user()->id : 1, GO_Base_Model_Acl::MANAGE_PERMISSION);
		}

		return parent::afterSave($wasNew);
	}

	protected function afterDelete() {

		return parent::afterDelete();
	}

}