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

$GLOBALS['GO_SECURITY']->json_authenticate('tasks');



require_once ($GLOBALS['GO_MODULES']->modules['tasks']['class_path']."tasks.class.inc.php");
$tasks = new tasks();
$tasks2 = new tasks();

$_task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';

try {

	switch($_task) {

		
		case 'task_with_items':
		case 'task':

			require($GLOBALS['GO_CONFIG']->class_path.'ical2array.class.inc');
			require($GLOBALS['GO_CONFIG']->class_path.'Date.class.inc.php');
			require_once($GLOBALS['GO_LANGUAGE']->get_language_file('tasks'));

			$task = $tasks->get_task(($_REQUEST['task_id']));
			$tasklist = $tasks->get_tasklist($task['tasklist_id']);

			$response['data']=$task;

			$response['data']['tasklist_name']=$tasklist['name'];

			$response['data']['status_text']=isset($lang['tasks']['statuses'][$task['status']]) ? $lang['tasks']['statuses'][$task['status']] : $lang['tasks']['statuses']['NEEDS-ACTION'];

			$response['data']['permission_level']=$GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $tasklist['acl_id']);
			$response['data']['write_permission']=$response['data']['permission_level']>1;
			if(!$response['data']['permission_level']) {
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

			$ical2array = new ical2array();
			if (!empty($response['data']['rrule']) && $rrule = $ical2array->parse_rrule($response['data']['rrule']))
			{
				if(isset($rrule['FREQ']))
				{
					if (isset($rrule['UNTIL']))
					{
						$response['data']['repeat_end_time'] = $ical2array->parse_date($rrule['UNTIL']);
					}elseif(isset($rrule['COUNT'])) {
						//go doesn't support this
					}else {
						$response['data']['repeat_forever'] = 1;
					}

					$response['data']['repeat_every'] = $rrule['INTERVAL'];
					switch($rrule['FREQ']) {
						case 'DAILY':
							$response['data']['repeat_type'] = REPEAT_DAILY;
							break;

						case 'WEEKLY':
							$response['data']['repeat_type'] = REPEAT_WEEKLY;

				
					
							$days = Date::byday_to_days($rrule['BYDAY']);
							$days = Date::shift_days_to_local($days, date('G', $task['due_time']), Date::get_timezone_offset($task['due_time']));
							

							$response['data']['repeat_days_0'] = $days['sun'];
							$response['data']['repeat_days_1'] = $days['mon'];
							$response['data']['repeat_days_2'] = $days['tue'];
							$response['data']['repeat_days_3'] = $days['wed'];
							$response['data']['repeat_days_4'] = $days['thu'];
							$response['data']['repeat_days_5'] = $days['fri'];
							$response['data']['repeat_days_6'] = $days['sat'];


							break;

						case 'MONTHLY':
							if (isset($rrule['BYDAY'])) {
								$response['data']['repeat_type'] = REPEAT_MONTH_DAY;

								$response['data']['month_time'] = $rrule['BYDAY'][0];
								$day = substr($rrule['BYDAY'], 1);

								switch($day) {
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
							}else {
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

			if($response['data']['category_id']==0){
				$response['data']['category_id']="";
			}else
			{
				$category = $tasks->get_category($response['data']['category_id']);
				$response['data']['category_name']=$category['name'];
			}

			$response['data']['remind']=$response['data']['reminder']>0;

			if($response['data']['remind']) {
				$response['data']['remind_date']=date($_SESSION['GO_SESSION']['date_format'], $response['data']['reminder']);
				$response['data']['remind_time']=date($_SESSION['GO_SESSION']['time_format'], $response['data']['reminder']);
			}else {
				$response['data']['remind_date']=date($_SESSION['GO_SESSION']['date_format'], $response['data']['start_time']);
				$response['data']['remind_time']=date($_SESSION['GO_SESSION']['time_format'], 28800);
			}

			if($_task!='task') {

				require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
				$GO_USERS = new GO_USERS();

				$response['data']['user_name']=$GO_USERS->get_user_realname($task['user_id']);
				$response['data']['description']=String::text_to_html($response['data']['description']);

				load_standard_info_panel_items($response, 12);
			}else
			{
				if(isset($GLOBALS['GO_MODULES']->modules['customfields'])) {
					require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$values = $cf->get_values($GLOBALS['GO_SECURITY']->user_id, 12, $response['data']['id']);
					$response['data']=array_merge($response['data'], $values);
				}
			}

			$response['success']=true;
			break;



		case 'tasklist':
			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$response['data']=$tasks->get_tasklist(($_POST['tasklist_id']));
			$response['data']['user_name']=$GO_USERS->get_user_realname($response['data']['user_id']);
			$response['success']=true;
			break;

		case 'init':

			$_REQUEST['limit']=$GLOBALS['GO_CONFIG']->nav_page_size;

			$categories = $GLOBALS['GO_CONFIG']->get_setting('tasks_categories_filter', $GLOBALS['GO_SECURITY']->user_id);
			$categories = ($categories) ? explode(',',$categories) : array();

			$response['categories']['results'] = array();
			$response['categories']['total'] = $tasks->get_categories();

			while($category = $tasks->next_record())
			{
				$category['checked'] = in_array($category['id'], $categories);

				$response['categories']['results'][] = $category;
			}

			$tasks->get_tasklists_json($response['tasklists'],'read','',0,$GLOBALS['GO_CONFIG']->nav_page_size,'name','ASC');
		break;

		case 'tasklists':

			if(isset($_POST['delete_keys']))
			{
				try
				{
					$response['deleteSuccess']=true;
					$tasklists = json_decode($_POST['delete_keys']);
					foreach($tasklists as $tasklist_id)
					{
						$tasklist = $tasks->get_tasklist($tasklist_id);
						if($GLOBALS['GO_MODULES']->modules['tasks']['permission_level'] < GO_SECURITY::WRITE_PERMISSION || $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $tasklist['acl_id']) < GO_SECURITY::DELETE_PERMISSION)
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

			$query = !empty($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';


			$auth_type = isset($_POST['auth_type']) ? $_POST['auth_type'] : 'read';

			$tasks->get_tasklists_json($response, $auth_type, $query, $start, $limit, $sort, $dir);

			break;


		case 'tasks':

			$GLOBALS['GO_LANGUAGE']->require_language_file('tasks');
			$readable_tasklists = array();
			if(isset($_REQUEST['portlet'])) {
				$user_id = $GLOBALS['GO_SECURITY']->user_id;
				$response['data']['write_permission']=true;

				$show_categories = array();

				$tasklist_names = array();

				if($tasks->get_visible_tasklists($user_id) == 0) {

					$tasklist = $tasks->get_default_tasklist($user_id);
					$vt['tasklist_id']=$tasklist['id'];
					$vt['user_id']=$user_id;
					$tasks->add_visible_tasklist($vt);

					$tasks->get_visible_tasklists($user_id);
				}
				while($tasks->next_record()) {
					$cur_tasklist = $tasks2->get_tasklist($tasks->f('tasklist_id'));
					$readable_tasklists[] = $tasks->f('tasklist_id');
					$tasklist_names[] = $cur_tasklist['name'];
				}

				$user_id = 0;
			}else {
				$response['data']['write_permission'] = false;
				/*if(isset($_POST['tasklists'])) {
					$tasklists = json_decode($_POST['tasklists'], true);
					$GLOBALS['GO_CONFIG']->save_setting('tasks_tasklists_filter',implode(',', $tasklists), $GLOBALS['GO_SECURITY']->user_id);
				}else {
					$tasklists = $GLOBALS['GO_CONFIG']->get_setting('tasks_tasklists_filter', $GLOBALS['GO_SECURITY']->user_id);
					$tasklists = ($tasklists) ? explode(',',$tasklists) : array();
				}*/

				$tasklists=get_multiselectgrid_selections('tasklists');

				if(!count($tasklists)) {
					$tasklist = $tasks->get_default_tasklist($GLOBALS['GO_SECURITY']->user_id);
					$tasklists[] = $tasklist['id'];
				}

				$user_id = 0;
				$readable_tasklists = array();
				$writable_tasklists = array();
				$response['data']['permission_level'] = $permission_level = 0;
				$tasklist_names = array();
				foreach($tasklists as $tasklist_id)
				{
					$tasklist = $tasks->get_tasklist($tasklist_id);

					$permission_level = $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $tasklist['acl_id']);
					if($permission_level) {
						$readable_tasklists[] = $tasklist_id;
						$tasklist_names[] = $tasklist['name'];
					}
					if($permission_level >= GO_SECURITY::DELETE_PERMISSION) {
						$writable_tasklists[] = $tasklist_id;
					}

					if($permission_level > $response['data']['permission_level']) {
						$response['data']['permission_level'] = $permission_level;
					}
				}

				if(count($tasklist_names))
				{
					//$response['grid_title'] = (count($tasklist_names) > 1) ? $lang['tasks']['multipleSelected'] : $tasklist_names[0];
					//$response['grid_title'] = implode(' & ', $tasklist_names);
				}

				$response['data']['write_permission']=$response['data']['permission_level']>1;
				if(!$response['data']['permission_level']) {
					throw new AccessDeniedException();
				}
				/*}else {
					$user_id = $GLOBALS['GO_SECURITY']->user_id;
				}*/

				if(isset($_POST['delete_keys'])) {
					try {
						$delete_tasks = json_decode($_POST['delete_keys']);
						$tasks_deleted = array();
						foreach($delete_tasks as $task_id) {
							$task = $tasks->get_task($task_id);
							if(in_array($task['tasklist_id'], $writable_tasklists)) {
								$tasks->delete_task($task_id);
								$tasks_deleted[] = $task_id;
							}
						}
						if(!count($tasks_deleted)) {
							throw new AccessDeniedException();
						}
						if(count($delete_tasks) != count($tasks_deleted)) {
							require_once($GLOBALS['GO_LANGUAGE']->get_language_file('tasks'));
							$response['feedback'] = $lang['tasks']['incomplete_delete'];
						}
						$response['deleteSuccess']=true;

					}catch(Exception $e) {
						$response['deleteSuccess']=false;
						$response['deleteFeedback']=$e->getMessage();
					}
				}

				if(isset($_POST['categories'])) {
					$show_categories = json_decode($_POST['categories'], true);
					$GLOBALS['GO_CONFIG']->save_setting('tasks_categories_filter',implode(',', $show_categories), $GLOBALS['GO_SECURITY']->user_id);
				}else {
					$show_categories = $GLOBALS['GO_CONFIG']->get_setting('tasks_categories_filter', $GLOBALS['GO_SECURITY']->user_id);
					$show_categories = ($show_categories) ? explode(',',$show_categories) : array();
				}
			}

			if(isset($_POST['completed_task_id'])) {
				$task=array();
				$task['id']=$_POST['completed_task_id'];

				$old_task = $tasks->get_task($task['id']);
				if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $old_task['acl_id'])<GO_SECURITY::WRITE_PERMISSION) {
					throw new AccessDeniedException();
				}

				if($_POST['checked']=='1') {
					$task['completion_time']=time();
					$task['status']='COMPLETED';

					//$tasks->copy_completed($task['id']);
				}else {
					$task['completion_time']=0;
					$task['status']='NEEDS-ACTION';
				}

				$tasks->update_task($task, false, $old_task);
			}

			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$query = !empty($_REQUEST['query']) ? '%'.$_REQUEST['query'].'%' : '';
			$groupBy = isset($_REQUEST['groupBy']) ? $_REQUEST['groupBy'] : '';
			$groupDir = isset($_REQUEST['groupDir']) ? $_REQUEST['groupDir'] : '';
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'due_time ASC, ctime';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';

			if($sort == 'tasklist_name') $sort = 'l.name';
			elseif($sort == 'category_name') $sort = 'c.name';

			if($groupBy && $groupDir) {
				if($groupBy == 'tasklist_name') $groupBy = 'l.name';
				elseif($groupBy == 'category_name') $groupBy = 'c.name';

				$sort = $groupBy.' '.$groupDir.', '.$sort;
			}

			//$show_completed=isset($_POST['show_completed']) && $_POST['show_completed']=='true';
			//$show_inactive=isset($_POST['show_inactive']) && $_POST['show_inactive']=='true';

			if(isset($_POST['show'])) {
				$GLOBALS['GO_CONFIG']->save_setting('tasks_filter', $_POST['show'], $GLOBALS['GO_SECURITY']->user_id);
			}
			if(empty($_POST['portlet'])){
				$show=$GLOBALS['GO_CONFIG']->get_setting('tasks_filter', $GLOBALS['GO_SECURITY']->user_id);
			}else
			{
				$show='portlet';
			}
			
			$response['total'] = $tasks->get_tasks($readable_tasklists,$user_id, null, $sort, $dir, $start, $limit,null, $query,'', $show_categories,0,0,$show);
			$response['results']=array();


			if($GLOBALS['GO_MODULES']->has_module('customfields')) {
				require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
			}else {
				$cf=false;
			}

			$categories = array();
			$categories_name = array();
			$tasks2->get_categories();
			while($tasks2->next_record()) {
				$categories[] = $tasks2->f('id');
				$categories_name[] = $tasks2->f('name');
			}

			while($task = $tasks->next_record(DB_ASSOC)) {
				

				$tl_id = array_search($task['tasklist_id'], $readable_tasklists);
				$task['tasklist_name'] = (isset($tasklist_names) && $tl_id !== false)? $tasklist_names[$tl_id]: '';

				$cat_index = array_search($task['category_id'], $categories);
				$task['category_name'] = ($cat_index !== false) ? $categories_name[$cat_index] : '';

				$tasks->format_task_record($task, $cf);

				//for disabling checkbox column
				$task['disabled']=!$response['data']['write_permission'];

				$response['results'][] = $task;
			}


			break;

		case 'settings':
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$query = !empty($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';

			if($tasks->get_visible_tasklists($GLOBALS['GO_SECURITY']->user_id) == 0) {
				$visible_tls = array('0');
			}

			$visible_cals = array();
			while($tasks->next_record()) {
				$visible_tls[] = $tasks->f('tasklist_id');
			}

			$response['total'] = $tasks->get_authorized_tasklists('read', $query, $GLOBALS['GO_SECURITY']->user_id, $start, $limit, $sort, $dir);

			$response['results']=array();

			while($tasks->next_record()) {
				$tasklists['tasklist_id'] = $tasks->f('id');
				$tasklists['name'] = $tasks->f('name');
				$tasklists['visible'] = (in_array($tasks->f('id'), $visible_tls));
				$response['results'][] = $tasklists;
			}
			break;


		case 'categories':

			if(isset($_POST['delete_keys']))
			{
				try {
					$response['deleteSuccess']=true;
					$categories = json_decode($_POST['delete_keys']);
					foreach($categories as $category_id)
					{
						$category = $tasks->get_category($category_id);
						if($GLOBALS['GO_SECURITY']->has_admin_permission($GLOBALS['GO_SECURITY']->user_id) || ($category['user_id'] == $GLOBALS['GO_SECURITY']->user_id))
						{
							$tasks->delete_category($category_id);
						}else
						{
							throw new AccessDeniedException();
						}
					}
				}
				catch(Exception $e) {
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			$categories = $GLOBALS['GO_CONFIG']->get_setting('tasks_categories_filter', $GLOBALS['GO_SECURITY']->user_id);
			$categories = ($categories) ? explode(',',$categories) : array();

			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';

			$response['results'] = array();
			$response['total'] = $tasks->get_categories();

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			while($category = $tasks->next_record())
			{
				$category['user_name']=$GO_USERS->get_user_realname($category['user_id']);

				$category['checked'] = in_array($category['id'], $categories);

				$response['results'][] = $category;
			}

			$response['success'] = true;

			break;
	}
}catch(Exception $e) {
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
