<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * The User model
 * 
 * @property int $id
 * @property String $username
 * @property String $password
 * @property String $password_type
 * @property Boolean $enabled
 * @property String $first_name
 * @property String $middle_name
 * @property String $last_name
 * @property String $initials
 * @property String $title
 * @property enum('M','F') $sex
 * @property date $birthday
 * @property String $email
 * @property String $company
 * @property String $department
 * @property String $function
 * @property String $home_phone
 * @property String $work_phone
 * @property String $fax
 * @property String $cellular
 * @property String $country
 * @property String $state
 * @property String $city
 * @property String $zip
 * @property String $address
 * @property String $address_no
 * @property String $homepage
 * @property String $work_address
 * @property String $work_address_no
 * @property String $work_zip
 * @property String $work_country
 * @property String $work_state
 * @property String $work_city
 * @property String $work_fax
 * @property int $acl_id
 * @property String $date_format
 * @property String $date_separator
 * @property String $time_format
 * @property String $thousands_separator
 * @property String $decimal_separator
 * @property String $currency
 * @property int $logins
 * @property int $lastlogin
 * @property int $ctime
 * @property int $max_rows_list
 * @property String $timezone
 * @property String $start_module
 * @property String $language
 * @property String $theme
 * @property int $first_weekday
 * @property String $sort_name
 * @property String $bank
 * @property String $bank_no
 * @property int $mtime
 * @property Boolean $mute_sound
 * @property Boolean $mute_reminder_sound
 * @property Boolean $mute_new_mail_sound
 * @property Boolean $show_smilies
 * @property String $list_separator
 * @property String $text_separator
 * @property int $files_folder_id
 * @property int $mail_reminders
 * @property int $popup_reminders
 * @property int $contact_id
 * @property String $cache
 */

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
	
	public function linkType(){
		return 8;
	}
	
	public function customfieldsModel() {
		return 'GO_Users_Model_CustomFieldsRecord';
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
				if (($this->isNew && $existing) || $existing && $existing->id != $this->id )
					throw new Exception(GO::t('error_email_exists', 'users'));
			}

			$existing = $this->findSingleByAttribute('username', $this->username);
			if (($this->isNew && $existing) || $existing && $existing->id != $this->id )
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
	
	

	public function afterSave($wasNew) {

		if($wasNew){
			$everyoneGroup = GO_Base_Model_Group::model()->findByPk(GO::config()->group_everyone);		
			$everyoneGroup->addUser($this->id);
			
		}	

		return parent::afterSave($wasNew);
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

