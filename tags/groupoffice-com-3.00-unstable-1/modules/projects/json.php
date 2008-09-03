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

$GO_SECURITY->json_authenticate('projects');



require_once ($GO_MODULES->modules['projects']['class_path']."projects.class.inc.php");
$projects = new projects();


$task=isset($_REQUEST['task']) ? smart_addslashes($_REQUEST['task']) : '';

try{

	switch($task)
	{
		case 'report':
			
			$sort = isset($_REQUEST['sort']) ? smart_addslashes($_REQUEST['sort']) : 'units';
			$dir = isset($_REQUEST['dir']) ? smart_addslashes($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? smart_addslashes($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? smart_addslashes($_REQUEST['limit']) : '0';
			
			$start_date = !empty($_POST['start_date']) ? Date::to_unixtime($_POST['start_date']) : 0;
			$end_date = !empty($_POST['end_date']) ? Date::date_add(Date::to_unixtime($_POST['end_date']),1) : 0;
			
			$group_by = isset($_REQUEST['group_by']) ? smart_addslashes($_REQUEST['group_by']) : 'project_id'; 
			
			
			$projects->get_report($group_by, $GO_SECURITY->user_id,  $start_date, $end_date, $sort, $dir, $start, $limit);
			
			
			$response['results']=array();
			while($projects->next_record())
			{
				$record = $projects->Record;
				
				switch($group_by)
				{
					case 'user_id':
						
						$user = $GO_USERS->get_user($record['user_id']);						
						$record['name']=String::format_name($user);						
						break;
					
					case 'project_id':						
						
						$record['name']=$record['project_name'];						
						break;
					
					case 'customer':
						$record['name']=$record['customer'];
						break;
					
				}
				
				$record['days']=Number::format($record['days'],0);
				$record['units']=Number::format($record['units']);
				$record['int_fee_value']=Number::format($record['int_fee_value']);
				$record['ext_fee_value']=Number::format($record['ext_fee_value']);
				
				$response['results'][]=$record;
			}
			
			if($limit>0)
			{
			
			}
			
			
			break;
		
		case 'fee':

			if(!$GO_MODULES->modules['projects']['write_permission'])
			{
				throw AccessDeniedException();
			}
			
			$fee = $projects->get_fee(smart_addslashes($_REQUEST['fee_id']));

			$fee['external_value']=Number::format($fee['external_value']);
			$fee['internal_value']=Number::format($fee['internal_value']);
			$fee['time']=Number::format($fee['time']);				
			
			$response['data']=$fee;
			$response['success']=true;

			break;
		
		case 'fees':
			
			
			$response['total']=$projects->get_authorized_fees($GO_SECURITY->user_id);
			$response['results']=array();
			while($projects->next_record())
			{
				$response['results'][] = array('id'=>$projects->f('id'), 'name'=>$projects->f('name'));
			}
			break;
			
		case 'writable_fees':
			
			if(!$GO_MODULES->modules['projects']['write_permission'])
			{
				throw AccessDeniedException();
			}
			
			
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_fees = json_decode(smart_stripslashes($_POST['delete_keys']));

					foreach($delete_fees as $fee_id)
					{
						$projects->delete_fee(addslashes($fee_id));
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			
			
			
			
			$response['total'] = $projects->get_fees();
			
			while($projects->next_record())
			{
				$fee = $projects->Record;
				$fee['external_value']=Number::format($fee['external_value']);
				$fee['internal_value']=Number::format($fee['internal_value']);
				$fee['time']=Number::format($fee['time']);
				$response['results'][] = $fee;
			}
			
			
			break;

		case 'hours_entry':

			$hours = $projects->get_hours(smart_addslashes($_REQUEST['hours_id']));
			$project = $projects->get_project($hours['project_id']);

			$hours['date']=date($_SESSION['GO_SESSION']['date_format'], $hours['date']);
			$hours['project_name']=$project['name'];
			$response['data']=$hours;
				

			$response['data']['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $project['acl_write']);
			if(!$response['data']['write_permission'] && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $project['acl_read']))
			{
				throw new AccessDeniedException();
			}
			$response['success']=true;

			break;


		case 'hours':

			$start_time = isset($_POST['start_date']) ? Date::to_unixtime($_POST['start_date']) : 0;
			$end_time = isset($_POST['end_date']) ? Date::date_add(Date::to_unixtime($_POST['end_date']),1) : 0;
			
			$project_id = isset($_POST['project_id']) ? smart_addslashes($_POST['project_id']) : 0;
			$user_id = isset($_POST['user_id']) ? smart_addslashes($_POST['user_id']) : 0;
			
			
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_hours = json_decode(smart_stripslashes($_POST['delete_keys']));

					foreach($delete_hours as $hours_id)
					{
						$projects->delete_hours(addslashes($hours_id));
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			

			$sort = isset($_REQUEST['sort']) ? smart_addslashes($_REQUEST['sort']) : 'date';
			$dir = isset($_REQUEST['dir']) ? smart_addslashes($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? smart_addslashes($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? smart_addslashes($_REQUEST['limit']) : '0';


			$response['total'] = $projects->select_hours($start_time,$end_time,$user_id,$project_id, $start, $limit, $sort, $dir);

			$response['results']=array();
			while($projects->next_record(MYSQL_ASSOC))
			{
				$booking = $projects->Record;

				unset($booking['int_fee_value'], $booking['ext_fee_value']);

				$booking['date']=date($_SESSION['GO_SESSION']['date_format'], $projects->f('date'));
				$booking['units']=Number::format($booking['units']);
				$user = $GO_USERS->get_user($booking['user_id']);
				$booking['user_name']=String::format_name($user);
				$response['results'][] = $booking;
			}

			break;

		case 'project_with_items':
		case 'project':

			$project = $projects->get_project(smart_addslashes($_REQUEST['project_id']));
				
			if(isset($GO_MODULES->modules['addressbook']))
			{
				//require($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
				//$ab = new addressbook();


			}

			$response['data']=$project;

			$response['data']['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $project['acl_write']);
			if(!$response['data']['write_permission'] && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $project['acl_read']))
			{
				throw new AccessDeniedException();
			}

			if(isset($GO_MODULES->modules['files']))
			{
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc');
				$fs = new files();

				$response['data']['files_path']='projects/'.$response['data']['id'];

				$full_path = $GO_CONFIG->file_storage_path.$response['data']['files_path'];
				if(!file_exists($full_path))
				{
					$fs->mkdir_recursive($full_path);

					if(!$fs->get_folder(addslashes($full_path)))
					{
						$folder['user_id']=$response['data']['user_id'];
						$folder['path']=addslashes($full_path);
						$folder['visible']='0';
						$folder['acl_read']=$project['acl_read'];
						$folder['acl_write']=$project['acl_write'];

						$fs->add_folder($folder);
					}
				}
			}
			$response['success']=true;
				
			if($task=='project')
			{
				if(isset($GO_MODULES->modules['customfields']))
				{
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$values = $cf->get_values($GO_SECURITY->user_id, 5, $response['data']['id']);				
					$response['data']=array_merge($response['data'], $values);			
				}
				break;
			}else
			{
					
				if(isset($GO_MODULES->modules['customfields']))
				{
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$response['data']['customfields']=
						$cf->get_all_fields_with_values(
							$GO_SECURITY->user_id, 5, $response['data']['id']);			
				}

				
				require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
				$search = new search();
			
				$links_json = $search->get_latest_links_json($GO_SECURITY->user_id, $response['data']['id'], 5);				
				$response['data']['links']=$links_json['results'];				

				$response['data']['milestones']=array();
				$count = $projects->get_milestones($project['id']);
				if($count)
				{	
					while($projects->next_record(MYSQL_ASSOC))
					{
						 $milestone = $projects->Record;
						 $milestone['completed']=$milestone['completion_time']>0;
						 $milestone['completion_time']=$milestone['completed']? date($_SESSION['GO_SESSION']['date_format'], $milestone['completion_time']) : '-';
						 $milestone['due_time']=date($_SESSION['GO_SESSION']['date_format'], $milestone['due_time']);
						 $milestone['late']=(!$milestone['completed'] && $milestone['due_time']< time());

						 $user = $GO_USERS->get_user($milestone['user_id']);
						 $milestone['user_name']=String::format_name($user);
						 
						 $response['data']['milestones'][]=$milestone;
					}
				}
				
				if(isset($GO_MODULES->modules['files']))
				{
					$response['data']['files']=$fs->get_content_json($full_path);
				}else
				{
					$response['data']['files']=array();				
				}

				break;
			}


		case 'projects':

			$auth_type = isset($_POST['auth_type']) ? smart_addslashes($_POST['auth_type']) : 'write';

			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_projects = json_decode(smart_stripslashes($_POST['delete_keys']));

					foreach($delete_projects as $project_id)
					{
						$projects->delete_project(addslashes($project_id));
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}


			$sort = isset($_REQUEST['sort']) ? smart_addslashes($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? smart_addslashes($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? smart_addslashes($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? smart_addslashes($_REQUEST['limit']) : '0';

			$show_completed=isset($_POST['show_completed']) && $_POST['show_completed']=='true';

			$response['total'] = $projects->get_authorized_projects($auth_type, $GO_SECURITY->user_id, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($projects->next_record(MYSQL_ASSOC))
			{
				$project = $projects->Record;
				$response['results'][] = $project;
			}

			break;
				
		case 'milestone':

			$milestone = $projects->get_milestone(smart_addslashes($_REQUEST['milestone_id']));
			$project = $projects->get_project($milestone['project_id']);

			$milestone['due_time']=date($_SESSION['GO_SESSION']['date_format'], $milestone['due_time']);
				
			$user = $GO_USERS->get_user($milestone['user_id']);
			$milestone['user_name']=String::format_name($user);
				
			$response['data']=$milestone;
			$response['data']['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $project['acl_write']);
			if(!$response['data']['write_permission'] && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $project['acl_read']))
			{
				throw new AccessDeniedException();
			}
			$response['success']=true;

			break;

				
		case 'milestones':

			$project_id=smart_addslashes($_POST['project_id']);

			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_milestones = json_decode(smart_stripslashes($_POST['delete_keys']));

					foreach($delete_milestones as $milestone_id)
					{
						$projects->delete_milestone(addslashes($milestone_id));
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
				
			if(isset($_POST['completed_milestone_id']))
			{
				$milestone['id']=smart_addslashes($_POST['completed_milestone_id']);
				$milestone['completion_time']=time();

				$projects->update_milestone($milestone);

			}


			$sort = isset($_REQUEST['sort']) ? smart_addslashes($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? smart_addslashes($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? smart_addslashes($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? smart_addslashes($_REQUEST['limit']) : '0';

			$response['total'] = $projects->get_milestones($project_id, $start, $limit, $sort, $dir);
			$response['results']=array();
			while($projects->next_record(MYSQL_ASSOC))
			{
				$milestone = $projects->Record;
				$milestone['completed']=$milestone['completion_time']>0;
				$milestone['completion_time']=$milestone['completed']? date($_SESSION['GO_SESSION']['date_format'], $milestone['completion_time']) : '-';
				$milestone['late']=(!$milestone['completed'] && $milestone['due_time']< time());
				$milestone['due_time']=date($_SESSION['GO_SESSION']['date_format'], $milestone['due_time']);
				$user = $GO_USERS->get_user($milestone['user_id']);
				$milestone['user_name']=String::format_name($user);

				$response['results'][] = $milestone;
			}

			break;
				
				
		case 'summaryGroupingView':

			//$project_id=smart_addslashes($_POST['project_id']);

			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_milestones = json_decode(smart_stripslashes($_POST['delete_keys']));

					foreach($delete_milestones as $milestone_id)
					{
						$projects->delete_milestone(addslashes($milestone_id));
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
				
			if(isset($_POST['completed_milestone_id']))
			{
				$milestone['id']=smart_addslashes($_POST['completed_milestone_id']);
				$milestone['completion_time']=time();

				$projects->update_milestone($milestone);

			}


			$sort = isset($_REQUEST['sort']) ? smart_addslashes($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? smart_addslashes($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? smart_addslashes($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? smart_addslashes($_REQUEST['limit']) : '0';

			$response['total'] = $projects->get_active_milestones($GO_SECURITY->user_id,$sort, $dir);
			$response['results']=array();
			while($projects->next_record(MYSQL_ASSOC))
			{
				$milestone = $projects->Record;
				$milestone['completed']=$milestone['completion_time']>0;
				$milestone['completion_time']=$milestone['completed']? date($_SESSION['GO_SESSION']['date_format'], $milestone['completion_time']) : '-';
				$milestone['due_time']=date($_SESSION['GO_SESSION']['date_format'], $milestone['due_time']);
				$milestone['late']=(!$milestone['completed'] && $milestone['due_time']< time());
				$user = $GO_USERS->get_user($milestone['user_id']);
				$milestone['user_name']=String::format_name($user);

				$milestone['project_name'] = '[#'.$milestone['project_id'].'] '.$milestone['project_name'];
				if(!empty($milestone['customer']))
				{
					$milestone['project_name'] .' ('.$milestone['customer'].')';
				}

				$response['results'][] = $milestone;
			}

			break;
				
				
		case 'summary':

			//$project_id=smart_addslashes($_POST['project_id']);

			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_milestones = json_decode(smart_stripslashes($_POST['delete_keys']));

					foreach($delete_milestones as $milestone_id)
					{
						$projects->delete_milestone(addslashes($milestone_id));
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
				
			if(isset($_POST['completed_milestone_id']))
			{
				$milestone['id']=smart_addslashes($_POST['completed_milestone_id']);
				$milestone['completion_time']=time();

				$projects->update_milestone($milestone);

			}


			$sort = isset($_REQUEST['sort']) ? smart_addslashes($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? smart_addslashes($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? smart_addslashes($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? smart_addslashes($_REQUEST['limit']) : '0';

			$projects2 = new projects();
				
			$response['total'] = $projects->get_authorized_projects('read',$GO_SECURITY->user_id);
			$response['results']=array();
			while($projects->next_record(MYSQL_ASSOC))
			{
				$project_name = '[#'.$projects->f('id').'] '.$projects->f('name');

				$project = $projects->Record;
				$project['name'] = '[#'.$project['id'].'] '.$project['name'];

				if(!empty($project['description']))
				{
					$project['description']='<p class="project-row-description">'.nl2br($project['description']).'</p>';
				}

				$count = $projects2->get_milestones($projects->f('id'));
				if($count)
				{
					$project['description'] .= '<table class="projects-items">';
						
					while($projects2->next_record(MYSQL_ASSOC))
					{
						/*$milestone = $projects2->Record;
						 $milestone['completed']=$milestone['completion_time']>0;
						 $milestone['completion_time']=$milestone['completed']? date($_SESSION['GO_SESSION']['date_format'], $milestone['completion_time']) : '-';
						 $milestone['due_time']=date($_SESSION['GO_SESSION']['date_format'], $milestone['due_time']);
						 $milestone['late']=(!$milestone['completed'] && $milestone['due_time']< time());

						 $milestone['user_name']=String::format_name($user);*/

						
						$class = '';
						if($projects2->f('due_time') < time())
						{
							$class='projects-late ';
						}
						
						if($projects2->f('completion_time')>0)
						{
							$class .= 'projects-completed';
						}


						$user = $GO_USERS->get_user($projects2->f('user_id'));
						$project['description'] .= '<tr id="pm-sum-milestone-'.$projects2->f('id').'" class="'.$class.'"><td><div class="projects-milestone">'.$projects2->f('name').'</div></td>'.
							'<td>'.String::format_name($user).'</td>'.
							'<td>'.date($_SESSION['GO_SESSION']['date_format'], $projects2->f('due_time')).'</td></tr>';
						
						

							
					}
					
					
					
						
					$project['description'] .= '</table>';
				}

				$response['results'][] = $project;
			}

			break;
				
			/*

			case 'writable_projects':


			if(isset($_POST['delete_keys']))
			{
			try{
			$response['deleteSuccess']=true;
			$delete_projects = json_decode(smart_stripslashes($_POST['delete_keys']));

			foreach($delete_projects as $project_id)
			{
			$projects->delete_project(addslashes($project_id));
			}
			}catch(Exception $e)
			{
			$response['deleteSuccess']=false;
			$response['deleteFeedback']=$e->getMessage();
			}
			}


			$sort = isset($_REQUEST['sort']) ? smart_addslashes($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? smart_addslashes($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? smart_addslashes($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? smart_addslashes($_REQUEST['limit']) : '0';

			//$show_completed=isset($_POST['show_completed']) && $_POST['show_completed']=='true';

			$response['total'] = $projects->get_writable_projects($GO_SECURITY->user_id, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($projects->next_record(MYSQL_ASSOC))
			{
			$project = $projects->Record;
			$response['results'][] = $project;
			}

			break;*/
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
