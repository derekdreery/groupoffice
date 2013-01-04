<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once("Group-Office.php");
$GO_SECURITY->authenticate();

if($_REQUEST['task']!='login' && $_REQUEST['task']!='complete_profile')
	$GO_SECURITY->check_token();



$response =array();
try{

	switch($_REQUEST['task'])
	{
		case 'save_advanced_query':

		require_once($GO_CONFIG->class_path.'advanced_query.class.inc.php');
		$aq = new advanced_query();

	    $aq->add_search_query(array('user_id'=>$GO_SECURITY->user_id, 'sql'=>$_POST['sql'],'name'=>$_POST['name'],'type'=>$_POST['type']));
	    $response['success']=true;

	    break;


		case 'upload_image':

			$response['success']=true;

			$dir = $GO_CONFIG->tmpdir.'attachments/';
			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			filesystem::mkdir_recursive($dir);

			if(isset($_FILES['Filedata']))
			{
				$file = $_FILES['Filedata'];
			}else
			{
				$file['name'] = $_FILES['attachments']['name'][0];
				$file['tmp_name'] = $_FILES['attachments']['tmp_name'][0];
				$file['size'] = $_FILES['attachments']['size'][0];
			}

			if(is_uploaded_file($file['tmp_name']))
			{
				$tmp_file = $dir.File::strip_invalid_chars($file['name']);
				move_uploaded_file($file['tmp_name'], $tmp_file);

				$extension = File::get_extension($file['name']);
				$response['file'] = array(
					'tmp_name'=>$tmp_file,
					'name'=>utf8_basename($tmp_file),
					'size'=>$file['size'],
					'type'=>File::get_filetype_description($extension),
					'extension'=>$extension,
					'human_size'=>Number::format_size($file['size'])
				);
			}

			echo json_encode($response);
			exit();

			break;

		case 'update_level':

			if(!$GO_SECURITY->has_permission_to_manage_acl($GO_SECURITY->user_id, $_POST['acl_id'])){
				throw new AccessDeniedException();
			}

			if(!empty($_POST['user_id'])){

				$acl = $GO_SECURITY->get_acl($_POST['acl_id']);

				if($_POST['user_id']==$acl['user_id'] || $_POST['user_id']==$GO_SECURITY->user_id){
					throw new Exception($lang['common']['dontChangeOwnersPermissions']);
				}

				$response['success']=$GO_SECURITY->add_user_to_acl($_POST['user_id'], $_POST['acl_id'], $_POST['level']);
			}else
			{
				if($_POST['group_id']==$GO_CONFIG->group_root){
					throw new Exception($lang['common']['dontChangeAdminsPermissions']);
				}
				$response['success']=$GO_SECURITY->add_group_to_acl($_POST['group_id'], $_POST['acl_id'], $_POST['level']);
			}

			break;

		
		case 'complete_profile':
			
			$user['id']=$GO_SECURITY->user_id;
			$user['first_name']=$_POST['first_name'];
			$user['middle_name']=$_POST['middle_name'];
			$user['last_name']=$_POST['last_name'];
			
			$user['title'] = isset($_POST["title"]);
			$user['initials'] = isset($_POST["initials"]);
			$user['sex'] = isset($_POST["sex"]);
			$user['birthday'] = isset($_POST['birthday']) ? Date::to_db_date($_POST['birthday']) : '';
			$user['address'] = isset($_POST["address"]) ? $_POST["address"] : '';
			$user['address_no'] = isset($_POST["address_no"]) ? $_POST["address_no"] : '';
			$user['zip'] = isset($_POST["zip"]) ? $_POST["zip"] : '';
			$user['city'] = isset($_POST["city"]) ? $_POST["city"] : '';
			$user['state'] = isset($_POST["state"]) ? $_POST["state"] : '';
			$user['country'] = isset($_POST["country"]) ? $_POST["country"] : '';

			$user['email'] = isset($_POST["email"]) ? $_POST["email"] : '';
			$user['home_phone'] = isset($_POST["home_phone"]) ? $_POST["home_phone"] : '';
			$user['fax'] = isset($_POST["fax"]) ? $_POST["fax"] : '';
			$user['cellular'] = isset($_POST["cellular"]) ? $_POST["cellular"] : '';
			
			$user['company'] = isset($_POST["company"]) ? $_POST["company"] : '';
			$user['department'] = isset($_POST["department"]) ? $_POST["department"] : '';
			$user['function'] = isset($_POST["function"]) ? $_POST["function"] : '';
			$user['work_address'] = isset($_POST["work_address"]) ? $_POST["work_address"] : '';
			$user['work_address_no'] = isset($_POST["work_address_no"]) ? $_POST["work_address_no"] : '';
			$user['work_zip'] = isset($_POST["work_zip"]) ? $_POST["work_zip"] : '';
			$user['work_city'] = isset($_POST["work_city"]) ? $_POST["work_city"] : '';
			$user['work_state'] = isset($_POST["work_state"]) ? $_POST["work_state"] : '';
			$user['work_country'] = isset($_POST["work_country"]) ? $_POST["work_country"] : '';
			$user['work_phone'] = isset($_POST["work_phone"]) ? $_POST["work_phone"] : '';
			$user['work_fax'] = isset($_POST["work_fax"]) ? $_POST["work_fax"] : '';
			$user['homepage'] = isset($_POST["homepage"]) ? $_POST["homepage"] : '';

			require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$GO_USERS->update_profile($user, true);
			
			$response['success']=true;
			
			break;
		
		case 'lost_password':

			require($GO_LANGUAGE->get_base_language_file('lostpassword'));

			require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();
			
			if($user = $GO_USERS->get_user_by_email($_POST['email']))
			{
				$url = $GO_CONFIG->full_url.'change_lost_password.php?username='.$user['username'].'&code1='.md5($user['password']).'&code2='.md5($user['lastlogin'].$user['registration_time']);

				$salutation = $lang['common']['default_salutation'][$user['sex']];
				if(!empty($user['middle_name']))
					$salutation .= ' '.$user['middle_name'];
				$salutation .= ' '.$user['last_name'];

				$mail_body = sprintf($lang['lostpassword']['lost_password_body'],
					$salutation,
					$GO_CONFIG->title,
					$user['username'],
					$url);
				
				require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
				$swift = new GoSwift($user['email'], $lang['lostpassword']['lost_password_subject']);
				$swift->set_body($mail_body,'plain');
				$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
				$swift->sendmail();				
				
				$response['success']=true;
				$response['feedback']=$lang['lostpassword']['lost_password_success'];
			}else
			{
				$response['success']=false;
				$response['feedback']=$lang['lostpassword']['lost_password_error'];
			}
			
			break;
		
		case 'save_settings':

			$GO_EVENTS->fire_event('before_save_settings');

			$GO_EVENTS->fire_event('save_settings');

			$response['success']=true;
			break;
			
		case 'snooze_reminders':
			
			require($GO_CONFIG->class_path.'base/reminder.class.inc.php');
			$rm = new reminder();
			

			$reminders = json_decode($_POST['reminders'], true);
			$snooze_time = intval($_POST['snooze_time']);
			
			foreach($reminders as $reminder_id)
			{
				$rm->add_user_to_reminder($GO_SECURITY->user_id, $reminder_id, time()+$_POST['snooze_time']);
			}
			$response['success']=true;			
			break;
		case 'dismiss_reminders':
			
			require($GO_CONFIG->class_path.'base/reminder.class.inc.php');
			$rm = new reminder();
			
			$reminders = json_decode($_POST['reminders'], true);
			
			foreach($reminders as $reminder_id)
			{				
				$reminder = $rm->get_reminder($reminder_id);
				
				//other modules can do something when a reminder is dismissed
				//eg. The calendar module sets a next reminder for a recurring event.
				$GO_EVENTS->fire_event('reminder_dismissed', array($reminder, $GO_SECURITY->user_id));
				//$rm->delete_reminder($reminder_id);
				$rm->remove_user_from_reminder($GO_SECURITY->user_id, $reminder_id);
			}
			
			$response['success']=true;
			break;
		
		case 'login':

			$response['success']=false;

			$username = !empty($_POST['domain']) ? $_POST['username'].$_POST['domain'] : $_POST['username'];
			$password = $_POST['password'];

			if (!$username || !$password)
			{
				throw new Exception($lang['common']['missingField']);
			}

			//attempt login using security class inherited from index.php
			//$params = isset( $auth_sources[$auth_source]) ?  $auth_sources[$auth_source] : false;

			require_once($GO_CONFIG->class_path.'base/auth.class.inc.php');
			$GO_AUTH = new GO_AUTH();

			if (!$GO_AUTH->login($username, $password))
			{
				throw new Exception($lang['common']['badLogin']);
			}
			//login is correct final check if login registration was ok
			if (!$GO_SECURITY->logged_in())
			{
				throw new Exception($lang['common']['saveError']);
			}
			/*if ($_REQUEST['language']=='00') {
				global $GO_LANGUAGE, $GO_SECURITY;
				$GO_USERS = new GO_USERS();
				$user = $GO_USERS->get_user($GO_SECURITY->user_id);
				$GO_LANGUAGE->set_language($user['language']);
				require($GO_LANGUAGE->get_base_language_file('common'));
			}*/
			if (isset($_POST['remind']))
			{
				require_once($GO_CONFIG->class_path.'cryptastic.class.inc.php');
				$c = new cryptastic();

				$enc_username = $c->encrypt($username);
				if(empty($enc_username)){
					$enc_username=$username;
					$enc_password=$password;
				}else
				{
					$enc_password=$c->encrypt($password);
				}

				SetCookie("GO_UN",$enc_username,time()+3600*24*30,"/",'',!empty($_SERVER['HTTPS']),true);
				SetCookie("GO_PW",$enc_password,time()+3600*24*30,"/",'',!empty($_SERVER['HTTPS']),true);
			}
			
			$fullscreen = isset($_POST['fullscreen']) ? '1' : '0';
			
			SetCookie("GO_FULLSCREEN",$fullscreen,time()+3600*24*30,"/",'',!empty($_SERVER['HTTPS']),true);
				
			$response['user_id']=$GO_SECURITY->user_id;
			$response['name']=$_SESSION['GO_SESSION']['name'];
			$response['email']=$_SESSION['GO_SESSION']['email'];
			//$response['sid']=session_id();

			$response['auth_token']=$_SESSION['GO_SESSION']['auth_token'];			
			
			//$response['fullscreen']=isset($_POST['fullscreen']);
				
			$response['settings'] = $GO_CONFIG->get_client_settings();
						
			require_once($GO_CONFIG->class_path.'cache.class.inc.php');
			$cache = new cache();
			$cache->cleanup();
			
			$response['success']=true;

			break;

		case 'logout':
			$GO_SECURITY->logout();

			unset($_SESSION);
			unset($_COOKIE);

			break;

		case 'link':

			require_once($GO_CONFIG->class_path.'base/links.class.inc.php');
			$GO_LINKS = new GO_LINKS();

			$fromLinks = json_decode($_POST['fromLinks'],true);
			$toLinks = json_decode($_POST['toLinks'],true);
			$from_folder_id=isset($_POST['folder_id']) ? $_POST['folder_id'] : 0;
			$to_folder_id=isset($_POST['to_folder_id']) ? $_POST['to_folder_id'] : 0;

			foreach($fromLinks as $fromLink)
			{
				foreach($toLinks as $toLink)
				{
					$GO_LINKS->add_link($fromLink['link_id'], $fromLink['link_type'], $toLink['link_id'], $toLink['link_type'],$from_folder_id, $to_folder_id, $_POST['description'], $_POST['description']);
				}
			}

			$response['success']=true;
				break;
		case 'updatelink':

			require_once($GO_CONFIG->class_path.'base/links.class.inc.php');
			$GO_LINKS = new GO_LINKS();
			
			$link['id']=$_POST['link_id1'];
			$link['link_id']=$_POST['link_id2'];
			$link['link_type']=$_POST['link_type2'];
			$link['description']=$_POST['description'];

			$GO_LINKS->update_link($_POST['link_type1'],$link);
			
			$link['id']=$_POST['link_id2'];
			$link['link_id']=$_POST['link_id1'];
			$link['link_type']=$_POST['link_type1'];

			$GO_LINKS->update_link($_POST['link_type2'],$link);
			
			$response['success']=true;
		break;
		
		case 'move_links':

			require_once($GO_CONFIG->class_path.'base/links.class.inc.php');
			$GO_LINKS = new GO_LINKS();
			
			$move_links = json_decode(($_POST['selections']), true);
			$target = json_decode(($_POST['target']), true);
			
			$response['moved_links']=array();
			
			foreach($move_links as $link_and_type)
			{
				$link = explode(':', $link_and_type);
				$link_type = $link[0];
				$link_id = $link[1];
				
				if($link_type=='folder')
				{
					if($target['folder_id'] != $link_id && !$GO_LINKS->is_sub_folder($link_id, $target['folder_id']))
					{
						$folder['id']=$link_id;
						$folder['parent_id']=$target['folder_id'];
						$GO_LINKS->update_folder($folder);
						
						$response['moved_links'][]=$link_and_type;
					}
				}else
				{
					$update_link['link_type']=$link_type;
					$update_link['link_id']=$link_id;
					$update_link['id']=$target['link_id'];
					$update_link['folder_id']=$target['folder_id'];
					$GO_LINKS->update_link($target['link_type'], $update_link);
					
					$response['moved_links'][]=$link_and_type;
				}
			}
			$response['success']=true;
			
			
			break;

		case 'save_link_folder':

			require_once($GO_CONFIG->class_path.'base/links.class.inc.php');
			$GO_LINKS = new GO_LINKS();

			
			$folder['id']=isset($_POST['folder_id']) ? ($_POST['folder_id']) : 0;
			$folder['name']=$_POST['name'];
			$folder['parent_id']=isset($_POST['parent_id']) ? ($_POST['parent_id']) : 0;
			$folder['link_id']=isset($_POST['link_id']) ? ($_POST['link_id']) : 0;
			$folder['link_type']=isset($_POST['link_type']) ? ($_POST['link_type']) : 0;


			
			if($folder['id']>0)
			{
				$GO_LINKS->update_folder($folder);
			}else
			{
				if($GO_LINKS->get_folder_by_name($folder['name'],$folder['link_id'], $folder['link_type'], $folder['parent_id']))
				{
					throw new Exception($lang['common']['theFolderAlreadyExists']);
				}

				$response['folder_id']=$GO_LINKS->add_folder($folder);
			}

			$response['success']=true;

			break;
	}
}
catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);