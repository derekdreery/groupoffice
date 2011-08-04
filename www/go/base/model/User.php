<?php
 class GO_Base_Model_User extends GO_Base_Db_ActiveRecord{

	public function aclField(){
		return 'acl_id';	
	}
	
	public function tableName(){
		return 'go_users';
	}


	/**
	 *
	 * @return String Full formatted name of the user
	 */
	public function getName(){
		return GO_Base_Util_String::format_name($this->last_name, $this->first_name, $this->middle_name);
	}
	
	/**
	 * Returns an array of user group id's
	 * 
	 * @return Array 
	 */
	public static function getGroupIds($userId) {
		if ($userId == GO::session()->values['user_id']) {
			if (!isset(GO::session()->values['user_groups'])) {
				GO::session()->values['user_groups'] = array();

				$stmt = $this->getDbConnection()->query("SELECT group_id FROM go_users_groups WHERE user_id=".intval($userId));
				while ($r = $this->next_record()) {
					GO::session()->values['user_groups'][] = $r['group_id'];
				}
			}

			return GO::session()->values['user_groups'];
		} else {
			$ids = array();
			$stmt = $this->getDbConnection()->query("SELECT group_id FROM go_users_groups WHERE user_id=".intval($userId));
			while ($r = $stmt->fetch()) {
				$ids[] = $r['group_id'];
			}
			return $ids;
		}
	}
	
	public function isAdmin(){
		return in_array(GO::config()->group_root, GO_Base_Model_User::getGroupIds($this->id));
	}
	
	public function getModulePermissionLevel($moduleId){
		if(GO::modules()->$moduleId)						
			return GO::modules()->$moduleId->permissionLevel;
		else
			return false;
	}
}

