<?php
/**
 * This class processes the submission of a contact form page. Call one of the
 * methods process_form() or process_simple_contact_form() for that.
 */
class formprocessor{
	
	/*
	 * For spammers...
	 */
	var $no_urls=true;

	var $user_groups=array();
	var $visible_user_groups=array();

	//will be replaced on send of confirmation
	var $confirmation_replacements=array();

	private $CREDENTIAL_KEYS = array ('username','first_name','middle_name','last_name','title','initials','sex','email',
			'home_phone','fax','cellular','address','address_no',
			'zip','city','state','country','company','department','function','work_phone',
			'work_fax');
	
	/**
	 * Fields that depend directly on user input.
	 */
//	private $_userCredentials = array();	
	private $_contactCredentials = array();	
	
	function localize_dbfields($post_fields)
	{
		global $lang, $GO_LANGUAGE;

		require_once($GO_LANGUAGE->get_language_file('addressbook'));

		$fields['name']=$lang['common']['name'];
		$fields['title']=$lang['common']['title'];
		$fields['first_name']=$lang['common']['firstName'];
		$fields['middle_name']=$lang['common']['middleName'];
		$fields['last_name']=$lang['common']['lastName'];
		$fields['initials']=$lang['common']['initials'];
		$fields['sex']=$lang['common']['sex'];
		$fields['birthday']=$lang['common']['birthday'];
		$fields['email']=$lang['common']['email'];
		$fields['country']=$lang['common']['country'];
		$fields['state']=$lang['common']['state'];
		$fields['city']=$lang['common']['city'];
		$fields['zip']=$lang['common']['zip'];
		$fields['address']=$lang['common']['address'];
		$fields['address_no']=$lang['common']['addressNo'];
		$fields['home_phone']=$lang['common']['phone'];
		$fields['work_phone']=$lang['common']['workphone'];
		$fields['fax']=$lang['common']['fax'];
		$fields['work_fax']=$lang['common']['workFax'];
		$fields['cellular']=$lang['common']['cellular'];
		$fields['company']=$lang['common']['company'];
		$fields['department']=$lang['common']['department'];
		$fields['function']=$lang['common']['function'];
		$fields['comment']=$lang['addressbook']['comment'];
		$fields['salutation']=$lang['common']['salutation'];

		$localized = array();
		foreach($post_fields as $key=>$value)
		{
			$newkey = isset($fields[$key]) ? $fields[$key] : $key;
			$localized[$newkey]=$value;
		}
		return $localized;
	}
	
	public function process_form()
	{
		global $GO_SECURITY, $GO_LANGUAGE, $lang;
//		global $GO_MODULES, $GO_CONFIG;
		require_once('../../GO.php');
		
		$this->check_required();
//		require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
//		$GO_USERS = new GO_USERS();

//		if(isset($_POST['language']) && $_POST['language']!=$GO_LANGUAGE->language)
//		{
//			$GO_LANGUAGE->set_language($_POST['language']);
//			require($GO_LANGUAGE->get_base_language_file('common'));
//		}

		if(!isset($_POST['salutation']))
			$_POST['salutation']=isset($_POST['sex']) ? GO::t('default_salutation_'.$_POST['sex']) : GO::t('default_salutation_unknown');

		//user registation
//		if(!empty($_POST['username'])) {
//			if($_POST['password1'] != $_POST['password2'])
//				throw new Exception(GO::t('error_match_pass','users'));
			
//			foreach($this->CREDENTIAL_KEYS as $key)
//				if(!empty($_REQUEST[$key]))
//					$this->_userCredentials[$key] = $_REQUEST[$key];

//			$this->_userCredentials['username']=$_POST['username'];
//			$this->_userCredentials['password']=$_POST['password1'];
//			$this->_userCredentials['first_name']=$_POST['first_name'];
//			$this->_userCredentials['last_name']=$_POST['last_name'];
//			$this->_userCredentials['email']=$_POST['email'];
//
//			$newUserModel = new GO_Base_Model_User();
//			$newUserModel->setAttributes($this->_userCredentials);
//			$newUserModel->setIsNew(true);
//			$newUserModel->save();
//			$this->_contactCredentials['go_user_id'] = $newUserModel->id;
			
//			require_once($GO_CONFIG->class_path.'base/auth.class.inc.php');
//			$GO_AUTH = new GO_AUTH();
//			$GO_AUTH->login($this->_userCredentials['username'], $this->_userCredentials['password']);
//		}

		if(!empty($_POST['email']) && !String::validate_email($_POST['email']))
			throw new Exception($lang['common']['invalidEmailError']);

		if(!empty($_REQUEST['addressbook']))
		{
			require($GO_LANGUAGE->get_language_file('addressbook'));
//			require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc.php');
//			$ab = new addressbook();

//			$addressbook = $ab->get_addressbook_by_name($_REQUEST['addressbook']);
			$addressbookModel = GO_Addressbook_Model_Addressbook::model()->findSingleByAttribute('name',$_REQUEST['addressbook']);
			
			if(empty($addressbookModel))
				throw new Exception('Addressbook not found!');

			foreach($this->CREDENTIAL_KEYS as $key)
				if(!empty($_REQUEST[$key]))
					$this->_contactCredentials[$key] = $_REQUEST[$key];

			if(isset($this->_contactCredentials['comment']) && is_array($this->_contactCredentials['comment']))
			{
				$comments='';
				foreach($this->_contactCredentials['comment'] as $key=>$value)
				{
					if($value=='date')
						$value = date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format']);

					if(!empty($value))
						$comments .= trim($key).":\n".trim($value)."\n\n";
				}
				$this->_contactCredentials['comment']=$comments;
			}


			if($this->no_urls && isset($this->_contactCredentials['comment']) && stripos($this->_contactCredentials['comment'], 'http'))
				throw new Exception('Sorry, but to prevent spamming we don\'t allow URL\'s in the message');

			$this->_contactCredentials['addressbook_id']=$addressbookModel->id;
			$this->_contactCredentials['email_allowed']=isset($_POST['email_allowed']) ? '1' : '0';

			if(!empty($this->_contactCredentials['company']) && empty($this->_contactCredentials['company_id']))
			{
				$companyActiveStmt = GO_Addressbook_Model_Company::model()->findByAttributes(array('name' => $this->_contactCredentials['company'],'addressbook_id' => $this->_contactCredentials['addressbook_id']));
				$companyModel = $companyActiveStmt->fetch();
				$this->_contactCredentials['company_id'] = $companyModel->id;
				if(empty($this->_contactCredentials['company_id']))
				{
					$company['addressbook_id'] = $this->_contactCredentials['addressbook_id'];
					$company['name'] = $this->_contactCredentials['company']; // bedrijfsnaam
					$company['user_id'] = $GO_SECURITY->user_id;
					$upCompanyModel = new GO_Addressbook_Model_Company();
					$upCompanyModel->setIsNew(true);
					$upCompanyModel->setAttributes($company);
					if (GO::modules()->isInstalled('customfields'))
						foreach ($_POST as $colKey => $colValue)
							if (substr($colKey,0,4)=='col_')
								$upCompanyModel->$colKey = $colValue;

					$upCompanyModel->save();
					$this->_contactCredentials['company_id'] = $upCompanyModel->id;
				}
			}
			if(isset($_POST['birthday']))
			{
				$this->_contactCredentials['birthday'] = Date::to_db_date($_POST['birthday'], false);

				if(!empty($_POST['birthday']) && $this->_contactCredentials['birthday']=='0000-00-00')
						throw new Exception($lang['common']['invalidDateError']);
			}

			unset($this->_contactCredentials['company']);
				
			/**
			 * Get existing contact using user input $_POST['contact_id'] or $_POST['email']
			 */
			if(!empty($_POST['contact_id'])){
				$existingContactModel = GO_Addressbook_Model_Contact::model()->findByPk($_POST['contact_id']);
			} elseif(!empty($this->_contactCredentials['email'])) {
				$existingContactModel = GO_Addressbook_Model_Contact::model()
					->find(
						GO_Base_Db_FindParams::newInstance()
							->single()
							->criteria(
								GO_Base_Db_FindCriteria::newInstance()
									->addCondition('addressbook_id',$this->_contactCredentials['addressbook_id'])
									->mergeWith(
										GO_Base_Db_FindCriteria::newInstance()
											->addCondition('email', $this->_contactCredentials['email'], '=', 't', false)
											->addCondition('email2', $this->_contactCredentials['email'], '=', 't', false)
											->addCondition('email3', $this->_contactCredentials['email'], '=', 't', false)
									)
							)
					);
			}
				
				
			if(!empty($existingContactModel))
			{
				$this->contact_id=$contact_id = $existingContactModel->id;
				$files_folder_id=$existingContactModel->files_folder_id;

				// Only update empty fields
				if(empty($_POST['contact_id']))
					foreach($this->_contactCredentials as $key=>$value)
						if($key!='comment')
							if(!empty($existingContactModel->$key))
								unset($this->_contactCredentials[$key]);

				$this->_contactCredentials['id']=$contact_id;

				if(!empty($existingContactModel->comment) && !empty($this->_contactCredentials['comment']))
				$this->_contactCredentials['comment']=$existingContactModel->comment."\n\n----\n\n".$this->_contactCredentials['comment'];

				if(empty($this->_contactCredentials['comment']))
					unset($this->_contactCredentials['comment']);

				$contactModel = GO_Addressbook_Model_Contact::model()->findByPk($this->contact_id);
				$contactModel->setAttributes($this->_contactCredentials);
				$contactModel->save();

			} else {
				$contactModel = new GO_Addressbook_Model_Contact();
				$contactModel->setAttributes($this->_contactCredentials);
				$contactModel->setIsNew(true);
				$contactModel->save();				
				
				$this->contact_id=$contact_id = $contactModel->id;
			
				if(isset($_POST['contact_id']) && empty($user_id) && GO::user()->id>0)
					$user_id=$this->user_id=GO::user()->id;

//				if(!empty($user_id)){
//					$user['id']=$user_id;
//					$user['contact_id']=$contact_id;
//					$GO_USERS->update_profile($user);
//				}
			}
			
			if(!$contact_id)
				throw new Exception(GO::t('saveError'));

//			if(GO::modules()->isInstalled('files'))
//			{
//				$full_path = $GO_CONFIG->file_storage_path.$contactModel->filesFolder->path;				
//
//				foreach($_FILES as $key=>$file)
//				{
//					if($key!='photo'){//photo is handled later
//						if (is_uploaded_file($file['tmp_name']))
//						{
//							move_uploaded_file($file['tmp_name'], $full_path.'/'.$file['name']);
//							chmod($full_path.'/'.$file['name'], GO::config()->file_create_mode);
//							GO_Files_Model_File::importFromFilesystem(new GO_Files_Model_File($full_path.'/'.$file['name']));
////							$fs->import_file($full_path.'/'.$file['name'], $files_folder_id);
//						}
//					}
//				}
//			}

//		See above around "$upCompanyModel->save()" to see how this functionality
//		works in the revamped version.
//		
//			if(isset($GO_MODULES->modules['customfields']))
//			{
//				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
//				$cf = new customfields();
//
//				$cf->update_fields(1, $contact_id, 2, $_POST, empty($existing_contact));
//			}
			
//			if(isset($GO_MODULES->modules['mailings']) && isset($_POST['mailings']))
//			{
//				require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
//				$ml = new mailings();

//			if (!empty($_POST['mailings'])) {
//				foreach($_POST['mailings'] as $mailing_name)
//				{
//					if(!empty($mailing_name))
//					{
//						$addresslistModel = GO_Addressbook_Model_Addresslist::model()->findSingleByAttribute('name', $mailing_name);
//						if (!empty($addresslistModel) && !$addresslistModel->hasManyMany('contacts', $contact_id))
//							$addresslistModel->addManyMany('contacts', $contact_id);
//						else
//							throw new Exception('Addresslist "'.$mailing_name.'" not found!');
////						$mailing=$ml->get_mailing_group_by_name($mailing_name);
////						if(!$mailing)
////						{
////							throw new Exception('Addresslist not found!');
////						}
////						if(!$ml->contact_is_in_group($contact_id, $mailing['id']))
////						$ml->add_contact_to_mailing_group($contact_id, $mailing['id']);
//					}
//				}
//			}

//			if ($this->contact_id > 0) {
//				if (isset($_FILES['photo']['tmp_name']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
//					move_uploaded_file($_FILES['photo']['tmp_name'], $GO_CONFIG->tmpdir . $_FILES['photo']['name']);
//					$tmp_file = $GO_CONFIG->tmpdir . $_FILES['photo']['name'];
//
//					$existingContactModel->photo = $tmp_file;
//					$result['image'] = GO::config()->root_path.'modules/addressbook/photo.php?contact_id='.$existingContactModel->id;
////					$result['image'] = $ab->save_contact_photo($tmp_file, $this->contact_id);
//				}
//			}

//			if(!isset($_POST['contact_id'])){
//				$notify_users = isset($_POST['notify_users']) ? explode(',', $_POST['notify_users']) : array();
//				if(!empty($_POST['notify_addressbook_owner']))
//				{
//					$notify_users[]=$addressbookModel->user_id;
//				}
//				$mail_to = array();
//				foreach($notify_users as $notify_user_id)
//				{
//					$user = $GO_USERS->get_user($notify_user_id);
//					$mail_to[]=$user['email'];
//				}
//				if(count($mail_to))
//				{
//
//					$url = create_direct_url('addressbook', 'showContact', array($contact_id));
//					$new_contact = $ab->get_contact($contact_id);
//					$company = !empty($new_contact['company_id']) ? $ab->get_company($new_contact['company_id']) : array('name'=>'');
//
//					$values = array('address_no', 'address', 'zip', 'city', 'state', 'country');
//					$formatted_address = str_replace(' ','<br />',$new_contact['address_format']);
//					$formatted_address = str_replace('{address}<br />{address_no}','{address} {address_no}',$formatted_address);
//					$formatted_address = str_replace('{address_no}<br />{address}','{address_no} {address}',$formatted_address);
//
//					foreach($values as $val)
//						$formatted_address = str_replace('{'.$val.'}', $new_contact[$val], $formatted_address);
//
//					$body = $lang['addressbook']['newContactFromSite'].':<br />';
//
//					$body .= "<br />".String::format_name($new_contact);
//					$body .= "<br />".$formatted_address;
//					if (!empty($new_contact['home_phone'])) $body .= "<br />".$lang['common']['phone'].': '.$new_contact['home_phone'];
//					if (!empty($new_contact['cellular'])) $body .= "<br />".$lang['common']['cellular'].': '.$new_contact['cellular'];
//					if (!empty($company['name'])) $body .= "<br /><br />".$company['name'];
//					if (!empty($new_contact['work_phone'])) $body .= "<br />".$lang['common']['workphone'].': '.$new_contact['work_phone'];
//
//					$body .= '<br /><a href="'.$url.'">'.$lang['addressbook']['clickHereToView'].'</a>'."<br />";
//
//					$mail_from = !empty($_POST['mail_from']) ? $_POST['mail_from'] : $GO_CONFIG->webmaster_email;
//
//					require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
//					$swift = new GoSwift(implode(',', $mail_to), $lang['addressbook']['newContactAdded']);
//					$swift->set_body($body);
//					$swift->set_from($mail_from, $GO_CONFIG->title);
//					try{
//						$swift->sendmail();
//					}
//					catch(Exception $e){
//						go_log(LOG_DEBUG, $e->getMessage());
//					}
//				}
//			}

//			if(isset($_POST['confirmation_template']))
//			{
//				if(empty($_POST['email']))
//				{
//					throw new Exception('Fatal error: No email given for confirmation e-mail!');
//				}
//
//				$url = create_direct_url('addressbook', 'showContact', array($contact_id));
//				$body = $lang['addressbook']['newContactFromSite'].'<br /><a href="'.$url.'">'.$lang['addressbook']['clickHereToView'].'</a>';
//
//				global $smarty;
//				$email = $smarty->fetch($_POST['confirmation_template']);
//
//				$pos = strpos($email,"\n");
//
//				$subject = trim(substr($email, 0, $pos));
//				$body = trim(substr($email,$pos));
//
//				require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
//				$swift = new GoSwift($_POST['email'], $subject);
//				$swift->set_body($body);
//				$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
//				$swift->sendmail();
//			}

//			if(isset($_POST['confirmation_email']))
//			{
//				if(File::path_leads_to_parent($_POST['confirmation_email']))
//					throw new Exception('Invalid path');
//				
//				$path = $GO_CONFIG->file_storage_path.$_POST['confirmation_email'];
//				if(!file_exists($path)){
//					$path = dirname($GO_CONFIG->get_config_file()).'/'.$_POST['confirmation_email'];
//				}
//				$email = file_get_contents($path);
//				require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
//				$swift = new GoSwiftImport($email);
//				$body=$swift->body;
//
//				foreach($this->confirmation_replacements as $key=>$value){
//					$body = str_replace('{'.$key.'}', $value, $body);
//				}
//
//				if(isset($GO_MODULES->modules['mailings'])){
//					require_once($GO_MODULES->modules['mailings']['path'].'classes/templates.class.inc.php');
//					$tp = new templates();
//
//					$body=$tp->replace_contact_data_fields($body, $this->contact_id, false);
//				}
//
//				$swift->set_body($body, 'html');
//
//				$swift->set_to($_POST['email']);
//				$swift->sendmail();
//			}
		}
	}

	function check_required(){
		global $lang;
		//remove empty texts
		
		if(isset($_POST['empty_texts'])){
			foreach($_POST['empty_texts'] as $value){
				
				$value = explode(':',$value);
				
				$key = $value[0];
				$value=$value[1];
				
				if($pos = strpos($key, '['))
				{
					$key1 = substr($key,0,$pos);
					$key2 = substr($key,$pos+1, -1);

					if(isset($_POST[$key1][$key2]) && $_POST[$key1][$key2]==$value){
						$_POST[$key1][$key2]='';
					}
				}else
				{
					if(isset($_POST[$key]) && $_POST[$key]==$value){
						$_POST[$key]='';
					}
				}
			}
		}
		
		if(isset($_POST['required']))
		{
			foreach($_POST['required'] as $key)
			{
				if($pos = strpos($key, '['))
				{
					$key1 = substr($key,0,$pos);
					$key2 = substr($key,$pos+1, -1);

					if(empty($_POST[$key1][$key2]))
					{
						throw new Exception($lang['common']['missingField']);
					}
				}else
				{
					if(empty($_POST[$key]))
					{
						throw new Exception($lang['common']['missingField']);
					}
				}
			}
		}
	}

	public function process_simple_contact_form($email, $from_email='', $from_name=''){
		global $GO_CONFIG, $lang;

		$this->check_required();
		
		if (empty($_POST['email']) || empty($_POST['subject']))
		{
			throw new Exception($lang['common']['missingField']);
		}elseif(!String::validate_email($_POST['email']))
		{
			throw new Exception($lang['common']['invalidEmailError']);
		}

		$body = isset($_POST['body']) ? $_POST['body'] : '';

		if(isset($_POST['extra'])){
			foreach($_POST['extra'] as $name=>$value){
				if(!empty($value))
					$body .= "\n\n".$name.":\n".$value;
			}
		}

		if(empty($from_email))
			$from_email = $_POST['email'];

		if(empty($from_name))
			$from_name = isset($_POST['name']) ? $_POST['name'].' (Via website)' : $from_email;		

		if($this->no_urls && stripos($body, 'http')!==false){
					throw new Exception('Sorry, but to prevent spamming we don\'t allow URL\'s in the message');
				}

		//if(empty($body))
			//throw new Exception($lang['common']['missingField']);

		require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
		$swift = new GoSwift($email, $_POST['subject']);
		$swift->set_body($body, 'plain');
		$swift->set_from($from_email, $from_name);
		return $swift->sendmail();
	}
}