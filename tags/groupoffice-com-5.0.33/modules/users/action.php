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
$GLOBALS['GO_SECURITY']->json_authenticate('users');

$GLOBALS['GO_SECURITY']->check_token();

require_once($GLOBALS['GO_LANGUAGE']->get_language_file('users'));

require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();


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
			require_once($GLOBALS['GO_CONFIG']->class_path.'base/groups.class.inc.php');
			$GO_GROUPS = new GO_GROUPS();


			ini_set('max_execution_time', 3600);

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


			$import_file = $GLOBALS['GO_CONFIG']->tmpdir.'userimport.csv';
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

			$map = array();
			$record = fgetcsv($fp, 4096, $_SESSION['GO_SESSION']['list_separator'], $_SESSION['GO_SESSION']['text_separator']);
			for($i=0;$i<count($record);$i++)
			{
				if(!empty($record[$i]))
					$map[$record[$i]]=$i;
			}

			if(!isset($map['username']))
			{
				throw new Exception($lang['users']['incorrectFormat'].': username missing');
			}
			if(!isset($map['first_name']))
			{
				throw new Exception($lang['users']['incorrectFormat'].': first_name missing');
			}
			if(!isset($map['last_name']))
			{
				throw new Exception($lang['users']['incorrectFormat'].': last_name missing');
			}
			if(!isset($map['email']))
			{
				throw new Exception($lang['users']['incorrectFormat'].': email missing');
			}

			$failed = array();

			$success_count = 0;

			while($record = fgetcsv($fp, 4096, $_SESSION['GO_SESSION']['list_separator'], $_SESSION['GO_SESSION']['text_separator']))
			{
				$modules_read =  isset($map['modules_read']) ? array_map('trim', explode(',',$record[$map['modules_read']])) : array();
				$modules_write = isset($map['modules_write']) ? array_map('trim', explode(',',$record[$map['modules_write']])) : array();

				//user groups the user will be added to.
				$user_groups = isset($map['groups']) ? $GO_GROUPS->groupnames_to_ids(array_map('trim', explode(',',$record[$map['groups']]))) : array();

				//user groups that this user will be visible to
				$visible_user_groups = isset($map['visible_groups']) ? $GO_GROUPS->groupnames_to_ids(array_map('trim', explode(',',$record[$map['visible_groups']]))) : array();

				if(isset($map['serverclient_domains']))
				{
					$_POST['serverclient_no_halt']=true;
					$_POST['serverclient_domains']=array_map('trim', explode(',',$record[$map['serverclient_domains']]));
					go_debug($_POST['serverclient_domains']);
				}
				$user = array();

				for($i=0;$i<count($cols);$i++)
				{
					if(isset($map[$cols[$i]])){
						$user[$cols[$i]]=$record[$map[$cols[$i]]];
					}
				}				

				try{
					//User is ok to add
					$send_invitation = !empty($map['send_invitation']) && !empty($record[$map['send_invitation']]);
					$user_id = $GO_USERS->add_user($user, $user_groups, $visible_user_groups, $modules_read, $modules_write,array(),$send_invitation);
				}
				catch (Exception $e){
					$failed[]=$user['username'].': '. $e->getMessage();
					continue;
				}

				if(!$user_id)
				{
					$failed[]=$user['username'].': '.$lang['common']['saveError'];
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

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/groups.class.inc.php');
			$GO_GROUPS = new GO_GROUPS();

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

				$existing_email_user = $GLOBALS['GO_CONFIG']->allow_duplicate_email ? false : $GO_USERS->get_user_by_email($user['email']);

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
				$user['mute_sound'] = isset($_POST["mute_sound"]) ? '1' : '0';
        $user['mute_reminder_sound'] = isset($_POST["mute_reminder_sound"]) ? '1' : '0';
        $user['mute_new_mail_sound'] = isset($_POST["mute_new_mail_sound"]) ? '1' : '0';
				$user['mail_reminders'] = isset($_POST["mail_reminders"]) ? '1' : '0';
				$user['popup_reminders'] = isset($_POST["popup_reminders"]) ? '1' : '0';
        $user['show_smilies'] = isset($_POST["show_smilies"]) ? '1' : '0';
        $user['auto_punctuation'] = isset($_POST["auto_punctuation"]) ? '1' : '0';
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

			$insert=true;

			if($user_id > 0)
			{
				$insert=false;
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
				$modules_read = array_map('trim', explode(',',$GLOBALS['GO_CONFIG']->register_modules_read));
				$modules_write = array_map('trim', explode(',',$GLOBALS['GO_CONFIG']->register_modules_write));
				$user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GLOBALS['GO_CONFIG']->register_user_groups)));
				$visible_user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GLOBALS['GO_CONFIG']->register_visible_user_groups)));

				$user_id = $GO_USERS->add_user($user, $user_groups, $visible_user_groups, $modules_read, $modules_write);

				//confirm registration to the user and exit the script so the form won't load				
				if($user_id)
				{
					$response['success'] = true;
					$response['user_id']=$user_id;
					$response['files_folder_id']=$user['files_folder_id'];

					//for permissions below
					$old_user = $GO_USERS->get_user($user_id);
				}else
				{
					throw new Exception($lang['users']['error_user']);
				}


				if(!empty($_POST['send_invitation'])){

					require_once($GLOBALS['GO_MODULES']->modules['users']['class_path'].'users.class.inc.php');
					$users = new users();

					$email = $users->get_register_email();

					if(!empty($email['register_email_body']) && !empty($email['register_email_subject'])){
						require_once($GLOBALS['GO_CONFIG']->class_path.'mail/GoSwift.class.inc.php');
						$swift = new GoSwift($user['email'], $email['register_email_subject']);
						foreach($user as $key=>$value){
							$email['register_email_body'] = str_replace('{'.$key.'}', $value, $email['register_email_body']);
						}

						$email['register_email_body']= str_replace('{url}', $GLOBALS['GO_CONFIG']->full_url, $email['register_email_body']);
						$email['register_email_body']= str_replace('{title}', $GLOBALS['GO_CONFIG']->title, $email['register_email_body']);
						$email['register_email_body']= str_replace('{password}', $_POST["password1"], $email['register_email_body']);

						$swift->set_body($email['register_email_body'],'plain');
						$swift->set_from($GLOBALS['GO_CONFIG']->webmaster_email, $GLOBALS['GO_CONFIG']->title);
						$swift->sendmail();
					}
				}
			}

			//set permissions


			if(isset($_POST['modules']))
			{
				$permissions['modules'] = json_decode($_POST['modules'], true);
				$permissions['group_member'] = json_decode($_POST['group_member'], true);
				$permissions['groups_visible'] = json_decode($_POST['groups_visible'], true);

				foreach($permissions['modules'] as $module)
				{
					$mod = $GLOBALS['GO_MODULES']->get_module($module['id']);

					$level = 0;
					if($module['write_permission']){
						$level = GO_SECURITY::WRITE_PERMISSION;
					}elseif($module['read_permission']){
						$level = GO_SECURITY::READ_PERMISSION;
					}

					if ($level)
					{
						$GLOBALS['GO_SECURITY']->add_user_to_acl($user_id, $mod['acl_id'], $level);
					} else {
						if($GLOBALS['GO_SECURITY']->user_in_acl($user_id, $mod['acl_id']))
						{
							$GLOBALS['GO_SECURITY']->delete_user_from_acl($user_id, $mod['acl_id']);
						}
					}
				}

				foreach($permissions['group_member'] as $group)
				{
					if($group['id']!=$GLOBALS['GO_CONFIG']->group_everyone)
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
						if(!$GLOBALS['GO_SECURITY']->group_in_acl($group['id'], $old_user['acl_id']))
						{
							$GLOBALS['GO_SECURITY']->add_group_to_acl($group['id'], $old_user['acl_id']);
						}
					} else {
						if($GLOBALS['GO_SECURITY']->group_in_acl($group['id'], $old_user['acl_id']))
						{
							$GLOBALS['GO_SECURITY']->delete_group_from_acl($group['id'], $old_user['acl_id']);
						}
					}					
				}
			}


			if($GLOBALS['GO_MODULES']->has_module('customfields'))
			{
				require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();

				$cf->update_fields($GLOBALS['GO_SECURITY']->user_id, $user_id, 8, $_POST, $insert);
			}


			if($GLOBALS['GO_MODULES']->has_module('mailings'))
			{
				require_once($GLOBALS['GO_MODULES']->modules['mailings']['class_path'].'mailings.class.inc.php');
				$ml = new mailings();
				$ml2 = new mailings();

				$ml->get_authorized_mailing_groups('write', $GLOBALS['GO_SECURITY']->user_id, 0,0);
				while($ml->next_record())
				{
					$is_in_group = $ml2->user_is_in_group($user_id, $ml->f('id'));
					$should_be_in_group = isset($_POST['mailing_'.$ml->f('id')]);

					if($is_in_group && !$should_be_in_group)
					{
						$ml2->remove_user_from_group($user_id, $ml->f('id'));
					}
					if(!$is_in_group && $should_be_in_group)
					{
						$ml2->add_user_to_mailing_group($user_id, $ml->f('id'));
					}
				}
			}

			//end permissions

			echo json_encode($response);
			break;

		case 'save_settings':
			
			$GLOBALS['GO_CONFIG']->save_setting('register_email_subject', $_POST['register_email_subject']);
			$GLOBALS['GO_CONFIG']->save_setting('register_email_body', $_POST['register_email_body']);
			
			$response['success']=true;
			echo json_encode($response);
			
			break;
	}
}
catch(Exception $e)
{
	$response['success']=false;
	$response['feedback']=$e->getMessage();
	echo json_encode($response);
}