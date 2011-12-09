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
	public $passwordConfirm;
	
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
		return false;
	}
	
	public function hasLinks() {
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
		
		if($this->isModified('password') && isset($this->passwordConfirm) && $this->passwordConfirm!=$this->password){
			$this->setValidationError('passwordConfirm', GO::t('passwordMatchError'));
		}

		if ($this->_maxUsersReached())				
			throw new Exception(GO::t('max_users_reached', 'users'));

		if (!GO::config()->allow_duplicate_email) {
			$existing = $this->findSingleByAttribute('email', $this->email);
			if (($this->isNew && $existing) || $existing && $existing->id != $this->id )
				$this->setValidationError('email', GO::t('error_email_exists', 'users'));
		}

		$existing = $this->findSingleByAttribute('username', $this->username);
		if (($this->isNew && $existing) || $existing && $existing->id != $this->id )
			$this->setValidationError('username', GO::t('error_username_exists', 'users'));

		if (empty($this->password)) {
			$this->password = GO_Base_Util_String::randomPassword();
			$this->generatedRandomPassword = true;
		}

		return parent::validate();
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
			
			$internalGroup = GO_Base_Model_Group::model()->findByPk(GO::config()->group_internal);
			if($internalGroup)
				$internalGroup->addUser($this->id);
			
			$this->acl->user_id=$this->id;
			$this->acl->save();
			
			$defaultModels = GO_Base_Model_AbstractUserDefaultModel::getAllUserDefaultModels();
		
			foreach($defaultModels as $model){
				$model->getDefault($this);
			}
		}	
		
		$this->createContact();
		
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
		
		$defaultModels = GO_Base_Model_AbstractUserDefaultModel::getAllUserDefaultModels();
	
		foreach($defaultModels as $model){
			$model->deleteByAttribute('user_id',$this->id);
		}
		
		GO::modules()->callModuleMethod('deleteUser', array(&$this));
		
		return parent::afterDelete();
	}
		
	

	/**
	 *
	 * @return String Full formatted name of the user
	 */
	public function getName() {
		return GO_Base_Util_String::format_name($this->last_name, $this->first_name, $this->middle_name,'first_name');
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
	
	public function defaultAttributes() {
		$attr = parent::defaultAttributes();
		
		$attr['language']=GO::config()->language;
		$attr['date_format']=GO::config()->default_date_format;
		$attr['date_separator']=GO::config()->default_date_separator;
		$attr['theme']=GO::config()->theme;
		$attr['timezone']=GO::config()->default_timezone;
		$attr['first_weekday']=GO::config()->default_first_weekday;
		$attr['currency']=GO::config()->default_currency;
		$attr['decimal_separator']=GO::config()->default_decimal_separator;
		$attr['thousands_separator']=GO::config()->default_thousands_separator;
		$attr['time_format']=GO::config()->default_time_format;
		$attr['sort_name']=GO::config()->default_sort_name;
	
		
		return $attr;
	}
	
	/**
	 * Get the contact model of this user. All the user profiles are stored in the
	 * addressbook.
	 * 
	 * @return GO_Addressbook_Model_Contact 
	 */
	public function createContact(){
		if (GO::modules()->isInstalled("addressbook")) {
			$contact = $this->contact();
			if (!$contact) {
				$contact = new GO_Addressbook_Model_Contact();
				$addressbook = GO_Addressbook_Model_Addressbook::model()->getUsersAddressbook();
				$contact->go_user_id = $this->id;
				$contact->addressbook_id = $addressbook->id;
				$contact->first_name = $this->first_name;
				$contact->middle_name = $this->middle_name;
				$contact->last_name = $this->last_name;
				$contact->email = $this->email;
				$contact->save();
			}			
			return $contact;
		}else
		{
			return false;
		}
	}
}

