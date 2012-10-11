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


require('../../Group-Office.php');

$GLOBALS['GO_SECURITY']->json_authenticate('calendar');

require($GLOBALS['GO_LANGUAGE']->get_language_file('calendar'));

require_once ($GLOBALS['GO_MODULES']->modules['calendar']['class_path']."calendar.class.inc.php");
$cal = new calendar();
$cal2 = new calendar();

$max_description_length=800;

session_write_close();


$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';


try {

	switch($task) {
		
		case 'load_calendar_colors':
		
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : 0;
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : 0;
			
			$response['results']=array();
			$response['total'] = $cal->get_authorized_calendars($GO_SECURITY->user_id,$start,$limit);
			if(!$response['total']) {
				$cal->get_calendar();
				$response['total'] = $cal->get_authorized_calendars($GO_SECURITY->user_id,$start,$limit);
			}

			$default_colors = array('F0AE67','FFCC00','FFFF00','CCFF00','66FF00',
							'00FFCC','00CCFF','0066FF','95C5D3','6704FB',
							'CC00FF','FF00CC','CC99FF','FB0404','FF6600',
							'C43B3B','996600','66FF99','999999','00FFFF');

			$default_colors_count = count($default_colors);
			$i = 0;
			
			while($record = $cal->next_record()) {
				$color = $cal2->getCalendarColor($record['id'],$GO_SECURITY->user_id);
				
				if(!$color){
					$color = $default_colors[$i];
					$i++;
					if($i >= $default_colors_count)
						$i = 0;
				}
				
				$record['color'] = $color;
				
				$response['results'][] = $record;
			}
			
			break;
		

		case 'startup':

			$_REQUEST['start']=0;
			$_REQUEST['limit']=$GLOBALS['GO_CONFIG']->nav_page_size;
			
			$cal->get_views_json($response['views']);
			$cal->get_calendars_json($response['calendars']);
			$cal->get_calendars_json($response['resources'], true);
			
//			require_once('../../GO.php');
//			$calCon=new GO_Calendar_Controller_Calendar();
//			$response['resources']=$calCon->run("calendarsWithGroup",array(),false);

			$cal->get_calendars_json($response['project_calendars'], false,true);

//			$response['categories']['results']=array();
//			$response['categories']['total'] = $cal->get_categories('name', 'asc', 0, 0);
//			while($category = $cal->next_record())
//			{
//				$response['categories']['results'][] = $category;
//			}

			break;

		case 'init_event_window':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			/*$response['writable_calendars']['total'] = $cal->get_writable_calendars($GLOBALS['GO_SECURITY']->user_id, 0, 0, 1, 1, -1, 1, 'name', 'ASC');
			
			$cal2=new calendar();
			$response['results']=array();
			while($record =$cal->next_record()) {

				$group = $cal2->get_group($record['group_id']);
				$record['group_name'] = $group['name'];			

				$response['writable_calendars']['results'][] = $record;
			}*/


			$response['groups']['total'] = $cal->get_groups();
			$response['groups']['results']=array();
			$response['resources']['results']=array();
			$total = 0;

			while($group = $cal->next_record()) {

				//for groupsStore
				$group['user_name'] =$GO_USERS->get_user_realname($group['user_id']);
				$response['groups']['results'][] = $group;



				//for resources panel
				$group['fields'] = explode(",", $group['fields']);
				$group['resources'] = array();
				$cal2->get_authorized_calendars($GLOBALS['GO_SECURITY']->user_id, 0, 0, 0, $group['id']);
				while($resource = $cal2->next_record()) {
					$resource['user_name'] =$GO_USERS->get_user_realname($resource['user_id']);
					if(isset($GLOBALS['GO_MODULES']->modules['customfields']))
					{
						require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
						$cf = new customfields();

						$values = $cf->get_values($GLOBALS['GO_SECURITY']->user_id, 21, $resource['id'],true);

						foreach($values as $k => $v) {
							if (substr($k,0,4)=='col_' && empty($v)) {
								unset($values[$k]);
							}
						}
						$resource=array_merge($resource, $values);
					}
					$group['resources'][] = $resource;
				}

				$num_resources = count($group['resources']);
				if($num_resources > 0) {
					$response['resources']['results'][] = $group;
					$total+=$num_resources;
				}
			}			
			$response['resources']['total'] = $total;

			$response['categories']['results']=array();
			$response['categories']['total'] = $cal->get_categories('name', 'asc', 0, 0);
			while($category = $cal->next_record())
			{
				$response['categories']['results'][] = $category;
			}


			break;

		case 'summary':
		//get the local times
			$local_time = time();
			$year = date("Y", $local_time);
			$month = date("m", $local_time);
			$day = date("j", $local_time);

			$interval_start_time = mktime(0, 0, 0, $month, $day, $year);
			$interval_end_time = mktime(0, 0, 0, $month, $day+2, $year);

			$user_id = $_REQUEST['user_id'];
			$calendars = array();
			$calendars_name = array();
			$calendars_with_bdays = array();

			if($cal->get_visible_calendars($user_id) == 0) {
				$default_calendar = $cal->get_default_calendar($user_id);
				$vc['calendar_id']=$default_calendar['id'];
				$vc['user_id']=$user_id;
				$cal->add_visible_calendar($vc);
				$cal->get_visible_calendars($user_id);
			}

			while($cal->next_record()) {
				$cur_calendar = $cal2->get_calendar($cal->f('calendar_id'));
				$calendars[] = $cal->f('calendar_id');
				$calendars_name[] = $cur_calendar['name'];

				if($cur_calendar['show_bdays']) {
					$calendars_with_bdays[] = $cur_calendar;
				}
			}

			$today_end = mktime(0, 0, 0, $month, $day+1, $year);

			$unsorted=array();
			$response['count']=0;
			$response['results']=array();

			$num_books = count($calendars_with_bdays);
			if($num_books) {
				require_once ($GLOBALS['GO_MODULES']->modules['addressbook']['class_path'].'addressbook.class.inc.php');
				$ab = new addressbook();

				$abooks = array();
				for($i=0; $i<$num_books; $i++) {
					$user_id = $calendars_with_bdays[$i]['user_id'];
					$abooks = array_merge($abooks, $ab->get_user_addressbook_ids($user_id));
				}
				$abooks = array_unique($abooks);

				$response['books'] = $abooks;
				$cal->get_bdays($interval_start_time, $interval_end_time-1, $abooks);
				while($contact = $cal->next_record()) {
					$name = String::format_name($contact['last_name'], $contact['first_name'], $contact['middle_name']);

					$start_time = $contact['upcoming'].' 00:00';
					$end_time = $contact['upcoming'].' 23:59';
					$start_timestamp = strtotime($start_time);

					$index = strtotime($start_time);
					while(isset($unsorted[$index])) {
						$index++;
					}

					$unsorted[$index] = array(
									'id'=>$response['count']++,
									'name'=>str_replace('{NAME}',$name,$lang['calendar']['birthday_name']),
									'description'=>str_replace(array('{NAME}','{AGE}'), array($name,$contact['upcoming']-$contact['birthday']), $lang['calendar']['birthday_desc']),
									'time'=>date($_SESSION['GO_SESSION']['date_format'], $start_timestamp),
									'start_time'=>$start_time,
									'end_time'=>$end_time,
									'day'=>$today_end<=$start_timestamp ? $lang['common']['tomorrow'] : $lang['common']['today'],
									'read_only'=>true,
									'contact_id'=>$contact['id']
					);
				}
			}

			$events = $cal->get_events_in_array($calendars, 0, $interval_start_time, $interval_end_time);

			foreach($events as $event) {

				$private = ($event['private']=='1' && $GLOBALS['GO_SECURITY']->user_id != $event['user_id']);
				if($private) {
					$event['name']=$lang['calendar']['private'];
					$event['description']='';
					$event['location']='';
				}

				if($event['all_day_event'] == '1') {
					$date_format = $_SESSION['GO_SESSION']['date_format'];
				}
				else {
					if (date($_SESSION['GO_SESSION']['date_format'], $event['start_time']) != date($_SESSION['GO_SESSION']['date_format'], $event['end_time'])) {
						$date_format = $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'];
					}
					else {
						$date_format = $_SESSION['GO_SESSION']['time_format'];
					}
				}
				$cal_id = array_search($event['calendar_id'], $calendars);

				$index = $event['start_time'];
				while(isset($unsorted[$index])) {
					$index++;
				}

				$unsorted[$index] = array(
								'id'=>$response['count'],
								'event_id'=> $event['id'],
								'name'=> htmlspecialchars($event['name'],ENT_COMPAT,'UTF-8'),
								'time'=>date($date_format, $event['start_time']),
								'start_time'=> date('Y-m-d H:i', $event['start_time']),
								'end_time'=> date('Y-m-d H:i', $event['end_time']),
								'location'=>htmlspecialchars($event['location'], ENT_COMPAT, 'UTF-8'),
								'description'=>nl2br(htmlspecialchars(String::cut_string($event['description'],$max_description_length), ENT_COMPAT, 'UTF-8')),
								'private'=>$private,
								'repeats'=>!empty($event['rrule']),
								'day'=>$event['start_time']<$today_end ? $lang['common']['today'] : $lang['common']['tomorrow'],
								'calendar_name'=>(isset($calendars_name) && $cal_id !== false)? $calendars_name[$cal_id]: ''
				);
				$response['count']++;
			}


			ksort($unsorted);

			while($event = array_shift($unsorted))
				$response['results'][]=$event;

			break;

		/*case 'invitation':

			require_once($GLOBALS['GO_CONFIG']->class_path.'mail/RFC822.class.inc');
			require_once($GLOBALS['GO_CONFIG']->class_path.'filesystem.class.inc');

			$RFC822 = new RFC822();

			$response['success']=true;

			$event_id = ($_REQUEST['event_id']);
			$event = $cal->get_event($event_id);

			$response['data']['subject']=$lang['calendar']['appointment'].$event['name'];

			$response['data']['body']='<p>'.$lang['calendar']['invited'].'</p>'.
			$cal->event_to_html($event).
				'<p>'.$lang['calendar']['acccept_question'].'</p>'.
				'<a href="'.$GLOBALS['GO_MODULES']->modules['calendar']['full_url'].'invitation.php?event_id='.$event_id.'&task=accept&email=%email%">'.$lang['calendar']['accept'].'</a>'.
				'&nbsp;|&nbsp;'.
				'<a href="'.$GLOBALS['GO_MODULES']->modules['calendar']['full_url'].'invitation.php?event_id='.$event_id.'&task=decline&email=%email%">'.$lang['calendar']['decline'].'</a>';

			$response['replace_personal_fields']=true;

			$participants=array();
			$cal->get_participants($event_id);
			while($cal->next_record())
			{
				if($cal->f('user_id')!=$GLOBALS['GO_SECURITY']->user_id)
				{
					$participants[] = $RFC822->write_address($cal->f('name'), $cal->f('email'));
				}
			}

			$response['data']['to']=implode(',', $participants);

			//create ics attachment
			require_once ($GLOBALS['GO_MODULES']->modules['calendar']['class_path'].'go_ical.class.inc');
			$ical = new go_ical();
			$ics_string = $ical->export_event($event_id);

			$name = File::strip_invalid_chars($event['name']).'.ics';

			$dir=$GLOBALS['GO_CONFIG']->tmpdir.'attachments/';
			filesystem::mkdir_recursive($dir);

			$tmp_file = $dir.$name;

			$fp = fopen($tmp_file,"wb");
			fwrite ($fp,$ics_string);
			fclose($fp);

			$response['data']['attachments']=array(array(
					'tmp_name'=>$tmp_file,
					'name'=>$name,
					'size'=>strlen($ics_string),
					'type'=>File::get_filetype_description('ics')
			));



			break;*/

		case 'event_with_items':
			require_once($GLOBALS['GO_CONFIG']->class_path.'ical2array.class.inc');
			require_once($GLOBALS['GO_CONFIG']->class_path.'Date.class.inc.php');

			$event = $cal->get_event($_REQUEST['event_id']);
			if(!$event) {
				throw new DatabaseSelectException();
			}
			$calendar = $cal->get_calendar($event['calendar_id']);

			$response['success']=true;
			$response['data']['calendar_name']=$calendar['name'];
			$response['data']['permission_level']=$GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $calendar['acl_id']);
			$response['data']['write_permission']=$response['data']['permission_level']>1;
			if(!$response['data']['permission_level'] ||
							($event['private']=='1' && $event['user_id']!=$GLOBALS['GO_SECURITY']->user_id)) {
				throw new AccessDeniedException();
			}

			$response['data']=array_merge($response['data'], $event);
			$response['data']['html_event']=$cal->event_to_html($event);

			load_standard_info_panel_items($response, 1);

			$response['success'] = true;

			break;


		case 'event':

			require_once($GLOBALS['GO_CONFIG']->class_path.'ical2array.class.inc');
			require_once($GLOBALS['GO_CONFIG']->class_path.'Date.class.inc.php');

			$event = $cal->get_event($_REQUEST['event_id']);

			if(!$event) {
				throw new DatabaseSelectException();
			}
			$calendar = $cal->get_calendar($event['calendar_id']);

			$response['data']['permission_level']=$GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $calendar['acl_id']);
			$response['data']['write_permission']=$response['data']['permission_level']>1;
			if(!$response['data']['permission_level'] ||
							($event['private']=='1' && $event['user_id']!=$GLOBALS['GO_SECURITY']->user_id)) {
				throw new AccessDeniedException();
			}

			$has_other_participants = 0;
			$continue = true;
			//$has_other_participants=$cal->get_participants($event['id']);
			$has_other_participants = $cal->count_participants($event['id']) > 0 ? 1 : 0;
			/*while($cal->next_record() && $continue)
			{
				if($cal->f('user_id') != $GLOBALS['GO_SECURITY']->user_id)
				{
					$has_other_participants++;
				}else
				if(!$cal->f('is_organizer'))
				{
					$has_other_participants = 0;
					$continue = false;
				}
			}*/
			$response['data']['has_other_participants'] = $has_other_participants;

			$response['data']=array_merge($response['data'], $cal->event_to_json_response($event));


			if(isset($GLOBALS['GO_MODULES']->modules['customfields'])) {
				require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();

				$response['data']['resources_checked'] = array();

				$values = $cf->get_values($GLOBALS['GO_SECURITY']->user_id, 1, $event['id']);
				$response['data']=array_merge($response['data'], $values);
			}else
			{
				$cf = false;
			}

			if($calendar['group_id'] == 1) {
				$cal->get_event_resources($response['data']['id']);
				while($cal->next_record()) {
					$values = $cf ? $cf->get_values($GLOBALS['GO_SECURITY']->user_id, 1, $cal->f('id')) : array('link_id'=>$cal->f('id'));
					$response['data']['resources'][$cal->f('calendar_id')] = $values;
					$response['data']['status_'.$cal->f('calendar_id')] = $lang['calendar']['statuses'][$cal->f('status')];
					$i = 0;
					foreach($values as $key=>$value) {
						$resource_cal_id = $cal->f('calendar_id');
						$resource_options = 'resource_options['.$resource_cal_id.']['.$key.']';
						$response['data'][$resource_options] = $value;
						$i++;
					}
					if($i > 0)
						$response['data']['resources_checked'][] = $cal->f('calendar_id');
				}
			}
			if($response['data']['category_id']>0){
				$category = $cal->get_category($response['data']['category_id']);
				if($category)
					$response['data']['category_name']=$category['name'];
			}
			


			$response['data']['calendar_name']=$calendar['name'];
			$response['data']['group_id'] = $calendar['group_id'];


			$params = array(&$response, $event);
			$GLOBALS['GO_EVENTS']->fire_event('load_event', $params);


			$response['success']=true;
			break;

		case 'events':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

		require_once($GLOBALS['GO_CONFIG']->class_path.'base/links.class.inc.php');
		$GO_LINKS = new GO_LINKS();

		//setlocale(LC_ALL, 'nl_NL@euro');

		//return all events for a given period
			$view_id = isset($_REQUEST['view_id']) ? $_REQUEST['view_id'] : 0;

			$calendar_id=isset($_REQUEST['calendar_id']) && !isNaN($_REQUEST['calendar_id']) ? ($_REQUEST['calendar_id']) : 0;
			//$view_id=isset($_REQUEST['view_id']) ? ($_REQUEST['view_id']) : 0;
			$start_time=isset($_REQUEST['start_time']) ? strtotime($_REQUEST['start_time']) : 0;
			$end_time=isset($_REQUEST['end_time']) ? strtotime($_REQUEST['end_time']) : 0;

			if ($view_id) {
				$calendar_names=array();
				$calendars = array();
				$cal->get_view_calendars($view_id);
				while($record = $cal->next_record()) {
					$calendars[] = $record['id'];
					$calendar_names[$record['id']]=htmlspecialchars($record['name'], ENT_QUOTES, 'UTF-8');
				}

				if (count($calendars)==0) {
					throw new Exception($lang['calendar']['noCalSelected']);
				}


			} else {
				$calendars=isset($_REQUEST['calendars']) ? json_decode($_REQUEST['calendars']) : array($calendar_id);
			}

			$owncolor = isset($_REQUEST['owncolor']) && count($calendars)>1 ? $_REQUEST['owncolor'] : 0;

			$default_bg = array();
			$default_colors = array('F0AE67','FFCC00','FFFF00','CCFF00','66FF00',
							'00FFCC','00CCFF','0066FF','95C5D3','6704FB',
							'CC00FF','FF00CC','CC99FF','FB0404','FF6600',
							'C43B3B','996600','66FF99','999999','00FFFF');

			$default_colors_count = count($default_colors);
			$i = 0;

			foreach($calendars as $key=>$cal_id)
			{
				$color = $cal->getCalendarColor($cal_id, $GO_SECURITY->user_id);
				
				if(!$color){
					$color = $default_colors[$i];
					$i++;
					if($i >= $default_colors_count)
					$i = 0;
				}

				$default_bg[$cal_id] = $color;
			}
			
			if(count($calendars)>1)
				$response['backgrounds']=$default_bg;

			$calendar_id=$calendars[0];

			$check_calendars = $calendars;
			$calendars=array();
			$calendar_names=array();
			$response['write_permission']=false;

			$calendar_props=array();

			$permission_levels=array();

			foreach($check_calendars as $calendar_id){
				$calendar = $calendar_props[] = $cal->get_calendar($calendar_id);

				$permissionLevel = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_id']);
				if($permissionLevel>=GO_SECURITY::READ_PERMISSION){
					if ($GO_SECURITY->user_id==$calendar['user_id']
							|| ( !isset($response['calendar_id']) && (!isset($response['permission_level']) || $response['permission_level']<GO_SECURITY::WRITE_PERMISSION) )
						) {
							$response['comment']=$calendar['comment'];
							$response['calendar_id']=$calendar['id'];
							$response['calendar_name']=$calendar['name'];

						$response['permission_level']=$permissionLevel;
						if($response['permission_level']>1){
							$response['write_permission']=true;
						}					
					}
					$calendars[]=$calendar_id;
					$calendar_names[$calendar_id]=$calendar['name'];

					$permission_levels[$calendar_id]=$permissionLevel;
				}
			}

	
			$response['title']=implode(' & ', $calendar_names);



			$events = $cal->get_events_in_array($calendars,0,$start_time,$end_time);

			$response['results']=array();
			$response['count']=0;
			$response['mtime']=0;

			$uuid_array = array();
			$event_nr = 0;

		//	$calmerg = new calendar();

			$cal_count = count($calendars);

			foreach($events as $event) {

				// merge events having several participants in merged view
				if ($cal_count>1) {
					//require_once('merge_events.php');
					if ($cal->merge_events($response['results'],$event,$uuid_array,$event_nr,$calendar_names)) continue;
				}
				$event_nr++;

				if($event['all_day_event'] == '1') {
					$date_format = $_SESSION['GO_SESSION']['date_format'];
				}
				else {
					if (date($_SESSION['GO_SESSION']['date_format'], $event['start_time']) != date($_SESSION['GO_SESSION']['date_format'], $event['end_time'])) {
						$date_format = $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'];
					}
					else {
						$date_format = $_SESSION['GO_SESSION']['time_format'];
					}
				}

				$duration_minutes = ($event['end_time']-$event['start_time'])/60;
				if($duration_minutes >= 60)
				{
					$duration_hours = floor($duration_minutes / 60);
					$duration_rest_minutes = $duration_minutes % 60;

					$duration = $duration_hours.' '.$lang['common']['hours'].', '.$duration_rest_minutes.' '.$lang['common']['mins'];
				}else
				{
					$duration = $duration_minutes.'m';
				}

				$private = ($event['private']=='1' && $GLOBALS['GO_SECURITY']->user_id != $event['user_id']);
				if($private) {
					$event['name']=$lang['calendar']['private'];
					$event['description']='';
					$event['location']='';
				}

				if ($owncolor)
				{
					$event['background'] = $default_bg[$event['calendar_id']];
				}
				
				$username = $GO_USERS->get_user_realname($event['user_id']);

				//TODO could be more efficient by doing these queries only when deleting or updating
				$has_other_participants = $cal->count_participants($event['id']) > 0 ? 1 : 0;
	
				$response['results'][] = array(
								'id'=>$response['count']++,
								'event_id'=> $event['id'],
								'link_count'=>$GO_LINKS->count_links($event['id'], 1),
								'name'=> htmlspecialchars($event['name'], ENT_COMPAT, 'UTF-8'),
								'time'=>date($date_format, $event['start_time']),
								'calendar_id'=>$event['calendar_id'],
								'calendar_name'=>isset($calendar_names[$event['calendar_id']]) ? $calendar_names[$event['calendar_id']] : '',
								'start_time'=> date('Y-m-d H:i', $event['start_time']),
								'ctime'=> date('Y-m-d H:i', $event['ctime']),
								'end_time'=> date('Y-m-d H:i', $event['end_time']),
								'location'=>htmlspecialchars($event['location'], ENT_COMPAT, 'UTF-8'),
								'description'=>nl2br(htmlspecialchars(String::cut_string($event['description'],$max_description_length), ENT_COMPAT, 'UTF-8')),
								'background'=>$event['background'],
								//'background'=>$default_colors[$response['count']-1],
								'private'=>($event['private']=='1' && $GLOBALS['GO_SECURITY']->user_id != $event['user_id']),
								'repeats'=>!empty($event['rrule']),
								'all_day_event'=>$event['all_day_event'],
								'day'=>$lang['common']['full_days'][date('w', $event['start_time'])].' '.date($_SESSION['GO_SESSION']['date_format'], $event['start_time']),
								'read_only'=> $event['read_only'] || ($event['private']=='1' && $GLOBALS['GO_SECURITY']->user_id != $event['user_id']) || $permission_levels[$event['calendar_id']]<GO_SECURITY::WRITE_PERMISSION ? true : false,
								'username' => $username,
								'duration' => $duration,
								'has_other_participants' => $has_other_participants,
								
								'category'=>!empty($event['category_id'])?$cal->get_category($event['category_id']):false
				);
				
				if($event['mtime'] > $response['mtime'])
				{
					$response['mtime'] = $event['mtime'];
				}
			}

			$response['count_events_only'] = $response['count'];
	
			if(isset($GLOBALS['GO_MODULES']->modules['addressbook']))
			{
				$contacts = array();
				foreach($check_calendars as $calendar_id)
				{
					$calendar = $cal->get_calendar($calendar_id);
					if($calendar['show_bdays'])
					{
						require_once ($GLOBALS['GO_MODULES']->modules['addressbook']['class_path'].'addressbook.class.inc.php');
						$ab = new addressbook();
						$abooks = $ab->get_user_addressbook_ids($calendar['user_id']);

						$cal->get_bdays($start_time, $end_time ,$abooks);
						while($contact = $cal->next_record()) {
							$name = String::format_name($contact['last_name'], $contact['first_name'], $contact['middle_name']);
							$start_arr = explode('-',$contact['upcoming']);
							$start_unixtime = mktime(0,0,0,$start_arr[1],$start_arr[2],$start_arr[0]);
							
							if(!in_array($contact['id'], $contacts))
							{
								$contacts[] = $contact['id'];
								$response['results'][] = array(
												'id'=>$response['count']++,
												'name'=>htmlspecialchars(str_replace('{NAME}',$name,$lang['calendar']['birthday_name']), ENT_COMPAT, 'UTF-8'),
												'description'=>htmlspecialchars(str_replace(array('{NAME}','{AGE}'), array($name,$contact['upcoming']-$contact['birthday']), $lang['calendar']['birthday_desc']), ENT_COMPAT, 'UTF-8'),
												'time'=>date($_SESSION['GO_SESSION']['date_format'], $start_unixtime),												
												'start_time'=>$contact['upcoming'].' 00:00',
												'end_time'=>$contact['upcoming'].' 23:59',
												'background'=>'EBF1E2',
												'day'=>$lang['common']['full_days'][date('w', $start_unixtime)].' '.date($_SESSION['GO_SESSION']['date_format'], $start_unixtime),
												'read_only'=>true,
												'contact_id'=>$contact['id']
								);
							}
						}
					}
				}
			}

			require_once($GLOBALS['GO_CONFIG']->class_path.'holidays.class.inc.php');
			$holidays = new holidays();

			if($holidays->get_holidays_for_period($GLOBALS['GO_LANGUAGE']->language, $start_time, $end_time)){
				while($record = $holidays->next_record()){
					$response['results'][] = array(
						'id'=>$response['count']++,
						'name'=>htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8'),
						'description'=>'',
						'time'=>date($_SESSION['GO_SESSION']['date_format'],$record['date']),
						'all_day_event'=>1,
						'start_time'=>date('Y-m-d',$record['date']).' 00:00',
						'end_time'=>date('Y-m-d',$record['date']).' 23:59',
						'background'=>'f1f1f1',
						'day'=>$lang['common']['full_days'][date('w', $record['date'])].' '.date($_SESSION['GO_SESSION']['date_format'], $record['date']),
						'read_only'=>true
						);
				}
			}

			if(isset($GLOBALS['GO_MODULES']->modules['tasks'])) {
				$visible_lists = array();

				$cal->get_visible_tasklists($calendars);
				while($cal->next_record()) {
					$visible_lists[] = $cal->f('tasklist_id');
				}

				if(count($visible_lists) > 0) {

					require_once ($GLOBALS['GO_MODULES']->modules['tasks']['class_path'].'tasks.class.inc.php');
					$tasks = new tasks();

					require($GLOBALS['GO_LANGUAGE']->get_language_file('tasks'));

					/*$tasklists_ids = array();
					$tasks->get_authorized_tasklists();
					while($list = $tasks->next_record()) {
						if(in_array($list['id'], $visible_lists)) {
							$tasklists_ids[] = $list['id'];
							$tasklists_names[$list['id']] = $list['name'];
						}
					}*/
					$tasklists_names=array();

					$tasks2= new tasks();


					$tasks->get_tasks($visible_lists, 0, false, 'due_time', 'ASC', 0, 0, null, '','', array(), $start_time, $end_time,'',false);
					while($task = $tasks->next_record()) {

						if(!isset($tasklists_names[$task['tasklist_id']])){
							$_tasklist=$tasks2->get_tasklist($task['tasklist_id']);
							$tasklists_names[$task['tasklist_id']]=$_tasklist['name'];
						}

						$name = htmlspecialchars($lang['tasks']['task'].': '.$task['name'], ENT_QUOTES, 'UTF-8');
						$description = $lang['tasks']['list'].': '.htmlspecialchars($tasklists_names[$task['tasklist_id']], ENT_QUOTES, 'UTF-8');
						$description .= ($task['description']) ? '<br /><br />'.htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8') : '';

						$start_time = date('Y-m-d',$task['due_time']).' 00:00';
						$end_time = date('Y-m-d',$task['due_time']).' 23:59';

						$response['results'][] = array(
										'id'=>$response['count']++,
										'link_count'=>$GO_LINKS->count_links($task['id'], 12),
										'name'=>$name,
										'description'=>$description,
										'time'=>'00:00',
										'start_time'=>$start_time,
										'end_time'=>$end_time,
										'background'=>'EBF1E2',
										'day'=>$lang['common']['full_days'][date('w', ($task['start_time']))].' '.date($_SESSION['GO_SESSION']['date_format'], ($task['start_time'])),
										'read_only'=>true,
										'task_id'=>$task['id']
						);
					}
				}
			}

			break;

		case 'view_events':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/links.class.inc.php');
			$GO_LINKS = new GO_LINKS();

			$view_id = ($_REQUEST['view_id']);
			$start_time=isset($_REQUEST['start_time']) ? strtotime($_REQUEST['start_time']) : 0;
			$end_time=isset($_REQUEST['end_time']) ? strtotime($_REQUEST['end_time']) : 0;

			$view = $cal->get_view($view_id);

			$response['title']=$view['name'];

			if(isset($_REQUEST['update_event_id'])) {
				//an event is moved or resized
				$update_event_id=$_REQUEST['update_event_id'];
				$old_event = $cal->get_event($update_event_id);

				if(isset($_REQUEST['createException']) && $_REQUEST['createException'] =='true') {

					$exceptionDate = strtotime(($_REQUEST['exceptionDate']));

					//an instance of a recurring event was modified. We must create an exception for the
					//recurring event.
					$exception['event_id'] = $update_event_id;
					$exception['time'] = mktime(date('G', $old_event['start_time']),date('i', $old_event['start_time']), 0, date('n', $exceptionDate), date('j', $exceptionDate), date('Y', $exceptionDate));

					//die(date('Ymd G:i', $exception['time']));
					$cal->add_exception($exception);

					//now we copy the recurring event to a new single event with the new time
					$update_event['repeat_type']=0;
					$update_event['start_time']=$exception['time'];
					$update_event['end_time']=$exception['time']+$old_event['end_time']-$old_event['start_time'];

					if(isset($_REQUEST['offset'])) {
						//move an event
						$offset = ($_REQUEST['offset']);


						$update_event['start_time']=$update_event['start_time']+$offset;
						$update_event['end_time']=$update_event['end_time']+$offset;

					}


					if(isset($_REQUEST['offsetDays'])) {
						//move an event
						$offsetDays = ($_REQUEST['offsetDays']);
						$update_event['start_time'] = Date::date_add($update_event['start_time'], $offsetDays);
						$update_event['end_time'] = Date::date_add($update_event['end_time'], $offsetDays);

					}

					if(isset($_REQUEST['duration'])) {
						//change duration
						$duration = ($_REQUEST['duration']);
						$update_event['end_time']=$update_event['start_time']+$duration;
					}

					if(isset($_REQUEST['update_calendar_id'])) {
						$update_event['calendar_id']=$_REQUEST['update_calendar_id'];
					}


					$update_event['id'] = $cal->copy_event($exception['event_id'], $update_event);
				}
				else {
					if(isset($_REQUEST['offset'])) {
						//move an event
						$offset = ($_REQUEST['offset']);


						$update_event['start_time']=$old_event['start_time']+$offset;
						$update_event['end_time']=$old_event['end_time']+$offset;
					}

					if(isset($_REQUEST['offsetDays'])) {
						//move an event
						$offsetDays = ($_REQUEST['offsetDays']);
						$update_event['start_time'] = Date::date_add($old_event['start_time'], $offsetDays);
						$update_event['end_time'] = Date::date_add($old_event['end_time'], $offsetDays);
					}

					if(isset($_REQUEST['duration'])) {
						//change duration
						$duration = ($_REQUEST['duration']);

						$update_event['start_time']=$old_event['start_time'];
						$update_event['end_time']=$old_event['start_time']+$duration;
					}

					if(isset($_REQUEST['update_calendar_id'])) {
						$update_event['calendar_id']=$_REQUEST['update_calendar_id'];
					}

					$update_event['id']=$update_event_id;
					$cal->update_event($update_event);

					//move the exceptions if a recurrent event is moved
					if($old_event['repeat_type']>0 && isset($offset)) {
						$cal->move_exceptions(($_REQUEST['update_event_id']), $offset);
					}
				}

			}


			$cal2 = new calendar();
			$user_cal_settings = $cal2->get_settings($GO_SECURITY->user_id);
			$response['results']=array();
			$count=0;
			$cal->get_view_calendars($view_id);
			while($view_calendar = $cal->next_record()) {

				if($user_cal_settings['calendar_id']==$view_calendar['id'] || !isset($response['calendar_id'])){
					$response['calendar_id']=$view_calendar['id'];
					$response['calendar_name']=$view_calendar['name'];
				}


				$permission_level = $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $view_calendar['acl_id']);
				if(!$permission_level)
					continue;

				//$response[$cal->f('id')] = $view_calendar;
				$view_calendar['write_permission'] = $permission_level>GO_SECURITY::READ_PERMISSION;

				$events = $cal2->get_events_in_array(array($cal->f('id')), 0,
								$start_time,
								$end_time
				);

				$view_calendar['events']=array();

				foreach($events as $event) {
					if($event['all_day_event'] == '1') {
						$date_format = $_SESSION['GO_SESSION']['date_format'];
					}
					else {
						if (date($_SESSION['GO_SESSION']['date_format'], $event['start_time']) != date($_SESSION['GO_SESSION']['date_format'], $event['end_time'])) {
							$date_format = $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'];
						}
						else {
							$date_format = $_SESSION['GO_SESSION']['time_format'];
						}
					}

//					if($event['category_id'])
//					{
//						$category = $cal2->get_category($event['category_id']);
//						if($category)
//						{
//							$event['background'] = $category['color'];
//						}
//					}


					$private = ($event['private']=='1' && $GLOBALS['GO_SECURITY']->user_id != $event['user_id']);
					if($private) {
						$event['name']=$lang['calendar']['private'];
						$event['description']='';
						$event['location']='';
					}


					$view_calendar['events'][] = array(
									'id'=>$count,
									'link_count'=>$GO_LINKS->count_links($event['id'], 1),
									'calendar_id'=>$cal->f('id'),
									'calendar_name'=>$cal->f('name'),
									'event_id'=> $event['id'],
									'name'=>htmlspecialchars($event['name'], ENT_COMPAT, 'UTF-8'),
									'start_time'=> date('Y-m-d H:i', $event['start_time']),
									'end_time'=> date('Y-m-d H:i', $event['end_time']),
									'location'=>htmlspecialchars($event['location'], ENT_COMPAT, 'UTF-8'),
									'description'=>nl2br(htmlspecialchars(String::cut_string($event['description'],$max_description_length), ENT_COMPAT, 'UTF-8')),
									'background'=>$event['background'],
									'repeats'=>!empty($event['rrule']),
									'private'=>$private,
									'write_permission'=>$view_calendar['write_permission'],
									'read_only'=> ($event['private']=='1' && $GLOBALS['GO_SECURITY']->user_id != $event['user_id']) || !$view_calendar['write_permission'] ? true : false,
									'mtime' => $event['mtime']
					);
					$count++;
				}

				$response['results'][]=$view_calendar;
			}

			break;


		case 'calendars':

			$resources = isset($_REQUEST['resources']) ? $_REQUEST['resources'] : 0;
			$project_calendars = isset($_REQUEST['project_calendars']) ? $_REQUEST['project_calendars'] : 0;

			$cal->get_calendars_json($response, $resources, $project_calendars);

			break;

		case 'user_calendars':
			$response['total'] =$cal->get_user_calendars($GLOBALS['GO_SECURITY']->user_id);

			while($record =$cal->next_record()) {
				$response['results'][] = $record;
			}
			break;

		case 'writable_calendars':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			if(isset($_REQUEST['delete_keys']))
			{
				try
				{
					$response['deleteSuccess']=true;
					$calendars = json_decode(($_REQUEST['delete_keys']));
					foreach($calendars as $calendar_id)
					{
						$calendar = $cal->get_calendar($calendar_id);
						if(($GLOBALS['GO_MODULES']->modules['calendar']['permission_level'] < GO_SECURITY::WRITE_PERMISSION) || ($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $calendar['acl_id']) < GO_SECURITY::DELETE_PERMISSION))
						{
							throw new AccessDeniedException();
						}
						
						$cal->delete_calendar($calendar_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : 0;
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : 0;
			$resources = isset($_REQUEST['resources']) ? $_REQUEST['resources'] : 0;
			$show_all = isset($_REQUEST['show_all']) ? $_REQUEST['show_all'] : 0;

			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'name';
			$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'ASC';
			$query = !empty($_REQUEST['query']) ? '%'.$_REQUEST['query'].'%' : '';

			$response['total'] = $cal->get_writable_calendars($GLOBALS['GO_SECURITY']->user_id, $start, $limit, $resources, 1, -1, $show_all, $sort, $dir, $query);
			if(!$response['total']) {
				$cal->get_calendar();
				$response['total'] = $cal->get_writable_calendars($GLOBALS['GO_SECURITY']->user_id, $start, $limit, $resources, 1, -1, $show_all, $sort, $dir,$query);
			}

			$response['results']=array();
			while($record =$cal->next_record()) {

				$group = $cal2->get_group($record['group_id']);
				$record['group_name'] = $group['name'];

				$record['user_name'] =$GO_USERS->get_user_realname($record['user_id']);

				$response['results'][] = $record;
			}
			break;


		case 'view_calendars':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$view_id = ($_REQUEST['view_id']);

			$response['total'] = $cal->get_authorized_calendars($GLOBALS['GO_SECURITY']->user_id,0,0,0,-1);
			if(!$response['total']) {
				$cal->get_calendar();
				$response['total'] = $cal->get_authorized_calendars($GLOBALS['GO_SECURITY']->user_id,0,0,0,-1);
			}
			$response['results']=array();
			while($record = $cal->next_record(DB_ASSOC)) {
				$record['user_name'] =$GO_USERS->get_user_realname($record['user_id']);
				$record['selected']=$cal2->is_view_calendar($cal->f('id'), $view_id) ? '1' : '0';

				$response['results'][] = $record;
			}

			$response['success'] = true;

			break;

		case 'views':
			$cal->get_views_json($response);
			break;


		case 'writable_views':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			if(isset($_REQUEST['delete_keys'])) {
				try {
					$response['deleteSuccess']=true;
					$views = json_decode(($_REQUEST['delete_keys']));

					foreach($views as $view_id) {
						$view = $cal->get_view($view_id);
						if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $view['acl_id'])<GO_SECURITY::DELETE_PERMISSION) {
							throw new AccessDeniedException();
						}
						$cal->delete_view($view_id);
					}
				}
				catch(Exception $e) {
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : 0;
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : 0;
			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'name';
			$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'ASC';

			$response['total'] = $cal->get_authorized_views($GLOBALS['GO_SECURITY']->user_id, $sort, $dir, $start, $limit, 'write');
			$response['results']=array();
			while($calendar=$cal->next_record(DB_ASSOC)) {
				$calendar['user_name'] =$GO_USERS->get_user_realname($calendar['user_id']);
				$response['results'][] =$calendar;
			}
			break;

		case 'view':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$response['data']=$cal->get_view($_REQUEST['view_id']);
			$response['data']['user_name'] =$GO_USERS->get_user_realname($response['data']['user_id']);
			$response['success']=true;
			break;

		case 'calendar':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$response['data']=$cal->get_calendar($_REQUEST['calendar_id']);
			$response['data']['user_name'] =$GO_USERS->get_user_realname($response['data']['user_id']);

			$url = create_direct_url('calendar', 'openCalendar', array(array(
				'calendars'=>array($response['data']['id']),
				'group_id'=>$response['data']['group_id'])
					),'ready');

			if(isset($GLOBALS['GO_MODULES']->modules['tasks']) && $response['data']['tasklist_id']>0){
				require_once ($GLOBALS['GO_MODULES']->modules['tasks']['class_path'].'tasks.class.inc.php');
				$tasks = new tasks();

				$tasklist = $tasks->get_tasklist($response['data']['tasklist_id']);
				if($tasklist)
					$response['data']['tasklist_name']=$tasklist['name'];
				else
					$response['data']['tasklist_id']='';
			}else
			{
				$response['data']['tasklist_id']='';
			}

			$response['data']['url']='<a class="normal-link" target="_blank" href="'.$url.'">'.$lang['calendar']['rightClickToCopy'].'</a>';
			$response['data']['ics_url']='<a class="normal-link" target="_blank" href="'.$GLOBALS['GO_MODULES']->modules['calendar']['full_url'].'export.php?calendar_id='.$response['data']['id'].'&months_in_past=1">'.$lang['calendar']['rightClickToCopy'].'</a>';

			if(isset($GLOBALS['GO_MODULES']->modules['customfields']))
			{
				require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$values = $cf->get_values($GLOBALS['GO_SECURITY']->user_id, 21, $response['data']['id'], false);
				$response['data']=array_merge($response['data'], $values);
			}

			$response['success']=true;
			break;

		case 'participants':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$event_id=$_REQUEST['event_id'];

			/*if(isset($_REQUEST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$participants = json_decode(($_REQUEST['delete_keys']));

					foreach($participants as $participant_id)
					{
						$cal->delete_participant($participant_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			if(isset($_REQUEST['add_participants']))
			{
				$participants = json_decode(($_REQUEST['add_participants']),true);
				foreach($participants as $participant)
				{
					$participant['event_id']=$event_id;
					//$participant['name']=$_REQUEST['name'];
					//$participant['email']=$_REQUEST['email'];

					$cal->add_participant($participant);
				}
			}*/

			if($event_id>0) {
				$event = $cal->get_event($event_id);

				$response['total'] = $cal->get_participants($event_id);
				$response['results']=array();
				while($participant =$cal->next_record(DB_ASSOC)) {
					$participant['available']='?';
					$user=$GO_USERS->get_user_by_email($participant['email']);
					if($user) {

						//Only show availability if user has access to the default calendar
//						if(!empty($GLOBALS['GO_CONFIG']->require_calendar_access_for_freebusy)){
//							$default_calendar = $cal2->get_default_calendar($user['id']);
//							$permission = $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $default_calendar['acl_id']);
//						}else
//						{
//							$permission=true;
//						}

						if($cal->has_freebusy_access($GLOBALS['GO_SECURITY']->user_id, $user['id'])){
							$participant['available']=$cal2->is_available($user['id'], $event['start_time'], $event['end_time'], $event) ? '1' : '0';
						}
					}

					$response['results'][]=$participant;
				}
			}
			else {

			}
			break;
//		case 'get_default_participant':
//
//			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
//			$GO_USERS = new GO_USERS();
//
//			$calendar = $cal->get_calendar($_REQUEST['calendar_id']);
//
//			if($calendar)
//			{
//				$calendar_user = $GO_USERS->get_user($calendar['user_id']);
//				if($calendar_user)
//				{
//					$response['user_id']=$calendar['user_id'];
//					$response['name']=$GO_USERS->get_user_realname($calendar['user_id']);
//					$response['email']=$calendar_user['email'];
//					$response['status']="1";
//					$response['is_organizer']="1";
//					$response['available']=$cal->is_available($response['user_id'], $_REQUEST['start_time'], $_REQUEST['end_time'], false) ? '1' : '0';
//				}
//			}
//			
//			break;


		case 'check_availability':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();
			

			$event = empty($_REQUEST['event_id']) ? false : $cal->get_event($_REQUEST['event_id']);

			$emails=explode(',', $_REQUEST['emails']);

			$response=array();
			foreach($emails as $email) {
				$user=$GO_USERS->get_user_by_email($email);

				$response[$email]='?';

				if($user) {
					//Only show availability if user has access to the default calendar
//					if(!empty($GLOBALS['GO_CONFIG']->require_calendar_access_for_freebusy)){
//						$default_calendar = $cal2->get_default_calendar($user['id']);
//						$permission = $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $default_calendar['acl_id']);
//					}else
//					{
//						$permission=true;
//					}

					if($cal->has_freebusy_access($GLOBALS['GO_SECURITY']->user_id, $user['id'])){
						$response[$email]=$cal->is_available($user['id'], $_REQUEST['start_time'], $_REQUEST['end_time'], $event) ? '1' : '0';
					}
				}
			}
			break;

		case 'availability':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();
			
			$event_id = empty($_REQUEST['event_id']) ? 0 : $_REQUEST['event_id'];
			$date = Date::to_unixtime($_REQUEST['date']);
			$emails = json_decode($_REQUEST['emails'], true);
			$names = isset($_REQUEST['names']) ? json_decode($_REQUEST['names'], true) : $emails;


			$merged_free_busy=array();
			for($i=0;$i<1440;$i+=15) {
				$merged_free_busy[$i]=0;
			}

			$response['participants']=array();
			while($email = array_shift($emails)) {
				$participant['name']=array_shift($names);
				$participant['email']=$email;
				$participant['freebusy']=array();

				$user = $GO_USERS->get_user_by_email($email);
				if($user) {

					//Only show availability if user has access to the default calendar
//					if(!empty($GLOBALS['GO_CONFIG']->require_calendar_access_for_freebusy)){
//						$default_calendar = $cal2->get_default_calendar($user['id']);
//						$permission = $GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $default_calendar['acl_id']);
//					}else
//					{
//						$permission=true;
//					}

					if($cal->has_freebusy_access($GLOBALS['GO_SECURITY']->user_id, $user['id'])){

						$freebusy=$cal->get_free_busy($user['id'], $date, $event_id);
						foreach($freebusy as $min=>$busy) {
							if($busy=='1') {
								$merged_free_busy[$min]=1;
							}
							$participant['freebusy'][]=array(
											'time'=>date('G:i', mktime(0,$min)),
											'busy'=>$busy);
						}
					}
				}
				$response['participants'][]=$participant;
			}


			$participant['name']=$lang['calendar']['allTogether'];
			$participant['email']='';
			$participant['freebusy']=array();

			foreach($merged_free_busy as $min=>$busy) {
				$participant['freebusy'][]=array(
								'time'=>date($_SESSION['GO_SESSION']['time_format'], mktime(0,$min)),
								'busy'=>$busy);
			}

			$response['participants'][]=$participant;


			break;


		case 'group':

			$group = $cal->get_group($_REQUEST['group_id']);
			
			$fields = explode(',', $group['fields']);
			foreach($fields as $field) {
				$group['fields['.$field.']'] = true;
			}

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$group['user_name'] = $GO_USERS->get_user_realname($group['user_id']);
			$response['data'] = $group;

			$response['success'] = true;
			break;

		case 'groups':

			if(isset($_POST['delete_keys']))
			{
				try {
					if($GLOBALS['GO_MODULES']->modules['calendar']['permission_level'] < GO_SECURITY::WRITE_PERMISSION)
					{
						throw new AccessDeniedException();
					}

					$response['deleteSuccess']=true;
					$delete_groups = json_decode($_POST['delete_keys']);
					foreach($delete_groups as $group_id)
					{
						if($group_id != 1)
						{
							$cal->get_calendars_by_group_id($group_id);
							while($cal->next_record())
							{
								$cal2->delete_calendar($cal->f('id'));
							}
							$cal->delete_group($group_id);
						}
					}
				}
				catch(Exception $e) {
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'id';
			$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC';
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$response['results']=array();
			$response['total'] = $cal->get_groups($sort, $dir, $start, $limit);
			while($group = $cal->next_record()) {
				$group['user_name'] = $GO_USERS->get_user_realname($group['user_id']);
				$response['results'][] = $group;
			}

			break;

		case 'resources':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$cal->get_groups();
			$response['results']=array();
			$total = 0;
			while($group = $cal->next_record()) {
				$group['fields'] = explode(",", $group['fields']);
				$group['resources'] = array();
				$cal2->get_authorized_calendars($GLOBALS['GO_SECURITY']->user_id, 0, 0, 0, $group['id']);

				while($resource = $cal2->next_record()) {
					$user = $GO_USERS->get_user_realname($group['user_id']);
					$resource['user_name']=String::format_name($user);

//					if(isset($GLOBALS['GO_MODULES']->modules['customfields']))
//					{
//						require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
//						$cf = new customfields();
//						$values = $cf->get_values($GLOBALS['GO_SECURITY']->user_id, 18, $resource['id'], true);
//						$resource=array_merge($resource, $values);
//					}

					$group['resources'][] = $resource;
				}

				$num_resources = count($group['resources']);
				if($num_resources > 0) {
					$response['results'][] = $group;
					$total+=$num_resources;
				}
			}

			$response['total'] = $total;
			break;


		case 'settings':
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$query = !empty($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';

			$cal->get_visible_calendars($GLOBALS['GO_SECURITY']->user_id);
			$visible_cals = array();
			while($cal->next_record()) {
				$visible_cals[] = $cal->f('calendar_id');
			}

			$response['total'] = $cal->get_authorized_calendars($GLOBALS['GO_SECURITY']->user_id, $start, $limit,0,1);

			$response['results']=array();

			while($cal->next_record()) {
				$calendars['calendar_id'] = $cal->f('id');
				$calendars['name'] = $cal->f('name');
				$calendars['visible'] = (in_array($cal->f('id'), $visible_cals));
				$response['results'][] = $calendars;
			}
			break;

		case 'group_admins':

			$group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] : 0;

			$response['total'] = 0;
			$response['results'] = array();
			$response['success'] = false;


			if($group_id > 0) {
				if(isset($_POST['add_users'])) {
					try {
						$response['addSuccess']=true;
						$add_group_admins = json_decode($_POST['add_users']);
						foreach($add_group_admins as $user_id) {
							if(!$cal->group_admin_exists($group_id, $user_id)) {
								$cal->add_group_admin(array('group_id' => $group_id, 'user_id' => $user_id));
							}
						}
					}
					catch(Exception $e) {
						$response['addSuccess']=false;
						$response['addFeedback']=$e->getMessage();
					}
				}

				if(isset($_POST['delete_keys'])) {
					try {
						$response['deleteSuccess']=true;

						$delete_group_admins = json_decode($_POST['delete_keys']);
						foreach($delete_group_admins as $user_id) {
							$cal->delete_group_admin($group_id, $user_id);
						}
					}
					catch(Exception $e) {
						$response['deleteSuccess']=false;
						$response['deleteFeedback']=$e->getMessage();
					}
				}

				require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
				$GO_USERS = new GO_USERS();

				$response['total'] = $cal->get_group_admins($group_id);
				while($cal->next_record()) {
					$admin['id'] = $cal->f('user_id');

					$user = $GO_USERS->get_user($admin['id']);
					$admin['email'] = $GO_USERS->f('email');
					$admin['name'] = String::format_name($GO_USERS->f('last_name'),$GO_USERS->f('first_name'),$GO_USERS->f('middle_name'));

					$response['results'][] = $admin;
				}
			}

			$response['success'] = true;
			break;


		case 'tasklists':

			$calendar_id = isset($_REQUEST['calendar_id']) ? $_REQUEST['calendar_id'] : 0;
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';

			$visible_lists = array();
			if($calendar_id) {
				$cal->get_visible_tasklists($calendar_id);
				while($cal->next_record()) {
					$visible_lists[] = $cal->f('tasklist_id');
				}
			}


			require_once ($GLOBALS['GO_MODULES']->modules['tasks']['class_path'].'tasks.class.inc.php');
			$tasks = new tasks();

			$response['results']=array();
			$response['total'] = $tasks->get_authorized_tasklists('read', '', $GLOBALS['GO_SECURITY']->user_id, 0, 0, $sort, $dir);
			while($tasks->next_record()) {
				$tasklist['id'] = $tasks->f('id');
				$tasklist['name'] = $tasks->f('name');
				$tasklist['visible'] = (in_array($tasklist['id'], $visible_lists));


				$response['results'][] = $tasklist;
			}

			$response['success'] = true;
			break;

		case 'my_calendar':

			$cal = $cal->get_calendar(0, $GLOBALS['GO_SECURITY']->user_id);

			$my_cal = $response['data'] = array();
			foreach($cal as $k=>$v) {
				$my_cal[$k] = $v;
			}
			$response['data'][0] = $my_cal;
			$response['success'] = true;
			break;
		/*
		case 'addressbooks_participants':
			$ids = json_decode($_POST['ids']);

			require_once ($GLOBALS['GO_MODULES']->modules['addressbook']['class_path']."addressbook.class.inc.php");
			$ab = new addressbook();

			$response['results'] = array();

			foreach($ids as $ab_id) {
				$ab->get_contacts($ab_id);
				while ($ab->next_record()) {
					$participant['email'] = $ab->record['email'];
					$participant['name'] = !empty($ab->record['middle_name']) ?
						$ab->record['first_name'].' '.$ab->record['middle_name'].' '.$ab->record['last_name'] :
						$ab->record['first_name'].' '.$ab->record['last_name'];
					$response['results'][] = $participant;
				}
				$ab->get_companies($ab_id);
				while ($ab->next_record()) {
					$company['email'] = $ab->record['email'];
					$company['name'] = $ab->record['name'];
					$response['results'][] = $company;
				}
			}

			$response['success'] =true;

			break;
		*/

		case 'mailings_participants':
			$ids = json_decode($_POST['ids']);

			require_once ($GLOBALS['GO_MODULES']->modules['mailings']['class_path']."mailings.class.inc.php");
			$mailings = new mailings();

			$response['results'] = array();

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			foreach($ids as $mailing_id) {
				$mailings->get_contacts_from_mailing_group($mailing_id);
				while ($mailings->next_record()) {

					$user = $GO_USERS->get_user_by_email($mailings->record['email']);

					$participant['user_id'] = $user ? $user['id'] : 0;
					$participant['email'] = $mailings->record['email'];
					$participant['name'] = String::format_name($mailings->record);
					$response['results'][] = $participant;
				}
				$mailings->get_companies_from_mailing_group($mailing_id);
				while ($mailings->next_record()) {
					$company['user_id'] = 0;
					$company['email'] = $mailings->record['email'];
					$company['name'] = $mailings->record['name'];
					$response['results'][] = $company;
				}
				$mailings->get_users_from_mailing_group($mailing_id);
				while ($mailings->next_record()) {
					$participant['user_id'] = $mailings->record['id'];
					$participant['email'] = $mailings->record['email'];
					$participant['name'] = String::format_name($mailings->record);
					$response['results'][] = $participant;
				}
			}

			$response['success'] =true;

			break;

		case 'usergroups_participants':
			$ids = json_decode($_POST['ids']);

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/groups.class.inc.php');
			$GO_GROUPS = new GO_GROUPS();

			$response['results'] = array();

			foreach($ids as $ug_id) {
				$GO_GROUPS->get_users_in_group($ug_id);
				while ($record = $GO_GROUPS->next_record()) {
					$participant['user_id'] = $record['id'];
					$participant['email'] = $record['email'];
					$participant['name'] = String::format_name($record);
					$response['results'][] = $participant;
				}
			}

			$response['success'] =true;

			break;


		case 'permissions':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$resources = (isset($_REQUEST['resources']) && $_REQUEST['resources']) ? 1 : 0;
			$group_id = (isset($_REQUEST['group_id']) && $_REQUEST['group_id']) ? $_REQUEST['group_id'] : 0;
			$level_id = (isset($_REQUEST['level_id']) && $_REQUEST['level_id']) ? $_REQUEST['level_id'] : 0;

			$response['success'] = false;
			$response['results'] = array();			
			if($group_id && $level_id)
			{
				$checked_calendars = array();
				$response['total'] = $cal->get_writable_calendars($GLOBALS['GO_SECURITY']->user_id, 0, 0, $resources);
				while($cal->next_record())
				{
					$calendar = $cal->record;		
					$calendar['user_name']=$GO_USERS->get_user_realname($calendar['user_id']);

					$acl_level = $GLOBALS['GO_SECURITY']->group_in_acl($group_id, $calendar['acl_id']);
					$calendar['checked'] = ($acl_level >= $level_id) ? true : false;
					
					if($calendar['checked'])
					    $checked_calendars[] = $calendar['id'];

					$response['results'][] = $calendar;
				}
				
				$response['success'] = true;
				$response['checked_calendars'] = $checked_calendars;
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
						$category = $cal->get_category($category_id);
						if($GLOBALS['GO_SECURITY']->has_admin_permission($GLOBALS['GO_SECURITY']->user_id) || ($category['user_id'] == $GLOBALS['GO_SECURITY']->user_id))
						{
							$cal->delete_category($category_id);
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

			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'id';
			$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC';
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';

			$response['results']=array();
			$response['total'] = $cal->get_categories($sort, $dir, $start, $limit);

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();
			
			while($category = $cal->next_record())
			{
				$category['user_name']=$GO_USERS->get_user_realname($category['user_id']);
				$response['results'][] = $category;
			}

			break;
		case 'participant_email_addresses':


			$response['total'] = $cal->get_participants($_POST['event_id']);
			$response['results'] = array();
			while ($part = $cal->next_record()) {
				$response['results'][] = $part;
			}
			
			$response['success'] = true;
			break;
	}
}
catch(Exception $e) {
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);