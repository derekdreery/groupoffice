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

$task = isset($_REQUEST['task']) ? ($_REQUEST['task']) : null;
$user_id = isset($_REQUEST['user_id']) ? ($_REQUEST['user_id']) : null;
$users = isset($_REQUEST['users']) ? json_decode(($_REQUEST['users']), true) : null;

$result['success']=false;
$feedback = null;

try
{
	switch($task)
	{
		case 'import':
			$cols[]='username';
			$cols[]='password';
			$cols[]='enabled';
			$cols[]='first_name';
			$cols[]='middle_name';
			$cols[]='last_name';
			$cols[]='initials';
			$cols[]='title';
			$cols[]='sex';
			$cols[]='birthday';
			$cols[]='email';
			$cols[]='company';
			$cols[]='department';
			$cols[]='function';
			$cols[]='home_phone';
			$cols[]='work_phone';
			$cols[]='fax';
			$cols[]='cellular';
			$cols[]='country';
			$cols[]='state';
			$cols[]='city';
			$cols[]='zip';
			$cols[]='address';
			$cols[]='address_no';
			$cols[]='homepage';
			$cols[]='work_address';
			$cols[]='work_address_no';
			$cols[]='work_zip';
			$cols[]='work_country';
			$cols[]='work_state';
			$cols[]='work_city';
			$cols[]='work_fax';



			$import_file = $GO_CONFIG->tmpdir.'userimport.csv';
			if (is_uploaded_file($_FILES['importfile']['tmp_name'][0]))
			{
				move_uploaded_file($_FILES['importfile']['tmp_name'][0], $import_file);
			}

			if(!file_exists($import_file))
			{
				throw new Exception('File was not uploaded!');
			}

			$fp = fopen($import_file, "r");
			if(!$fp)
			{
				throw new Exception('Could not open uploaded file');
			}

			$record = fgetcsv($fp, 4096, $_SESSION['GO_SESSION']['list_separator'], $_SESSION['GO_SESSION']['text_separator']);
			for($i=0;$i<count($cols);$i++)
			{
				if(empty($record[$i]) || $record[$i]!=$cols[$i])
				{
					throw new Exception($lang['users']['incorrectFormat']);
				}
			}

			$modules_read = array_map('trim', explode(',',$GO_CONFIG->register_modules_read));
			$modules_write = array_map('trim', explode(',',$GO_CONFIG->register_modules_write));

			//user groups the user will be added to.
			$user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_user_groups)));

			//user groups that this user will be visible to
			$visible_user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_visible_user_groups)));


			$failed = array();

			$success_count = 0;

			while($record = fgetcsv($fp, 4096, $_SESSION['GO_SESSION']['list_separator'], $_SESSION['GO_SESSION']['text_separator']))
			{
				$user = array();

				for($i=0;$i<count($cols);$i++)
				{
					$user[$cols[$i]]=$record[$i];
				}

				if(empty($user['username']) || empty($user['password']) || empty($user['email']) || empty($user['first_name']) || empty($user['last_name']))
				{
					$failed[]=$user['username'].': '.$lang['common']['missingField'];
					continue;
				}

				if (!$GO_USERS->check_username($user['username'])) {
					$failed[]=$user['username'].': '.$lang['users']['error_username'];
					continue;
				}

				if (!String::validate_email($user['email'])) {
					$failed[]=$user['username'].': '.$lang['users']['error_email'];
					continue;
				}

				$existing_email_user = $GO_CONFIG->allow_duplicate_email ? false : $GO_USERS->get_user_by_email($user['email']);

				if ($existing_email_user && ($user_id == 0 || $existing_email_user['id'] != $user_id)) {
					$failed[]=$user['username'].': '.$lang['users']['error_email_exists'];
					continue;
				}

				//User is ok to add
				$user_id = $GO_USERS->add_user($user, $user_groups, $visible_user_groups, $modules_read, $modules_write);

				if(!$user_id)
				{
					$failed[]=$user['username'].': '.$lang['comon']['saveError'];
					continue;
				}else
				{
					$success_count++;
				}
			}

			$response['feedback'] = sprintf($lang['users']['imported'],$success_count);

			if(count($failed))
			{
				$response['feedback'] .= "BRBR".$lang['users']['failed'].":BR".implode("BRBR", $failed);
			}

			$response['success']=true;

			echo json_encode($response);
			break;


		case 'save_user':
			$user['id'] = isset($_POST['user_id']) ? ($_POST['user_id']) : 0;
			if(isset($_POST['first_name']))
			{
				$user['first_name'] = $_POST['first_name'];
				$user['middle_name'] = $_POST['middle_name'];
				$user['last_name'] = $_POST['last_name'];

				$user['email'] = $_POST["email"];
					
				$user['enabled'] = isset($_POST['enabled']) ? '1' : '0' ;
				$user['title'] = $_POST["title"];


				$user['initials'] = $_POST["initials"];
				$user['birthday'] = Date::to_db_date($_POST['birthday']);
				$user['work_phone'] = $_POST["work_phone"];
				$user['home_phone'] = $_POST["home_phone"];
				$user['fax'] = $_POST["fax"];
				$user['cellular'] = $_POST["cellular"];
				$user['country'] = $_POST["country"];
				$user['state'] = $_POST["state"];
				$user['city'] = $_POST["city"];
				$user['zip'] = $_POST["zip"];
				$user['address'] = $_POST["address"];
				$user['address_no'] = $_POST["address_no"];
				$user['department'] = $_POST["department"];
				$user['function'] = $_POST["function"];
				$user['company'] = $_POST["company"];
				$user['work_country'] = $_POST["work_country"];
				$user['work_state'] = $_POST["work_state"];
				$user['work_city'] = $_POST["work_city"];
				$user['work_zip'] = $_POST["work_zip"];
				$user['work_address'] = $_POST["work_address"];
				$user['work_address_no'] = $_POST["work_address_no"];
				$user['work_fax'] = $_POST["work_fax"];
				$user['homepage'] = $_POST["homepage"];
				$user['sex'] = $_POST["sex"];


				if(empty($user['email']) || empty($user['first_name']) || empty($user['last_name']))
				{
					throw new MissingFieldException();
				}


				if (!String::validate_email($user['email'])) {
					throw new Exception($lang['users']['error_email']);
				}

				$existing_email_user = $GO_CONFIG->allow_duplicate_email ? false : $GO_USERS->get_user_by_email($user['email']);

				if ($existing_email_user && ($user_id == 0 || $existing_email_user['id'] != $user_id)) {
					throw new Exception($lang['users']['error_email_exists']);
				}
			}

			if(isset($_POST['theme']))
			{
				$user['theme'] = $_POST["theme"];

				$user['language'] = $_POST["language"];
				$user['max_rows_list'] = $_POST["max_rows_list"];
				$user['sort_name'] = $_POST["sort_name"];
				$user['start_module'] = $_POST["start_module"];
			}

			if($_POST['language'])
			{
				$user['language']=$_POST['language'];
				$user['first_weekday'] = $_POST["first_weekday"];
				$user['date_format'] = $_POST["date_format"];
				$user['date_separator'] = $_POST["date_separator"];
				$user['decimal_separator'] = $_POST["decimal_separator"];
				$user['thousands_separator'] = $_POST["thousands_separator"];
				$user['time_format'] = $_POST["time_format"];
				$user['timezone'] = $_POST["timezone"];
				$user['currency'] = $_POST["currency"];
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
						$user['password']=($_POST["password2"]);
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


				$user['password'] = $_POST["password1"];
				$password2 = $_POST["password2"];
				$user['username'] = trim($_POST['username']);

				if (empty($user['username']) || empty($user['password']) || empty($password2))
				{
					throw new MissingFieldException();
				}

				if (empty($user['username']) || !$GO_USERS->check_username($user['username'])) {
					throw new Exception($lang['users']['error_username']);
				}


				if ($user['password'] != $password2) {
					throw new Exception($lang['users']['error_match_pass']);
				}


				if($user['enabled'] == '1')
				{
					$password = $user['password']; // = ($_POST["pass1"]);
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
				$response['files_folder_id']=$user['files_folder_id'];

				//for permissions below
				$old_user = $GO_USERS->get_user($user_id);
			}

			//set permissions


			if(isset($_POST['modules']))
			{
				$permissions['modules'] = json_decode($_POST['modules'], true);
				$permissions['group_member'] = json_decode($_POST['group_member'], true);
				$permissions['groups_visible'] = json_decode($_POST['groups_visible'], true);

				foreach($permissions['modules'] as $module)
				{
					$mod = $GO_MODULES->get_module($module['id']);

					$read = $module['read_permission'];
					$write = $module['write_permission'];

					if (!empty($module['read_permission']))
					{
						if(!$GO_SECURITY->has_permission($user_id, $mod['acl_read']))
						{
							$GO_SECURITY->add_user_to_acl($user_id, $mod['acl_read']);
						}
					} else {
						if($GO_SECURITY->user_in_acl($user_id, $mod['acl_read']))
						{
							$GO_SECURITY->delete_user_from_acl($user_id, $mod['acl_read']);
						}
					}

					if (!empty($module['write_permission']))
					{
						if(!$GO_SECURITY->has_permission($user_id, $mod['acl_write']))
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
					if($group['id']!=$GO_CONFIG->group_everyone)
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
				}


				foreach($permissions['groups_visible'] as $group)
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

			//end permissions

			echo json_encode($response);
			break;

		/*case 'save_setting':
			$email['confirmed'] = $_POST["confirmed"];
			$email['unconfirmed'] = $_POST["unconfirmed"];
			$email['confirmed_subject'] = $_POST["confirmed_subject"];
			$email['unconfirmed_subject'] = $_POST["unconfirmed_subject"];

			$GO_CONFIG->save_setting('registration_confirmation', $email['confirmed']);
			$GO_CONFIG->save_setting('registration_confirmation_subject', $email['confirmed_subject']);
			$GO_CONFIG->save_setting('registration_unconfirmed', $email['unconfirmed']);
			$GO_CONFIG->save_setting('registration_unconfirmed_subject', $email['unconfirmed_subject']);
			break;*/
	}
}
catch(Exception $e)
{
	$response['success']=false;
	$response['feedback']=$e->getMessage();
	echo json_encode($response);
}