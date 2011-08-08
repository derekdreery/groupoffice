<?php

class GO_Base_Model_User extends GO_Base_Db_ActiveRecord {

	public $generatedRandomPassword = false;

	public function aclField() {
		return 'acl_id';
	}

	public function tableName() {
		return 'go_users';
	}

	public function relations() {
		return array(
				'contact' => array('type' => self::HAS_ONE, 'model' => 'GO_Addressbook_Model_Contact', 'field' => 'go_user_id')
		);
	}
	
	public function hasFiles(){
		return true;
	}

	public function init() {
		$this->columns['email']['regex'] = GO_Base_Util_String::get_email_validation_regex();
		$this->columns['email']['required'] = true;

		$this->columns['username']['required'] = true;
		$this->columns['username']['regex'] = '/^[A-Za-z0-9_\-\.\@]*$/';

		$this->columns['first_name']['required'] = true;

		$this->columns['last_name']['required'] = true;
		return parent::init();
	}

	private function _maxUsersReached() {
		return GO::config()->max_users > 0 && $this->count() >= GO::config()->max_users;
	}

	public function validate() {
		if (parent::validate()) {

			if ($this->_maxUsersReached())
				throw new Exception(GO::t('max_users_reached', 'users'));

			if (!GO::config()->allow_duplicate_email) {
				$existing = $this->findSingleByAttribute('email', $this->email);
				if ($existing)
					throw new Exception(GO::t('error_email_exists', 'users'));
			}

			$existing = $this->findSingleByAttribute('username', $this->username);
			if ($existing)
				throw new Exception(GO::t('error_username_exists', 'users'));

			if (!isset($this->language))
				$this->language = GO::config()->language;

			if (!isset($this->currency))
				$this->currency = GO::config()->default_currency;

			if (!isset($this->decimal_separator))
				$this->decimal_separator = GO::config()->default_decimal_separator;

			if (!isset($this->thousands_separator))
				$this->thousands_separator = GO::config()->default_thousands_separator;

			if (!isset($this->time_format))
				$this->time_format = GO::config()->default_time_format;

			if (!isset($this->date_format))
				$this->date_format = GO::config()->default_date_format;

			if (!isset($this->date_separator))
				$this->date_separator = GO::config()->default_date_separator;

			if (!isset($this->first_weekday))
				$this->first_weekday = GO::config()->default_first_weekday;

			if (!isset($this->timezone))
				$this->timezone = GO::config()->default_timezone;

			if (!isset($this->sort_name))
				$this->sort_name = GO::config()->default_sort_name;


			if (empty($this->password)) {
				$this->password = GO_Base_Util_String::random_password();
				$this->generatedRandomPassword = true;
			}


			return true;
		}
	}
	
	protected function buildFilesPath() {
		return 'users/'.$this->username;
	}

	public function afterSave() {



		return parent::afterSave();
	}

	/**
	 *
	 * @return String Full formatted name of the user
	 */
	public function getName() {
		return GO_Base_Util_String::format_name($this->last_name, $this->first_name, $this->middle_name);
	}

	/**
	 * Returns an array of user group id's
	 * 
	 * @return Array 
	 */
	public static function getGroupIds($userId) {
		if ($userId == GO::user()->id) {
			if (!isset(GO::session()->values['user_groups'])) {
				GO::session()->values['user_groups'] = array();

				$stmt = $this->getDbConnection()->query("SELECT group_id FROM go_users_groups WHERE user_id=" . intval($userId));
				while ($r = $this->next_record()) {
					GO::session()->values['user_groups'][] = $r['group_id'];
				}
			}

			return GO::session()->values['user_groups'];
		} else {
			$ids = array();
			$stmt = $this->getDbConnection()->query("SELECT group_id FROM go_users_groups WHERE user_id=" . intval($userId));
			while ($r = $stmt->fetch()) {
				$ids[] = $r['group_id'];
			}
			return $ids;
		}
	}

	public function isAdmin() {
		return in_array(GO::config()->group_root, GO_Base_Model_User::getGroupIds($this->id));
	}

	public function getModulePermissionLevel($moduleId) {
		if (GO::modules()->$moduleId)
			return GO::modules()->$moduleId->permissionLevel;
		else
			return false;
	}

}

