<?php

class GO_Addressbook_AddressbookModule extends GO_Base_Module{

	public function author() {
		return 'Merijn Schering';
	}
	
	public function authorEmail() {
		return 'mschering@intermesh.nl';
	}
	
	public static function initListeners() {
		GO_Base_Model_User::model()->addListener('delete', "GO_Addressbook_AddressbookModule", "deleteUser");
	}
	
	// Load the settings for the "Addresslists" tab in the Settings panel
	public static function loadSettings(&$settingsController, &$params, &$response, $user) {

		$findParams = GO_Base_Db_FindParams::newInstance()
						->joinCustomFields();
		
		$contact = $user->contact($findParams);
		if($contact){
			
			// If there are customfields then load them too in the settings panel
			$contactCfs = $contact->getCustomfieldsRecord();			
			if($contactCfs)
				$response['data'] = array_merge($response['data'],$contactCfs->getAttributes());
			
			$response['data']['email_allowed'] = $contact->email_allowed;
		
			$addresslists = $contact->addresslists();
			foreach($addresslists as $addresslist){
				$response['data']['addresslist_'.$addresslist->id]=1;
			}
		}
			
		return parent::loadSettings($settingsController, $params, $response, $user);
	}
	
	// Save the settings for the "Addresslists" tab in the Settings panel
	public static function submitSettings(&$settingsController, &$params, &$response, $user) {
		
		$contact = $user->contact;
		
		if($contact){
		
			$addresslists = GO_Addressbook_Model_Addresslist::model()->find(GO_Base_Db_FindParams::newInstance()->permissionLevel(GO_Base_Model_Acl::READ_PERMISSION));
			foreach($addresslists as $addresslist){
				$linkModel = $addresslist->hasManyMany('contacts', $contact->id);
				$mustHaveLinkModel = isset($params['addresslist_' . $addresslist->id]);
				if ($linkModel && !$mustHaveLinkModel) {
					$linkModel->delete();
				}
				if (!$linkModel && $mustHaveLinkModel) {
					$addresslist->addManyMany('contacts',$contact->id);
				}
			}	
		}
		
		return parent::submitSettings($settingsController, $params, $response, $user);
	}
	
	/**
	 * 
	 * When a user is created, updated or logs in this function will be called.
	 * The function can check if the default calendar, addressbook, notebook etc.
	 * is created for this user.
	 * 
	 */
	public static function firstRun(){
		parent::firstRun();
	}
	
	public static function deleteUser($user){
		GO_Addressbook_Model_Addresslist::model()->deleteByAttribute('user_id', $user->id);
		GO_Addressbook_Model_Template::model()->deleteByAttribute('user_id', $user->id);		
	}
	
	public function autoInstall() {
		return true;
	}
	
	public function install() {
		parent::install();
		
		$default_language = GO::config()->default_country;
		if (empty($default_language))
			$default_language = 'US';

		$addressbook = new GO_Addressbook_Model_Addressbook();
		$addressbook->setAttributes(array(
				'user_id' => 1,
				'name' => GO::t('prospects','addressbook'),
//				'default_iso_address_format' => $default_language,
				'default_salutation' => GO::t('defaultSalutation','addressbook')
		));
		$addressbook->save();
		$addressbook->acl->addGroup(GO::config()->group_internal,GO_Base_Model_Acl::WRITE_PERMISSION);

		$addressbook = new GO_Addressbook_Model_Addressbook();
		$addressbook->setAttributes(array(
				'user_id' => 1,
				'name' => GO::t('suppliers','addressbook'),
//				'default_iso_address_format' => $default_language,
				'default_salutation' => GO::t('defaultSalutation','addressbook')
		));
		$addressbook->save();
		$addressbook->acl->addGroup(GO::config()->group_internal,GO_Base_Model_Acl::WRITE_PERMISSION);

		if (!is_dir(GO::config()->file_storage_path.'contacts/contact_photos'))
			mkdir(GO::config()->file_storage_path.'contacts/contact_photos',0755, true);

		$addressbook = new GO_Addressbook_Model_Addressbook();
		$addressbook->setAttributes(array(
			'user_id' => 1,
			'name' => GO::t('customers','addressbook'),
			'default_salutation' => GO::t('defaultSalutation','addressbook')
		));
		$addressbook->save();
		$addressbook->acl->addGroup(GO::config()->group_internal,GO_Base_Model_Acl::WRITE_PERMISSION);
		
		//Each user should have a contact
		$stmt = GO_Base_Model_User::model()->find(GO_Base_Db_FindParams::newInstance()->ignoreAcl());
		while($user = $stmt->fetch())
			$user->createContact();
		
		$message = new GO_Base_Mail_Message();
		$message->setHtmlAlternateBody('{salutation},<br />
<br />
{body}<br />
<br />
'.GO::t('greet','addressbook').'<br />
<br />
<br />
{user:name}<br />
{usercompany:name}<br />');
		
		$template = new GO_Addressbook_Model_Template();
		$template->setAttributes(array(
			'content' => $message->toString(),
			'name' => GO::t('default'),
			'type' => GO_Addressbook_Model_Template::TYPE_EMAIL,
			'user_id' => 1
		));
		$template->save();
		$template->acl->addGroup(GO::config()->group_internal);
		
		
		$dt = GO_Addressbook_Model_Template::model()->findSingleByAttribute('name', 'Letter');
		if (!$dt) {
			$dt = new GO_Addressbook_Model_Template();	
			$dt->type = GO_Addressbook_Model_Template::TYPE_DOCUMENT;
			$dt->content = file_get_contents(GO::modules()->addressbook->path . 'install/letter_template.docx');
			$dt->extension = 'docx';
			$dt->name = 'Letter';
			$dt->save();
			
			$dt->acl->addGroup(GO::config()->group_internal);
		}
		
		
		$this->setFolderPermissions();
		
	}
	
	public function setFolderPermissions(){
		if(GO::modules()->isInstalled('files')){
			$folder = GO_Files_Model_Folder::model()->findByPath('addressbook', true);
			if($folder){
				$folder->acl_id=GO::modules()->addressbook->acl_id;
				$folder->readonly=1;
				$folder->save();
			}			
			
			$folder = GO_Files_Model_Folder::model()->findByPath('addressbook/photos', true);
			if($folder && !$folder->acl_id){
				$folder->setNewAcl(1);
				$folder->readonly=1;
				$folder->save();
			}			
			
		  //hide old contacts folder if it exists
			$folder = GO_Files_Model_Folder::model()->findByPath('contacts');
			if($folder){
				if(!$folder->acl_id){
					$folder->setNewAcl(1);
					$folder->readonly=1;
					$folder->save();
				}  else {
					
					$folder->getAcl()->clear();
					
				}
			}		
		}
		
	}
	
	
	public function checkDatabase(&$response) {
		
		$this->setFolderPermissions();
		
		return parent::checkDatabase($response);
	}

}