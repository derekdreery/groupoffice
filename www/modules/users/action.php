<?php
/**
* @copyright Intermesh 2007
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.13 $ $Date: 2006/10/20 12:36:43 $3
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */


require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('users');

ini_set('display_errors','off');


$result =array();
switch($_REQUEST['task'])
{
	case 'save':
		$result['success']=true;
		$result['user_id']=1;
		
		break;
	case 'delete':

		$selectedRows = json_decode(smart_stripslashes($_POST['selectedRows']));
		foreach($selectedRows as $delete_user_id)
		{
			if (($delete_user_id != $GO_SECURITY->user_id) && ($delete_user_id != 1) && $GO_USERS->delete_user($delete_user_id))
			{

			}else
			{
				require($GO_LANGUAGE->get_language_file('users'));
				$result['errors']= $delete_fail;
				$result['success']=false;
				break;
			}
		}
		if(!isset($result['success']))
		{
			$result['success']=true;
		}


		break;

		/*case 'save':

		$user['birthday'] = date_to_db_date(smart_addslashes($_POST['birthday']));
		$user['first_name'] = smart_addslashes(trim($_POST['first_name']));
		$user['middle_name'] = smart_addslashes(trim($_POST['middle_name']));
		$user['last_name'] = smart_addslashes(trim($_POST['last_name']));
		$user['initials'] = smart_addslashes($_POST["initials"]);
		$user['title'] = smart_addslashes($_POST["title"]);
		$user['email'] = smart_addslashes($_POST["email"]);
		//$user['work_phone'] = smart_addslashes($_POST["work_phone"]);
		$user['home_phone'] = smart_addslashes($_POST["home_phone"]);
		$user['fax'] = smart_addslashes($_POST["fax"]);
		$user['cellular'] = smart_addslashes($_POST["cellular"]);
		$user['country_id'] = smart_addslashes($_POST["country_id"]);
		$user['state'] = smart_addslashes($_POST["state"]);
		$user['city'] = smart_addslashes($_POST["city"]);
		$user['zip'] = smart_addslashes($_POST["zip"]);
		$user['address'] = smart_addslashes($_POST["address"]);
		$user['address_no'] = smart_addslashes($_POST["address_no"]);
		//$user['department'] = smart_addslashes($_POST["department"]);
		//$user['function'] = smart_addslashes($_POST["function"]);
		$user['company'] = smart_addslashes($_POST["company"]);
		//$user['work_country_id'] = smart_addslashes($_POST["work_country_id"]);
		$user['work_state'] = smart_addslashes($_POST["work_state"]);
		$user['work_city'] = smart_addslashes($_POST["work_city"]);
		$user['work_zip'] = smart_addslashes($_POST["work_zip"]);
		$user['work_address'] = smart_addslashes($_POST["work_address"]);
		$user['work_address_no'] = smart_addslashes($_POST["work_address_no"]);
		$user['work_fax'] = smart_addslashes($_POST["work_fax"]);
		$user['homepage'] = smart_addslashes($_POST["homepage"]);
		$user['enabled'] = isset($_POST["enabled"]) ? '1' : '0';
		//$user['language'] = isset($_POST['language']) ? smart_stripslashes($_POST['language']) : $GO_CONFIG->language;
		//$user['theme'] = isset($_POST['theme']) ? smart_stripslashes($_POST['theme']) : $GO_CONFIG->theme;
		$user['sex'] = $_POST['sex'];

		$user['id'] = $_POST['user_id'];


		$existing_email_user = $GO_CONFIG->allow_duplicate_email ? false : $GO_USERS->get_user_by_email($user['email']);


		if
		(
		empty($user['first_name']) ||
		empty($user['last_name'])
		)
		{
		$result['errors'] = $error_missing_field;
		}elseif(!validate_email($user['email']))
		{
		$result['errors'] = $error_email;
		}elseif($existing_email_user && ($existing_email_user['id'] != $user['id']))
		{
		$result['errors'] =  $error_email_exists;
		}else
		{
		if (!$GO_USERS->update_user($user))
		{
		$result['errors'] = $strSaveError;
		}else
		{
		$result['success']=true;
		}
		}
		break;
		*/
	case 'save_profile':
		
		require($GO_LANGUAGE->get_language_file('users'));
		
		$result['success']=false;
		
		//translate the given birthdayto gmt unix time
		$user['birthday'] = date_to_db_date(smart_addslashes($_POST['birthday']));
		$user['first_name'] = smart_addslashes(trim($_POST['first_name']));
		$user['middle_name'] = smart_addslashes(trim($_POST['middle_name']));
		$user['last_name'] = smart_addslashes(trim($_POST['last_name']));
		$user['initials'] = smart_addslashes($_POST["initials"]);
		$user['title'] = smart_addslashes($_POST["title"]);
		$user['email'] = smart_addslashes($_POST["email"]);
		$user['work_phone'] = smart_addslashes($_POST["work_phone"]);
		$user['home_phone'] = smart_addslashes($_POST["home_phone"]);
		$user['fax'] = smart_addslashes($_POST["fax"]);
		$user['cellular'] = smart_addslashes($_POST["cellular"]);
		$user['country_id'] = smart_addslashes($_POST["country_id"]);
		$user['state'] = smart_addslashes($_POST["state"]);
		$user['city'] = smart_addslashes($_POST["city"]);
		$user['zip'] = smart_addslashes($_POST["zip"]);
		$user['address'] = smart_addslashes($_POST["address"]);
		$user['address_no'] = smart_addslashes($_POST["address_no"]);
		$user['department'] = smart_addslashes($_POST["department"]);
		$user['function'] = smart_addslashes($_POST["function"]);
		$user['company'] = smart_addslashes($_POST["company"]);
		$user['work_country_id'] = smart_addslashes($_POST["work_country_id"]);
		$user['work_state'] = smart_addslashes($_POST["work_state"]);
		$user['work_city'] = smart_addslashes($_POST["work_city"]);
		$user['work_zip'] = smart_addslashes($_POST["work_zip"]);
		$user['work_address'] = smart_addslashes($_POST["work_address"]);
		$user['work_address_no'] = smart_addslashes($_POST["work_address_no"]);
		$user['work_fax'] = smart_addslashes($_POST["work_fax"]);
		$user['homepage'] = smart_addslashes($_POST["homepage"]);
		$user['enabled'] = isset($_POST["enabled"]) ? '1' : '0';
		//$user['language'] = isset($_POST['language']) ? smart_stripslashes($_POST['language']) : $GO_CONFIG->language;
		//$user['theme'] = isset($_POST['theme']) ? smart_stripslashes($_POST['theme']) : $GO_CONFIG->theme;
		$user['sex'] = isset($_POST['sex']) ? $_POST['sex'] : 'M';

		$user['id'] = $user_id= $_POST['user_id'];


		$existing_email_user = $GO_CONFIG->allow_duplicate_email ? false : $GO_USERS->get_user_by_email($user['email']);
		if($user['id'] == 0)
		{
			$user['username'] = smart_addslashes(trim($_POST['username']));
			$pass1 = smart_stripslashes($_POST["pass1"]);
			$pass2 = smart_stripslashes($_POST["pass2"]);
		}

		if (
		((empty($user['username']) || empty($pass1) || empty ($pass2)) && $user['enabled']=='1' && $user['id']=='0') ||
		empty($user['first_name']) ||
		empty($user['last_name'])
		)
		{
			$result['errors'] = $error_missing_field;
		}elseif($user_id==0 && !preg_match('/^[a-z0-9_-]*$/', $user['username']))
		{
			$result['errors'] = $error_username;
		}elseif(!validate_email($user['email']))
		{
			$result['errors'] = $error_email;
		}elseif($GO_USERS->get_user_by_username($user['username']))
		{
			$result['errors'] = $error_username_exists;
		}elseif($existing_email_user && ($user_id==0 || $existing_email_user['id'] != $user_id))
		{
			$result['errors'] =  $error_email_exists;
		}elseif($user['id'] == 0 && $pass1 != $pass2)
		{
			$result['errors'] = $error_match_pass;
		}else
		{
			if($user_id>0)
			{
				$old_user = $GO_USERS->get_user($user_id);
				if($old_user['password']=='' && $user['enabled']=='1')
				{
					$password = $GO_USERS->random_password();
					$user['password']=md5($password);
				}
				if (!$GO_USERS->update_user($user))
				{
					$result['errors'] = $strSaveError;
				}else
				{
					if($old_user['password']=='' && $user['enabled']=='1')
					{
						$registration_mail_body = $GO_CONFIG->get_setting('registration_confirmation');
						$registration_mail_subject = $GO_CONFIG->get_setting('registration_confirmation_subject');

						if(!empty($registration_mail_body) && !empty($registration_mail_subject))
						{
							//send email to the user with password
							$registration_mail_body = str_replace("%beginning%", $sir_madam[$_POST['sex']], $registration_mail_body);
							// If $title is not set, then use $sex (sir_madam) instead for $title.
							$registration_mail_body = str_replace("%title%", ( ($user['title'] != '') ? $user['title'] : $sir_madam[$_POST['sex']] ), $registration_mail_body);
							$registration_mail_body = str_replace("%last_name%", smart_stripslashes($_POST['last_name']), $registration_mail_body);
							$registration_mail_body = str_replace("%middle_name%", smart_stripslashes($_POST['middle_name']), $registration_mail_body);
							$registration_mail_body = str_replace("%first_name%", smart_stripslashes($_POST['first_name']), $registration_mail_body);
							$registration_mail_body = str_replace("%username%",$old_user['username'], $registration_mail_body);
							$registration_mail_body = str_replace("%password%",smart_stripslashes($password), $registration_mail_body);
							$registration_mail_body = str_replace("%full_url%",'<a href="'.$GO_CONFIG->full_url.'">'.$GO_CONFIG->full_url.'</a>', $registration_mail_body);


							sendmail($user['email'], $GO_CONFIG->webmaster_email, $GO_CONFIG->title, $registration_mail_subject, $registration_mail_body,'3','text/HTML');
						}
					}
					$result['success']=true;
				}
			}else {
				if($user['enabled']=='1')
				{
					$password = $user['password'] = smart_stripslashes($_POST["pass1"]);
				}else{
					$password='';
				}

				$modules_read = array_map('trim', explode(',',$GO_CONFIG->register_modules_read));
				$modules_write = array_map('trim', explode(',',$GO_CONFIG->register_modules_write));
				$user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_user_groups)));
				$visible_user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_visible_user_groups)));

				$user['link_id']= $GO_LINKS->get_link_id();
				
				

				if ($user_id = $GO_USERS->add_user($user, $user_groups, $visible_user_groups, $modules_read, $modules_write	))
				{
					if($user['enabled']=='1')
					{
						$registration_mail_body = $GO_CONFIG->get_setting('registration_confirmation');
						$registration_mail_subject = $GO_CONFIG->get_setting('registration_confirmation_subject');
					}else {
						$registration_mail_body = $GO_CONFIG->get_setting('registration_unconfirmed');
						$registration_mail_subject = $GO_CONFIG->get_setting('registration_unconfirmed_subject');
					}

					if(!empty($registration_mail_body) && !empty($registration_mail_subject))
					{
						//send email to the user with password
						$registration_mail_body = str_replace("%beginning%", $sir_madam[$_POST['sex']], $registration_mail_body);
						// If $title is not set, then use $sex (sir_madam) instead for $title.
						$registration_mail_body = str_replace("%title%", ( ($user['title'] != '') ? $user['title'] : $sir_madam[$_POST['sex']] ), $registration_mail_body);
						$registration_mail_body = str_replace("%last_name%", smart_stripslashes($_POST['last_name']), $registration_mail_body);
						$registration_mail_body = str_replace("%middle_name%", smart_stripslashes($_POST['middle_name']), $registration_mail_body);
						$registration_mail_body = str_replace("%first_name%", smart_stripslashes($_POST['first_name']), $registration_mail_body);
						$registration_mail_body = str_replace("%username%",smart_stripslashes($_POST['username']), $registration_mail_body);
						$registration_mail_body = str_replace("%password%",smart_stripslashes($password), $registration_mail_body);
						$registration_mail_body = str_replace("%full_url%",'<a href="'.$GO_CONFIG->full_url.'">'.$GO_CONFIG->full_url.'</a>', $registration_mail_body);

						sendmail($user['email'], $GO_CONFIG->webmaster_email, $GO_CONFIG->title, $registration_mail_subject, $registration_mail_body,'3','text/HTML');
					}

					//create Group-Office home directory
					$old_umask = umask(000);
					mkdir($GO_CONFIG->file_storage_path.'users/'.stripslashes($user['username']), $GO_CONFIG->create_mode);
					umask($old_umask);

					//confirm registration to the user and exit the script so the form won't load

					$result['user_id']=$user_id;
					$result['errors'] = $registration_success;
					$result['success']=true;

				}else {
					$result['errors'] = $strSaveError;
				}

			}
		}
		break;

	case 'save_permissions':

		$user= $GO_USERS->get_user($_POST['user_id']);

		$user_groups = isset($_POST['user_groups']) ? $_POST['user_groups'] : array();
		$modules_read =  isset($_POST['modules_read']) ? $_POST['modules_read'] : array();
		$modules_write =  isset($_POST['modules_write']) ? $_POST['modules_write'] : array();
		$visible_user_groups = isset($_POST['visible_user_groups']) ? $_POST['visible_user_groups'] : array();

		$GO_MODULES->get_modules();
		while ($GO_MODULES->next_record())
		{
			$could_read = $GO_SECURITY->has_permission($user['id'], $GO_MODULES->f('acl_read'));
			$can_read =  in_array($GO_MODULES->f('id'), $modules_read);

			if ($could_read && !$can_read)
			{
				$GO_SECURITY->delete_user_from_acl($user['id'], $GO_MODULES->f('acl_read'));
			}

			if ($can_read && !$could_read)
			{
				$GO_SECURITY->add_user_to_acl($user['id'], $GO_MODULES->f('acl_read'));
			}

			$could_write = $GO_SECURITY->has_permission($user['id'], $GO_MODULES->f('acl_write'));
			$can_write =  in_array($GO_MODULES->f('id'), $modules_write);

			if ($could_write && !$can_write)
			{
				$GO_SECURITY->delete_user_from_acl($user['id'], $GO_MODULES->f('acl_write'));
			}

			if ($can_write && !$could_write)
			{
				$GO_SECURITY->add_user_to_acl($user['id'], $GO_MODULES->f('acl_write'));
			}
		}



		$GO_GROUPS->get_groups();
		$groups2 = new $go_groups_class();
		while($GO_GROUPS->next_record())
		{
			$is_in_group = $groups2->is_in_group($user['id'], $GO_GROUPS->f('id'));
			$should_be_in_group = in_array($GO_GROUPS->f('id'), $user_groups);

			if ($is_in_group && !$should_be_in_group)
			{
				$groups2->delete_user_from_group($user['id'], $GO_GROUPS->f('id'));
			}

			if (!$is_in_group && $should_be_in_group)
			{
				$groups2->add_user_to_group($user['id'], $GO_GROUPS->f('id'));
			}



			$group_is_visible = $GO_SECURITY->group_in_acl($GO_GROUPS->f('id'), $user['acl_id']);
			$group_should_be_visible = in_array($GO_GROUPS->f('id'), $visible_user_groups);

			if ($group_is_visible && !$group_should_be_visible)
			{
				$GO_SECURITY->delete_group_from_acl($GO_GROUPS->f('id'), $user['acl_id']);
			}

			if (!$group_is_visible  && $group_should_be_visible)
			{
				$GO_SECURITY->add_group_to_acl($GO_GROUPS->f('id'), $user['acl_id']);
			}
		}
		$result['success']=true;

		break;

	case 'save_lookandfeel':
		$user['id'] = $_REQUEST['user_id'];
		$user['max_rows_list'] = smart_addslashes($_POST['max_rows_list']);
		$user['start_module'] = smart_addslashes($_POST['start_module']);
		$user['sort_name'] =	smart_addslashes($_POST['sort_name']);
		$user['theme'] = smart_addslashes($_POST['theme']);
		$user['use_checkbox_select'] = isset($_POST['use_checkbox_select']) ? '1' : '0';

		if($GO_USERS->update_profile($user))
		{
			$result['success']=true;
		}else {
			$result['success']=false;
			$result['errors'] = $strSaveError;
		}
		break;
	case 'save_regional':
		$user=array();
		$user['id'] = $_REQUEST['user_id'];
		$user['language'] = smart_addslashes($_POST['language']);
		$user['DST'] =isset($_POST['DST']) ? '1' : '0';
		$user['date_format'] =	smart_addslashes($_POST['date_format']);
		$user['date_seperator'] =	smart_addslashes($_POST['date_seperator']);
		$user['time_format'] =	smart_addslashes($_POST['time_format']);
		$user['thousands_seperator'] =	smart_addslashes($_POST['thousands_seperator']);
		$user['decimal_seperator'] =	smart_addslashes($_POST['decimal_seperator']);
		$user['currency'] =	smart_addslashes($_POST['currency']);
		$user['timezone'] =	smart_addslashes($_POST['timezone']);
		$user['first_weekday'] =	smart_addslashes($_POST['first_weekday']);


		if($GO_USERS->update_profile($user))
		{
			$result['success']=true;
		}else {
			$result['success']=false;
		}

		break;
}

echo json_encode($result);