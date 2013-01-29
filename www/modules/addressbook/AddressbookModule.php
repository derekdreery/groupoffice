<?php

class GO_Addressbook_AddressbookModule extends GO_Base_Module{
	
	
	public function author() {
		return 'Merijn Schering';
	}
	
	public function authorEmail() {
		return 'mschering@intermesh.nl';
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
				'default_iso_address_format' => $default_language,
				'default_salutation' => GO::t('defaultSalutation','addressbook')
		));
		$addressbook->save();
		$addressbook->acl->addGroup(GO::config()->group_internal,GO_Base_Model_Acl::WRITE_PERMISSION);

		$addressbook = new GO_Addressbook_Model_Addressbook();
		$addressbook->setAttributes(array(
				'user_id' => 1,
				'name' => GO::t('suppliers','addressbook'),
				'default_iso_address_format' => $default_language,
				'default_salutation' => GO::t('defaultSalutation','addressbook')
		));
		$addressbook->save();
		$addressbook->acl->addGroup(GO::config()->group_internal,GO_Base_Model_Acl::WRITE_PERMISSION);

		if(GO::config()->product_name=="Group-Office"){
			$company = new GO_Addressbook_Model_Company();
			$company->setAttributes(array(
				'addressbook_id' => $addressbook->id,
				'name' => 'Intermesh BV',
				'address' => 'Zuid Willemsvaart',
				'address_no' => '35',
				'zip' => '5211 SB',
				'city' => '\'s-Hertogenbosch',
				'state' => 'Noord-Brabant',
				'country' => 'NL',
				'iso_address_format' => $default_language,
				'post_address' => 'Zuid Willemsvaart',
				'post_address_no' => '35',
				'post_zip' => '5211 SB',
				'post_city' => '\'s-Hertogenbosch',
				'post_state' => 'Noord-Brabant',
				'post_country' => 'NL',
				'post_iso_address_format' => $default_language,
				'phone' => '+31 (0) 73 - 644 55 08',
				'fax' => '+31 (0) 84 738 03 70',
				'email' => 'info@intermesh.nl',
				'homepage' => 'http://www.intermesh.nl',
				'bank_no' => '',
				'vat_no' => 'NL 1502.03.871.B01',
				'user_id' => 1,
				'comment' => ''
			));
			$company->save();
		}

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
			'type' => '0',
			'user_id' => 1
		));
		$template->save();
		$template->acl->addGroup(GO::config()->group_internal);
	}
	
	
	public function checkDatabase(&$response) {
		
		if(GO::modules()->isInstalled('files')){
			$folder = GO_Files_Model_Folder::model()->findByPath('projects');
			$folder->acl_id=GO::modules()->projects->acl_id;
			$folder->readonly=1;
			$folder->save();
		}
		
		return parent::checkDatabase($response);
	}
}