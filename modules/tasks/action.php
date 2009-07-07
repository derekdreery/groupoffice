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
$GO_SECURITY->json_authenticate('tasks');

require_once ($GO_MODULES->modules['tasks']['class_path']."tasks.class.inc.php");
//require_once ($GO_LANGUAGE->get_language_file('tasks'));
$tasks = new tasks();

//we are unsuccessfull by default
$response =array('success'=>false);

//for servers with register_globals on
unset($task);

try{
	switch($_REQUEST['task'])
	{	
		case 'import':
			require_once ($GO_LANGUAGE->get_language_file('tasks'));
			
			if (!file_exists($_FILES['ical_file']['tmp_name'][0]))
			{
				throw new Exception($lang['common']['noFileUploaded']);
			}else
			{
				if($count = $tasks->import_ical_file($_FILES['ical_file']['tmp_name'][0], $_POST['tasklist_id']))
				{
					$response['feedback'] = sprintf($lang['tasks']['import_success'], $count);
					$response['success']=true;					
				}else
				{
					throw new Exception($lang['common']['saveError']);
				}
				unlink($_FILES['ical_file']['tmp_name'][0]);
			}
			break;
			
		case 'schedule_call':
			
			$task['name']=$_POST['name'];
			$task['start_time']=$task['due_time']=Date::to_unixtime($_POST['date']);
			$task['description']=$_POST['description'];
			$task['status']='NEEDS-ACTION';
			$task['tasklist_id']=$_POST['tasklist_id'];
			$task['reminder']=Date::to_unixtime(($_POST['date'].' '.$_POST['remind_time']));	
			$task['user_id']=$GO_SECURITY->user_id;
			
			$response['task_id']= $task_id = $tasks->add_task($task);

			$links = json_decode(($_POST['links']), true);
			
			foreach($links as $link)
			{
				if($link['link_id']>0)
				{
					$GO_LINKS->add_link(
					($link['link_id']),
					($link['link_type']),
					$task_id,
					12);
				}
			}
			
			$comment_link_index = isset($_POST['comment_link_index']) ? $_POST['comment_link_index'] : 0; 
			
			if(isset($GO_MODULES->modules['comments']) && isset($links[$comment_link_index]))
			{
				require($GO_LANGUAGE->get_language_file('tasks'));
				
				require_once($GO_MODULES->modules['comments']['class_path'].'comments.class.inc.php');
				$comments = new comments();
				
				$comment['comments']=sprintf($lang['tasks']['scheduled_call'], Date::get_timestamp($task['reminder']));
				if(!empty($task['description']))
					$comment['comments'] .= "\n\n".$task['description'];
					
				$comment['link_id']=($links[$comment_link_index]['link_id']);
				$comment['link_type']=($links[$comment_link_index]['link_type']);			
				$comment['user_id']=$GO_SECURITY->user_id;
				
				$comments->add_comment($comment);
			}
			
			
			$response['success']=true;
			
			break;
		
		case 'save_task':
			$conflicts=array();

			
			
			$task_id=$task['id']=isset($_POST['task_id']) ? ($_POST['task_id']) : 0;

			$task['name']=$_POST['name'];
			$task['due_time']=Date::to_unixtime($_POST['due_date']);
			$task['start_time']=Date::to_unixtime($_POST['start_date']);
			$task['tasklist_id']=$_POST['tasklist_id'];
			
			$tasklist = $tasks->get_tasklist($task['tasklist_id']);
			if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $tasklist['acl_write']))
			{
				throw new AccessDeniedException();
			}
			
			if(isset($_POST['status']))
				$task['status']=$_POST['status'];
			if(isset($_POST['description']))
				$task['description']=$_POST['description'];
				
			$old_task = $task_id>0 ? $tasks->get_task($task_id) : false;
			if(isset($task['status']))
			{
				
				if($task['status']=='COMPLETED' && (!$old_task || $old_task['completion_time']==0))
				{
					$task['completion_time']=time();
				}
				
				if($task['status']!='COMPLETED' && $old_task && $old_task['completion_time']>0)
				{
					$task['completion_time']=0;
				}
			}
			
			if(isset($_POST['remind']))
			{
				$task['reminder']=Date::to_unixtime(($_POST['remind_date'].' '.$_POST['remind_time']));	
			}else
			{
				$task['reminder']=0;
			}
			$timezone_offset = Date::get_timezone_offset($task['due_time']);

			if(empty($task['tasklist_id']))
			{
				throw new Exception('FATAL: No tasklist ID!');
			}			

			$repeat_every = isset ($_POST['repeat_every']) ? $_POST['repeat_every'] : '1';
			$task['repeat_end_time'] = (isset ($_POST['repeat_forever']) || !isset($_POST['repeat_end_date'])) ? '0' : Date::to_unixtime($_POST['repeat_end_date']);
			$month_time = isset ($_POST['month_time']) ? $_POST['month_time'] : '0';

			$days['mon'] = isset ($_POST['repeat_days_1']) ? '1' : '0';
			$days['tue'] = isset ($_POST['repeat_days_2']) ? '1' : '0';
			$days['wed'] = isset ($_POST['repeat_days_3']) ? '1' : '0';
			$days['thu'] = isset ($_POST['repeat_days_4']) ? '1' : '0';
			$days['fri'] = isset ($_POST['repeat_days_5']) ? '1' : '0';
			$days['sat'] = isset ($_POST['repeat_days_6']) ? '1' : '0';
			$days['sun'] = isset ($_POST['repeat_days_0']) ? '1' : '0';


			$days = Date::shift_days_to_gmt($days, date('G', $task['due_time']), Date::get_timezone_offset($task['due_time']));
			if(isset($_POST['repeat_type']) && $_POST['repeat_type']>0)
			{
				$task['rrule']=Date::build_rrule($_POST['repeat_type'], $repeat_every,$task['repeat_end_time'], $days, $month_time);
			}

			if(empty($task['name']) || empty($task['due_time']))
			{
				throw new Exception($lang['common']['missingField']);
			}

			if($task['id']>0)
			{
				$tasks->update_task($task, $tasklist, $old_task);
				$response['success']=true;

			}else
			{
				$task['user_id']=$GO_SECURITY->user_id;
				$task_id= $tasks->add_task($task, $tasklist);
				if($task_id)
				{					
					$response['task_id']=$task_id;
					$response['success']=true;
				}					
			}
			
			if(!empty($_POST['tmp_files']) && $GO_MODULES->has_module('files'))
			{
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
				$files = new files();
				$fs = new filesystem();
				
				$task = $tasks->get_task($task_id);
				$path = $files->build_path($task['files_folder_id']);
					

				$tmp_files = json_decode($_POST['tmp_files'], true);
				while($tmp_file = array_shift($tmp_files))
				{
					$new_path = $GO_CONFIG->file_storage_path.$path.'/'.$tmp_file['name'];
					$fs->move($tmp_file['tmp_file'], $new_path);
					$files->import_file($new_path, $task['files_folder_id']);
				}
			}

			if(!empty($_POST['link']))
			{
				$link_props = explode(':', $_POST['link']);
				$GO_LINKS->add_link(
				($link_props[1]),
				($link_props[0]),
				$task_id,
				12);
			}
			break;


		case 'save_tasklist':

			$tasklist['id']=$_POST['tasklist_id'];
			$tasklist['user_id'] = isset($_POST['user_id']) ? ($_POST['user_id']) : $GO_SECURITY->user_id;
			$tasklist['name']=$_POST['name'];


			if(empty($tasklist['name']))
			{
				throw new Exception($lang['common']['missingField']);
			}

			$existing_tasklist = $tasks->get_tasklist_by_name($tasklist['name']);
			if($existing_tasklist && ($tasklist['id']==0 || $existing_tasklist['id']!=$tasklist['id']))
			{
				throw new Exception($sc_tasklist_exists);
			}

			if($tasklist['id']>0)
			{
				$old_tasklist = $tasks->get_tasklist($tasklist['id']);
				if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_tasklist['acl_write']))
				{
					throw new AccessDeniedException();
				}
				$tasks->update_tasklist($tasklist, $old_tasklist);
			}else
			{
				if(!$GO_MODULES->modules['tasks']['write_permission'])
				{
					throw new AccessDeniedException();
				}
				$response['acl_read'] = $tasklist['acl_read'] = $GO_SECURITY->get_new_acl('tasklist read: '.$tasklist['name'], $tasklist['user_id']);
				$response['acl_write'] = $tasklist['acl_write'] = $GO_SECURITY->get_new_acl('tasklist write: '.$tasklist['name'], $tasklist['user_id']);
					
				$response['acl_read'] =
				$response['tasklist_id']=$tasks->add_tasklist($tasklist);
			}
			$response['success']=true;

			break;





			$response['success']=true;

			break;
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}

echo json_encode($response);