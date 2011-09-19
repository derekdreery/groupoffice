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
$GLOBALS['GO_SECURITY']->json_authenticate('tasks');

require_once ($GLOBALS['GO_MODULES']->modules['tasks']['class_path']."tasks.class.inc.php");
//require_once ($GLOBALS['GO_LANGUAGE']->get_language_file('tasks'));
$tasks = new tasks();

//we are unsuccessfull by default
$response =array('success'=>false);

//for servers with register_globals on
unset($task);

try {
	switch($_REQUEST['task']) {
		case 'import':

			ini_set('max_execution_time', 180);

			require_once ($GLOBALS['GO_LANGUAGE']->get_language_file('tasks'));

			if (!file_exists($_FILES['ical_file']['tmp_name'][0])) {
				throw new Exception($lang['common']['noFileUploaded']);
			}else {
				$tmpfile = $GLOBALS['GO_CONFIG']->tmpdir.uniqid(time());
				move_uploaded_file($_FILES['ical_file']['tmp_name'][0], $tmpfile);
				File::convert_to_utf8($tmpfile);

				if($count = $tasks->import_ical_file($tmpfile, $_POST['tasklist_id'])) {
					$response['feedback'] = sprintf($lang['tasks']['import_success'], $count);
					$response['success']=true;
				}else {
					throw new Exception($lang['common']['saveError']);
				}
				unlink($tmpfile);
			}
			break;

		case 'schedule_call':

			$task['name']=$_POST['name'];
			$task['start_time']=$task['due_time']=Date::to_unixtime($_POST['date']);
			$task['description']=$_POST['description'];
			$task['status']='NEEDS-ACTION';
			$task['tasklist_id']=$_POST['tasklist_id'];
			$task['reminder']=Date::to_unixtime(($_POST['date'].' '.$_POST['remind_time']));
			$task['user_id']=$GLOBALS['GO_SECURITY']->user_id;

			$response['task_id']= $task_id = $tasks->add_task($task);

			$links = json_decode($_POST['links'], true);

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/links.class.inc.php');
			$GO_LINKS = new GO_LINKS();

			foreach($links as $link) {
				if($link['link_id']>0) {
					$GO_LINKS->add_link(
									($link['link_id']),
									($link['link_type']),
									$task_id,
									12);
				}
			}

			$comment_link_index = isset($_POST['comment_link_index']) ? $_POST['comment_link_index'] : 0;

			/*if(isset($GLOBALS['GO_MODULES']->modules['comments']) && isset($links[$comment_link_index]))
			{
				require($GLOBALS['GO_LANGUAGE']->get_language_file('tasks'));
				
				require_once($GLOBALS['GO_MODULES']->modules['comments']['class_path'].'comments.class.inc.php');
				$comments = new comments();
				
				$comment['comments']=sprintf($lang['tasks']['scheduled_call'], Date::get_timestamp($task['reminder']));
				if(!empty($task['description']))
					$comment['comments'] .= "\n\n".$task['description'];
					
				$comment['link_id']=($links[$comment_link_index]['link_id']);
				$comment['link_type']=($links[$comment_link_index]['link_type']);			
				$comment['user_id']=$GLOBALS['GO_SECURITY']->user_id;
				
				$comments->add_comment($comment);
			}*/


			$response['success']=true;

			break;

		case 'continue_task':

			$GLOBALS['GO_LANGUAGE']->require_language_file('tasks');

			$old_task= $tasks->get_task($_POST['task_id']);
			$old_tasklist = $tasks->get_tasklist($old_task['tasklist_id']);
			if($old_task['tasklist_id'] != $_POST['tasklist_id']){
				$new_tasklist = $tasks->get_tasklist($_POST['tasklist_id']);

				$_POST['description'].= sprintf("\n\n".$lang['tasks']['tasklistChanged'], $old_tasklist['name'], $new_tasklist['name']);
			}

			if($old_task['status'] != $_POST['status']){
				$new_tasklist = $tasks->get_tasklist($_POST['tasklist_id']);

				$old_status_lang = isset( $lang['tasks']['statuses'][$old_task['status']]) ?  $lang['tasks']['statuses'][$old_task['status']] : $old_task['status'];
				$new_status_lang = isset( $lang['tasks']['statuses'][$_POST['status']]) ?  $lang['tasks']['statuses'][$_POST['status']] : $_POST['status'];

				$_POST['description'].= sprintf("\n\n".$lang['tasks']['statusChanged'], $old_status_lang, $new_status_lang);
			}


			$task['id']=$_POST['task_id'];
			$task['due_time']=Date::to_unixtime($_POST['date']);
			$task['status']=$_POST['status'];
			$task['tasklist_id']=$_POST['tasklist_id'];
			$task['reminder']=Date::to_unixtime($_POST['date'].' '.$_POST['remind_time']);

			$tasks->update_task($task, $old_tasklist, $old_task);

			if(isset($GLOBALS['GO_MODULES']->modules['comments']))
			{
				require($GLOBALS['GO_LANGUAGE']->get_language_file('tasks'));

				require_once($GLOBALS['GO_MODULES']->modules['comments']['class_path'].'comments.class.inc.php');
				$comments = new comments();

				$comment['comments']=$_POST['description'];

				$comment['link_id']=$task['id'];
				$comment['link_type']=12;
				$comment['user_id']=$GLOBALS['GO_SECURITY']->user_id;

				$comments->add_comment($comment);
			}

			$response['success']=true;

			break;

		case 'save_task':
//			$conflicts=array();
//
//			$task_id=$task['id']=isset($_POST['task_id']) ? ($_POST['task_id']) : 0;
//
//			$task['name']=$_POST['name'];
//			$task['due_time']=Date::to_unixtime($_POST['due_date']);
//			$task['start_time']=Date::to_unixtime($_POST['start_date']);
//			$task['tasklist_id']=$_POST['tasklist_id'];
//                        $task['category_id']=(isset($_REQUEST['category_id']) && $_REQUEST['category_id']) ? $_REQUEST['category_id'] : 0;
//			$task['priority']=(isset($_REQUEST['priority'])) ? $_REQUEST['priority'] : 1;
//
//			if(isset($_POST['project_name']))
//				$task['project_name']=$_POST['project_name'];
//
//			$tasklist = $tasks->get_tasklist($task['tasklist_id']);
//			if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $tasklist['acl_id'])<GO_SECURITY::WRITE_PERMISSION) {
//				throw new AccessDeniedException();
//			}
//
//			if(isset($_POST['status']))
//				$task['status']=$_POST['status'];
//			if(isset($_POST['description']))
//				$task['description']=$_POST['description'];
//
//			$old_task = $task_id>0 ? $tasks->get_task($task_id) : false;

//			if(isset($_POST['remind'])) {
//				$task['reminder']=Date::to_unixtime($_POST['remind_date'].' '.$_POST['remind_time']);
//			}elseif(!isset($_POST['status'])) {
//				//this task is added with the quick add option
//				$settings=$tasks->get_settings($GLOBALS['GO_SECURITY']->user_id);
//				if(!empty($settings['remind'])) {
//					$reminder_day = $task['due_time'];
//					if(!empty($settings['reminder_days']))
//						$reminder_day = Date::date_add($reminder_day,-$settings['reminder_days']);
//
//					$task['reminder']=Date::to_unixtime(Date::get_timestamp($reminder_day, false).' '.$settings['reminder_time']);
//				}
//			}else {
//				$task['reminder']=0;
//			}
//			$timezone_offset = Date::get_timezone_offset($task['due_time']);
//
//			if(empty($task['tasklist_id'])) {
//				throw new Exception('FATAL: No tasklist ID!');
//			}
//
//			$repeat_every = isset ($_POST['repeat_every']) ? $_POST['repeat_every'] : '1';
//			$task['repeat_end_time'] = (isset ($_POST['repeat_forever']) || !isset($_POST['repeat_end_date'])) ? '0' : Date::to_unixtime($_POST['repeat_end_date']);
//			$month_time = isset ($_POST['month_time']) ? $_POST['month_time'] : '0';
//
//			$days['mon'] = isset ($_POST['repeat_days_1']) ? '1' : '0';
//			$days['tue'] = isset ($_POST['repeat_days_2']) ? '1' : '0';
//			$days['wed'] = isset ($_POST['repeat_days_3']) ? '1' : '0';
//			$days['thu'] = isset ($_POST['repeat_days_4']) ? '1' : '0';
//			$days['fri'] = isset ($_POST['repeat_days_5']) ? '1' : '0';
//			$days['sat'] = isset ($_POST['repeat_days_6']) ? '1' : '0';
//			$days['sun'] = isset ($_POST['repeat_days_0']) ? '1' : '0';
//
//
//			$days = Date::shift_days_to_gmt($days, date('G', $task['due_time']), Date::get_timezone_offset($task['due_time']));
//			if(isset($_POST['repeat_type']) && $_POST['repeat_type']>0) {
//				$task['rrule']=Date::build_rrule($_POST['repeat_type'], $repeat_every,$task['repeat_end_time'], $days, $month_time);
//			}
//
//			if(empty($task['name']) || empty($task['due_time'])) {
//				throw new Exception($lang['common']['missingField']);
//			}
//
//			if($task['id']>0) {
//				$tasks->update_task($task, $tasklist, $old_task);
//				$insert = false;
//				$response['success']=true;
//
//			}else {
//				$task['user_id']=$GLOBALS['GO_SECURITY']->user_id;
//				$task_id= $tasks->add_task($task, $tasklist);
//				if($task_id) {
//					$insert = true;
//					$response['task_id']=$task_id;
//					$response['success']=true;
//				}					
//			}
//
//			if(isset($GLOBALS['GO_MODULES']->modules['customfields']) && $GLOBALS['GO_MODULES']->modules['customfields']['read_permission']) {
//				require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
//				$cf = new customfields();
//				$cf->update_fields($GLOBALS['GO_SECURITY']->user_id, $task_id, 12, $_POST, $insert);
//			}

//			if(!empty($_POST['tmp_files']) && $GLOBALS['GO_MODULES']->has_module('files')) {
//				require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
//				$files = new files();
//				$fs = new filesystem();
//
//				$task = $tasks->get_task($task_id);
//				$path = $files->build_path($task['files_folder_id']);
//
//
//				$tmp_files = json_decode($_POST['tmp_files'], true);
//				while($tmp_file = array_shift($tmp_files)) {
//					if(!empty($tmp_file['tmp_file'])) {
//						$new_path = $GLOBALS['GO_CONFIG']->file_storage_path.$path.'/'.$tmp_file['name'];
//						$fs->move($tmp_file['tmp_file'], $new_path);
//						$files->import_file($new_path, $task['files_folder_id']);
//					}
//				}
//			}

//			if(!empty($_POST['link'])) {
//				require_once($GLOBALS['GO_CONFIG']->class_path.'base/links.class.inc.php');
//				$GO_LINKS = new GO_LINKS();
//				
//				$link_props = explode(':', $_POST['link']);
//				$GO_LINKS->add_link(
//								($link_props[1]),
//								($link_props[0]),
//								$task_id,
//								12);
//			}
//			break;


//		case 'save_tasklist':

//			$tasklist['id']=$_POST['tasklist_id'];
//			$tasklist['user_id'] = isset($_POST['user_id']) ? ($_POST['user_id']) : $GLOBALS['GO_SECURITY']->user_id;
//			$tasklist['name']=$_POST['name'];
//
//
//			if(empty($tasklist['name'])) {
//				throw new Exception($lang['common']['missingField']);
//			}
//
//			/*$existing_tasklist = $tasks->get_tasklist_by_name($tasklist['name']);
//			if($existing_tasklist && ($tasklist['id']==0 || $existing_tasklist['id']!=$tasklist['id']))
//			{
//				throw new Exception($sc_tasklist_exists);
//			}*/
//
//			if($tasklist['id']>0) {
//				$old_tasklist = $tasks->get_tasklist($tasklist['id']);
//				if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $old_tasklist['acl_id'])<GO_SECURITY::WRITE_PERMISSION) {
//					throw new AccessDeniedException();
//				}
//				if(!$GLOBALS['GO_SECURITY']->has_admin_permission($GLOBALS['GO_SECURITY']->user_id))
//				{
//					unset($tasklist['user_id']);
//				}				
//				$tasks->update_tasklist($tasklist, $old_tasklist);
//			}else {
//				if(!$GLOBALS['GO_MODULES']->modules['tasks']['write_permission']) {
//					throw new AccessDeniedException();
//				}
//				$response['acl_id'] = $tasklist['acl_id'] = $GLOBALS['GO_SECURITY']->get_new_acl('tasks', $tasklist['user_id']);
//
//				$response['tasklist_id']=$tasks->add_tasklist($tasklist);
//			}
//			$response['success']=true;
//
//			break;
//

		case 'save_portlet':
			$tasklists = json_decode($_POST['tasklists'], true);
			$response['data'] = array();
			foreach($tasklists as $tasklist) {
				$tasklist['user_id'] = $GLOBALS['GO_SECURITY']->user_id;
				if($tasklist['visible'] == 0) {
					$tasks->delete_visible_tasklist($tasklist['tasklist_id'], $tasklist['user_id']);
				}
				else {
					$tasklist['tasklist_id']=$tasks->add_visible_tasklist(array('tasklist_id'=>$tasklist['tasklist_id'], 'user_id'=>$tasklist['user_id']));
				}
				$response['data'][$tasklist['tasklist_id']]=$tasklist;
			}
			$response['success']=true;
			break;

//
//                case 'save_category':
//
//			$category['id'] = (isset($_REQUEST['id']) && $_REQUEST['id']) ? $_REQUEST['id'] : 0;
//                        $category['name'] = (isset($_REQUEST['name']) && $_REQUEST['name']) ? $_REQUEST['name'] : '';
//                        $category['user_id'] = (isset($_REQUEST['global'])) ? 0 : $GLOBALS['GO_SECURITY']->user_id;
//
//			if(empty($category['name']))
//			{
//				throw new Exception($lang['common']['missingField']);
//			}
//
//			if($category['id']>0)
//			{
//				$tasks->update_category($category);
//			}else
//			{
//				$response['id'] = $tasks->add_category($category);
//			}
//
//			$response['success'] = true;
//			break;                        		


		case 'move_tasks':

			$items = (isset($_REQUEST['items']) && count($_REQUEST['items'])) ? json_decode($_REQUEST['items']) : array();
			$tasklist_id = (isset($_REQUEST['tasklist_id']) && $_REQUEST['tasklist_id']) ? $_REQUEST['tasklist_id'] : 0;

			$num_updated = 0;
			if($tasklist_id)
			{				
				$tasklist = $tasks->get_tasklist($tasklist_id);
				for($i=0; $i<count($items); $i++)
				{
					$old_task = $tasks->get_task($items[$i]);
					if($old_task['tasklist_id'] != $tasklist_id)
					{
						$task = array('id' => $items[$i], 'tasklist_id' => $tasklist_id);
						$tasks->update_task($task, $tasklist);

						$num_updated++;
					}
				}
			}

			if($num_updated)
			{
			    $response['reload_store'] = true;
			}

			$response['success'] = true;
			break;

	}
}catch(Exception $e) {
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}

echo json_encode($response);