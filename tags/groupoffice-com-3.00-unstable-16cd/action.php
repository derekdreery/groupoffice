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



$response =array();
try{

	switch($_REQUEST['task'])
	{
		
		case 'complete_profile':
			
			$user['id']=$GO_SECURITY->user_id;
			$user['first_name']=$_POST['first_name'];
			$user['last_name']=$_POST['last_name'];
			
			$GO_USERS->update_profile($user, true);
			
			$response['success']=true;
			
			break;
		
		case 'lost_password':
			
			if($user = $GO_USERS->get_user_by_email(($_POST['email'])))
			{
				$new_password = $GO_USERS->random_password();
				
				$up_user['id']=$user['id'];
				$up_user['password']=$new_password;
				
				$GO_USERS->update_profile($up_user);
		
				$salutation = $lang['common']['default_salutation'][$user['sex']];
				if(!empty($user['middle_name']))
					$salutation .= ' '.$user['middle_name'];
				$salutation .= ' '.$user['last_name'];

				$mail_body = sprintf($lang['common']['lost_password_body'],
					$salutation,
					$GO_CONFIG->title,
					$user['username'],
					$new_password);
				
				require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
				$swift = new GoSwift($user['email'], $lang['common']['lost_password_subject']);
				$swift->set_body($mail_body);
				$swift->sendmail($GO_CONFIG->webmaster_email, $GO_CONFIG->title);				
				
				$response['success']=true;
				$response['feedback']=$lang['common']['lost_password_success'];
			}else
			{
				$response['success']=false;
				$response['feedback']=$lang['common']['lost_password_error'];
			}
			
			break;
		
		case 'save_settings':			

			$GO_MODULES->fire_event('save_settings');

			$response['success']=true;
			break;
			
		case 'snooze_reminders':
			
			require($GO_CONFIG->class_path.'base/reminder.class.inc.php');
			$rm = new reminder();
			
			$reminders = json_decode(($_POST['reminders']));
			
			$snooze_time = ($_POST['snooze_time']);
			
			foreach($reminders as $reminder_id)
			{
				$reminder['id']=$reminder_id;
				$reminder['time']=time()+$snooze_time;
				$rm->update_reminder($reminder);
			}
			$response['success']=true;			
			break;
		case 'dismiss_reminders':
			
			require($GO_CONFIG->class_path.'base/reminder.class.inc.php');
			$rm = new reminder();
			
			$reminders = json_decode(($_POST['reminders']));
			
			foreach($reminders as $reminder_id)
			{				
				$reminder = $rm->get_reminder($reminder_id);
				
				//other modules can do something when a reminder is dismissed
				//eg. The calendar module sets a next reminder for a recurring event.
				$GO_MODULES->fire_event('reminder_dismissed', $reminder);
				$rm->delete_reminder($reminder_id);
			}
			
			$response['success']=true;
			break;
		
		case 'login':

			$response['success']=false;

			$username = ($_POST['username']);
			$password = ($_POST['password']);

			if (!$username || !$password)
			{
				throw new Exception($lang['common']['missingField']);
			}

			//attempt login using security class inherited from index.php
			//$params = isset( $auth_sources[$auth_source]) ?  $auth_sources[$auth_source] : false;
			if (!$GO_AUTH->login($username, $password))
			{
				throw new Exception($lang['common']['badLogin']);
			}
			//login is correct final check if login registration was ok
			if (!$GO_SECURITY->logged_in())
			{
				throw new Exception($lang['common']['saveError']);
			}
			if (isset($_POST['remind']))
			{
				SetCookie("GO_UN",$username,time()+3600*24*30,"/",'',0);
				SetCookie("GO_PW",$password,time()+3600*24*30,"/",'',0);
			}
			
			$fullscreen = isset($_POST['fullscreen']) ? '1' : '0';
			
			SetCookie("GO_FULLSCREEN",$fullscreen,time()+3600*24*30,"/",'',0);
				
			$response['user_id']=$GO_SECURITY->user_id;
			$response['name']=$_SESSION['GO_SESSION']['name'];
			//$response['sid']=session_id();
			
			
			//$response['fullscreen']=isset($_POST['fullscreen']);
				
			$response['settings'] = $GO_CONFIG->get_client_settings();
			
			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			$fs = new filesystem();
			if(file_exists($GO_CONFIG->tmpdir.$GO_SECURITY->user_id.'/'))
			{
				$fs->delete($GO_CONFIG->tmpdir.$GO_SECURITY->user_id.'/');
			}
			$fs->mkdir_recursive($GO_CONFIG->tmpdir.$GO_SECURITY->user_id.'/');
			
			$response['success']=true;

			break;

		case 'logout':
			$GO_SECURITY->logout();
				
			SetCookie("GO_UN","",time()-3600,"/","",0);
			SetCookie("GO_PW","",time()-3600,"/","",0);
			unset($_SESSION);
			unset($_COOKIE);

			break;

		case 'link':

			$fromLinks = json_decode(($_POST['fromLinks']),true);
			$toLinks = json_decode(($_POST['toLinks']),true);
			$from_folder_id=isset($_POST['folder_id']) ? ($_POST['folder_id']) : 0;

			foreach($fromLinks as $fromLink)
			{
				foreach($toLinks as $toLink)
				{
					$GO_LINKS->add_link($fromLink['link_id'], $fromLink['link_type'], $toLink['link_id'], $toLink['link_type'],$from_folder_id);
				}
			}

			$response['success']=true;
				break;
		
		case 'move_links':
			
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