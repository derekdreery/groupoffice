<?php
/*
 Copyright Intermesh 2003
 Author: Merijn Schering <mschering@intermesh.nl>
 Version: 1.0 Release date: 08 July 2003

 This program is free software; you can redistribute it and/or modify it
 under the terms of the GNU General Public License as published by the
 Free Software Foundation; either version 2 of the License, or (at your
 option) any later version.
 */

require('../../Group-Office.php');

$GO_SECURITY->json_authenticate('tasks');



require_once ($GO_MODULES->modules['tasks']['class_path']."tasks.class.inc.php");
$tasks = new tasks();
$tasks2 = new tasks();

$_task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';

try{

	switch($_task)
	{
		case 'task_with_items':
		case 'task':

			require($GO_CONFIG->class_path.'ical2array.class.inc');
			require($GO_CONFIG->class_path.'Date.class.inc.php');
			require_once($GO_LANGUAGE->get_language_file('tasks'));

			$task = $tasks->get_task(($_REQUEST['task_id']));
			$tasklist = $tasks->get_tasklist($task['tasklist_id']);

			$response['data']=$task;

			$response['data']['tasklist_name']=$tasklist['name'];

			$response['data']['status_text']=isset($lang['tasks']['statuses'][$task['status']]) ? $lang['tasks']['statuses'][$task['status']] : $lang['tasks']['statuses']['NEEDS-ACTION'];

			$response['data']['permission_level']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $tasklist['acl_id']);
			$response['data']['write_permission']=$response['data']['permission_level']>1;
			if(!$response['data']['permission_level'])
			{
				throw new AccessDeniedException();
			}

			$response['data']['subject']=$response['data']['name'];

			$due_time = $response['data']['due_time'];

			$response['data']['due_date']=date($_SESSION['GO_SESSION']['date_format'], $due_time);
			$response['data']['start_date']=date($_SESSION['GO_SESSION']['date_format'], $response['data']['start_time']);

			$response['data']['repeat_every'] = 1;
			$response['data']['repeat_forever'] = 0;
			$response['data']['repeat_type'] = REPEAT_NONE;
			$response['data']['repeat_end_time'] = 0;
			$response['data']['month_time'] = 0;


			if (!empty($response['data']['rrule']) && $rrule = ical2array::parse_rrule($response['data']['rrule']))
			{
				if(isset($rrule['FREQ']))
				{
					if (isset($rrule['UNTIL']))
					{
						$response['data']['repeat_end_time'] = ical2array::parse_date($rrule['UNTIL']);
					}elseif(isset($rrule['COUNT']))
					{
						//go doesn't support this
					}else
					{
						$response['data']['repeat_forever'] = 1;
					}

					$response['data']['repeat_every'] = $rrule['INTERVAL'];
					switch($rrule['FREQ'])
					{
						case 'DAILY':
							$response['data']['repeat_type'] = REPEAT_DAILY;
							break;

						case 'WEEKLY':
							$response['data']['repeat_type'] = REPEAT_WEEKLY;

							$days = explode(',', $rrule['BYDAY']);

							$response['data']['repeat_days_0'] = in_array('SU', $days) ? '1' : '0';
							$response['data']['repeat_days_1'] = in_array('MO', $days) ? '1' : '0';
							$response['data']['repeat_days_2'] = in_array('TU', $days) ? '1' : '0';
							$response['data']['repeat_days_3'] = in_array('WE', $days) ? '1' : '0';
							$response['data']['repeat_days_4'] = in_array('TH', $days) ? '1' : '0';
							$response['data']['repeat_days_5'] = in_array('FR', $days) ? '1' : '0';
							$response['data']['repeat_days_6'] = in_array('SA', $days) ? '1' : '0';
							break;

						case 'MONTHLY':
							if (isset($rrule['BYDAY']))
							{
								$response['data']['repeat_type'] = REPEAT_MONTH_DAY;

								$response['data']['month_time'] = $rrule['BYDAY'][0];
								$day = substr($rrule['BYDAY'], 1);

								switch($day)
								{
									case 'MO':
										$response['data']['repeat_days_1'] = 1;
										break;

									case 'TU':
										$response['data']['repeat_days_2'] = 1;
										break;

									case 'WE':
										$response['data']['repeat_days_3'] = 1;
										break;

									case 'TH':
										$response['data']['repeat_days_4'] = 1;
										break;

									case 'FR':
										$response['data']['repeat_days_5'] = 1;
										break;

									case 'SA':
										$response['data']['repeat_days_6'] = 1;
										break;

									case 'SU':
										$response['data']['repeat_days_0'] = 1;
										break;
								}
							}else
							{
								$response['data']['repeat_type'] = REPEAT_MONTH_DATE;
							}
							break;

									case 'YEARLY':
										$response['data']['repeat_type'] = REPEAT_YEARLY;
										break;
					}
				}
			}

			$response['data']['repeat_end_date']=$response['data']['repeat_end_time']>0 ? date($_SESSION['GO_SESSION']['date_format'], $response['data']['repeat_end_time']) : '';


			$response['data']['remind']=$response['data']['reminder']>0;

			if($response['data']['remind'])
			{
				$response['data']['remind_date']=date($_SESSION['GO_SESSION']['date_format'], $response['data']['reminder']);
				$response['data']['remind_time']=date($_SESSION['GO_SESSION']['time_format'], $response['data']['reminder']);
			}else
			{
				$response['data']['remind_date']=date($_SESSION['GO_SESSION']['date_format'], $response['data']['start_time']);
				$response['data']['remind_time']=date($_SESSION['GO_SESSION']['time_format'], 28800);
			}
			
			if($_task!='task') {
				$response['data']['description']=String::text_to_html($response['data']['description']);

				require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
				$search = new search();

				$links_json = $search->get_latest_links_json($GO_SECURITY->user_id, $response['data']['id'], 12);
				$response['data']['links']=$links_json['results'];

				if(isset($GO_MODULES->modules['files'])) {
					require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
					$files = new files();
					$response['data']['files']=$files->get_content_json($response['data']['files_folder_id']);
				}else {
					$response['data']['files']=array();
				}

				if(isset($GO_MODULES->modules['comments'])) {
					require_once ($GO_MODULES->modules['comments']['class_path'].'comments.class.inc.php');
					$comments = new comments();

					$response['data']['comments']=$comments->get_comments_json($response['data']['id'], 12);
				}
			}

			if(isset($GO_MODULES->modules['customfields'])) {
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$values = $cf->get_values($GO_SECURITY->user_id, 12, $response['data']['id']);
				$response['data']=array_merge($response['data'], $values);
			}

			$response['success']=true;
			break;



		case 'tasklist':

			$response['data']=$tasks->get_tasklist(($_POST['tasklist_id']));
			$user = $GO_USERS->get_user($response['data']['user_id']);
			$response['data']['user_name']=String::format_name($user);
			$response['success']=true;
			break;

		case 'tasklists':

			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$tasklists = json_decode($_POST['delete_keys']);

					foreach($tasklists as $tasklist_id)
					{
						$tasklist = $tasks->get_tasklist($tasklist_id);
						if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $tasklist['acl_id'])<GO_SECURITY::DELETE_PERMISSION)
						{
							throw new AccessDeniedException();
						}
						$tasks->delete_tasklist($tasklist_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';

			$query = isset($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';


			$auth_type = isset($_POST['auth_type']) ? $_POST['auth_type'] : 'read';

			$response['total'] = $tasks->get_authorized_tasklists($auth_type, $query, $GO_SECURITY->user_id, $start, $limit, $sort, $dir);
			if(!$response['total'])
			{
				$response['new_default_tasklist']= $tasks->get_tasklist();
				$response['total'] = $tasks->get_authorized_tasklists($auth_type, $query, $GO_SECURITY->user_id, $start, $limit, $sort, $dir);
			}
			$response['results']=array();
			while($tasklist = $tasks->next_record(DB_ASSOC))
			{
				$tasklist['dom_id']='tl-'.$tasks->f('id');
				$user = $GO_USERS->get_user($tasklist['user_id']);
				$tasklist['user_name']=String::format_name($user);
				$response['results'][] = $tasklist;
			}

			break;


		case 'tasks':

			$GO_LANGUAGE->require_language_file('tasks');

			$response['write_permission']=true;
			if(isset($_REQUEST['tasklist_id']))
			{
				$tasklist_id = $_REQUEST['tasklist_id'];
				$user_id=0;
				$tasklists = array($tasklist_id);


				$tasklist = $tasks->get_tasklist($tasklist_id);

				$response['data']['permission_level']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $tasklist['acl_id']);
				$response['data']['write_permission']=$response['data']['permission_level']>1;
				if(!$response['data']['permission_level'])
				{
					throw new AccessDeniedException();
				}
			}else
			{
				$user_id = $_REQUEST['user_id'];
				$tasklists = array();
				$tasklists_name = array();
				if(isset($_REQUEST['portlet']))
				{
					if($tasks->get_visible_tasklists($user_id) == 0){

						$tasklist = $tasks->get_default_tasklist($user_id);
						$vt['tasklist_id']=$tasklist['id'];
						$vt['user_id']=$user_id;
						$tasks->add_visible_tasklist($vt);

						$tasks->get_visible_tasklists($user_id);
					}
					while($tasks->next_record())
					{
						$cur_tasklist = $tasks2->get_tasklist($tasks->f('tasklist_id'));
						$tasklists[] = $tasks->f('tasklist_id');
						$tasklists_name[] = $cur_tasklist['name'];
					}

					$user_id = 0;
				}
			}

			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_tasks = json_decode($_POST['delete_keys']);

					foreach($delete_tasks as $task_id)
					{
						$old_task = $tasks->get_task($task_id);
						if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_task['acl_id'])<GO_SECURITY::DELETE_PERMISSION)
						{
							throw new AccessDeniedException();
						}
						$tasks->delete_task($task_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}


			if(isset($_POST['completed_task_id']))
			{
				$task=array();
				$task['id']=$_POST['completed_task_id'];

				$old_task = $tasks->get_task($task['id']);
				if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_task['acl_id'])<GO_SECURITY::WRITE_PERMISSION)
				{
					throw new AccessDeniedException();
				}

				if($_POST['checked']=='1')
				{
					$task['completion_time']=time();
					$task['status']='COMPLETED';

					//$tasks->copy_completed($task['id']);
				}else
				{
					$task['completion_time']=0;
					$task['status']='NEEDS-ACTION';
				}

				$tasks->update_task($task);
			}

			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'due_time ASC, ctime';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
                        $query = isset($_REQUEST['query']) ? '%'.$_REQUEST['query'].'%' : '';

			//$show_completed=isset($_POST['show_completed']) && $_POST['show_completed']=='true';
			//$show_inactive=isset($_POST['show_inactive']) && $_POST['show_inactive']=='true';

			if(isset($_POST['show_completed']))
			{
				$GO_CONFIG->save_setting('tasks_show_completed', $_POST['show_completed'], $GO_SECURITY->user_id);
			}
			if(isset($_POST['show_inactive']))
			{
				$GO_CONFIG->save_setting('tasks_show_inactive', $_POST['show_inactive'], $GO_SECURITY->user_id);
			}
			$show_completed=$GO_CONFIG->get_setting('tasks_show_completed', $GO_SECURITY->user_id);
			$show_inactive=$GO_CONFIG->get_setting('tasks_show_inactive', $GO_SECURITY->user_id);

			$response['total'] = $tasks->get_tasks($tasklists,$user_id, $show_completed, $sort, $dir, $start, $limit,$show_inactive, $query);
			$response['results']=array();

			

			if($GO_MODULES->has_module('customfields')) {
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
			}else
			{
				$cf=false;
			}

			while($task = $tasks->next_record(DB_ASSOC))
			{
				$tasks->format_task_record($task, $cf);

				$tl_id = array_search($task['tasklist_id'], $tasklists);
				$task['tasklist_name'] = (isset($tasklists_name) && $tl_id !== false)? $tasklists_name[$tl_id]: '';
				$response['results'][] = $task;
			}

			break;
		
		case 'settings':
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$query = isset($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';

			if($tasks->get_visible_tasklists($GO_SECURITY->user_id) == 0)
			{
				$visible_tls = array('0');
			}
			
			$visible_cals = array();
			while($tasks->next_record())
			{
				$visible_tls[] = $tasks->f('tasklist_id');
			}

			$response['total'] = $tasks->get_authorized_tasklists('read', $query, $GO_SECURITY->user_id, $start, $limit, $sort, $dir);

			$response['results']=array();

			while($tasks->next_record())
			{
				$tasklists['tasklist_id'] = $tasks->f('id');
				$tasklists['name'] = $tasks->f('name');
				$tasklists['visible'] = (in_array($tasks->f('id'), $visible_tls));
				$response['results'][] = $tasklists;
			}
			break;
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
