<?php
/*
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 
 */

/**
 * The User model
 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.model
 * 
 * @property int $id
 * @property String $username
 * @property String $password
 * @property String $password_type
 * @property Boolean $enabled
 * @property String $first_name
 * @property String $middle_name
 * @property String $last_name
 * @property int $acl_id
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
 * 
 * @property $completeDateFormat
 */

class GO_Base_Model_User extends GO_Base_Db_ActiveRecord {

	public $generatedRandomPassword = false;
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Base_Model_User 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

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
	
	protected function getLocalizedName() {
		return GO::t('strUser');
	}

	public function customfieldsModel() {
		return 'GO_Users_Model_CustomFieldsRecord';
	}
	
	public function hasFiles(){
		return true;
	}
	
	public function getAttributes($outputType = 'formatted') {
		
		$attr = parent::getAttributes($outputType);
		$attr['name']=$this->getName();
		
		return $attr;
	}
	
	/**
	 * Getter function for the ACL function
	 * @return int 
	 */
	protected function getUser_id(){
		return $this->id;
	}

	public function init() {
		$this->columns['email']['regex'] = GO_Base_Util_String::get_email_validation_regex();
		$this->columns['email']['required'] = true;

		$this->columns['username']['required'] = true;
		$this->columns['username']['regex'] = '/^[A-Za-z0-9_\-\.\@]*$/';

		$this->columns['first_name']['required'] = true;

		$this->columns['last_name']['required'] = true;
		
		$this->columns['lastlogin']['gotype']='unixtimestamp';
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
	
	public function buildFilesPath() {
		return 'users/'.$this->username;
	}
	
	public function beforeSave(){
		
		if($this->isModified('password')){
			$this->password=crypt($this->password);
			$this->password_type='crypt';
		}
		
		return parent::beforeSave();
	}	
	

	public function afterSave($wasNew) {

		if($wasNew){
			$everyoneGroup = GO_Base_Model_Group::model()->findByPk(GO::config()->group_everyone);		
			$everyoneGroup->addUser($this->id);			
			
			$this->acl->user_id=$this->id;
			$this->acl->save();
		}	
		
		GO::modules()->callModuleMethod('saveUser', array(&$this, $wasNew));

		return parent::afterSave($wasNew);
	}
	
	protected function afterDelete() {
		
		
		//delete all acl records
		$stmt = GO_Base_Model_AclUsersGroups::model()->find(array(
				"by"=>array(array('user_id',$this->id))
		));
		
		while($r = $stmt->fetch())
			$r->delete();
		
		GO::modules()->callModuleMethod('deleteUser', array(&$this));
		
		return parent::afterDelete();
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
		if (GO::user() && $userId == GO::user()->id) {
			if (!isset(GO::session()->values['user_groups'])) {
				GO::session()->values['user_groups'] = array();

				$stmt = GO::getDbConnection()->query("SELECT group_id FROM go_users_groups WHERE user_id=" . intval($userId));
				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				while ($r = $stmt->fetch()) {
					GO::session()->values['user_groups'][] = $r['group_id'];
				}
			}
		
			return GO::session()->values['user_groups'];
		} else {
			$ids = array();
			$stmt = GO::getDbConnection()->query("SELECT group_id FROM go_users_groups WHERE user_id=" . intval($userId));
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
	
	
	protected function getCompleteDateFormat(){
		return $this->date_format[0].
						$this->date_separator.
						$this->date_format[1].
						$this->date_separator.
						$this->date_format[2];
	}
	
	
	/**
	 * Check if the password is correct for this user.
	 * 
	 * @param string $password
	 * @return boolean 
	 */
	public function checkPassword($password){

		if ($this->password_type == 'crypt') {
			if (crypt($password, $this->password) != $this->password) {
				return false;
			}
		} else {
			//pwhash is not set yet. We're going to use the old md5 hashed password
			if (md5($password) != $this->password) {
				return false;
			} else {				
				$this->password=$password;
				$this->save();				
			}
		}
		return true;
	}	
}

