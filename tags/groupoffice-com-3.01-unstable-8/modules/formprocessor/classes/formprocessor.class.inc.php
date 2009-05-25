<?php
class formprocessor{
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
		global $GO_SECURITY, $GO_LANGUAGE, $GO_MODULES, $GO_USERS, $GO_CONFIG, $lang;

		if(isset($_POST['language']) && $_POST['language']!=$GO_LANGUAGE->language)
		{
			$GO_LANGUAGE->set_language($_POST['language']);
			require($GO_LANGUAGE->get_base_language_file('common'));
		}		
		

		if(isset($_POST['required']))
		{
			foreach($_POST['required'] as $key)
			{
				if(empty($_POST[$key]))
				{
					throw new Exception($lang['common']['missingField']);
				}
			}
		}

		if(isset($_POST['email']) && !String::validate_email($_POST['email']))
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

			$contact_credentials['email_allowed']='1';

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

			$contact_credentials['addressbook_id']=$addressbook['id'];
				
			$contact_credentials['email_allowed']='1';
				
				

			if(!empty($contact_credentials['company']) && empty($contact_credentials['company_id']))
			{
				if(!$contact_credentials['company_id'] = $ab->get_company_id_by_name($contact_credentials['company'], $contact_credentials['addressbook_id']))
				{
					$company['addressbook_id'] = $contact_credentials['addressbook_id'];
					$company['name'] = $contact_credentials['company']; // bedrijfsnaam
					$company['user_id'] = $GO_SECURITY->user_id;
					$contact_credentials['company_id'] = $ab->add_company($company);
				}
			}
			if(isset($_POST['birthday']))
			{
				$contact_credentials['birthday'] = Date::to_db_date($_POST['birthday'], false);
			}

			unset($contact_credentials['company']);
				
				
			$existing_contact=false;
			if(!empty($contact_credentials['email']))
			{
				$existing_contact = $ab->get_contact_by_email($contact_credentials['email'], 0, $contact_credentials['addressbook_id']);
			}
				
				
			if($existing_contact)
			{
				$contact_id = $existing_contact['id'];


				/*
				 * Only update empty fields
				 */
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

				$contact_credentials['id']=$contact_id;

				if(!empty($existing_contact['comment']) && !empty($contact_credentials['comment']))
				$contact_credentials['comment']=$existing_contact['comment']."\n\n----\n\n".$contact_credentials['comment'];

				$ab->update_contact($contact_credentials);
			}else
			{
				$contact_id = $ab->add_contact($contact_credentials);
			}
			if(!$contact_id)
			{
				throw new Exception($lang['comon']['saveError']);
			}

			if($GO_MODULES->modules['files'])
			{
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
				$fs = new files();

				$response['files_path']='contacts/'.$contact_id;
				$full_path = $GO_CONFIG->file_storage_path.$response['files_path'];
				$fs->check_share($full_path, $GO_SECURITY->user_id, $addressbook['acl_read'], $addressbook['acl_write'],true);

				while($file = array_shift($_FILES))
				{
					if (is_uploaded_file($file['tmp_name']))
					{
						move_uploaded_file($file['tmp_name'], $full_path.'/'.$file['name']);
						chmod($full_path.'/'.$file['name'], $GO_CONFIG->file_create_mode);
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
				$body = $lang['addressbook']['newContactFromSite'].'<br /><a href="go:showContact('.$contact_id.');">'.$lang['addressbook']['clickHereToView'].'</a>';

				require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
				$swift = new GoSwift(implode(',', $mail_to), $lang['addressbook']['newContactAdded']);
				$swift->set_body($body);
				$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
				$swift->sendmail();
			}
		}

		if(isset($_POST['confirmation_template']))
		{
			if(empty($_POST['email']))
			{
				throw new Exception('Fatal error: No email given for confirmation e-mail!');
			}
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
	}
}