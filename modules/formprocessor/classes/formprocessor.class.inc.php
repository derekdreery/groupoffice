<?php
class formprocessor{
	/*
	 * For spammers...
	 */
	var $no_urls=true;

	var $user_groups=array();
	var $visible_user_groups=array();

	//will be replaced on send of confirmation
	var $confirmation_replacements=array();

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


	function process_form()
	{
		global $GO_SECURITY, $GO_LANGUAGE, $GO_MODULES, $GO_CONFIG, $lang;

		$this->check_required();


		require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		

		if(isset($_POST['language']) && $_POST['language']!=$GO_LANGUAGE->language)
		{
			$GO_LANGUAGE->set_language($_POST['language']);
			require($GO_LANGUAGE->get_base_language_file('common'));
		}

		if(!isset($_POST['salutation']))
			$_POST['salutation']=isset($_POST['sex']) ? $lang['common']['default_salutation'][$_POST['sex']] : $lang['common']['default_salutation']['unknown'];


		//user registation
		if(!empty($_POST['username'])){
			$credentials = array ('username','first_name','middle_name','last_name','title','initials','sex','email',
			'home_phone','fax','cellular','address','address_no',
			'zip','city','state','country','company','department','function','work_phone',
			'work_fax');

			if($_POST['password1'] != $_POST['password2'])
			{
				require($GO_LANGUAGE->get_language_file('users'));
				throw new Exception($lang['users']['error_match_pass']);
			}

			foreach($credentials as $key)
			{
				if(!empty($_REQUEST[$key]))
				{
					$user_credentials[$key] = $_REQUEST[$key];
				}
			}
			$user_credentials['password']=$_POST['password1'];

			$this->user_id=$user_id=$GO_USERS->add_user($user_credentials, $this->user_groups, $this->visible_user_groups);

			require_once($GO_CONFIG->class_path.'base/auth.class.inc.php');
			$GO_AUTH = new GO_AUTH();

			$GO_AUTH->login($user_credentials['username'], $user_credentials['password']);
		}		

		if(!empty($_POST['email']) && !String::validate_email($_POST['email']))
		{
			throw new Exception($lang['common']['invalidEmailError']);
		}

		if(!empty($_REQUEST['addressbook']))
		{
			require($GO_LANGUAGE->get_language_file('addressbook'));
			require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc.php');
			$ab = new addressbook();

			$addressbook = $ab->get_addressbook_by_name($_REQUEST['addressbook']);
			if(!$addressbook)
			{
				throw new Exception('Addressbook not found!');
			}

			$credentials = array ('first_name','middle_name','last_name','title','initials','sex','email',
			'email2','email3','home_phone','fax','cellular','comment','address','address_no',
			'zip','city','state','country','company','department','function','work_phone',
			'work_fax','salutation');			

			foreach($credentials as $key)
			{
				if(!empty($_REQUEST[$key]))
				{
					$contact_credentials[$key] = $_REQUEST[$key];
				}
			}

			if(isset($contact_credentials['comment']) && is_array($contact_credentials['comment']))
			{
				$comments='';
				foreach($contact_credentials['comment'] as $key=>$value)
				{
					if($value=='date')
					{
						$value = date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format']);
					}
					if(!empty($value))
					{
						$comments .= trim($key).":\n".trim($value)."\n\n";
					}
				}
				$contact_credentials['comment']=$comments;
			}


			if($this->no_urls && isset($contact_credentials['comment']) && stripos($contact_credentials['comment'], 'http')){
				throw new Exception('Sorry, but to prevent spamming we don\'t allow URL\'s in the message');
			}

			$contact_credentials['addressbook_id']=$addressbook['id'];
			$contact_credentials['iso_address_format']=$addressbook['default_iso_address_format'];
			$contact_credentials['email_allowed']=isset($_POST['email_allowed']) ? '1' : '0';

				
				

			if(!empty($contact_credentials['company']) && empty($contact_credentials['company_id']))
			{
				if(!$contact_credentials['company_id'] = $ab->get_company_id_by_name($contact_credentials['company'], $contact_credentials['addressbook_id']))
				{
					$company['addressbook_id'] = $contact_credentials['addressbook_id'];
					$company['name'] = $contact_credentials['company']; // bedrijfsnaam
					$company['user_id'] = $GO_SECURITY->user_id;
					$company['iso_address_format']=$company['post_iso_address_format']=$addressbook['default_iso_address_format'];
					$contact_credentials['company_id'] = $ab->add_company($company);
				}
			}
			if(isset($_POST['birthday']))
			{
				$contact_credentials['birthday'] = Date::to_db_date($_POST['birthday'], false);

				if(!empty($_POST['birthday']) && $contact_credentials['birthday']=='0000-00-00')
						throw new Exception($lang['common']['invalidDateError']);
			}

			unset($contact_credentials['company']);
				
				
			$existing_contact=false;
			if(!empty($_POST['contact_id'])){
				$existing_contact = $ab->get_contact($_POST['contact_id']);
			}elseif(!empty($contact_credentials['email']))
			{
				$existing_contact = $ab->get_contact_by_email($contact_credentials['email'], 0, $contact_credentials['addressbook_id']);
			}
				
				
			if($existing_contact)
			{
				$this->contact_id=$contact_id = $existing_contact['id'];

				$files_folder_id=$existing_contact['files_folder_id'];


				/*
				 * Only update empty fields
				 */

				if(empty($_POST['contact_id'])){
					foreach($contact_credentials as $key=>$value)
					{
						if($key!='comment')
						{
							if(!empty($existing_contact[$key]))
							{
								unset($contact_credentials[$key]);
							}
						}
					}
				}

				$contact_credentials['id']=$contact_id;

				if(!empty($existing_contact['comment']) && !empty($contact_credentials['comment']))
				$contact_credentials['comment']=$existing_contact['comment']."\n\n----\n\n".$contact_credentials['comment'];

				if(empty($contact_credentials['comment']))
					unset($contact_credentials['comment']);

				//var_dump($contact_credentials);

				$ab->update_contact($contact_credentials);
			}else
			{
				$this->contact_id=$contact_id = $ab->add_contact($contact_credentials);
				$files_folder_id=$contact_credentials['files_folder_id'];

				if(isset($_POST['contact_id']) && empty($user_id) && $GO_SECURITY->user_id>0)
					$user_id=$this->user_id=$GO_SECURITY->user_id;

				if(!empty($user_id)){
					$user['id']=$user_id;
					$user['contact_id']=$contact_id;
					$GO_USERS->update_profile($user);
				}
			}
			if(!$contact_id)
			{
				throw new Exception($lang['common']['saveError']);
			}

			//var_dump($_FILES);
			if($GO_MODULES->modules['files'])
			{
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
				$fs = new files();
				$path = $fs->build_path($files_folder_id);

				$response['files_folder_id']=$files_folder_id;

				$full_path = $GO_CONFIG->file_storage_path.$path;				

				foreach($_FILES as $key=>$file)
				{
					if($key!='photo'){//photo is handled later
						if (is_uploaded_file($file['tmp_name']))
						{
							move_uploaded_file($file['tmp_name'], $full_path.'/'.$file['name']);
							chmod($full_path.'/'.$file['name'], $GO_CONFIG->file_create_mode);

							$fs->import_file($full_path.'/'.$file['name'], $files_folder_id);
						}
					}
				}
			}

			if(isset($GO_MODULES->modules['customfields']))
			{
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();

				$cf->update_fields(1, $contact_id, 2, $_POST, empty($existing_contact));
			}

			if(isset($GO_MODULES->modules['mailings']) && isset($_POST['mailings']))
			{
				require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');
				$ml = new mailings();

				foreach($_POST['mailings'] as $mailing_name)
				{
					if(!empty($mailing_name))
					{
						$mailing=$ml->get_mailing_group_by_name($mailing_name);
						if(!$mailing)
						{
							throw new Exception('Addresslist not found!');
						}
						if(!$ml->contact_is_in_group($contact_id, $mailing['id']))
						$ml->add_contact_to_mailing_group($contact_id, $mailing['id']);
					}
				}
			}


			if ($this->contact_id > 0) {
				if (isset($_FILES['photo']['tmp_name']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
					move_uploaded_file($_FILES['photo']['tmp_name'], $GO_CONFIG->tmpdir . $_FILES['photo']['name']);
					$tmp_file = $GO_CONFIG->tmpdir . $_FILES['photo']['name'];

					$result['image'] = $ab->save_contact_photo($tmp_file, $this->contact_id);
				}
			}

			if(!isset($_POST['contact_id'])){
				$notify_users = isset($_POST['notify_users']) ? explode(',', $_POST['notify_users']) : array();
				if(!empty($_POST['notify_addressbook_owner']))
				{
					$notify_users[]=$addressbook['user_id'];
				}
				$mail_to = array();
				foreach($notify_users as $notify_user_id)
				{
					$user = $GO_USERS->get_user($notify_user_id);
					$mail_to[]=$user['email'];
				}
				if(count($mail_to))
				{

					$url = create_direct_url('addressbook', 'showContact', array($contact_id));
					$new_contact = $ab->get_contact($contact_id);
					$company = !empty($new_contact['company_id']) ? $ab->get_company($new_contact['company_id']) : array('name'=>'');

					$values = array('address_no', 'address', 'zip', 'city', 'state', 'country');
					$formatted_address = str_replace(' ','<br />',$new_contact['address_format']);
					$formatted_address = str_replace('{address}<br />{address_no}','{address} {address_no}',$formatted_address);
					$formatted_address = str_replace('{address_no}<br />{address}','{address_no} {address}',$formatted_address);

					foreach($values as $val)
						$formatted_address = str_replace('{'.$val.'}', $new_contact[$val], $formatted_address);

					$body = $lang['addressbook']['newContactFromSite'].':<br />';

					$body .= "<br />".String::format_name($new_contact);
					$body .= "<br />".$formatted_address;
					if (!empty($new_contact['home_phone'])) $body .= "<br />".$lang['common']['phone'].': '.$new_contact['home_phone'];
					if (!empty($new_contact['cellular'])) $body .= "<br />".$lang['common']['cellular'].': '.$new_contact['cellular'];
					if (!empty($company['name'])) $body .= "<br /><br />".$company['name'];
					if (!empty($new_contact['work_phone'])) $body .= "<br />".$lang['common']['workphone'].': '.$new_contact['work_phone'];

					$body .= '<br /><a href="'.$url.'">'.$lang['addressbook']['clickHereToView'].'</a>'."<br />";

					$mail_from = !empty($_POST['mail_from']) ? $_POST['mail_from'] : $GO_CONFIG->webmaster_email;

					require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
					$swift = new GoSwift(implode(',', $mail_to), $lang['addressbook']['newContactAdded']);
					$swift->set_body($body);
					$swift->set_from($mail_from, $GO_CONFIG->title);
					try{
						$swift->sendmail();
					}
					catch(Exception $e){
						go_log(LOG_DEBUG, $e->getMessage());
					}
				}
			}
		

			if(isset($_POST['confirmation_template']))
			{
				if(empty($_POST['email']))
				{
					throw new Exception('Fatal error: No email given for confirmation e-mail!');
				}

				$url = create_direct_url('addressbook', 'showContact', array($contact_id));
				$body = $lang['addressbook']['newContactFromSite'].'<br /><a href="'.$url.'">'.$lang['addressbook']['clickHereToView'].'</a>';

				global $smarty;
				$email = $smarty->fetch($_POST['confirmation_template']);

				$pos = strpos($email,"\n");

				$subject = trim(substr($email, 0, $pos));
				$body = trim(substr($email,$pos));

				require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
				$swift = new GoSwift($_POST['email'], $subject);
				$swift->set_body($body);
				$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
				$swift->sendmail();
			}

			if(isset($_POST['confirmation_email']))
			{
				if(File::path_leads_to_parent($_POST['confirmation_email']))
					throw new Exception('Invalid path');
				
				$path = $GO_CONFIG->file_storage_path.$_POST['confirmation_email'];
				if(!file_exists($path)){
					$path = dirname($GO_CONFIG->get_config_file()).'/'.$_POST['confirmation_email'];
				}
				$email = file_get_contents($path);
				require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
				$swift = new GoSwiftImport($email);
				$body=$swift->body;

				foreach($this->confirmation_replacements as $key=>$value){
					$body = str_replace('{'.$key.'}', $value, $body);
				}

				if(isset($GO_MODULES->modules['mailings'])){
					require_once($GO_MODULES->modules['mailings']['path'].'classes/templates.class.inc.php');
					$tp = new templates();

					$body=$tp->replace_contact_data_fields($body, $this->contact_id, false);
				}

				$swift->set_body($body, 'html');

				$swift->set_to($_POST['email']);
				$swift->sendmail();
			}
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

	function process_simple_contact_form($email, $from_email='', $from_name=''){
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