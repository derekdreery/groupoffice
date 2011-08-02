<?php

class GO_Base_Model_Acl extends GO_Base_Db_ActiveRecord {
	
	/**
	 * Permission level constants.
	 */
	const READ_PERMISSION=1;
	const WRITE_PERMISSION=2;
	const DELETE_PERMISSION=3;
	const MANAGE_PERMISSION=4;

	public $tableName = "go_acl_items";

	/**
	 * Return the permission level that a user has for this ACL.
	 * 
	 * @param int $userId
	 * @param bool $checkGroupPermissionOnly
	 * @return int Permission level. See constants in GO_Base_Model_Acl for values. 
	 */
	public function getUserPermissionLevel($userId=0, $checkGroupPermissionOnly=false) {
		
		if($userId==0)
			$userId=GO::session ()->values['user_id'];
		
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
	public function addUser($userId, $level) {

		return $this->getDbConnection()->query("REPLACE INTO go_acl (acl_id,user_id,level) " .
						"VALUES ('" . intval($this->id) . "','" . intval($userId) . "','" . intval($level) . "')");
	}

	/**
	 * Add a group to the ACL with a permission level.
	 *  
	 * @param int $groupId
	 * @param int $level See constants in GO_Base_Model_Acl for values. 
	 * @return bool True on success
	 */
	public function addGroup($groupId, $level) {
		return $this->getDbConnection()->query("REPLACE INTO go_acl (acl_id,group_id,level) " .
						"VALUES ('" . intval($this->id) . "','" . intval($groupId) . "','" . intval($level) . "')");
	}

	/**
	 * Remove a user from the ACL
	 * 
	 * @param int $userId
	 * @return bool 
	 */
	public function removeUser($userId) {
		$sql = "DELETE FROM go_acl WHERE user_id=".intval($userId);
		return $this->getDbConnection()->query($sql);
	}
	
	/**
	 * Remove a group from the ACL
	 * 
	 * @param int $groupId
	 * @return bool 
	 */
	public function removeGroup($groupId) {
		$sql = "DELETE FROM go_acl WHERE group_id=".intval($groupId);
		return $this->getDbConnection()->query($sql);
	}

	protected function afterSave() {

		$this->addGroup(GO::config()->group_root, GO_Base_Model_Acl::MANAGE_PERMISSION);
		$this->addUser(GO::session()->values['user_id'], GO_Base_Model_Acl::MANAGE_PERMISSION);

		return parent::afterSave();
	}

	protected function afterDelete() {

		return parent::afterDelete();
	}

}