<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 

require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('users');
require_once($GO_LANGUAGE->get_language_file('users'));

$task = isset($_REQUEST['task']) ? smart_addslashes($_REQUEST['task']) : null;
$user_id = isset($_REQUEST['user_id']) ? smart_addslashes($_REQUEST['user_id']) : null;
$users = isset($_REQUEST['users']) ? json_decode(smart_stripslashes($_REQUEST['users']), true) : null;

$result['success']=false;
$feedback = null;

try
{
	switch($task)
	{
		case 'save_user':
			$user['id'] = isset($_POST['user_id']) ? smart_addslashes(trim($_POST['user_id'])) : 0;
			if(isset($_POST['first_name']))
			{
				$user['first_name'] = smart_addslashes(trim($_POST['first_name']));
				$user['middle_name'] = smart_addslashes(trim($_POST['middle_name']));
				$user['last_name'] = smart_addslashes(trim($_POST['last_name']));
				
				$user['email'] = smart_addslashes($_POST["email"]);
			
				$user['enabled'] = (isset($_POST['enabled'])) ? '1' : '0' ;
				$user['title'] = smart_addslashes($_POST["title"]);


				$user['initials'] = smart_addslashes($_POST["initials"]);
				$user['birthday'] = Date::to_db_date(smart_addslashes($_POST['birthday']));
				$user['work_phone'] = smart_addslashes($_POST["work_phone"]);
				$user['home_phone'] = smart_addslashes($_POST["home_phone"]);
				$user['fax'] = smart_addslashes($_POST["fax"]);
				$user['cellular'] = smart_addslashes($_POST["cellular"]);
				$user['country'] = smart_addslashes($_POST["country"]);
				$user['state'] = smart_addslashes($_POST["state"]);
				$user['city'] = smart_addslashes($_POST["city"]);
				$user['zip'] = smart_addslashes($_POST["zip"]);
				$user['address'] = smart_addslashes($_POST["address"]);
				$user['address_no'] = smart_addslashes($_POST["address_no"]);
				$user['department'] = smart_addslashes($_POST["department"]);
				$user['function'] = smart_addslashes($_POST["function"]);
				$user['company'] = smart_addslashes($_POST["company"]);
				$user['work_country'] = smart_addslashes($_POST["work_country"]);
				$user['work_state'] = smart_addslashes($_POST["work_state"]);
				$user['work_city'] = smart_addslashes($_POST["work_city"]);
				$user['work_zip'] = smart_addslashes($_POST["work_zip"]);
				$user['work_address'] = smart_addslashes($_POST["work_address"]);
				$user['work_address_no'] = smart_addslashes($_POST["work_address_no"]);
				$user['work_fax'] = smart_addslashes($_POST["work_fax"]);
				$user['homepage'] = smart_addslashes($_POST["homepage"]);
				$user['sex'] = smart_addslashes($_POST["sex"]);


				if(empty($user['email']) || empty($user['first_name']) || empty($user['last_name']))
				{
					throw new MissingFieldException();
				}
				

				if (!String::validate_email($user['email'])) {
					throw new Exception($lang['users']['error_email']);
				}

				$existing_email_user = $GO_CONFIG->allow_duplicate_email ? false : $GO_USERS->get_user_by_email($user['email']);

				if ($existing_email_user && ($user_id == 0 || $existing_email_user['id'] != $user_id)) {
					{
						throw new Exception($lang['users']['error_email_exists']);
					}
				}
				
			}


			if(isset($_POST['theme']))
			{
				$user['theme'] = smart_addslashes($_POST["theme"]);

				$user['language'] = smart_addslashes($_POST["language"]);
				$user['max_rows_list'] = smart_addslashes($_POST["max_rows_list"]);
				$user['sort_name'] = smart_addslashes($_POST["sort_name"]);
				$user['start_module'] = smart_addslashes($_POST["start_module"]);
			}

			if($_POST['language'])
			{
				$user['language']=smart_addslashes($_POST['language']);
				$user['first_weekday'] = smart_addslashes($_POST["first_weekday"]);
				$user['date_format'] = smart_addslashes($_POST["date_format"]);
				$user['date_seperator'] = smart_addslashes($_POST["date_seperator"]);
				$user['decimal_seperator'] = smart_addslashes($_POST["decimal_seperator"]);
				$user['thousands_seperator'] = smart_addslashes($_POST["thousands_seperator"]);
				$user['time_format'] = smart_addslashes($_POST["time_format"]);
				$user['timezone'] = smart_addslashes($_POST["timezone"]);
				$user['currency'] = smart_addslashes($_POST["currency"]);
			}


			if($user_id > 0)
			{
				if (!empty($_POST["password1"]) || !empty($_POST["password2"]))
				{
					if($_POST["password1"] != $_POST["password2"])
					{
						throw new Exception($lang['users']['error_match_pass']);
					}
					if(!empty($_POST["password2"]))
					{
						$user['password']=smart_stripslashes($_POST["password2"]);
					}
				}

				$old_user = $GO_USERS->get_user($user_id);

				if($old_user['password'] == '' && $user['enabled']== '1')
				{
					$user['password']=$GO_USERS->random_password();
				}

				$GO_USERS->update_user($user);

				$response['success']=true;					
					
			} else {
				
				
				$user['password'] = smart_addslashes($_POST["password1"]);
				$password2 = smart_addslashes($_POST["password2"]);
				$user['username'] = smart_addslashes($_POST['username']);

				if (empty($user['username']) || empty($user['password']) || empty($password2))
				{
					throw new MissingFieldException();
				}

				if (!preg_match('/^[a-z0-9_-]*$/', $user['username'])) {
					throw new Exception($error_username);
				}


				if ($user['password'] != $password2) {
					throw new Exception($lang['users']['error_match_pass']);
				}
				
				
				if($user['enabled'] == '1')
				{
					$password = $user['password']; // = smart_stripslashes($_POST["pass1"]);
				}else{
					$password='';
				}

				
				//deprecated modules get updated below
				$modules_read = array_map('trim', explode(',',$GO_CONFIG->register_modules_read));
				$modules_write = array_map('trim', explode(',',$GO_CONFIG->register_modules_write));
				$user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_user_groups)));
				$visible_user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_visible_user_groups)));

				$user_id = $GO_USERS->add_user($user, $user_groups, $visible_user_groups, $modules_read, $modules_write);
				
				
				//confirm registration to the user and exit the script so the form won't load
				$response['success'] = true;
				$response['user_id']=$user_id;
				
				//for permissions below
				$old_user = $GO_USERS->get_user($user_id);
			}
				
			//set permissions


			if(isset($_POST['modules']))
			{
				$permissions['modules'] = json_decode(smart_stripslashes($_POST['modules']), true);
				$permissions['group_member'] = json_decode(smart_stripslashes($_POST['group_member']), true);
				$permissions['groups_visible'] = json_decode(smart_stripslashes($_POST['groups_visible']), true);

				foreach($permissions['modules'] as $module)
				{
					$mod = $GO_MODULES->get_module($module['id']);

					$read = $module['read_permission'];
					$write = $module['write_permission'];

					if ($module['read_permission'])
					{
						if(!$GO_SECURITY->user_in_acl($user_id, $mod['acl_read']))
						{
							$GO_SECURITY->add_user_to_acl($user_id, $mod['acl_read']);
						}
					} else {
						if($GO_SECURITY->user_in_acl($user_id, $mod['acl_read']))
						{
							$GO_SECURITY->delete_user_from_acl($user_id, $mod['acl_read']);
						}
					}

					if ($module['write_permission'])
					{
						if(!$GO_SECURITY->user_in_acl($user_id, $mod['acl_write']))
						{
							$GO_SECURITY->add_user_to_acl($user_id, $mod['acl_write']);
						}
					} else {
						if($GO_SECURITY->user_in_acl($user_id, $mod['acl_write']))
						{
							$GO_SECURITY->delete_user_from_acl($user_id, $mod['acl_write']);
						}
					}

				}

				foreach($permissions['group_member'] as $group)
				{
					if ($group['group_permission'])
					{
						if(!$GO_GROUPS->is_in_group($user_id, $group['id']))
						{
							$GO_GROUPS->add_user_to_group($user_id, $group['id']);
						}
					} else {
						if($GO_GROUPS->is_in_group($user_id, $group['id']))
						{
							$GO_GROUPS->delete_user_from_group($user_id, $group['id']);
						}
					}
				}


				foreach($permissions['groups_visible'] as $group)
				{
					if($group['id']!=$GO_CONFIG->group_everyone)
					{
						if ($group['visible_permission'])
						{
							if(!$GO_SECURITY->group_in_acl($group['id'], $old_user['acl_id']))
							{
								$GO_SECURITY->add_group_to_acl($group['id'], $old_user['acl_id']);
							}
						} else {
							if($GO_SECURITY->group_in_acl($group['id'], $old_user['acl_id']))
							{
								$GO_SECURITY->delete_group_from_acl($group['id'], $old_user['acl_id']);
							}
						}
					}
				}
			}

			//end permissions
				
				
			echo json_encode($response);
			break;

		case 'save_setting':
			$email['confirmed'] = smart_addslashes($_POST["confirmed"]);
			$email['unconfirmed'] = smart_addslashes($_POST["unconfirmed"]);
			$email['confirmed_subject'] = smart_addslashes($_POST["confirmed_subject"]);
			$email['unconfirmed_subject'] = smart_addslashes($_POST["unconfirmed_subject"]);

			$GO_CONFIG->save_setting('registration_confirmation', $email['confirmed']);
			$GO_CONFIG->save_setting('registration_confirmation_subject', $email['confirmed_subject']);
			$GO_CONFIG->save_setting('registration_unconfirmed', $email['unconfirmed']);
			$GO_CONFIG->save_setting('registration_unconfirmed_subject', $email['unconfirmed_subject']);
			break;
	}
}
catch(Exception $e)
{
	$response['success']=false;
	$response['feedback']=$e->getMessage();

	echo json_encode($response);
}
?>
