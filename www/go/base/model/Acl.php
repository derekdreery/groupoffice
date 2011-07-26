<?php

class GO_Base_Model_Acl extends GO_Base_Db_ActiveRecord {
	const READ_PERMISSION=1;
	const WRITE_PERMISSION=2;
	const DELETE_PERMISSION=3;
	const MANAGE_PERMISSION=4;

	public $tableName = "go_acl_items";
	protected $_columns = array(
			'id' => array('type' => PDO::PARAM_INT),
			'user_id' => array('type' => PDO::PARAM_INT, 'required' => true),
			'description' => array('type' => PDO::PARAM_INT, 'required' => true),
	);

	public function getUserPermissionLevel($userId=0) {
		
		if($userId==0)
			$userId=GO::session ()->values['user_id'];
		
		if ($user_id > 0 && $acl_id > 0) {
			$sql = "SELECT a.acl_id, a.level FROM go_acl a " .
							"LEFT JOIN go_users_groups ug ON a.group_id=ug.group_id " .
							"WHERE a.acl_id=" . intval($this->id) . " AND " .
							"(ug.user_id=" . intval($userId);

			if (!$groups_only)
				$sql .= " OR a.user_id=" . intval($userId) . ") ORDER BY a.level DESC";
			else
				$sql .= ")";

			$this->getDbConnection()->query($sql);
			if ($r = $this->fetch()) {
				return intval($r['level']);
			}
		}
		return false;
	}

	public function addUser($userId, $level) {

		return $this->getDbConnection()->query("REPLACE INTO go_acl (acl_id,user_id,level) " .
						"VALUES ('" . intval($this->id) . "','" . intval($userId) . "','" . $this->escape($level) . "')");
	}

	public function addGroup($groupId, $level) {
		return $this->getDbConnection()->query("REPLACE INTO go_acl (acl_id,group_id,level) " .
						"VALUES ('" . intval($this->id) . "','" . intval($groupId) . "','" . $this->escape($level) . "')");
	}

	public function removeUser($userId) {
		
	}

	public function deleteUser() {
		
	}

	public function afterSave() {

		$this->addGroup(GO::config()->group_root, GO_Base_Model_Acl::MANAGE_PERMISSION);
		$this->addUser($this->user_id, GO_Base_Model_Acl::MANAGE_PERMISSION);

		return parent::afterSave();
	}

	protected function afterDelete() {

		return parent::afterDelete();
	}

}