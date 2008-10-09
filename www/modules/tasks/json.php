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


$task=isset($_REQUEST['task']) ? smart_addslashes($_REQUEST['task']) : '';

try{

	switch($task)
	{
		case 'task_with_items':
		case 'task':
			
			require($GO_CONFIG->class_path.'ical2array.class.inc');
			require($GO_CONFIG->class_path.'Date.class.inc.php');
			require_once($GO_LANGUAGE->get_language_file('tasks'));
			
			$task = $tasks->get_task(smart_addslashes($_REQUEST['task_id']));
			$tasklist = $tasks->get_tasklist($task['tasklist_id']);
				
			$response['data']=$task;
				
			$response['data']['tasklist_name']=$tasklist['name'];
			
			$response['data']['status_text']=isset($lang['tasks']['statuses'][$task['status']]) ? addslashes($lang['tasks']['statuses'][$task['status']]) : $lang['tasks']['statuses']['NEEDS-ACTION'];
				
			$response['data']['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $tasklist['acl_write']);
			if(!$response['data']['write_permission'] && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $tasklist['acl_read']))
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
				
			if($GO_MODULES->modules['files'])
			{
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc');
				$fs = new files();

				$response['data']['files_path']='tasks/'.$response['data']['id'];

				$full_path = $GO_CONFIG->file_storage_path.$response['data']['files_path'];
				if(!file_exists($full_path))
				{
					$fs->mkdir_recursive($full_path);
						
					$folder['user_id']=$response['data']['user_id'];
					$folder['path']=addslashes($full_path);
					$folder['visible']='0';
					$folder['acl_read']=$tasklist['acl_read'];
					$folder['acl_write']=$tasklist['acl_write'];
						
					$fs->add_folder($folder);
				}
			}
			
			if($task!='task')
			{
				$response['data']['description']=String::text_to_html($response['data']['description']);

				require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
				$search = new search();
			
				$links_json = $search->get_latest_links_json($GO_SECURITY->user_id, $response['data']['id'], 12);				
				$response['data']['links']=$links_json['results'];
				
				if(isset($GO_MODULES->modules['files']))
				{
					$response['data']['files']=$fs->get_content_json($full_path);
				}else
				{
					$response['data']['files']=array();				
				}
				
				if(isset($GO_MODULES->modules['comments']))
				{
					require_once ($GO_MODULES->modules['comments']['class_path'].'comments.class.inc.php');
					$comments = new comments();
					
					$response['data']['comments']=$comments->get_comments_json($response['data']['id'], 12);
				}
			}
				
			$response['success']=true;
			break;



									case 'tasklist':

										$response['data']=$tasks->get_tasklist(smart_addslashes($_POST['tasklist_id']));
										$user = $GO_USERS->get_user($response['data']['user_id']);
										$response['data']['user_name']=String::format_name($user);
										$response['success']=true;
										break;


											
									case 'tasklists':
											
										if(isset($_POST['delete_keys']))
										{
											try{
												$response['deleteSuccess']=true;
												$tasklists = json_decode(smart_stripslashes($_POST['delete_keys']));

												foreach($tasklists as $tasklist_id)
												{
													$tasklist = $tasks->get_tasklist($tasklist_id);
													if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $tasklist['acl_write']))
													{
														throw new AccessDeniedException();
													}
													$tasks->delete_tasklist(addslashes($tasklist_id));
												}
											}catch(Exception $e)
											{
												$response['deleteSuccess']=false;
												$response['deleteFeedback']=$e->getMessage();
											}
										}
											
										$sort = isset($_REQUEST['sort']) ? smart_addslashes($_REQUEST['sort']) : 'name';
										$dir = isset($_REQUEST['dir']) ? smart_addslashes($_REQUEST['dir']) : 'ASC';
										$start = isset($_REQUEST['start']) ? smart_addslashes($_REQUEST['start']) : '0';
										$limit = isset($_REQUEST['limit']) ? smart_addslashes($_REQUEST['limit']) : '0';
											
										$query = isset($_REQUEST['query']) ? '%'.smart_addslashes($_REQUEST['query']).'%' : '';
											
											
										$auth_type = isset($_POST['auth_type']) ? $_POST['auth_type'] : 'read';
											
										$response['total'] = $tasks->get_authorized_tasklists($auth_type, $query, $GO_SECURITY->user_id, $start, $limit, $sort, $dir);
										if(!$response['total'])
										{
											$tasks->get_tasklist();
											$response['total'] = $tasks->get_authorized_tasklists($auth_type, $query, $GO_SECURITY->user_id, $start, $limit, $sort, $dir);
										}
										$response['results']=array();
										while($tasks->next_record(MYSQL_ASSOC))
										{
											$tasklist = $tasks->Record;
											$tasklist['dom_id']='tl-'.$tasks->f('id');
											$response['results'][] = $tasklist;
										}

										break;

											
									case 'tasks':
											
											
										if(isset($_REQUEST['tasklist_id']))
										{
											$tasklist_id = smart_addslashes($_REQUEST['tasklist_id']);
											$user_id=0;
											$tasklists = array($tasklist_id);


											$tasklist = $tasks->get_tasklist($tasklist_id);

											if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $tasklist['acl_read']))
											{
												throw new AccessDeniedException();
											}
												

											if(isset($_POST['delete_keys']) && $GO_SECURITY->has_permission($GO_SECURITY->user_id, $tasklist['acl_write']))
											{
												try{
													$response['deleteSuccess']=true;
													$delete_tasks = json_decode(smart_stripslashes($_POST['delete_keys']));

													foreach($delete_tasks as $task_id)
													{
														$tasks->delete_task(addslashes($task_id));
													}
												}catch(Exception $e)
												{
													$response['deleteSuccess']=false;
													$response['deleteFeedback']=$e->getMessage();
												}
											}
										}else
										{
											$user_id = smart_addslashes($_REQUEST['user_id']);
											$tasklists = array();
										}
											
											
										if(isset($_POST['completed_task_id']))
										{
											$task=array();
											$task['id']=smart_addslashes($_POST['completed_task_id']);

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
											
											
											
										$sort = isset($_REQUEST['sort']) ? smart_addslashes($_REQUEST['sort']) : 'due_time ASC, ctime';
										$dir = isset($_REQUEST['dir']) ? smart_addslashes($_REQUEST['dir']) : 'ASC';
										$start = isset($_REQUEST['start']) ? smart_addslashes($_REQUEST['start']) : '0';
										$limit = isset($_REQUEST['limit']) ? smart_addslashes($_REQUEST['limit']) : '0';
											
										$show_completed=isset($_POST['show_completed']) && $_POST['show_completed']=='true';
										$show_inactive=isset($_POST['show_inactive']) && $_POST['show_inactive']=='true';
											
										$response['total'] = $tasks->get_tasks($tasklists,$user_id, $show_completed, $sort, $dir, $start, $limit,$show_inactive);
										$response['results']=array();
										
										$now=time();
										
										while($tasks->next_record(MYSQL_ASSOC))
										{
											$task = $tasks->Record;
											$task['completed']=$tasks->f('completion_time')>0;
											$task['late']=!$task['completed'] && $task['due_time']<$now;	
											$task['due_time']=date($_SESSION['GO_SESSION']['date_format'], $tasks->f('due_time'));
											
											$response['results'][] = $task;
										}

										break;
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
