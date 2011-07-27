<?php
 class GO_Base_Model_User extends GO_Base_Db_ActiveRecord{

	public $aclField=false;

	public $tableName="go_users";

	protected $_columns=array(
		'id'=>array('type'=>PDO::PARAM_INT),
		'username'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'password'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>64, 'gotype'=>'textfield'),
		'enabled'=>array('type'=>PDO::PARAM_STR, 'required'=>false, 'gotype'=>'textfield'),
		'first_name'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'middle_name'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'last_name'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>100, 'gotype'=>'textfield'),
		'initials'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>10, 'gotype'=>'textfield'),
		'title'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>10, 'gotype'=>'textfield'),
		'sex'=>array('type'=>PDO::PARAM_STR, 'required'=>false, 'gotype'=>'textfield'),
		'birthday'=>array('type'=>PDO::PARAM_STR, 'required'=>false, 'gotype'=>'textfield'),
		'email'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>100, 'gotype'=>'textfield'),
		'company'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'department'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'function'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'home_phone'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>30, 'gotype'=>'textfield'),
		'work_phone'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>30, 'gotype'=>'textfield'),
		'fax'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>30, 'gotype'=>'textfield'),
		'cellular'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>30, 'gotype'=>'textfield'),
		'country'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>2, 'gotype'=>'textfield'),
		'state'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'city'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'zip'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>10, 'gotype'=>'textfield'),
		'address'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>100, 'gotype'=>'textfield'),
		'address_no'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>10, 'gotype'=>'textfield'),
		'homepage'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>100, 'gotype'=>'textfield'),
		'work_address'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>100, 'gotype'=>'textfield'),
		'work_address_no'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>10, 'gotype'=>'textfield'),
		'work_zip'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>10, 'gotype'=>'textfield'),
		'work_country'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>2, 'gotype'=>'textfield'),
		'work_state'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'work_city'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'work_fax'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>30, 'gotype'=>'textfield'),
		'acl_id'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'date_format'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>20, 'gotype'=>'textfield'),
		'date_separator'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>1, 'gotype'=>'textfield'),
		'time_format'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>10, 'gotype'=>'textfield'),
		'thousands_separator'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>1, 'gotype'=>'textfield'),
		'decimal_separator'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>1, 'gotype'=>'textfield'),
		'currency'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>3, 'gotype'=>'textfield'),
		'logins'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'lastlogin'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'registration_time'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'max_rows_list'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'timezone'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'start_module'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'language'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>20, 'gotype'=>'textfield'),
		'theme'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>20, 'gotype'=>'textfield'),
		'first_weekday'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'sort_name'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>20, 'gotype'=>'textfield'),
		'bank'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'bank_no'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>50, 'gotype'=>'textfield'),
		'mtime'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'mute_sound'=>array('type'=>PDO::PARAM_STR, 'required'=>false, 'gotype'=>'textfield'),
		'list_separator'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>3, 'gotype'=>'textfield'),
		'text_separator'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>3, 'gotype'=>'textfield'),
		'files_folder_id'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'mail_reminders'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'popup_reminders'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'password_type'=>array('type'=>PDO::PARAM_STR, 'required'=>false,'length'=>20, 'gotype'=>'textfield'),
		'contact_id'=>array('type'=>PDO::PARAM_INT, 'required'=>false),
		'cache'=>array('type'=>PDO::PARAM_STR, 'required'=>false, 'gotype'=>'textarea'),
		
	);	
	
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
	
	public function getModulePermissionLevel($moduleId){
		if(GO::modules()->$moduleId)						
			return GO::modules()->$moduleId->permissionLevel;
		else
			return false;
	}
}

