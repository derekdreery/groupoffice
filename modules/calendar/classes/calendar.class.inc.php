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


define('DB_DATETIME_FORMAT', 'Y-m-d H:i:00');
define('DB_DATE_FORMAT', 'Y-m-d');
define('DB_TIME_FORMAT', 'H:i:00');



class calendar extends db {
	var $events = array();
	var $events_sort = array(); //used to sort the events at start_time
	var $all_day_events = array();
	var $backgrounds = array();

	/**
	 * Get the color for a calendar for a user.
	 * Each user can define an own color for each calendar.
	 * 
	 * @param int $cal_id
	 * @param int $user_id
	 * @return mixed (boolean or string) 
	 */
	function getCalendarColor($cal_id, $user_id){
		// TODO: De kleur ophalen en returnen
		$this->query("SELECT color FROM cal_calendar_user_colors WHERE user_id='".intval($user_id)."' AND calendar_id='".intval($cal_id)."';");
		if($record=$this->next_record()) {
			return $record['color'];
		}
		return false;
	}
		
	/**
	 * Set the correct color for the calendars in the multi calendar view.
	 * Each user can define an own color for each calendar.
	 * 
	 * @param int $cal_id
	 * @param int $user_id
	 * @param string $color
	 * @return boolean 
	 */
	function setCalendarColor($cal_id, $user_id, $color){
		$this->query("REPLACE INTO cal_calendar_user_colors (user_id, calendar_id, color) VALUES ('".intval($user_id)."','".intval($cal_id)."', '".$this->escape($color)."')");
		return true;
	}
	
	function reminder_seconds_to_form_input($reminder) {
		$multipliers[] = 604800;
		$multipliers[] = 86400;
		$multipliers[] = 3600;
		$multipliers[] = 60;

		$settings['reminder_multiplier'] = 60;
		$settings['reminder_value'] = 0;

		if(!empty($reminder)) {
			for ($i = 0; $i < count($multipliers); $i ++) {
				$devided = $reminder / $multipliers[$i];
				$match = (int) $devided;
				if ($match == $devided) {
					$settings['reminder_multiplier'] = $multipliers[$i];
					$settings['reminder_value'] = $devided;
					break;
				}
			}
		}
		return $settings;
	}

	function get_settings($user_id)
	{
		$this->query("SELECT * FROM cal_settings WHERE user_id='".intval($user_id)."'");
		if ($record=$this->next_record(DB_ASSOC)) {
			if(empty($record['background']))
				$record['background']='EBF1E2';

			return $record;
		}else
		{
			$this->query("INSERT INTO cal_settings (user_id, background) VALUES ('".intval($user_id)."', 'EBF1E2')");
			return $this->get_settings($user_id);
		}
	}

	function update_settings($settings) {
		if(!isset($settings['user_id'])) {
			global $GO_SECURITY;
			$settings['user_id'] = $GLOBALS['GO_SECURITY']->user_id;
		}
		return $this->update_row('cal_settings', 'user_id', $settings);
	}



	function event_to_html($event, $custom=false, $ics=false) {
		global $GO_LANGUAGE, $GO_CONFIG, $lang;

		require($GLOBALS['GO_LANGUAGE']->get_language_file('calendar'));

		//go_debug($event);

		$html = '<table>'.
						'<tr><td>'.$lang['calendar']['subject'].':</td>'.
						'<td>'.$event['name'].'</td></tr>';
		if(!$ics)
		{
			$html .= '<tr><td>'.$lang['calendar']['status'].':</td>'.
				'<td>'.$lang['calendar']['statuses'][$event['status']].'</td></tr>';
		}

		if (!empty($event['location'])) {
			$html .= '<tr><td style="vertical-align:top">'.$lang['calendar']['location'].':</td>'.
							'<td>'.String::text_to_html($event['location']).'</td></tr>';
		}

		//don't calculate timezone offset for all day events
		$timezone_offset_string = Date::get_timezone_offset($event['start_time']);

		if ($timezone_offset_string > 0) {
			$gmt_string = '(\G\M\T +'.$timezone_offset_string.')';
		}
		elseif ($timezone_offset_string < 0) {
			$gmt_string = '(\G\M\T -'.$timezone_offset_string.')';
		} else {
			$gmt_string = '(\G\M\T)';
		}

		if ($event['all_day_event']=='1') {
			$event_datetime_format = $_SESSION['GO_SESSION']['date_format'];
		} else {
			$event_datetime_format = $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'].' '.$gmt_string;
		}

		$html .= '<tr><td colspan="2">&nbsp;</td></tr>';

		$html .= '<tr><td>'.$lang['calendar']['startsAt'].':</td>'.
						'<td>'.date($event_datetime_format, $event['start_time']).'</td></tr>'.
						'<tr><td>'.$lang['calendar']['endsAt'].':</td>'.
						'<td>'.date($event_datetime_format, $event['end_time']).'</td></tr>';



		if(!empty($event['rrule'])) {
			require_once($GLOBALS['GO_CONFIG']->class_path.'ical2array.class.inc');
			$ical2array = new ical2array();

			$rrule = $ical2array->parse_rrule($event['rrule']);

			if (isset($rrule['UNTIL'])) {
				if($event['repeat_end_time'] = $ical2array->parse_date($rrule['UNTIL'])) {
					$event['repeat_forever']='0';
					$event['repeat_end_time'] = mktime(0,0,0, date('n', $event['repeat_end_time']), date('j', $event['repeat_end_time'])+1, date('Y', $event['repeat_end_time']));
				}else {
					$event['repeat_forever'] = 1;
				}
			}elseif(isset($rrule['COUNT'])) {
				//figure out end time later when event data is complete
				$event['repeat_forever'] = 1;
				$event_count = intval($rrule['COUNT']);
				if($event_count==0) {
					unset($event_count);
				}
			}else {
				$event['repeat_forever'] = 1;
			}

			$event['repeat_every']=$rrule['INTERVAL'];



			if(isset($rrule['BYDAY'])) {
				//var_dump($rrule);

				//$days = explode(',', $rrule['BYDAY']);



				/*$event['sun'] = strpos($rrule['BYDAY'],'SU')!==false ? '1' : '0';
				$event['mon'] = strpos($rrule['BYDAY'],'MO')!==false ? '1' : '0';
				$event['tue'] = strpos($rrule['BYDAY'],'TU')!==false ? '1' : '0';
				$event['wed'] = strpos($rrule['BYDAY'],'WE')!==false ? '1' : '0';
				$event['thu'] = strpos($rrule['BYDAY'],'TH')!==false ? '1' : '0';
				$event['fri'] = strpos($rrule['BYDAY'],'FR')!==false ? '1' : '0';
				$event['sat'] = strpos($rrule['BYDAY'],'SA')!==false ? '1' : '0';*/

				$days = Date::byday_to_days($rrule['BYDAY']);
				$event = array_merge($event, $days);

			}


			$html .= '<tr><td colspan="2">';
			switch($rrule['FREQ']) {
				case 'WEEKLY':
					$event = Date::shift_days_to_local($event, date('G', $event['start_time']),Date::get_timezone_offset($event['start_time']));



					$days=array();

					if(isset($rrule['BYDAY'])) {
						if($event['sun']=='1') {
							$days[]=$lang['common']['full_days'][0];
						}
						if($event['mon']=='1') {
							$days[]=$lang['common']['full_days'][1];
						}
						if($event['tue']=='1') {
							$days[]=$lang['common']['full_days'][2];
						}
						if($event['wed']=='1') {
							$days[]=$lang['common']['full_days'][3];
						}
						if($event['thu']=='1') {
							$days[]=$lang['common']['full_days'][4];
						}
						if($event['fri']=='1') {
							$days[]=$lang['common']['full_days'][5];
						}
						if($event['sat']=='1') {
							$days[]=$lang['common']['full_days'][6];
						}
					}else
					{
						$days[]=$lang['common']['full_days'][date('w', $event['start_time'])];
					}

					if(count($days)==1) {
						$daysStr=$days[0];
					}else {
						$daysStr = ' '.$lang['calendar']['and'].' '.array_pop($days);
						$daysStr = implode(', ', $days).$daysStr;
					}

					if($event['repeat_every']>1) {
						$html .= sprintf($lang['calendar']['repeats_at_not_every'],
										$event['repeat_every'], $lang['common']['weeks'],
										$daysStr);
					}else {
						$html .= sprintf($lang['calendar']['repeats_at'],
										$lang['common']['week'],
										$daysStr);
					}

					break;

				case 'DAILY':
					if($event['repeat_every']>1) {
						$html .= sprintf($lang['calendar']['repeats_not_every'],
										$event['repeat_every'], $lang['common']['days']);
					}else {
						$html .= sprintf($lang['calendar']['repeats'],
										$lang['common']['day']);
					}
					break;

				case 'MONTHLY':
					if (!isset($rrule['BYDAY'])) {
						if($event['repeat_every']>1) {
							$html .= sprintf($lang['calendar']['repeats_not_every'],
											$event['repeat_every'], $lang['common']['months']);
						}else {
							$html .= sprintf($lang['calendar']['repeats'],
											$lang['common']['month']);
						}
					}else {


						$event = Date::shift_days_to_local($event, date('G', $event['start_time']),Date::get_timezone_offset($event['start_time']));

						$days=array();
						if($event['sun']=='1') {
							$days[]=$lang['common']['full_days'][0];
						}
						if($event['mon']=='1') {
							$days[]=$lang['common']['full_days'][1];
						}
						if($event['tue']=='1') {
							$days[]=$lang['common']['full_days'][2];
						}
						if($event['wed']=='1') {
							$days[]=$lang['common']['full_days'][3];
						}
						if($event['thu']=='1') {
							$days[]=$lang['common']['full_days'][4];
						}
						if($event['fri']=='1') {
							$days[]=$lang['common']['full_days'][5];
						}
						if($event['sat']=='1') {
							$days[]=$lang['common']['full_days'][6];
						}

						if(count($days)==1) {
							$daysStr=$lang['calendar']['month_times'][$rrule['BYDAY'][0]].' '.$days[0];
						}else {
							$daysStr = ' '.$lang['calendar']['and'].' '.array_pop($days);
							$daysStr = $lang['calendar']['month_times'][$rrule['BYDAY'][0]].' '.implode(', ', $days).$daysStr;
						}

						if($event['repeat_every']>1) {
							$html .= sprintf($lang['calendar']['repeats_at_not_every'],
											$event['repeat_every'], $lang['common']['strMonths'], $daysStr);
						}else {
							$html .= sprintf($lang['calendar']['repeats_at'],
											$lang['common']['month'], $daysStr);
						}
					}
					break;

				case 'YEARLY':
					if($event['repeat_every']>1) {
						$html .= sprintf($lang['calendar']['repeats_not_every'],
										$event['repeat_every'], $lang['common']['years']);
					}else {
						$html .= sprintf($lang['calendar']['repeats'],
										$lang['calendar']['year']);
					}
					break;
			}

			if ($event['repeat_forever'] != '1') {
				$html .= ' '.$lang['calendar']['until'].' '.date($_SESSION['GO_SESSION']['date_format'], $event['repeat_end_time']);
			}
			$html .= '</td></tr>';
		}

		$html .= '<tr><td colspan="2">&nbsp;</td></tr>';


		if(!empty($event['description'])) {
			$html .= '<tr><td style="vertical-align:top">'.$lang['common']['description'].':</td>'.
							'<td>'.String::text_to_html($event['description']).'</td></tr>';
		}

		if($custom) {
			$html .= '<tr><td style="vertical-align:top">{CUSTOM_FIELDS}</td>'.
							'<td>{CUSTOM_VALUES}</td></tr>';
		}

		$html .= '</table>';



		return $html;
	}

	function copy_participants($src_event_id, $target_event_id){

		$cal = new calendar();

		$this->get_participants($src_event_id);
		while($r = $this->next_record()){

			$r['event_id']=$target_event_id;
			$cal->add_participant($r);
		}
	}


	function copy_event($event_id, $new_values=array()) {
		global $GO_SECURITY;

		$src_event = $dst_event = $this->get_event($event_id);
		unset($dst_event['id'], $dst_event['resource_event_id'],$dst_event['uuid'],$dst_event['files_folder_id']);

		foreach($new_values as $key=>$value) {
			$dst_event[$key] = $value;
		}

		return $this->add_event($dst_event);

	}

	/*
	 takes a sting YYYY-MM-DD HH:MM in GMT time and converts it to an array with
	 hour, min etc. with	a timezone offset. If 0000 or 00 is set in a date
	 (not time) then it will be replaced with current locale	date.
	*/
	function explode_datetime($datetime_stamp, $timezone_offset) {
		$local_time = time();

		$datetime_array = explode(' ', $datetime_stamp);
		$date_stamp = $datetime_array[0];
		$time_stamp = isset($datetime_array[1]) ? $datetime_array[1] : '00:00:00';

		$date_array = explode('-',$date_stamp);

		$year = $date_array[0] == '0000' ? date('Y', $local_time) : $date_array[0];
		$month = $date_array[1] == '00' ? date('n', $local_time) : $date_array[1];
		$day = $date_array[2] == '00' ? date('j', $local_time) : $date_array[2];
		;

		$time_array = explode(':',$time_stamp);
		$hour = $time_array[0];
		$min = $time_array[1];

		$unix_time = mktime($hour, $min, 0, $month, $day, $year);

		$unix_time = $unix_time+($timezone_offset*3600);

		$result['year'] = date('Y', $unix_time);
		$result['month'] = date('n', $unix_time);
		$result['day'] = date('j', $unix_time);
		$result['hour'] = date('G', $unix_time);
		$result['min'] = date('i', $unix_time);

		return $result;
	}

	function add_view($view) {
		$view['id'] = $this->nextid("cal_views");
		$this->insert_row('cal_views',$view);
		return $view['id'];
	}

	function update_view($view) {
		$this->update_row('cal_views','id', $view);
	}

	function delete_view($view_id) {
		if($this->query("DELETE FROM cal_views_calendars WHERE view_id='".$this->escape($view_id)."'")) {
			return $this->query("DELETE FROM cal_views WHERE id='".$this->escape($view_id)."'");
		}
	}

	function get_user_views($user_id) {
		$sql = "SELECT * FROM cal_views WHERE user_id='".intval($user_id)."'";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_authorized_views($user_id, $sort='name', $dir='ASC', $start=0, $offset=0, $auth_type='read') {
		$sql = "SELECT v.* ".
		"FROM cal_views v ".
		"INNER JOIN go_acl a ON (v.acl_id = a.acl_id";

		if($auth_type=='write'){
			$sql .= " AND a.level>".GO_SECURITY::READ_PERMISSION;
		}

		$sql .= " AND (a.user_id=".intval($user_id)." OR a.group_id IN (".implode(',',$GLOBALS['GO_SECURITY']->get_user_group_ids($user_id))."))) ".
		" GROUP BY v.id ORDER BY `".preg_replace("/[^a-zA-Z0-9_]/","",$this->escape($sort)).'` '.($dir=='ASC' ? 'ASC' : 'DESC');

		$sql = $this->add_limits_to_query($sql, $start, $offset);
		$this->query($sql);
		
		return $this->limit_count();
	}

	function get_view($view_id) {
		$sql = "SELECT * FROM cal_views WHERE id='".$this->escape($view_id)."'";
		$this->query($sql);
		if($this->next_record()) {
			return $this->record;
		}
		return false;
	}

	function get_view_calendars($view_id) {
		$sql = "SELECT c.name, c.group_id, c.user_id, c.id, c.acl_id, vc.background FROM cal_calendars c ".
		"INNER JOIN cal_views_calendars vc ON c.id=vc.calendar_id ".
		"WHERE vc.view_id='".$this->escape($view_id)."' ORDER BY c.name ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function add_calendar_to_view($calendar_id, $background, $view_id) {
		$vc['view_id']=$view_id;
		$vc['calendar_id']=$calendar_id;
		$vc['background']=$background;

		return $this->insert_row('cal_views_calendars', $vc);
	}

	function remove_calendar_from_view($calendar_id, $view_id) {
		$sql = "DELETE FROM cal_views_calendars WHERE calendar_id='".$this->escape($calendar_id)."' AND view_id='".$this->escape($view_id)."'";
		return $this->query($sql);
	}

	function remove_calendars_from_view($view_id) {
		$sql = "DELETE FROM cal_views_calendars WHERE view_id='".$this->escape($view_id)."'";
		return $this->query($sql);
	}

	function is_view_calendar($calendar_id, $view_id) {
		$sql = "SELECT * FROM cal_views_calendars WHERE calendar_id='".$this->escape($calendar_id)."' AND view_id='".$this->escape($view_id)."'";
		$this->query($sql);
		return $this->next_record();
	}

	function get_view_by_name($user_id, $name) {
		$sql = "SELECT * FROM cal_views WHERE user_id='".intval($user_id)."' AND name='".$this->escape($name)."'";
		$this->query($sql);

		if($this->next_record()) {
			return $this->record;
		}
		return false;
	}

	function user_has_calendar($user_id) {
		$sql = "SELECT id FROM cal_calendars WHERE user_id='".intval($user_id)."'";
		$this->query($sql);
		return $this->next_record();
	}	

	function add_participant($participant) {
		$participant['id'] = $this->nextid("cal_participants");
		$this->insert_row('cal_participants', $participant);
		return $participant['id'];
	}

	function delete_participant($participant_id) {
		$sql = "DELETE FROM cal_participants WHERE id='".$this->escape($participant_id)."'";
		return $this->query($sql);
	}

	function delete_other_participants($event_id, $keep_ids) {
		$sql = "DELETE FROM cal_participants WHERE event_id=".intval($event_id);

		if(count($keep_ids))
			$sql .= " AND id NOT IN (".$this->escape(implode(',', $keep_ids)).")";

		return $this->query($sql);
	}

	function remove_participants($event_id) {
		$sql = "DELETE FROM cal_participants WHERE event_id='".$this->escape($event_id)."'";
		return $this->query($sql);
	}

	function is_participant($event_id, $email) {
		$sql = "SELECT * FROM cal_participants WHERE event_id='".intval($event_id)."' AND email='".$this->escape($email)."'";
		$this->query($sql);
		return $this->next_record();
	}
	function update_participant($participant) {
		return $this->update_row('cal_participants', 'id', $participant);
	}

	function get_participants($event_id) {
		$sql = "SELECT * FROM cal_participants WHERE event_id='".$this->escape($event_id)."' ORDER BY is_organizer DESC, email ASC" ;
		$this->query($sql);
		return $this->num_rows();
	}
	
	function count_participants($event_id){
		$sql = "SELECT count(*) AS c FROM cal_participants WHERE event_id='".$this->escape($event_id)."' AND is_organizer=0";
		go_debug($sql);
		$this->query($sql);
		$r = $this->next_record();
		return intval($r['c']);
	}

	function get_participant_user($participant_id) {
		$sql = "SELECT u.* FROM go_users u ".
			"INNER JOIN cal_participants p ON p.user_id=u.id ".
			"WHERE p.id='".intval($participant_id)."' ";
		$this->query($sql);
		if ($user = $this->next_record()) {
			return $user;
		} else {
			return false;
		}
	}

	function get_participant_contact($contact_id) {
		$sql = "SELECT * FROM ab_contacts WHERE id='".intval($contact_id)."';";
		$this->query($sql);
		if ($contact = $this->next_record()) {
			return $contact;
		} else {
			return false;
		}
	}

	function set_default_calendar($user_id, $calendar_id) {
		$sql = "UPDATE cal_settings SET default_cal_id='".$this->escape($calendar_id)."' WHERE user_id='".intval($user_id)."'";
		return $this->query($sql);
	}

	function set_default_view($user_id, $calendar_id, $view_id, $merged_view = '') {
		$sql = "UPDATE cal_settings SET default_cal_id='".$this->escape($calendar_id)."', default_view_id='".$this->escape($view_id)."' ";

		if($merged_view != '') {
			$sql .= ",merged_view='".$this->escape($merged_view)."' ";
		}
		$sql .= "WHERE user_id='$user_id'";
		return $this->query($sql);
	}



	function add_calendar($calendar) {
		$calendar['id'] = $this->nextid("cal_calendars");

		global $GO_MODULES;
		if(isset($GLOBALS['GO_MODULES']->modules['files'])) {
			require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();

			$files->check_share('events/'.File::strip_invalid_chars($calendar['name']),$calendar['user_id'], $calendar['acl_id']);
		}

		$this->insert_row('cal_calendars',$calendar);
		return $calendar['id'];
	}

	function delete_calendar($calendar_id) {
		global $GO_SECURITY, $GO_MODULES;
		$delete = new calendar;

		$calendar = $this->get_calendar($calendar_id);

		if(isset($GLOBALS['GO_MODULES']->modules['files'])) {
			require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();

			$folder = $files->resolve_path('calendar/'.File::strip_invalid_chars($calendar['name']));
			if($folder) {
				$files->delete_folder($folder);
			}
		}

		$sql = "SELECT * FROM cal_events WHERE calendar_id='".$this->escape($calendar_id)."'";
		$this->query($sql);

		while ($this->next_record()) {
			$delete->delete_event($this->f('id'));
		}
		
		$sql = "DELETE FROM cal_views_calendars WHERE calendar_id='".$this->escape($calendar_id)."'";
		$this->query($sql);
		
		$sql = "DELETE FROM cal_calendar_user_colors WHERE calendar_id='".$this->escape($calendar_id)."'";
		$this->query($sql);

		$sql= "DELETE FROM cal_calendars WHERE id='".$this->escape($calendar_id)."'";
		$this->query($sql);

		$this->query("DELETE FROM cal_visible_tasklists WHERE calendar_id=?", 'i', $calendar_id);

		if(isset($GLOBALS['GO_MODULES']->modules['summary'])) {
			$this->query("DELETE FROM su_visible_calendars WHERE calendar_id=?", 'i', $calendar_id);
		}

		if(empty($calendar['shared_acl'])) {
			$GLOBALS['GO_SECURITY']->delete_acl($calendar['acl_id']);
		}
	}

	function update_calendar($calendar, $old_calendar=false) {
		if(!$old_calendar)$old_calendar=$this->get_calendar($calendar['id']);

		global $GO_MODULES;
		if(isset($GLOBALS['GO_MODULES']->modules['files']) && $old_calendar &&  $calendar['name']!=$old_calendar['name']) {
			require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
			$files->move_by_paths('events/'.File::strip_invalid_chars($old_calendar['name']), 'events/'.File::strip_invalid_chars($calendar['name']));
		}

		global $GO_SECURITY;
		//user id of the calendar changed. Change the owner of the ACL as well
		if(isset($calendar['user_id']) && $old_calendar['user_id'] != $calendar['user_id']) {
			$GLOBALS['GO_SECURITY']->chown_acl($old_calendar['acl_id'], $calendar['user_id']);
		}
		
		return $this->update_row('cal_calendars','id', $calendar);
	}

	function get_default_import_calendar($user_id) {

		$settings = $this->get_settings($user_id);
		$calendar_id = $settings['calendar_id'];

		if($calendar_id) {
			$this->query("SELECT * FROM cal_calendars WHERE user_id = ? AND id=?", 'ii', array($user_id, $calendar_id));
			if($this->next_record(DB_ASSOC)) {
				return $this->record;
			}
		}

		$this->get_user_calendars($user_id, 0, 1);
		if($this->next_record(DB_ASSOC)) {
			return $this->record;
		}

		return false;
	}

	

	function get_default_calendar($user_id) {

		$settings = $this->get_settings($user_id);

		if(!empty($settings['calendar_id'])){
			$calendar = $this->get_calendar($settings['calendar_id']);
			if($calendar)
				return $calendar;
		}

		$this->get_user_calendars($user_id, 0, 1);
		if($calendar = $this->next_record(DB_ASSOC)) {

			$this->update_row('cal_settings', 'user_id', array('user_id'=>$user_id, 'calendar_id'=>$calendar['id']));

			return $calendar;
		}else {
			global $GO_SECURITY;

			global $GO_CONFIG;
			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$calendar['user_id']=$user_id;
			$user = $GO_USERS->get_user($user_id);
			if(!$user) {
				return false;
			}

			$tpl = $GLOBALS['GO_CONFIG']->get_setting('calendar_name_template');
			if(!$tpl)
				$tpl = '{first_name} {middle_name} {last_name}';

			$calendar_name = String::reformat_name_template($tpl,$user);
			
			//$calendar_name = String::format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'last_name');
			$calendar['name'] = $calendar_name;
			$calendar['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('calendar',$user_id);
			$x = 1;
			while($this->get_calendar_by_name($calendar['name'])) {
				$calendar['name'] = $calendar_name.' ('.$x.')';
				$x++;
			}

			$calendar['name'] = $calendar['name'];


			global $GO_MODULES;
			if($GLOBALS['GO_MODULES']->has_module('tasks')){
				require_once($GLOBALS['GO_MODULES']->modules['tasks']['class_path'].'tasks.class.inc.php');
				$tasks = new tasks();
				$tasks_settings = $tasks->get_settings($user_id);
				$calendar['tasklist_id']=$tasks_settings['default_tasklist_id'];
			}

			if (!$calendar_id = $this->add_calendar($calendar)) {
				throw new DatabaseInsertException();
			}else {

				$this->update_row('cal_settings', 'user_id', array('user_id'=>$user_id, 'calendar_id'=>$calendar_id));

				return $this->get_calendar($calendar_id);
			}
		}
	}


	function get_calendar($calendar_id=0, $user_id=0) {
		if($calendar_id > 0) {
			$sql = "SELECT * FROM cal_calendars WHERE id='".$this->escape($calendar_id)."'";
			$this->query($sql);
			if ($this->next_record(DB_ASSOC)) {
				return $this->record;
			}else {
				return false;
			}
		}else {
			global $GO_SECURITY;
			$user_id = !empty($user_id) ? $user_id : $GLOBALS['GO_SECURITY']->user_id;
			return $this->get_default_calendar($user_id);
		}
	}

	function get_calendar_by_name($name, $user_id=0) {
		$sql = "SELECT * FROM cal_calendars WHERE name='".$this->escape($name)."'";

		if($user_id>0) {
			$sql .= " AND user_id=".intval($user_id);
		}
		$this->query($sql);
		if ($this->next_record()) {
			return $this->record;
		}else {
			return false;
		}
	}

	function get_user_calendars($user_id,$start=0,$offset=0, $group_id=1)
	{
		$sql = "SELECT * FROM cal_calendars WHERE user_id=".intval($user_id);

		if($group_id>0) {
			$sql .= ' AND group_id='.$group_id;
		}

		$sql .= " ORDER BY id ASC";
		$this->query($sql);
		$count= $this->num_rows();

		if($offset>0) {
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			$this->query($sql);
		}
		return $count;
	}

	function get_default_user_calendar($user_id) {
		$this->query("SELECT value FROM go_settings WHERE user_id=? AND name='calendar_default_calendar'", 'i', array($user_id));
		$deb = $this->next_record();
		$calendar_id = $this->f('value');
		if($calendar_id > 0) {
			$this->query("SELECT * FROM cal_calendars WHERE user_id = ? AND id=?", 'ii', array($user_id, $calendar_id));
			return $this->num_rows();
		}else {
			return $this->get_user_calendars($user_id, 0, 1);
		}
		return false;
	}

	function get_calendars() {
		$sql = "SELECT * FROM cal_calendars ORDER BY name ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_authorized_calendars($user_id, $start=0, $offset=0, $resources=0, $group_id=1, $projects=false, $query='') {
		$sql = "SELECT c.* ";

		if($group_id<0) {
			$sql .= ",g.name AS group_name ";
		}

		$sql .= "FROM cal_calendars c ";

		if($group_id<0) {
			$sql .= " LEFT JOIN cal_groups g ON g.id=c.group_id ";
		}

		$sql .= "INNER JOIN go_acl a ON (c.acl_id = a.acl_id AND (a.user_id=".intval($user_id)." OR a.group_id IN (".implode(',',$GLOBALS['GO_SECURITY']->get_user_group_ids($user_id))."))) ";
	
		$where = false;

		if($resources)
		{
			$sql .= $where ? ' AND ' : ' WHERE ';
			$where=true;
			$sql .= "c.group_id > 1";
		}elseif($group_id>-1)
		{
			$sql .= $where ? ' AND ' : ' WHERE ';
			$where=true;
			$sql .= "c.group_id = ".$this->escape($group_id);
		}

		$sql .= $where ? ' AND ' : ' WHERE ';

		$sql .= "c.project_id";
		$sql .= $projects ? ">0" : "=0";

		if(!empty($query)){
			$sql .= " AND c.name LIKE '".$this->escape($query)."'";
		}

		$sql .= ' GROUP BY c.id';

		$sql .= $group_id==-1 ? " ORDER BY g.id, c.name ASC" : " ORDER BY c.name ASC";


		$sql = $this->add_limits_to_query($sql, $start, $offset);
		$this->query($sql);

		return $this->limit_count();
	}

	function get_writable_calendars($user_id, $start=0, $offset=0, $resources=0, $groups=0, $group_id=-1, $show_all=0, $sort='name', $dir='ASC', $query='') {
		$sql = "SELECT c.* ";
		if($groups)
			$sql .= ", g.fields ";
		$sql .= "FROM cal_calendars c ";

		$sql .= "INNER JOIN go_acl a ON (c.acl_id = a.acl_id AND a.level>1 AND (a.user_id=".intval($user_id)." OR a.group_id IN (".implode(',',$GLOBALS['GO_SECURITY']->get_user_group_ids($user_id))."))) ";

        if($groups)
            $sql .= "LEFT JOIN cal_groups g ON c.group_id = g.id ";

		$where=false;
		if(!empty($query)){
			$where = true;
			$sql .= " WHERE c.name LIKE '".$this->escape($query)."'";
		}

		if(!$show_all) {

			$sql .= $where ? ' AND ' : ' WHERE ';


			if($resources) {
				$sql .= " c.group_id > 1";
			}else
				$group_id = 1;

			if($group_id>-1) {
				$sql .= " c.group_id = ".$this->escape($group_id);
			}
			$sql .= " GROUP BY c.id ORDER BY c.".$this->escape($sort.' '.$dir);
		}else {
			$sql .= " GROUP BY c.id ORDER BY c.group_id ASC, c.".$this->escape($sort.' '.$dir);
		}

		$sql = $this->add_limits_to_query($sql, $start, $offset);
		$this->query($sql);

		return $this->limit_count();
	}

	function get_calendars_by_group_id($group_id) {
		$sql = "SELECT * FROM cal_calendars WHERE group_id = ? ORDER BY name ASC";
		$this->query($sql, 'i', array($group_id));
	}
	/*
	 Times in GMT!
	*/

	function build_event_files_path($event, $calendar) {
		return 'events/'.File::strip_invalid_chars($calendar['name']).'/'.date('Y', $event['start_time']).'/'.date('m', $event['start_time']).'/'.File::strip_invalid_chars($event['name']);
	}

	function add_participants($event, $participants, $update=false) {

		global $GO_CONFIG;

		require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		//if(!$update)
			//$this->remove_participants($event['id']);

		foreach ($participants as $participant_email => $participant) {
			$participant['event_id'] = $event['id'];
			$participant['email'] = $participant_email;
			$participant['role'] = ($participant['role']) ? $participant['role'] : 'REQ-PARTICIPANT';
			$participant['last_modified'] = $event['mtime'];

			$user = $GO_USERS->get_user_by_email($participant['email']);
			$participant['user_id'] = ($user) ? $user['id'] : 0;
			
			$existing_participant = $this->is_participant($event['id'], $participant['email']);
			if(!$existing_participant)
				$this->add_participant($participant);
			else{
				$participant['id']=$existing_participant['id'];
				$this->update_participant($participant);
			}
		}
	}

	function add_event(&$event, $calendar=false) {
    GLOBAL $GO_EVENTS;
    
    $GO_EVENTS->fire_event('before_add_event', array(&$event,&$before_event_response));
    
       
		if(empty($event['calendar_id'])) {
			return false;
		}		

		if(isset($event['name']) && strlen($event['name'])>150){
			$event['name']=substr($event['name'],0,150);
		}

		if (empty($event['user_id'])) {
			global $GO_SECURITY;
			$event['user_id'] = $GLOBALS['GO_SECURITY']->user_id;
		}

		if(empty($event['ctime'])) {
			$event['ctime']  =  time();
		}

		if(empty($event['mtime'])) {
			$event['mtime']  =  $event['ctime'];
		}

		if(empty($event['background'])) {
			$settings = $this->get_settings($event['user_id']);
			$event['background']  =  $settings ? $settings['background'] : 'EBF1E2';
		}


		if(!isset($event['status'])) {
			$event['status'] = 'ACCEPTED';
		}

		if(isset($event['participants']))
			$participants = $event['participants'];

		unset($event['acl_id'], $event['participants']);



		if(!isset($event['start_time'])) {
			$local_start_time = time();
			$year = date('Y', $local_start_time );
			$month = date('n', $local_start_time );
			$day = date('j', $local_start_time );
			$event['start_time'] = mktime(0,0,0,$month, $day, $year);
			$event['all_day_event']='1';
		}


		if(!isset($event['end_time']) || $event['end_time']<$event['start_time']) {
			$event['end_time']=$event['start_time']+3600;
		}
		$event['id'] = $this->nextid("cal_events");

		if(empty($event['uuid']))
		{
			$event['uuid'] = UUID::create('event', $event['id']);
		}

		if(!isset($event['resource_event_id'])) {
			$event['resource_event_id']=$event['id'];
		}

		global $GO_MODULES;
		if(!isset($event['files_folder_id']) && isset($GLOBALS['GO_MODULES']->modules['files'])) {
			global $GO_CONFIG;

			if(!$calendar) {
				$calendar = $this->get_calendar($event['calendar_id']);
			}
			require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();

			$new_path = $this->build_event_files_path($event, $calendar);
			if($folder=$files->create_unique_folder($new_path)) {
				$event['files_folder_id']=$folder['id'];
			}
		}


		$exceptions = isset($event['exceptions']) ? $event['exceptions'] : array();
		unset($event['exceptions']);

		if ($event['id'] > 0 &&  $this->insert_row('cal_events', $event)) {
			foreach($exceptions as $exception_time) {
				if($exception_time!==false) {
					$exception['event_id']=$event['id'];
					$exception['time']=$exception_time;

					$this->add_exception($exception);
				}
			}

			$this->cache_event($event['id']);

			if(!empty($event['reminder'])) {
				global $GO_CONFIG;

				require_once($GLOBALS['GO_CONFIG']->class_path.'base/reminder.class.inc.php');
				$rm = new reminder();

				if(!$calendar) {
					$calendar = $this->get_calendar($event['calendar_id']);
				}

				$reminder['user_id']=$calendar['user_id'];
				$reminder['name']=$event['name'];
				$reminder['link_type']=1;
				$reminder['link_id']=$event['id'];

				if(empty($event['rrule']))
					$reminder['vtime']=$event['start_time'];
				else
					$reminder['vtime'] = Date::get_next_recurrence_time($event['start_time'],time(), $event['end_time']-$event['start_time'],$event['rrule']);

				$reminder['time']=$reminder['vtime']-$event['reminder'];

				if($reminder['time']>time())
					$rm->add_reminder($reminder);
			}

			if(isset($participants)){
				$this->add_participants($event,$participants);
			}
      
      $GO_EVENTS->fire_event('calendar_add_event', array($event, $before_event_response));
      
			return $event['id'];
		}				
		return false;
	}


	function is_duplicate_event($event) {
		$sql = "SELECT id FROM cal_events WHERE ".
						"name='".$this->escape($event['name'])."' AND ".
						"start_time='".$this->escape($event['start_time'])."' AND ".
						"end_time='".$this->escape($event['end_time'])."' AND ".
						"calendar_id='".$this->escape($event['calendar_id'])."' AND ".
						"user_id='".$this->escape($event['user_id'])."'";

		$this->query($sql);
		if($this->next_record()) {
			return $this->f('id');
		}
		return false;
	}

	function update_event(&$event, $calendar=false, $old_event=false, $update_related=true, $update_related_status=true) {

		go_debug('calendar::update_event');

		
		if(!$old_event) {
			$old_event = $this->get_event($event['id']);
			if(!$old_event) {
				return false;
			}
		}

		if(isset($event['name']) && strlen($event['name'])>150){
			$event['name']=substr($event['name'],0,150);
		}

		unset($event['read_permission'], $event['write_permission']);
		if(empty($event['mtime'])) {
			$event['mtime']  = time();
		}
		//for building files path we need this.
		if(empty($event['start_time'])) {
			$event['start_time']  = $old_event['start_time'];
		}

		if(isset($event['completion_time']) && $event['completion_time'] > 0 && $this->copy_completed($event['id'])) {
			$event['repeat_type'] = REPEAT_NONE;
			$event['repeat_end_time'] = 0;
		}

		if(isset($event['exceptions'])) {
			$this->delete_exceptions($event['id']);
			foreach($event['exceptions'] as $exception_time) {
				if($exception_time!==false) {
					$exception['event_id']=$event['id'];
					$exception['time']=$exception_time;

					$this->add_exception($exception);
				}
			}
			unset($event['exceptions']);
		}

		

		global $GO_MODULES;
		if(isset($GLOBALS['GO_MODULES']->modules['files'])) {

			if(!$calendar) {
				$calendar = $this->get_calendar($event['calendar_id']);
			}
			require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();

			if(!isset($event['ctime'])) {
				$event['ctime']=$old_event['ctime'];
			}
			if(!isset($event['calendar_id'])) {
				$event['calendar_id']=$old_event['calendar_id'];
			}
			if(!isset($event['name'])) {
				$event['name']=$old_event['name'];
			}

			$new_path = $this->build_event_files_path($event, $calendar);
			$event['files_folder_id']=$files->check_folder_location($old_event['files_folder_id'], $new_path);
		}

		if(isset($event['participants'])){
			$participants = $event['participants'];
			unset($event['participants']);
		}


		$r = $this->update_row('cal_events', 'id', $event);

		$this->cache_event($event['id']);


		if(isset($event['start_time'])) {

			if(!isset($event['reminder'])) {
				$event['reminder']=$old_event['reminder'];
			}

			global $GO_CONFIG;

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/reminder.class.inc.php');
			$rm = new reminder();

			$rm->get_reminders_by_link_id($event['id'], 1);
			$existing_reminder = $rm->next_record();

			if(empty($event['reminder']) && $existing_reminder) {
				$rm->delete_reminder($existing_reminder['id']);
			}

			if(!empty($event['reminder'])) {
				if(!$calendar) {
					if(empty($event['calendar_id'])) {
						$event['calendar_id']=$old_event['calendar_id'];
					}
					$calendar = $this->get_calendar($event['calendar_id']);
				}

				$reminder['id']=$existing_reminder['id'];

				if(isset($event['name']))
					$reminder['name']=$event['name'];

				$reminder['link_type']=1;
				$reminder['link_id']=$event['id'];

				if(empty($event['rrule']))
					$reminder['vtime']=$event['start_time'];
				else
					$reminder['vtime'] = Date::get_next_recurrence_time($event['start_time'],time(), $event['end_time']-$event['start_time'],$event['rrule']);

				$reminder['time']=$reminder['vtime']-$event['reminder'];

				if($reminder['time']>time()) {
					if($existing_reminder) {
						$rm->update_reminder($reminder);
					}else {
						$reminder['user_id']=$calendar['user_id'];
						$rm->add_reminder($reminder);
					}
				}elseif($existing_reminder) {
					$rm->delete_reminder($existing_reminder['id']);
				}
			}
		}

		if(!empty($old_event['rrule']) && $old_event['start_time']!=$event['start_time']) {
			$this->move_exceptions($event['id'], $event['start_time']-$old_event['start_time']);
		}

		if(isset($participants)){
			$this->add_participants($event,$participants, true);
		}

		if($update_related && !empty($event['id'])) {
			$related_event = $event;
			unset($related_event['user_id'], $related_event['calendar_id'], $related_event['resource_event_id']);

			$cal = new calendar();
			/*if(!empty($old_event['resource_event_id'])){
				$sql = "SELECT * FROM cal_events WHERE id!=".$this->escape($event['id'])." AND (resource_event_id=".intval($old_event['resource_event_id'])." OR id=".intval($old_event['resource_event_id']).")";
			}else
			{
				$sql = "SELECT * FROM cal_events WHERE resource_event_id=".intval($event['id']);
			 * $cal->query($sql);
			}*/

			$resource_event_id=!empty($old_event['resource_event_id']) ? $old_event['resource_event_id'] : $event['id'];
			$cal->get_participants_events($resource_event_id, $event['id']);

			while($old_event = $cal->next_record()) {
				$related_event['id']=$cal->f('id');
				$related_event['calendar_id'] = $cal->f('calendar_id');

				if(!$update_related_status) {
					$related_event['status'] = $cal->f('status');
					$related_event['background'] = $cal->f('background');
				}

				$this->update_event($related_event, false, $old_event, false);
			}
		}

		return $r;
	}

	function send_resource_notification($message_type, $resource, $calendar, $user_name, $recipient, $resource_group) {
		global $GO_CONFIG, $GO_MODULES, $lang;

		if(!isset($lang['calendar'])) {
			global $GO_LANGUAGE;
			$GLOBALS['GO_LANGUAGE']->require_language_file('calendar');
		}

		$url = create_direct_url('calendar', 'showEvent', array(array('values'=>array('event_id' => $resource['id']))));

		switch($message_type) {
			case 'new':
				$body = sprintf($lang['calendar']['resource_mail_body'],$user_name,$calendar['name']).'<br /><br />'
								. $this->event_to_html($resource, true)
								. '<br /><a href="'.$url.'">'.$lang['calendar']['open_resource'].'</a>';
				$subject = sprintf($lang['calendar']['resource_mail_subject'],$calendar['name'], $resource['name'], date($_SESSION['GO_SESSION']['date_format'], $resource['start_time']));
				break;

			case 'modified_for_admin':
				$body = sprintf($lang['calendar']['resource_modified_mail_body'], $user_name, $calendar['name']).'<br /><br />'
								. $this->event_to_html($resource, true)
								. '<br /><a href="'.$url.'">'.$lang['calendar']['open_resource'].'</a>';
				$subject = sprintf($lang['calendar']['resource_modified_mail_subject'],$calendar['name'], $resource['name'], date($_SESSION['GO_SESSION']['date_format'], $resource['start_time']));
				break;

			case 'mofified_for_user':
				$body = sprintf($lang['calendar']['your_resource_modified_mail_body'],$user_name,$calendar['name']).'<br /><br />';
				$body .= $this->event_to_html($resource, true);

				$subject = sprintf($lang['calendar']['your_resource_modified_mail_subject'],$calendar['name'], date($_SESSION['GO_SESSION']['date_format'], $resource['start_time']),$lang['calendar']['statuses'][$resource['status']]);
				break;

			case 'declined':
				$body = sprintf($lang['calendar']['your_resource_declined_mail_body'],$user_name,$calendar['name']).'<br /><br />';
				$body .= $this->event_to_html($resource, true);

				$subject = sprintf($lang['calendar']['your_resource_declined_mail_subject'],$calendar['name'], date($_SESSION['GO_SESSION']['date_format'], $resource['start_time']));
				break;

			case 'accepted':
				$body = sprintf($lang['calendar']['your_resource_accepted_mail_body'],$user_name,$calendar['name']).'<br /><br />';
				$body .= $this->event_to_html($resource, true);

				$subject = sprintf($lang['calendar']['your_resource_accepted_mail_subject'],$calendar['name'], date($_SESSION['GO_SESSION']['date_format'], $resource['start_time']));

				break;
		}

		require_once($GLOBALS['GO_CONFIG']->class_path.'mail/GoSwift.class.inc.php');
		$swift = new GoSwift($recipient, $subject);

		$swift->set_from($GLOBALS['GO_CONFIG']->webmaster_email, $GLOBALS['GO_CONFIG']->title);

		$values = '';
		$labels = '';

		if(isset($GLOBALS['GO_MODULES']->modules['customfields']) && $GLOBALS['GO_MODULES']->modules['customfields']['read_permission']) {
			require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
			$cf = new customfields();

			$categories = explode(',',$resource_group['fields']);
			$fields = $cf->get_fields_with_values(1, 1, $resource['id']);

			$cf = array();
			for($j=0; $j<count($fields); $j++) {
				if(in_array('cf_category_'.$fields[$j]['category_id'], $categories) && $fields[$j]['datatype'] == 'checkbox') {
					$labels .= $fields[$j]['name'].': <br />';

					$value = (empty($fields[$j]['value'])) ? $lang['common']['no'] : $lang['common']['yes'];
					$values .= $value.'<br />';
				}
			}
		}

		$body = str_replace(array('{CUSTOM_FIELDS}', '{CUSTOM_VALUES}'), array($labels, $values), $body);
		$swift->set_body($body);

		return $swift->sendmail();
	}

	function get_participants_events($resource_event_id, $skip_event_id=0) {
		$sql = "SELECT * FROM cal_events ".
			"WHERE resource_event_id=".intval($resource_event_id);

		if(!empty($skip_event_id))
			$sql .= " AND id!=".$this->escape($skip_event_id);

		$this->query($sql);
		return $this->num_rows();
	}

	function add_exception_for_all_participants($resource_event_id, $exception) {
		$cal = new calendar();

		$this->get_participants_events($resource_event_id);
		while($event = $this->next_record()) {
			$exception['event_id']=$event['id'];


			$cal->add_exception($exception);
		}

	}


	function search_events(
					$user_id,
					$calendar_id=0,
					$view_id=0,
					$query,
					$start_time,
					$end_time,
					$sort_field='start_time',
					$sort_order='ASC',
					$start,
					$offset) {

		$sql  = "SELECT * FROM cal_events WHERE ";

		if($view_id>0 || $calendar_id==0) {
			if($view_id>0) {
				$calendars = $this->get_view_calendars($view_id);
			}else {
				$calendars = array();
				$this->get_authorized_calendars($user_id);
				while($this->next_record()) {
					$calendars[] = $this->f('id');
				}
			}

			if(!count($calendars)) {
				return false;
			}else {
				foreach($calendars as $calendar) {
					$ids[]=$calendar['id'];
				}
			}
			$sql .= "calendar_id IN (".implode(',', $ids).")";
		}else {
			$sql .= "calendar_id=$calendar_id";
		}

		if ($start_time > 0) {
			$sql .= " AND ((repeat_type='".REPEAT_NONE."' AND (";
			if($end_time>0) {
				$sql .= "start_time<='$end_time' AND ";
			}
			$sql .= "end_time>='$start_time')) OR ".
							"(repeat_type!='".REPEAT_NONE."' AND ";
			if($end_time>0) {
				$sql .= "start_time<='$end_time' AND ";
			}
			$sql .= "(repeat_end_time>='$start_time' OR repeat_forever='1')))";
		}
		$sql .= " AND name LIKE '".$this->escape($query)."'";

		if($sort_field != '' && $sort_order != '') {
			$sql .=	" ORDER BY ".$this->escape($sort_field." ".$sort_order);
		}

		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0) {
			$sql .= " LIMIT ".$this->escape($start,$offset);
			$this->query($sql);

		}
		return $count;
	}

	/*
	 Times in GMT!
	*/

	function get_events(
					$calendars,
					$user_id=0,
					$interval_start=0,
					$interval_end=0,
					$sort_field='start_time',
					$sort_order='ASC',
					$start=0,
					$offset=0,
					$only_busy_events=false,
					$query_field='',
					$query_param='') {
		
		$interval_start=intval($interval_start);
		$interval_end=intval($interval_end);
		$user_id=intval($user_id);

		$sql  = "SELECT e.* FROM cal_events e";

		if($user_id > 0) {
			$sql .= " INNER JOIN cal_calendars c ON (e.calendar_id=c.id)";
		}

		if(!empty($query_param) && substr($query_field,0,4)=='col_'){
			$sql .= " LEFT JOIN cf_1 ON cf_1.link_id=e.id";
		}

		$where=false;

		if($only_busy_events) {
			if($where) {
				$sql .= " AND ";
			}else {
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "busy='1'";
		}


		if($user_id > 0) {
			if($where) {
				$sql .= " AND ";
			}else {
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "c.user_id='$user_id' ";
		}else {
			if($where) {
				$sql .= " AND ";
			}else {
				$sql .= " WHERE ";
				$where=true;
			}
			
			$calendars=array_map('intval', $calendars);
			
			$sql .= "e.calendar_id IN (".implode(',', $calendars).")";
		}


		if ($interval_start > 0) {
			if($where) {
				$sql .= " AND ";
			}else {
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "((e.rrule='' AND (";
			if($interval_end>0) {
				$sql .= "e.start_time<'$interval_end' AND ";
			}
			$sql .= "e.end_time>'$interval_start')) OR ".
							"(e.rrule!='' AND ";
			if($interval_end>0) {
				$sql .= "e.start_time<'$interval_end' AND ";
			}
			$sql .= "(e.repeat_end_time>'$interval_start' OR e.repeat_end_time=0)))";
		}

		//if($sort_field != '' && $sort_order != '') {
			//$sql .=	" ORDER BY ".$this->escape($sort_field." ".$sort_order);
		//}

		if(!empty($query_field)){
			$sql .= " AND $query_field='".$this->escape($query_param)."'";
		}

		if($offset == 0) {
			$this->query($sql);
			return $this->num_rows();
		}else {
			$this->query($sql);
			$count = $this->num_rows();

			$sql .= " LIMIT ".intval($start).",".intval($offset);

			$this->query($sql);

			return $count;
		}
	}

	/**
	 * Returns events that are within the start and end time
	 *
	 * @param <type> $calendars
	 * @param <type> $user_id
	 * @param <type> $interval_start_time
	 * @param <type> $interval_end_time
	 * @param <type> $only_busy_events
	 * @return <type>
	 */

	function get_events_in_array(
					$calendars,
					$user_id,
					$interval_start_time,
					$interval_end_time,
					$only_busy_events=false) {
		$this->events = array();
		$this->events_sort=array();


		if($count = $this->get_events(
		$calendars,
		$user_id,
		$interval_start_time,
		$interval_end_time,
		'start_time','ASC',0,0,$only_busy_events)) {
			while($record=$this->next_record()) {
				//go_debug($record);
				$this->calculate_event($record,
								$interval_start_time,
								$interval_end_time);
			}
		}

		asort($this->events_sort);

		//go_debug($this->events_sort);

		$sorted_events=array();
		foreach($this->events_sort as $key=>$value) {
			$sorted_events[] = &$this->events[$key];
		}
		//go_debug($sorted_events);
		return $sorted_events;
	}

	function calculate_event($event, $interval_start_time, $interval_end_time) {
		global $GO_SECURITY;

		//go_debug('interval: '.date('Ymd G:i', $interval_start_time).' - '.date('Ymd G:i', $interval_end_time));

		if(empty($event['rrule'])) {
			if($event['start_time'] < $interval_end_time && $event['end_time'] > $interval_start_time) {
				$this->events[] = $event;
				$this->events_sort[] = $event['start_time'].$event['name'];
			}
		}else {
			$cal = new calendar();
			$duration = $event['end_time'] - $event['start_time'];
			if($duration == 0) $duration = 3600;
			
			if(!empty($event['all_day_event'])){				
				//For DST offsets
				$duration_days = round($duration/86400);
			}

			$calculated_event=$event;

			$first_occurrence_time=$event['start_time'];
			$start_time=$interval_start_time;


			//calculate the next occurrence from the start_time minus one second because an event
			//may start exactly on the start of display.
			$calculated_event['start_time']=$interval_start_time-1-$duration;

			//go_debug($calculated_event['name'].': '.date('Ymd G:i', $calculated_event['start_time']));

			$last_time = 0;
			$loops = 0;
			while($calculated_event['start_time'] = Date::get_next_recurrence_time($first_occurrence_time, $calculated_event['start_time'], $duration, $event['rrule'])) {
				$loops++;

				if(empty($event['all_day_event'])){
					$calculated_event['end_time'] = $calculated_event['start_time']+$duration;
				}else
				{
					$calculated_event['end_time'] = Date::date_add($calculated_event['start_time'], $duration_days)-60;
				}

				//go_debug($calculated_event['name'].': '.date('Ymd G:i', $calculated_event['start_time']).' - '.date('Ymd G:i', $calculated_event['end_time']));

				
//							echo date('c',$calculated_event['start_time'])."<br>\n";
//			echo date('c', $event['repeat_end_time'])."<br>\n";

				//outside display
				if($calculated_event['start_time'] >= $interval_end_time || $calculated_event['end_time'] <= $interval_start_time || ($calculated_event['repeat_end_time'] && $calculated_event['start_time'] > $event['repeat_end_time']))
				{
					break;
				}

				//same as last
				//if($calculated_event['start_time']==$last_time)
					//break;

				if(!$cal->is_exception($calculated_event['id'],$calculated_event['start_time'])) {
					//go_debug('Adding');
					$this->events[] = $calculated_event;
					$this->events_sort[] = $calculated_event['start_time'].$calculated_event['name'];
				}

				if($loops==100) {					
					throw new Exception('Warning: event looped 100 times '.
									date('Ymd G:i', $calculated_event['start_time']).'  '.
									$calculated_event['name'].' event_id='.$calculated_event['id']);					
				}
			}
		}
	}


	function has_participants_event($resource_event_id, $calendar_id) {
		$sql = "SELECT * FROM cal_events WHERE resource_event_id=? AND calendar_id=?";
		$this->query($sql, 'ii', array($resource_event_id, $calendar_id));
		return $this->next_record();
	}

	function get_event($event_id) {
		$sql = "SELECT e.*, c.acl_id FROM cal_events e LEFT JOIN cal_calendars c ON c.id=e.calendar_id WHERE e.id='".intval($event_id)."'";
		$this->query($sql);
		return $this->next_record(DB_ASSOC);
	}


	/**
	 * The uuid field is a universal unique identifier for external programs.
	 * It's used by CalDAV.
	 * 
	 * @param <type> $uuid
	 * @return <type>
	 */

	function get_event_by_uuid($uuid, $user_id=0, $calendar_id=0, $recurrense_exception_time=0) {		

		if($user_id>0){
			$sql = "SELECT e.*, c.acl_id FROM cal_events e ".
				"LEFT JOIN cal_calendars c ON c.id=e.calendar_id ".
				"WHERE e.uuid='".$this->escape($uuid)."' AND c.user_id=".intval($user_id);
		}else
		{
			$sql = "SELECT e.* FROM cal_events e ".
				"WHERE e.uuid='".$this->escape($uuid)."' AND e.calendar_id=".intval($calendar_id);
		}
		
		if($recurrense_exception_time>0){
			
			$start_of_day = Date::clear_time($recurrense_exception_time);
			$end_of_day = Date::date_add($start_of_day, 1);
			
			$sql .= " AND e.exception_for_event_id>0 AND (e.start_time>=$start_of_day AND e.start_time<$end_of_day)";
		}else
		{
			$sql .= " AND e.exception_for_event_id=0";
		}

		$this->query($sql);
		return $this->next_record(DB_ASSOC);
	}
	
	
	function get_events_by_uuid($uuid, $calendar_id){
		$sql = "SELECT e.* FROM cal_events e ".
			"WHERE e.uuid='".$this->escape($uuid)."' AND e.calendar_id=".intval($calendar_id);
			
		$this->query($sql);
		return $this->num_rows();
	}

	function get_events_for_period($user_id, $start_offset, $days, $index_hour=false) {
		$interval_end = mktime(0, 0, 0, date("m", $start_offset)  , date("d", $start_offset)+$days, date("Y", $start_offset));
		$year = date("Y", $start_offset);
		$month = date("m", $start_offset);
		$day = date("d", $start_offset);

		$events = $this->get_events_in_array(0, 0, $user_id, $start_offset, $interval_end, $day, $month, $year, 0, 'Ymd', $index_hour);

		return $events;
	}


	function delete_event($event_id, $delete_related=true) {
		
		return GO_Calendar_Model_Event::model()->findByPk($event_id)->delete();
		
		
		if($event = $this->get_event($event_id)) {
			$event_id = $this->escape($event_id);

			global $GO_MODULES,$GO_CONFIG;
			if(isset($GLOBALS['GO_MODULES']->modules['files'])) {
				require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
				$files = new files();
				try {
					$files->delete_folder($event['files_folder_id']);
				}
				catch(Exception $e) {

				}
			}


			$sql = "DELETE FROM cal_events WHERE id='$event_id'";
			$this->query($sql);
			$sql = "DELETE FROM cal_participants WHERE event_id='$event_id'";
			$this->query($sql);
			$sql = "DELETE FROM cal_exceptions WHERE event_id='$event_id'";
			$this->query($sql);

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/search.class.inc.php');
			$search = new search();
			$search->delete_search_result($event_id, 1);

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/reminder.class.inc.php');
			$rm = new reminder();
			$rm2 = new reminder();
			$rm->get_reminders_by_link_id($event_id, 1);
			while($r = $rm->next_record()) {
				$rm2->delete_reminder($r['id']);
			}

			if($delete_related && !empty($event_id)) {
				$cal = new calendar();
				$sql = "SELECT id FROM cal_events WHERE resource_event_id=".intval($event_id);
				$cal->query($sql);
				while($cal->next_record()) {
					$this->delete_event($cal->f('id'),false);
				}

				$sql = "SELECT id FROM cal_events WHERE exception_for_event_id=".intval($event_id);
				$cal->query($sql);
				while($cal->next_record()) {
					$this->delete_event($cal->f('id'),false);
				}
			}

			if(isset($GLOBALS['GO_MODULES']->modules['customfields']) && $GLOBALS['GO_MODULES']->modules['customfields']['read_permission']) {
				$this->query("DELETE FROM cf_1 WHERE link_id = ?", 'i', array($event_id));
			}
		}
	}

	function delete_exceptions($event_id) {
		$event_id = $this->escape($event_id);
		$sql = "DELETE FROM cal_exceptions WHERE event_id='$event_id'";
		return $this->query($sql);
	}

	function add_exception($exception) {
		$exception['id'] = $this->nextid('cal_exceptions');
		return $this->insert_row('cal_exceptions', $exception);
	}

	function move_exceptions($event_id, $diff) {
		$event_id = intval($event_id);

		$sql = "UPDATE cal_exceptions SET time=time+".$this->escape($diff)." WHERE event_id=$event_id";
		return $this->query($sql);
	}

	function is_exception($event_id, $time) {
		$sql = "SELECT * FROM cal_exceptions WHERE event_id='".$this->escape($event_id)."' AND time='".$this->escape($time)."'";

		$this->query($sql);
		return $this->next_record();
	}

	function get_exceptions($event_id) {
		$sql = "SELECT * FROM cal_exceptions WHERE event_id='".$this->escape($event_id)."'";

		$this->query($sql);
		return $this->num_rows();
	}



	function get_view_color($view_id, $event_id) {
		$sql = "SELECT cal_views_calendars.background FROM cal_events_calendars ".
		"INNER JOIN cal_views_calendars ON cal_events_calendars.calendar_id=".
		"cal_views_calendars.calendar_id WHERE cal_events_calendars.event_id=".intval($event_id)." AND cal_views_calendars.view_id=".intval($view_id);

		$this->query($sql);
		if($this->num_rows() == 1 && $this->next_record()) {
			return $this->f('background');
		}
		return 'FFFFCC';
	}

	function convert_attendees_to_participants($attendees, $organizer=false)
	{
		go_debug($attendees);
		$participants = array();
		foreach($attendees as $attendee)
		{
			if(isset($attendee['value'])){
				$email = strtolower($attendee['value']);
				$email = (strpos($email, 'mailto') === 0) ? $email = substr($email, 7) : $email;

				if(!isset($participants[$email]))
				{
					$participants[$email] = array();
				}

				$participant['name'] = isset($attendee['params']['CN']) ? $attendee['params']['CN'] : $email;
				$status = isset($attendee['params']['PARTSTAT']) ? $attendee['params']['PARTSTAT'] : 'ACCEPTED';
				$participant['status'] = $this->get_participant_status_id($status);
				$participant['role'] = isset($attendee['params']['ROLE']) ? $attendee['params']['ROLE'] : '';

				if($organizer)
				{
					$participant['is_organizer'] = true;
				}

				$participants[$email] = $participant;
			}
		}
					
		return $participants;
	}

	function get_event_from_ical_object($object) {
		global $GO_MODULES, $GO_CONFIG;

		if(!isset($this->ical2array)) {
			require_once($GLOBALS['GO_CONFIG']->class_path.'ical2array.class.inc');
			$this->ical2array = new ical2array();
		}

		$event['uuid'] = (isset($object['UID']['value']) && $object['UID']['value'] != '') ? trim($object['UID']['value']) : '';

		$event['participants'] = array();
		$attendees = (isset($object['ATTENDEES']) && count($object['ATTENDEES'])) ? $object['ATTENDEES'] : array();
		if(count($attendees))
		{
			$event['participants'] = $this->convert_attendees_to_participants($attendees);
		}

		if(isset($object['DTSTAMP']['value'])){
			$event['mtime']=$this->ical2array->parse_date($object['DTSTAMP']['value']);
		}else
		{
			$event['mtime']=time();
		}

		$organizer = (isset($object['ORGANIZER']) && count($object['ORGANIZER'])) ? $object['ORGANIZER'] : '';
		if($organizer)
		{
			$email = strtolower($organizer['value']);
			$organizer_email = (strpos($email, 'mailto') === 0) ? $email = substr($email, 7) : $email;
			if(array_key_exists($organizer_email, $event['participants']))
			{
				// existing attendee is organizer
				$event['participants'][$organizer_email]['is_organizer']=true;				
			}else
			{
				// set organizer
				$event['participants'] = array_merge($event['participants'], $this->convert_attendees_to_participants(array($organizer), true));
			}
		}

		$event['busy']=true;
		
		$event['sequence'] = (isset($object['SEQUENCE']['value']) && $object['SEQUENCE']['value'] != '') ? trim($object['SEQUENCE']['value']) : 0;

		$event['name'] = (isset($object['SUMMARY']['value']) && $object['SUMMARY']['value'] != '') ? trim($object['SUMMARY']['value']) : '';
		if(isset($object['SUMMARY']['params']['ENCODING']) && $object['SUMMARY']['params']['ENCODING'] == 'QUOTED-PRINTABLE') {
			$event['name'] = quoted_printable_decode($event['name']);
		}
		$event['description'] = isset($object['DESCRIPTION']['value']) ? trim($object['DESCRIPTION']['value']) : '';

		if(isset($object['DESCRIPTION']['params']['ENCODING']) && $object['DESCRIPTION']['params']['ENCODING'] == 'QUOTED-PRINTABLE') {
			$event['description'] = String::trim_lines(quoted_printable_decode($event['description']));
		}
		
		if(empty($event['name']))
		{
			$event['name']=!empty($event['description']) ? substr($event['description'],0,100) : 'Unnamed';
		}

		$event['location'] = isset($object['LOCATION']['value']) ? trim($object['LOCATION']['value']) : '';
		if(isset($object['LOCATION']['params']['ENCODING']) && $object['LOCATION']['params']['ENCODING'] == 'QUOTED-PRINTABLE') {
			$event['location'] = quoted_printable_decode($event['location']);
		}

		$event['status'] = isset($object['STATUS']['value']) ? $object['STATUS']['value'] : 'ACCEPTED';

		$event['all_day_event'] = (isset($object['DTSTART']['params']['VALUE']) &&
										strtoupper($object['DTSTART']['params']['VALUE']) == 'DATE') ? '1' : '0';

		if(isset($object['DTSTART'])) {
			$timezone_id = isset($object['DTSTART']['params']['TZID']) ? $object['DTSTART']['params']['TZID'] : '';
			$event['start_time'] = $this->ical2array->parse_date($object['DTSTART']['value'], $timezone_id);
		}

		if(empty($event['start_time'])) {
			return false;
		}

		if(isset($object['DTEND']['value'])) {
			$timezone_id = isset($object['DTEND']['params']['TZID']) ? $object['DTEND']['params']['TZID'] : '';
			$event['end_time'] = $this->ical2array->parse_date($object['DTEND']['value'],  $timezone_id);

		}elseif(isset($object['DURATION']['value'])) {
			$duration = $this->ical2array->parse_duration($object['DURATION']['value']);
			$event['end_time'] = $event['start_time']+$duration;

		}elseif(isset($object['DUE']['value'])) {
			$timezone_id = isset($object['DUE']['params']['TZID']) ? $object['DUE']['params']['TZID'] : '';
			$event['end_time'] = $this->ical2array->parse_date($object['DUE']['value'],  $timezone_id);
		}

		if($event['all_day_event']=='1') {
			$event['end_time']-=60;
		}

		//reminder
		if(isset($object['DALARM']['value'])) {
			$dalarm = explode(';', $object['DALARM']['value']);
			if(isset($dalarm[0]) && $remind_time = $this->ical2array->parse_date($dalarm[0])) {
				$event['reminder'] = $event['start_time']-$remind_time;
			}
		}

		if(!isset($event['reminder']) && isset($object['AALARM']['value'])) {
			$aalarm = explode(';', $object['AALARM']['value']);
			if(isset($aalarm[0]) && $remind_time = $this->ical2array->parse_date($aalarm[0])) {
				$event['reminder'] = $event['start_time']-$remind_time;
			}
		}

		/*
		 * ["TRIGGER"]=>
            array(2) {
              ["params"]=>
              array(2) {
                ["VALUE"]=>
                string(8) "DURATION"
                ["RELATED"]=>
                string(5) "START"
              }
              ["value"]=>
              string(6) "-PT15M"

		 */

		if(isset($object['objects'])) {
			foreach($object['objects'] as $o){
				if($o['type']=='VALARM'){
					if(isset($o['TRIGGER'])){
						//$offset_time = isset($o['TRIGGER']['RELATED']) && $o['TRIGGER']["RELATED"]=='END' ? $event['end_time'] : $event['start_time'];
						if(!isset($o['TRIGGER']['params']['VALUE']) || $o['TRIGGER']['params']['VALUE']=='DURATION'){
							$offset = $this->ical2array->parse_duration($o['TRIGGER']['value']);

							$event['reminder']=$offset*-1;
						}else
						{
							$time = $this->ical2array->parse_date($o['TRIGGER']['value']);
							$event['reminder']=$event['start_time']-$time;
						}

					}
				}
			}
		}


		if(isset($event['reminder']) && $event['reminder']<0) {
			//If we have a negative reminder value default to half an hour before
			$event['reminder'] = 1800;
		}

		if($event['name'] != '')// && $event['start_time'] > 0 && $event['end_time'] > 0)
		{			
			//$event['all_day_event'] = (isset($object['DTSTART']['params']['VALUE']) &&
			//strtoupper($object['DTSTART']['params']['VALUE']) == 'DATE') ? true : false;

			//for Nokia. It doesn't send all day event in any way. If the local times are equal and the
			//time is 0:00 hour then this is probably an all day event.

			$end_hour = date('G', $event['end_time']);

			if($event['end_time'] == $event['start_time'] || (($end_hour==23 || $end_hour==0) && date('G', $event['start_time'])==0)) {
				$event['all_day_event'] = '1';

				//make sure times are 0 - 23

				$start_date = getdate($event['start_time']);
				$end_date = getdate($event['end_time']-60);

				$event['start_time']=mktime(0,0,0,$start_date['mon'], $start_date['mday'], $start_date['year']);
				$event['end_time']=mktime(23,59,0,$end_date['mon'], $end_date['mday'], $end_date['year']);
			}

			if(isset($object['CLASS']['value']) && $object['CLASS']['value'] == 'PRIVATE') {
				$event['private'] = '1';
			}else {
				$event['private']= '0';
			}


			$event['rrule'] = '';
			$event['repeat_end_time'] = 0;


			if (!empty($object['RRULE']['value']) && $rrule = $this->ical2array->parse_rrule($object['RRULE']['value'])) {

				$event['rrule'] = 'RRULE:'.$object['RRULE']['value'];
				

				if(isset($rrule['BYDAY'])) {

					$month_time=1;
					if($rrule['FREQ']=='MONTHLY') {
						if(!isset($rrule['BYSETPOS'])){
							$month_time = $rrule['BYDAY'][0];
							$day = substr($rrule['BYDAY'], 1);
						}else
						{
							$month_time = $rrule['BYSETPOS'];
							$day = $rrule['BYDAY'];
						}
						$days_arr =array($day);
					}else {
						$days_arr = explode(',', $rrule['BYDAY']);
					}

					$days['sun'] = in_array('SU', $days_arr) ? '1' : '0';
					$days['mon'] = in_array('MO', $days_arr) ? '1' : '0';
					$days['tue'] = in_array('TU', $days_arr) ? '1' : '0';
					$days['wed'] = in_array('WE', $days_arr) ? '1' : '0';
					$days['thu'] = in_array('TH', $days_arr) ? '1' : '0';
					$days['fri'] = in_array('FR', $days_arr) ? '1' : '0';
					$days['sat'] = in_array('SA', $days_arr) ? '1' : '0';


					$days=Date::shift_days_to_gmt($days, date('G', $event['start_time']), Date::get_timezone_offset($event['start_time']));

					
				}
				
				
				
				if (isset($rrule['UNTIL'])) {
					if($event['repeat_end_time'] = $this->ical2array->parse_date($rrule['UNTIL'])) {
						$event['repeat_end_time'] = mktime(0,0,0, date('n', $event['repeat_end_time']), date('j', $event['repeat_end_time'])+1, date('Y', $event['repeat_end_time']));
					}
				}elseif(!empty($rrule['COUNT']))
				{
					$event_count = $rrule['COUNT'];

					//figure out end time of event

					$event['repeat_end_time']='0';
					$start_time=$event['start_time'];
					for($i=1;$i<$event_count;$i++) {
						$event['repeat_end_time']=$start_time=Date::get_next_recurrence_time($event['start_time'], $start_time, $event['end_time']-$event['start_time'],$event['rrule']);
					}
					if($event['repeat_end_time']>0) {
						$event['repeat_end_time']+=$event['end_time']-$event['start_time'];
					}
					
				}
				
				if(isset($rrule['BYDAY'])) 
					$event['rrule']=Date::build_rrule(Date::ical_freq_to_repeat_type($rrule), $rrule['INTERVAL'], $event['repeat_end_time'], $days, $month_time);
			}
			
			
			




			if(isset($object['EXDATE']['value'])) {
				$exception_dates = explode(';', $object['EXDATE']['value']);
				foreach($exception_dates as $exception_date) {
					$exception_time = $this->ical2array->parse_date($exception_date);
					if($exception_time>0) {
						$event['exceptions'][] = $exception_time;
					}
				}
			}

			
			
		

			return $event;
		}
		return false;
	}

	function get_event_from_ical_file($ical_file) {
		global $GO_CONFIG;

		require_once($GLOBALS['GO_CONFIG']->class_path.'ical2array.class.inc');
		$this->ical2array = new ical2array();

		$vcalendar = $this->ical2array->parse_file($ical_file);

		while($object = array_shift($vcalendar[0]['objects'])) {
			if($object['type'] == 'VEVENT' || $object['type'] == 'VTODO') {
				if($event = $this->get_event_from_ical_object($object)) {
					return $event;
				}
			}
		}
		return false;
	}

	function get_event_from_ical_string($ical_string, $multiple=false, &$vcalendar_objects=array()){
		global $GO_MODULES, $GO_CONFIG;

		$count=0;

		require_once($GLOBALS['GO_CONFIG']->class_path.'ical2array.class.inc');
		$this->ical2array = new ical2array();

		$vcalendar = $this->ical2array->parse_string($ical_string);
		
		$events = array();

		if(isset($vcalendar[0]['objects'])) {
			while($object = array_shift($vcalendar[0]['objects'])) {
				if($object['type'] == 'VEVENT') {
					
					$vcalendar_objects[]=$object;
						
					if($event = $this->get_event_from_ical_object($object)) {
						if($multiple)
							$events[]=$event;
						else
							return $event;
					}
				}
			}
		}
		
		if($multiple)
			return $events;
		else
			return false;
	}


	function import_ical_string($ical_string, $calendar_id=-1) {
		global $GO_MODULES, $GO_CONFIG;

		$count=0;

		require_once($GLOBALS['GO_CONFIG']->class_path.'ical2array.class.inc');
		$this->ical2array = new ical2array();

		$vcalendar = $this->ical2array->parse_string($ical_string);

		if(isset($vcalendar[0]['objects'])) {
			while($object = array_shift($vcalendar[0]['objects'])) {
				if($object['type'] == 'VEVENT') {
					if($event = $this->get_event_from_ical_object($object)) {
						if ($calendar_id != -1) {
							$event['calendar_id']=$calendar_id;
							if ($event_id = $this->add_event($event)) {
								$count++;
							}
						}
					}
				}
			}
		}
		return $count;
	}


	//TODO: attendee support
	function import_ical_file($ical_file, $calendar_id) {
		$data = file_get_contents($ical_file);
		return $this->import_ical_string($data, $calendar_id);
	}

	function get_conflicts($start_time, $end_time, $calendars) {
		$conflicts = array();
		$cal_events = $this->get_events_in_array($calendars, 0, $start_time, $end_time, false);
		foreach($cal_events as $event) {
			$conflicts[$event['id']]=$event;
		}

		return $conflicts;
	}


	function user_delete($user) {
		$cal = new calendar();

		$delete = new calendar();
		$sql = "SELECT * FROM cal_calendars WHERE user_id='".$cal->escape($user['id'])."'";
		$cal->query($sql);
		while($cal->next_record()) {
			$delete->delete_calendar($cal->f('id'));
		}
		
		$sql = "DELETE FROM cal_calendar_user_colors WHERE user_id=".$cal->escape($user['id']);
		$cal->query($sql);

		$sql = "DELETE FROM cal_settings WHERE user_id=".$cal->escape($user['id']);
		$cal->query($sql);


		$cal->get_user_views($user['id']);

		while($cal->next_record()) {
			$delete->delete_view($cal->f('id'));
		}

	}

	public static function add_user($user) {
		global $GO_SECURITY, $GO_LANGUAGE, $GO_CONFIG, $GO_MODULES;

		$cal2 = new calendar();

		$cal = new calendar();

		//$calendar['name']=String::format_name($user,'','','last_name');
		//$calendar['user_id']=$user['id'];
		//$calendar['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('calendar', $user['id']);

		$calendar = $cal->get_default_calendar($user['id']);


		$GLOBALS['GO_SECURITY']->add_group_to_acl($GLOBALS['GO_CONFIG']->group_internal, $calendar['acl_id'],2);

		//$calendar_id = $cal->add_calendar($calendar);

		require($GLOBALS['GO_LANGUAGE']->get_language_file('calendar'));

		$sql = "SELECT * FROM cal_views WHERE name LIKE '".$cal->escape($lang['calendar']['groupView'])."'";
		$cal->query($sql);
		if($cal->next_record()) {
			$view_id = $cal->f('id');

			$count = $cal2->get_view_calendars($view_id);

			if($count<=20)
				$cal2->add_calendar_to_view($calendar['id'], '', $view_id);
		}

		if(isset($GLOBALS['GO_MODULES']->modules['summary'])) {
			$cal2->add_visible_calendar(array('user_id'=>$user['id'], 'calendar_id'=>$calendar['id']));
		}

	}



	function cache_event($event_id) {
		global $GO_CONFIG, $GO_LANGUAGE, $lang;

		require_once($GLOBALS['GO_CONFIG']->class_path.'/base/search.class.inc.php');
		$search = new search();

		$GLOBALS['GO_LANGUAGE']->require_language_file('calendar');

		$sql  = "SELECT DISTINCT cal_events.*, cal_calendars.acl_id FROM cal_events ".
						"INNER JOIN cal_calendars ON cal_events.calendar_id=cal_calendars.id ".
						"WHERE cal_events.id=?";

		$this->query($sql, 'i', $event_id);
		$record = $this->next_record();
		if($record) {
			if(!empty($record['private'])) {
				unset($record['description']);
				$record['name']=$lang['calendar']['private'];
			}

			$cache['id']=$this->f('id');
			$cache['user_id']=$this->f('user_id');
			$cache['name'] = htmlspecialchars($record['name'].' ('.Date::get_timestamp($this->f('start_time'), true).')', ENT_QUOTES, 'utf-8');
			$cache['link_type']=1;
			$cache['module']='calendar';
			$cache['description']='';
			$cache['type']=$lang['link_type'][1];
			$cache['keywords']=$search->record_to_keywords($record).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			$cache['acl_id']=$this->f('acl_id');

			$search->cache_search_result($cache);
		}
	}
	public function build_search_index() {
		$cal = new calendar();
		$cal2 = new calendar();
		$sql = "SELECT id FROM cal_events";
		$cal->query($sql);

		while($record = $cal->next_record()) {
			$cal2->cache_event($record['id']);
		}
		/* {ON_BUILD_SEARCH_INDEX_FUNCTION} */
	}



	function is_available($user_id, $start, $end, $ignore_event=false) {
		$events = $this->get_events_in_array(array(), $user_id, $start, $end, true);

		if($ignore_event) {
			$newevents=array();
			foreach($events as $event) {
				if($event['id']!=$ignore_event['id'] && $event['uuid']!=$ignore_event['uuid']) {
					$newevents[]=$event;
				}
			}
			$events = $newevents;
		}

		return count($events) > 0 ? false : true;
	}


	function get_free_busy($user_id, $date, $ignore_event_id=0) {
		$date=getdate($date);

		$daystart = mktime(0,0,0,$date['mon'], $date['mday'], $date['year']);
		$dayend = mktime(0,0,0,$date['mon'], $date['mday']+1, $date['year']);

		$freebusy=array();
		for($i=0;$i<1440;$i+=15) {
			$freebusy[$i]=0;
		}

		$events = $this->get_events_in_array(array(), $user_id, $daystart, $dayend, true);



		foreach($events as $event) {
			if($event['id']!=$ignore_event_id) {
				if($event['end_time'] > $dayend) {
					$event['end_time']=$dayend;
				}

				if($event['start_time'] < $daystart) {
					$event['start_time']=$daystart;
				}
				$event_start = getdate($event['start_time']);
				$event_end = getdate($event['end_time']);

				if($event_start['minutes']<15) {
					$minutes=0;
				}elseif($event_start['minutes']<30) {
					$minutes=15;
				}elseif($event_start['minutes']<45) {
					$minutes=30;
				}else {
					$minutes=45;
				}

				$start_minutes = $minutes+($event_start['hours']*60);
				$end_minutes = $event_end['minutes']+($event_end['hours']*60);

				for($i=$start_minutes;$i<$end_minutes;$i+=15) {
					$freebusy[$i]=1;
				}
			}
		}
		return $freebusy;

	}

	function clear_event_status($event_id, $accepted_email) {
		$sql = "UPDATE cal_participants SET status='0' WHERE email!='".$this->escape($accepted_email)."' AND event_id='".$this->escape($event_id)."'";
		return $this->query($sql);
	}


	function set_event_status($event_id, $status, $email, $last_modified=0) {
		$sql = "UPDATE cal_participants SET status='".$this->escape($status)."'";
		if($last_modified)
		{
			$sql .= ", last_modified='".$this->escape($last_modified)."'";
		}
		$sql .= " WHERE email='".$this->escape($email)."' AND event_id='".$this->escape($event_id)."'";
		return $this->query($sql);
	}

	function get_event_status($event_id, $email) {
		$sql = "SELECT status FROM cal_participants WHERE email='".$this->escape($email)."' AND event_id='".$this->escape($event_id)."'";
		if($this->query($sql)) {
			if($this->next_record()) {
				return $this->f('status');
			}
		}
		return false;
	}


	/**
	 * Add a Group
	 *
	 * @param Array $group Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_group($group) {
		if(!$group['id'])
			$group['id']=$this->nextid('cal_groups');

		if($this->insert_row('cal_groups', $group)) {
			return $group['id'];
		}
		return false;
	}
	/**
	 * Update a Group
	 *
	 * @param Array $group Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_group($group) {
		$r = $this->update_row('cal_groups', 'id', $group);
		return $r;
	}
	/**
	 * Delete a Group
	 *
	 * @param Int $group_id ID of the group
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_group($group_id) {
		return $this->query("DELETE FROM cal_groups WHERE id=?", 'i', $group_id);
	}
	/**
	 * Gets a Groups record
	 *
	 * @param Int $group_id ID of the group
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_group($group_id) {
		$this->query("SELECT * FROM cal_groups WHERE id=?", 'i', $group_id);
		return $this->next_record();
	}
	/**
	 * Gets a Group record by the name field
	 *
	 * @param String $name Name of the group
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_group_by_name($name) {
		$this->query("SELECT * FROM cal_groups WHERE name=?", 's', $name);
		return $this->next_record();
	}
	/**
	 * Gets all Groups
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_groups($sortfield='name', $sortorder='ASC', $start=0, $offset=0, $hide_cal=1) {
		$sql = "SELECT ";
		if($offset>0) {
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}
		$sql .= "* FROM cal_groups ";
		if($hide_cal) {
			$sql .= "WHERE id > 1";
		}

		$sql .= " ORDER BY ".$this->escape($sortfield.' '.$sortorder);
		if($offset>0) {
			$sql .= " LIMIT ".intval($start).",".intval($offset);
		}
		$this->query($sql);
		return $offset>0 ? $this->found_rows() : $this->num_rows();
	}
	/**
	 * Gets all Event Resources
	 *
	 * @param Int $event_id ID of the event
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_event_resources($event_id) {
		if($event_id>0) {
			$sql = "SELECT cal_events.* FROM cal_events WHERE resource_event_id ='$event_id' AND id!='$event_id'";
			$this->query($sql);
			return $this->num_rows();
		}
		return false;
	}
	/**
	 * Gets Event Resource
	 *
	 * @param Int $event_id ID of the event
	 * @param Int $calendar_id ID of the calendar
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_event_resource($event_id, $calendar_id) {
		if($event_id>0 && $calendar_id>0) {
			$sql = "SELECT cal_events.* FROM cal_events WHERE resource_event_id='".intval($event_id)."' AND calendar_id='".intval($calendar_id)."'";

			$this->query($sql);
			if($this->next_record()) {
				return $this->record;
			}
		}
		return false;
	}

	function get_bdays($start_time,$end_time,$abooks=array()) {
		global $response;

		$start = date('Y-m-d',$start_time);
		$end = date('Y-m-d',$end_time);

		$sql = "SELECT DISTINCT id, birthday, first_name, middle_name, last_name, "
			."IF (STR_TO_DATE(CONCAT(YEAR('$start'),'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e') >= '$start', "
			."STR_TO_DATE(CONCAT(YEAR('$start'),'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e') , "
			."STR_TO_DATE(CONCAT(YEAR('$start')+1,'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e')) "
			."as upcoming FROM ab_contacts "
			."WHERE birthday != '0000-00-00' ";

		if(count($abooks)) {
			
			$abooks=array_map('intval', $abooks);
			
			$sql .= "AND addressbook_id IN (".implode(',', $abooks).") ";
		}

		$sql .= "HAVING upcoming BETWEEN '$start' AND '$end' ORDER BY upcoming";

		$this->query($sql);//, 'ssssss', array($start,$start,$start,$start,$start,$end));

		return $this->num_rows();
	}


	/**
	 * When a an item gets deleted in a panel with links. Group-Office attempts
	 * to delete the item by finding the associated module class and this function
	 *
	 * @param int $id The id of the linked item
	 * @param int $link_type The link type of the item. See /classes/base/links.class.inc
	 */

	function __on_delete_link($id, $link_type) {
		//echo $id.':'.$link_type;
		if($link_type==1) {
			return $this->delete_event($id);
		}
	}


	function event_to_json_response($event) {

		global $GO_CONFIG;
		require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		if(!empty($event['user_id'])) {
			$event['user_name']=$GO_USERS->get_user_realname($event['user_id']);
		}

		//for IE
		if(empty($event['background']))
			$event['background']='EBF1E2';

		$event['subject']=$event['name'];

		$start_time = $event['start_time'];
		$end_time = $event['end_time'];

		$event['start_date']=date($_SESSION['GO_SESSION']['date_format'], $start_time);
		$event['start_time'] = date($_SESSION['GO_SESSION']['time_format'], $start_time);

		$event['end_date']=date($_SESSION['GO_SESSION']['date_format'], $end_time);
		$event['end_time'] = date($_SESSION['GO_SESSION']['time_format'], $end_time);

		$event['repeat_every'] = 1;
		$event['repeat_forever'] = 0;
		$event['repeat_type'] = REPEAT_NONE;
		$event['repeat_end_time'] = 0;
		$event['month_time'] = 1;

		if(!isset($event['category_id']))
			$event['category_id']='';
		
		if($event['category_id']==0)
			$event['category_id']='';

		$ical2array = new ical2array();
		if (!empty($event['rrule']) && $rrule = $ical2array->parse_rrule($event['rrule']))
		{
			if(isset($rrule['FREQ']))
			{
				if (isset($rrule['UNTIL']))
				{
					$event['repeat_end_time'] = $ical2array->parse_date($rrule['UNTIL']);
				}elseif(isset($rrule['COUNT'])) {
					//go doesn't support this
				}else {
					$event['repeat_forever'] = 1;
				}

				$event['repeat_every'] = $rrule['INTERVAL'];
				switch($rrule['FREQ']) {
					case 'DAILY':
						$event['repeat_type'] = REPEAT_DAILY;
						break;

					case 'WEEKLY':
						$event['repeat_type'] = REPEAT_WEEKLY;



						if(empty($rrule['BYDAY'])){

							$day_db_field[0] = 'sun';
							$day_db_field[1] = 'mon';
							$day_db_field[2] = 'tue';
							$day_db_field[3] = 'wed';
							$day_db_field[4] = 'thu';
							$day_db_field[5] = 'fri';
							$day_db_field[6] = 'sat';

							$days=array(
								'mon'=>0,
								'tue'=>0,
								'wed'=>0,
								'thu'=>0,
								'fri'=>0,
								'sat'=>0,
								'sun'=>0
							);

							$days[$day_db_field[date('w',$start_time)]]='1';
						}else
						{
							$days = Date::byday_to_days($rrule['BYDAY']);
							$days = Date::shift_days_to_local($days, date('G', $start_time), Date::get_timezone_offset($start_time));
						}

						$event['repeat_days_0'] = $days['sun'];
						$event['repeat_days_1'] = $days['mon'];
						$event['repeat_days_2'] = $days['tue'];
						$event['repeat_days_3'] = $days['wed'];
						$event['repeat_days_4'] = $days['thu'];
						$event['repeat_days_5'] = $days['fri'];
						$event['repeat_days_6'] = $days['sat'];
						break;

					case 'MONTHLY':
						if (isset($rrule['BYDAY'])) {
							$event['repeat_type'] = REPEAT_MONTH_DAY;

							$event['month_time'] = $rrule['BYDAY'][0];
							$day = substr($rrule['BYDAY'], 1);

							$days = Date::byday_to_days($day);

							$days = Date::shift_days_to_local($days, date('G', $start_time), Date::get_timezone_offset($start_time));

							$event['repeat_days_0'] = $days['sun'];
							$event['repeat_days_1'] = $days['mon'];
							$event['repeat_days_2'] = $days['tue'];
							$event['repeat_days_3'] = $days['wed'];
							$event['repeat_days_4'] = $days['thu'];
							$event['repeat_days_5'] = $days['fri'];
							$event['repeat_days_6'] = $days['sat'];

						}else {
							$event['repeat_type'] = REPEAT_MONTH_DATE;
						}
						break;

					case 'YEARLY':
						$event['repeat_type'] = REPEAT_YEARLY;
						break;
				}
			}
		}

		$event['repeat_end_date']=$event['repeat_end_time']>0 ? date($_SESSION['GO_SESSION']['date_format'], $event['repeat_end_time']) : '';

		if(isset($event['reminder'])) {
			$event = array_merge($event, $this->reminder_seconds_to_form_input($event['reminder']));
		}
		return $event;
	}

	public function get_visible_calendars($user_id) {
		$this->query("SELECT * FROM su_visible_calendars WHERE user_id = ".intval($user_id));
		return $this->num_rows();
	}

	public function add_visible_calendar($calendar) {
		if($this->replace_row('su_visible_calendars', $calendar)) {
			return $this->insert_id();
		}
		return false;
	}

	public function delete_visible_calendar($calendar_id, $user_id) {
		$this->query("DELETE FROM su_visible_calendars WHERE calendar_id = $calendar_id AND user_id = $user_id");
	}

	public function get_group_admins($group_id) {
		$this->query("SELECT user_id FROM cal_group_admins WHERE group_id=?", 'i', $group_id);
		return $this->num_rows();
	}

	public function group_admin_exists($group_id, $user_id) {
		$this->query("SELECT user_id FROM cal_group_admins WHERE group_id=? AND user_id=?", 'ii', array($group_id, $user_id));
		return ($this->num_rows() > 0) ? true : false;
	}

	public function add_group_admin($group_admin) {
		global $GO_SECURITY;

		$this->get_calendars_by_group_id($group_admin['group_id']);
		while($calendar = $this->next_record()) {
			$GLOBALS['GO_SECURITY']->add_user_to_acl($group_admin['user_id'], $calendar['acl_id'], GO_SECURITY::MANAGE_PERMISSION);
		}

		return $this->insert_row('cal_group_admins', $group_admin);
	}

	public function delete_group_admin($group_id, $user_id) {
		return $this->query("DELETE FROM cal_group_admins WHERE group_id=? AND user_id=?" , 'ii', array($group_id, $user_id));
	}

	public function get_visible_tasklists($calendars)
	{
		if(!is_array($calendars))
			$calendars = array($calendars);
		
		$calendars=array_map('intval', $calendars);
		
		$this->query("SELECT DISTINCT tasklist_id FROM cal_visible_tasklists WHERE calendar_id IN (".implode(',', $calendars).")");
		return $this->num_rows();
	}

	public function add_visible_tasklist($tasklist) {
		return $this->replace_row('cal_visible_tasklists', $tasklist);
	}

	public function delete_visible_tasklist($calendar_id, $tasklist_id) {
		return $this->query("DELETE FROM cal_visible_tasklists WHERE calendar_id = ? AND tasklist_id = ?", 'ii', array($calendar_id, $tasklist_id));
	}

	/**
	 * Add a Category
	 *
	 * @param Array $category Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_category($category)
	{
		if(!$category['id'])
			$category['id']=$this->nextid('cal_categories');

		if($this->insert_row('cal_categories', $category)) {
			return $category['id'];
		}
		return false;
	}
	/**
	 * Update a Category
	 *
	 * @param Array $category Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_category($category)
	{
		
		
		$r = $this->update_row('cal_categories', 'id', $category);
		
		if($r && isset($category['color'])){
			$sql = "UPDATE cal_events SET background='".$this->escape($category['color'])."' WHERE category_id=".intval($category['id']);
			$this->query($sql);
		}
		return $r;
	}
	/**
	 * Delete a Category
	 *
	 * @param Int $category_id ID of the category
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_category($category_id)
	{
		return $this->query("DELETE FROM cal_categories WHERE id=?", 'i', $category_id);
	}
	/**
	 * Gets a Categories record
	 *
	 * @param Int $category_id ID of the category
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_category($category_id)
	{
		$this->query("SELECT * FROM cal_categories WHERE id=?", 'i', $category_id);
		return $this->next_record();
	}
	/**
	 * Gets all Categories
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_categories($sortfield='name', $sortorder='ASC', $start=0, $offset=0, $user_id=0)
	{
		global $GO_SECURITY;

		$user_id = !empty($user_id) ? $user_id : $GLOBALS['GO_SECURITY']->user_id;

		$sql = "SELECT ";
		if($offset>0) {
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}
		$sql .= "* FROM cal_categories "
			. "WHERE user_id = 0 OR user_id = ? "
			. "ORDER BY ".$this->escape($sortfield.' '.$sortorder);
		if($offset>0) {
			$sql .= " LIMIT ".intval($start).",".intval($offset);
		}

		$this->query($sql, 'i', $user_id);
		return $offset>0 ? $this->found_rows() : $this->num_rows();
	}

	function get_participant_status_id($status_name)
	{
		$statuses = array(
			'NEEDS-ACTION' => 0,
			'ACCEPTED' => 1,
			'DECLINED' => 2,
			'TENTATIVE' => 3
		);

		return $statuses[$status_name];
	}

	function get_participant_status_name($status_id)
	{
		$statuses = array(
			0 => 'NEEDS-ACTION',
			1 => 'ACCEPTED',
			2 => 'DECLINED',
			3 => 'TENTATIVE'
		);

		return $statuses[$status_id];
	}

	function add_declined_event_uid($declined_event)
	{
		return $this->insert_row('cal_events_declined',$declined_event);
	}
	
	function delete_declined_event_uid($uid, $email)
	{
		$sql = "DELETE FROM cal_events_declined WHERE uid='".$this->escape($uid)."' AND email='".$this->escape($email)."'";
		return $this->query($sql);
	}

	function is_event_declined($uid, $email)
	{
		$this->query("SELECT * FROM cal_events_declined WHERE uid='".$this->escape($uid)."' AND email='".$this->escape($email)."'");
		return ($this->num_rows() > 0) ? true : false;
	}

	function update_event_sequence($event_id, $sequence)
	{
		$sql = "UPDATE cal_events SET sequence='".$this->escape($sequence)."' WHERE id='".intval($event_id)."'";
		return $this->query($sql);
	}


	function get_linked_events($user_id, $link_id, $link_type){
		
		$model = get_model_by_type_id($link_type);
		
		
		$sql = "SELECT DISTINCT e.*, c.name AS calendar_name FROM cal_events e ".
			"INNER JOIN cal_calendars c ON c.id=e.calendar_id ".
			"INNER JOIN go_links_{$model->tableName()} l ON l.id=e.id AND l.model_type_id=1 ".
			"WHERE l.id=? AND start_time>".time()." ORDER BY start_time ASC";

		$this->query($sql, 'i', array($link_id));
	}

	function get_linked_events_json($link_id, $link_type){
		global $GO_SECURITY, $GO_CONFIG;

		require_once($GLOBALS['GO_CONFIG']->class_path.'base/links.class.inc.php');
		$GO_LINKS = new GO_LINKS();

		$records=array();

		$this->get_linked_events($GLOBALS['GO_SECURITY']->user_id, $link_id, $link_type);
		while($e=$this->next_record()){
			$e['link_count']=$GO_LINKS->count_links($e['id'], 1);
			$e['start_time']=Date::get_timestamp($e['start_time']);
			$records[]=$e;
		}

		return $records;
	}


	function get_calendars_json(&$response, $resources=false, $project_calendars=false){

		global $GO_SECURITY;

		$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : 0;
		$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : 0;
		$query = !empty($_REQUEST['query']) ? '%'.$_REQUEST['query'].'%' : '';
		
		$response['total'] = $this->get_authorized_calendars($GLOBALS['GO_SECURITY']->user_id, $start, $limit, $resources, 1, $project_calendars, $query);
		if($response['total']==0 && $resources==false && $project_calendars==false && empty($query)){
			$dc = $this->get_default_calendar($GLOBALS['GO_SECURITY']->user_id);
			$response['total'] = $this->get_authorized_calendars($GLOBALS['GO_SECURITY']->user_id, $start, $limit, $resources, 1, $project_calendars);
		}

		$response['results']=array();

		$cal2 = new calendar();

		while($record =$this->next_record(DB_ASSOC)) {

			$group = $cal2->get_group($record['group_id']);
			$record['group_name'] = $group['name'];
			$record['comment']=nl2br($record['comment']);
			$response['results'][] = $record;
		}
	}

	function get_views_json(&$response){

		global $GO_SECURITY, $GO_CONFIG;

		require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		$response['total'] = $this->get_authorized_views($GLOBALS['GO_SECURITY']->user_id);
		$response['results']=array();
		while($record= $this->next_record(DB_ASSOC)) {
			$record['user_name'] = $GO_USERS->get_user_realname($record['user_id']);
			$response['results'][] = $record;
		}
	}
	
	/* Function that MUST be used within a loop over events, that merges events in
	 * the output event array $chosen_events if the input events (each passed as
	 * $current_event) have the same uuid (the uuids are maintained in $uuid_array).
	 *
	 * $event_nr is the current event index over $chosen_events, and is to be
	 * incremented within the loop after calling this function.
	 *
	 * The function returns true to let the calling loop know to continue, specifically
	 * when an merged event shouldn't be put into the output array. False otherwise.
	 *
	 * See modules/calendar/json.php task 'events' for example of using this
	 */
	public function merge_events(&$chosen_events,&$current_event,&$uuid_array,$event_nr,$calendar_names,$GO_USERS=null) {
		global $lang;
		if (empty($GO_USERS)) global $GO_USERS;

		if (empty($current_event['uuid'])) return false;
		
		//append start_time for recurring events.
		$merge_index = $current_event['uuid'].'-'.$current_event['start_time'];
		
		
		if (array_key_exists($merge_index,$uuid_array)) {
			
			$uuid_array[$merge_index][] = $event_nr;
			if (count($uuid_array[$merge_index])==2) {
				$merged_event_nr = $uuid_array[$merge_index][0];
				
				$chosen_events[$merged_event_nr]['background'] = 'FFFFFF';
				$chosen_events[$merged_event_nr]['username'] = '';//$lang['calendar']['non_selected'];
				
				$name_exploded = explode('(',$chosen_events[$merged_event_nr]['name']);
				if (count($name_exploded)>1) array_pop($name_exploded);
				$chosen_events[$merged_event_nr]['name'] = implode('(',$name_exploded);
				$chosen_events[$merged_event_nr]['name'] .= ' ('.String::get_first_letters($calendar_names[$chosen_events[$merged_event_nr]['calendar_id']]).')';
				
				$chosen_events[$merged_event_nr]['read_only']=true;
			}
			if (count($uuid_array[$merge_index])>=2) {
				$merged_event_nr = $uuid_array[$merge_index][0];
				
				$chosen_events[$merged_event_nr]['calendar_name'] .= '; '.$calendar_names[$current_event['calendar_id']];
				$chosen_events[$merged_event_nr]['name'] = substr($chosen_events[$merged_event_nr]['name'],0,-1);
				$chosen_events[$merged_event_nr]['name'] .= ','.String::get_first_letters($calendar_names[$current_event['calendar_id']]).')';
				
				$chosen_events[$merged_event_nr]['read_only']=true;
				//$chosen_events[$merged_event_nr]['name'] .= ', '.$participating_calendar['name'];
				//if ($current_event['invitation_uuid']=='') {
					//$chosen_events[$merged_event_nr]['username'] = $GO_USERS->get_user_realname($current_event['user_id']);
					//$chosen_events[$merged_event_nr]['num_participants']++;
				//}
				return true;
			}
		} else {
			$uuid_array[$merge_index] = array($event_nr);
		}
		return false;
	}


/*
 * (optional)param $new_added = array of participant id's that are newly added to this event.
 */
	function send_invitation($event, $calendar, $insert=true,$new_added = false){
		global $GO_CONFIG, $GO_MODULES, $lang, $GO_LANGUAGE, $GO_SECURITY;
		
		go_debug("send_invitation");

		$GLOBALS['GO_LANGUAGE']->require_language_file('calendar');
		
		require_once($GLOBALS['GO_CONFIG']->class_path.'mail/GoSwift.class.inc.php');

		$RFC822 = new RFC822();
		//$event['id']=empty($event['resource_event_id']) ? $event_id : $event['resource_event_id'];
		//go_debug($event['id']);
		if(!$insert){
			//if this is an update to the event reset the accepted status of everyone except for the logged in user and the calendar user this event is saved in.
			$sql = "UPDATE cal_participants SET status='0' WHERE user_id!=? AND user_id!=? AND event_id=?";
			$this->query($sql,'iii', array($GLOBALS['GO_SECURITY']->user_id, $calendar['user_id'], $event['id']));
		}
			
		$participants=array();
		$this->get_participants($event['id']);
		while($p = $this->next_record()) {
			//don't send invitation to the user that is doing this and don't send
			//it to the user of the calendar in which this event is created.
			if($this->f('status') !=1 || ($this->f('email')!=$_SESSION['GO_SESSION']['email'] && $calendar['user_id']!=$this->f('user_id'))) {

        // Also don't send invitation to users that allready had one.
        // TODO: find out which ones are new users
        if($new_added === false || in_array($this->f('id'), $new_added))
          $participants[] = $RFC822->write_address($this->f('name'), $this->f('email'));
			}
		}

		go_debug($participants);
		if(count($participants))
		{
			$subject = ($insert) ? $lang['calendar']['invitation'] : $lang['calendar']['invitation_update'];

			// ics attachment
			require_once ($GLOBALS['GO_MODULES']->modules['calendar']['class_path'].'go_ical.class.inc');
			$ical = new go_ical();
			$ical->dont_use_quoted_printable = true;

			$ics_string = $ical->export_event($event['id']);

			$swift = new GoSwift(
					implode(',', $participants),
					$subject.': '.$event['name']);

			require_once ($GLOBALS['GO_MODULES']->modules['calendar']['class_path'].'Replacements.class.inc.php');

			//Load the plugin with the extended replacements class
			$swift->registerPlugin(new Swift_Plugins_DecoratorPlugin(new Cal_Event_Replacements()));

			$swift->set_body('<p>'.$lang['calendar']['invited'].'</p>'.
					$this->event_to_html($event).
					'<p><b>'.$lang['calendar']['linkIfCalendarNotSupported'].'</b></p>'.
					'<p>'.$lang['calendar']['acccept_question'].'</p>'.
					'<a href="'.$GLOBALS['GO_MODULES']->modules['calendar']['full_url'].'invitation.php?event_id='.$event['id'].'&task=accept&email=%email%">'.$lang['calendar']['accept'].'</a>'.
					'&nbsp;|&nbsp;'.
					'<a href="'.$GLOBALS['GO_MODULES']->modules['calendar']['full_url'].'invitation.php?event_id='.$event['id'].'&task=decline&email=%email%">'.$lang['calendar']['decline'].'</a>');

			$swift->message->attach(new Swift_MimePart($ics_string, 'text/calendar; name="calendar.ics"; charset="utf-8"; METHOD="REQUEST"'));
			//$name = File::strip_invalid_chars($event['name']).'.ics';
			//$swift->message->attach(Swift_Attachment::newInstance($ics_string, $name, 'text/calendar; name="calendar.ics"; charset="utf-8"; METHOD="REQUEST"'));

			$swift->set_from($_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name']);

			if(!$swift->sendmail(true)) {
				throw new Exception('Could not send invitation');
			}
		}
	}



	public function has_freebusy_access($requesting_user_id, $target_user_id){

		
		//Only show availability if user has access to the default calendar
//		if(!empty($GO_CONFIG->require_calendar_access_for_freebusy)){
//			$default_calendar = $cal2->get_default_calendar($user['id']);
//			$permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $default_calendar['acl_id']);
//		}else
//		{
//			$permission=true;
//		}

		$permission=true;
		
		require_once($GLOBALS['GO_CONFIG']->root_path.'GO.php');

//		$GO_EVENTS->fire_event('has_freebusy_access', array($requesting_user_id, $target_user_id, &$permission));
		
		if(GO::modules()->isInstalled("freebusypermissions")){
			$permission = GO_Freebusypermissions_FreebusypermissionsModule::hasFreebusyAccess($requesting_user_id, $target_user_id)>0;
		}

		return $permission;

	}
}

