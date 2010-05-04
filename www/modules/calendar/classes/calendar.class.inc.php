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



class calendar extends db
{
	var $events = array();
	var $events_sort = array(); //used to sort the events at start_time
	var $all_day_events = array();
	var $backgrounds = array();

	public function __on_load_listeners($events){
		$events->add_listener('load_settings', __FILE__, 'calendar', 'load_settings');
		$events->add_listener('save_settings', __FILE__, 'calendar', 'save_settings');
		$events->add_listener('reminder_dismissed', __FILE__, 'calendar', 'reminder_dismissed');
		$events->add_listener('user_delete', __FILE__, 'calendar', 'user_delete');
		$events->add_listener('add_user', __FILE__, 'calendar', 'add_user');
		$events->add_listener('build_search_index', __FILE__, 'calendar', 'build_search_index');
		$events->add_listener('check_database', __FILE__, 'calendar', 'check_database');
	}
	
	public static function check_database(){
		global $GO_CONFIG, $GO_MODULES, $GO_LANGUAGE;

		$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

		echo 'Calendar folders'.$line_break;

		if(isset($GO_MODULES->modules['files']))
		{
			$cal = new calendar();
			$db = new db();

			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
			

			$sql = "SELECT * FROM cal_calendars";
			$db->query($sql);
			while($calendar = $db->next_record())
			{
				try{
					$files->check_share('events/'.$calendar['name'], $calendar['user_id'], $calendar['acl_id'], false);
				}
				catch(Exception $e){
					echo $e->getMessage().$line_break;
				}
			}
		

			$db->query("SELECT c.*,a.name AS calendar_name,a.acl_id FROM cal_events c INNER JOIN cal_calendars a ON a.id=c.calendar_id");
			while($event = $db->next_record())
			{
				try{
					$path = $cal->build_event_files_path($event, array('name'=>$event['calendar_name']));
                    echo $path.$line_break;
					$up_event['files_folder_id']=$files->check_folder_location($event['files_folder_id'], $path);
	
					if($up_event['files_folder_id']!=$event['files_folder_id']){
						$up_event['id']=$event['id'];
						$cal->update_row('cal_events', 'id', $up_event);
					}
					$files->set_readonly($up_event['files_folder_id']);
				}
				catch(Exception $e){
					echo $e->getMessage().$line_break;
				}
			}
		}

		if($GO_MODULES->modules['customfields']){
			$db = new db();
			echo "Deleting non existing custom field records".$line_break.$line_break;
			$db->query("delete from cf_1 where link_id not in (select id from cal_events);");
		}
		echo 'Done'.$line_break.$line_break;

	}

	public static function load_settings($response)
	{
		global $GO_MODULES;

		if($GO_MODULES->has_module('calendar'))
		{
			$cal = new calendar();
			$settings = $cal->get_settings($_POST['user_id']);
			$settings = array_merge($settings, $cal->reminder_seconds_to_form_input($settings['reminder']));
			if(empty($settings['calendar_id']))
			{
				$calendar = $cal->get_default_calendar($_POST['user_id']);				
			}else
			{
				$calendar = $cal->get_calendar($settings['calendar_id']);
			}
			if($calendar)
			{
				//calendar_id conflicts with sync
				
				unset($settings['calendar_id']);
				$settings['default_calendar_id']=$calendar['id'];
				$settings['default_calendar_name']=$calendar['name'];
			}
			$response['data']=array_merge($response['data'], $settings);
		}
	}

	public static function save_settings(){

		global $GO_MODULES;

		if($GO_MODULES->has_module('calendar'))
		{
			$settings['user_id']=$_POST['user_id'];
			$settings['background']=$_POST['background'];
			$settings['reminder']=$_POST['reminder_multiplier'] * $_POST['reminder_value'];
			$settings['calendar_id']=$_POST['default_calendar_id'];

			$cal = new calendar();
			$cal->update_settings($settings);
		}
	}

	public static function reminder_dismissed($reminder)
	{
		$cal = new calendar();

		$event = $cal->get_event($reminder['link_id']);
		if($event && !empty($event['rrule']))
		{
			$reminder['time'] = Date::get_next_recurrence_time($event['start_time'], time(),$event['rrule']);

			if($reminder['time'])
			{
				$rm = new reminder();
				$rm->add_reminder($reminder);
			}
		}
	}


	function reminder_seconds_to_form_input($reminder)
	{
		$multipliers[] = 604800;
		$multipliers[] = 86400;
		$multipliers[] = 3600;
		$multipliers[] = 60;

		$settings['reminder_multiplier'] = 60;
		$settings['reminder_value'] = 0;

		if(!empty($reminder))
		{
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
		$this->query("SELECT * FROM cal_settings WHERE user_id='".$this->escape($user_id)."'");
		if ($record=$this->next_record(DB_ASSOC))
		{
			if(empty($record['background']))
				$record['background']='EBF1E2';

			return $record;
		}else
		{
			$this->query("INSERT INTO cal_settings (user_id, background) VALUES ('".$this->escape($user_id)."', 'EBF1E2')");			
			return $this->get_settings($user_id);
		}
	}

	function update_settings($settings)
	{
		if(!isset($settings['user_id']))
		{
			global $GO_SECURITY;
			$settings['user_id'] = $GO_SECURITY->user_id;
		}
		return $this->update_row('cal_settings', 'user_id', $settings);
	}



	function event_to_html($event, $custom=false)
	{
		global $GO_LANGUAGE, $GO_CONFIG, $lang;

		require($GO_LANGUAGE->get_language_file('calendar'));

		go_debug($event);

		$html = '<table>'.
			'<tr><td>'.$lang['calendar']['subject'].':</td>'.
			'<td>'.$event['name'].'</td></tr>'.

			'<tr><td>'.$lang['calendar']['status'].':</td>'.
			'<td>'.$lang['calendar']['statuses'][$event['status']].'</td></tr>';

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



		if(!empty($event['rrule']))
		{
			require_once($GO_CONFIG->class_path.'ical2array.class.inc');
			$ical2array = new ical2array();

			$rrule = $ical2array->parse_rrule($event['rrule']);

			if (isset($rrule['UNTIL']))
			{
				if($event['repeat_end_time'] = $ical2array->parse_date($rrule['UNTIL']))
				{
					$event['repeat_forever']='0';
					$event['repeat_end_time'] = mktime(0,0,0, date('n', $event['repeat_end_time']), date('j', $event['repeat_end_time'])+1, date('Y', $event['repeat_end_time']));
				}else
				{
					$event['repeat_forever'] = 1;
				}
			}elseif(isset($rrule['COUNT']))
			{
				//figure out end time later when event data is complete
				$event['repeat_forever'] = 1;
				$event_count = intval($rrule['COUNT']);
				if($event_count==0)
				{
					unset($event_count);
				}
			}else
			{
				$event['repeat_forever'] = 1;
			}

			$event['repeat_every']=$rrule['INTERVAL'];



			if(isset($rrule['BYDAY']))
			{
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
			switch($rrule['FREQ'])
			{
				case 'WEEKLY':
					$event = Date::shift_days_to_local($event, date('G', $event['start_time']),Date::get_timezone_offset($event['start_time']));



					$days=array();
					if($event['sun']=='1')
					{
						$days[]=$lang['common']['full_days'][0];
					}
					if($event['mon']=='1')
					{
						$days[]=$lang['common']['full_days'][1];
					}
					if($event['tue']=='1')
					{
						$days[]=$lang['common']['full_days'][2];
					}
					if($event['wed']=='1')
					{
						$days[]=$lang['common']['full_days'][3];
					}
					if($event['thu']=='1')
					{
						$days[]=$lang['common']['full_days'][4];
					}
					if($event['fri']=='1')
					{
						$days[]=$lang['common']['full_days'][5];
					}
					if($event['sat']=='1')
					{
						$days[]=$lang['common']['full_days'][6];
					}

					if(count($days)==1)
					{
						$daysStr=$days[0];
					}else
					{
						$daysStr = ' '.$lang['calendar']['and'].' '.array_pop($days);
						$daysStr = implode(', ', $days).$daysStr;
					}

					if($event['repeat_every']>1)
					{
						$html .= sprintf($lang['calendar']['repeats_at_not_every'],
						$event['repeat_every'], $lang['common']['weeks'],
						$daysStr);
					}else
					{
						$html .= sprintf($lang['calendar']['repeats_at'],
						$lang['common']['week'],
						$daysStr);
					}

					break;

				case 'DAILY':
					if($event['repeat_every']>1)
					{
						$html .= sprintf($lang['calendar']['repeats_not_every'],
						$event['repeat_every'], $lang['common']['days']);
					}else
					{
						$html .= sprintf($lang['calendar']['repeats'],
						$lang['common']['day']);
					}
					break;

				case 'MONTHLY':
					if (!isset($rrule['BYDAY']))
					{
						if($event['repeat_every']>1)
						{
							$html .= sprintf($lang['calendar']['repeats_not_every'],
							$event['repeat_every'], $lang['common']['months']);
						}else
						{
							$html .= sprintf($lang['calendar']['repeats'],
							$lang['common']['month']);
						}
					}else
					{


						$event = Date::shift_days_to_local($event, date('G', $event['start_time']),Date::get_timezone_offset($event['start_time']));

						$days=array();
						if($event['sun']=='1')
						{
							$days[]=$lang['common']['full_days'][0];
						}
						if($event['mon']=='1')
						{
							$days[]=$lang['common']['full_days'][1];
						}
						if($event['tue']=='1')
						{
							$days[]=$lang['common']['full_days'][2];
						}
						if($event['wed']=='1')
						{
							$days[]=$lang['common']['full_days'][3];
						}
						if($event['thu']=='1')
						{
							$days[]=$lang['common']['full_days'][4];
						}
						if($event['fri']=='1')
						{
							$days[]=$lang['common']['full_days'][5];
						}
						if($event['sat']=='1')
						{
							$days[]=$lang['common']['full_days'][6];
						}

						if(count($days)==1)
						{
							$daysStr=$lang['calendar']['month_times'][$rrule['BYDAY'][0]].' '.$days[0];
						}else
						{
							$daysStr = ' '.$lang['calendar']['and'].' '.array_pop($days);
							$daysStr = $lang['calendar']['month_times'][$rrule['BYDAY'][0]].' '.implode(', ', $days).$daysStr;
						}

						if($event['repeat_every']>1)
						{
							$html .= sprintf($lang['calendar']['repeats_at_not_every'],
							$event['repeat_every'], $lang['common']['strMonths'], $daysStr);
						}else
						{
							$html .= sprintf($lang['calendar']['repeats_at'],
							$lang['common']['month'], $daysStr);
						}
					}
					break;

				case 'YEARLY':
					if($event['repeat_every']>1)
					{
						$html .= sprintf($lang['calendar']['repeats_not_every'],
						$event['repeat_every'], $lang['common']['years']);
					}else
					{
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


		if(!empty($event['description']))
		{
			$html .= '<tr><td style="vertical-align:top">'.$lang['common']['description'].':</td>'.
					'<td>'.String::text_to_html($event['description']).'</td></tr>';
		}

        if($custom)
		{
			$html .= '<tr><td style="vertical-align:top">{CUSTOM_FIELDS}</td>'.
					'<td>{CUSTOM_VALUES}</td></tr>';
		}

		$html .= '</table>';



		return $html;
	}



	function copy_event($event_id, $new_values=array())
	{
		global $GO_SECURITY;

		$src_event = $dst_event = $this->get_event($event_id);
		unset($dst_event['id'], $dst_event['participants_event_id']);

		foreach($new_values as $key=>$value)
		{
			$dst_event[$key] = $value;
		}

		return $this->add_event($dst_event);

	}

	/*
	 takes a sting YYYY-MM-DD HH:MM in GMT time and converts it to an array with
	 hour, min etc. with	a timezone offset. If 0000 or 00 is set in a date
	 (not time) then it will be replaced with current locale	date.
	 */
	function explode_datetime($datetime_stamp, $timezone_offset)
	{
		$local_time = time();

		$datetime_array = explode(' ', $datetime_stamp);
		$date_stamp = $datetime_array[0];
		$time_stamp = isset($datetime_array[1]) ? $datetime_array[1] : '00:00:00';

		$date_array = explode('-',$date_stamp);

		$year = $date_array[0] == '0000' ? date('Y', $local_time) : $date_array[0];
		$month = $date_array[1] == '00' ? date('n', $local_time) : $date_array[1];
		$day = $date_array[2] == '00' ? date('j', $local_time) : $date_array[2];;

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

	function add_view($view)
	{
		$view['id'] = $this->nextid("cal_views");
		$this->insert_row('cal_views',$view);
		return $view['id'];
	}

	function update_view($view)
	{
		$this->update_row('cal_views','id', $view);
	}

	function delete_view($view_id)
	{
		if($this->query("DELETE FROM cal_views_calendars WHERE view_id='".$this->escape($view_id)."'"))
		{
			return $this->query("DELETE FROM cal_views WHERE id='".$this->escape($view_id)."'");
		}
	}

	function get_user_views($user_id)
	{
		$sql = "SELECT * FROM cal_views WHERE user_id='".$this->escape($user_id)."'";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_authorized_views($user_id, $start=0, $offset=0)
	{
		$sql = "SELECT DISTINCT cal_views . * ".
		"FROM cal_views ".
		"INNER JOIN go_acl ON cal_views.acl_id = go_acl.acl_id ".
		"LEFT JOIN go_users_groups ON go_acl.group_id = go_users_groups.group_id ".
		"WHERE go_acl.user_id=".$this->escape($user_id)." ".
		"OR go_users_groups.user_id=".$this->escape($user_id)." ".
		" ORDER BY cal_views.name ASC";

		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}

	function get_writable_views($user_id, $sort='name', $dir='ASC')
	{

		$sql = "SELECT DISTINCT cal_views . * ".
		"FROM cal_views ".
		"INNER JOIN go_acl ON (cal_views.acl_id = go_acl.acl_id AND level>1) ".
		"LEFT JOIN go_users_groups ON go_acl.group_id = go_users_groups.group_id ".
		"WHERE go_acl.user_id=".$this->escape($user_id)." ".
		"OR go_users_groups.user_id=".$this->escape($user_id)." ".
		" ORDER BY cal_views.".$this->escape($sort.' '.$dir);

		$this->query($sql);
		return $this->num_rows();
	}

	function get_view($view_id)
	{
		$sql = "SELECT * FROM cal_views WHERE id='".$this->escape($view_id)."'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->record;
		}
		throw new DatabaseSelectException();
	}

	function get_view_calendars($view_id)
	{
		$sql = "SELECT cal_calendars.name, cal_calendars.user_id, cal_calendars.id, cal_views_calendars.background FROM cal_calendars ".
		"INNER JOIN cal_views_calendars ON cal_calendars.id=cal_views_calendars.calendar_id ".
		"WHERE cal_views_calendars.view_id='".$this->escape($view_id)."' ORDER BY cal_calendars.name ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function add_calendar_to_view($calendar_id, $background, $view_id)
	{
		$vc['view_id']=$view_id;
		$vc['calendar_id']=$calendar_id;
		$vc['background']=$background;

		return $this->insert_row('cal_views_calendars', $vc);
	}

	function remove_calendar_from_view($calendar_id, $view_id)
	{
		$sql = "DELETE FROM cal_views_calendars WHERE calendar_id='".$this->escape($calendar_id)."' AND view_id='".$this->escape($view_id)."'";
		return $this->query($sql);
	}

	function remove_calendars_from_view($view_id)
	{
		$sql = "DELETE FROM cal_views_calendars WHERE view_id='".$this->escape($view_id)."'";
		return $this->query($sql);
	}

	function is_view_calendar($calendar_id, $view_id)
	{
		$sql = "SELECT * FROM cal_views_calendars WHERE calendar_id='".$this->escape($calendar_id)."' AND view_id='".$this->escape($view_id)."'";
		$this->query($sql);
		return $this->next_record();
	}

	function get_view_by_name($user_id, $name)
	{
		$sql = "SELECT * FROM cal_views WHERE user_id='".$this->escape($user_id)."' AND name='".$this->escape($name)."'";
		$this->query($sql);

		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	function user_has_calendar($user_id)
	{
		$sql = "SELECT id FROM cal_calendars WHERE user_id='".$this->escape($user_id)."'";
		$this->query($sql);
		return $this->next_record();
	}

	function add_participant($participant)
	{
		$participant['id'] = $this->nextid("cal_participants");
		$this->insert_row('cal_participants', $participant);
		return $participant['id'];
	}

	function delete_participant($participant_id)
	{
		$sql = "DELETE FROM cal_participants WHERE id='".$this->escape($participant_id)."'";
		return $this->query($sql);
	}

	function delete_other_participants($event_id, $keep_ids)
	{			
		$sql = "DELETE FROM cal_participants WHERE event_id=".$this->escape($event_id);

		if(count($keep_ids))
			$sql .= " AND id NOT IN (".$this->escape(implode(',', $keep_ids)).")";
			
		return $this->query($sql);
	}

	function remove_participants($event_id)
	{
		$sql = "DELETE FROM cal_participants WHERE event_id='".$this->escape($event_id)."'";
		return $this->query($sql);
	}

	function is_participant($event_id, $email)
	{
		$sql = "SELECT id, user_id FROM cal_participants WHERE event_id='".$this->escape($event_id)."' AND email='".$this->escape($email)."'";
		$this->query($sql);
		return $this->next_record();
	}

	function get_participants($event_id)
	{
		$sql = "SELECT * FROM cal_participants WHERE event_id='".$this->escape($event_id)."' ORDER BY email ASC" ;
		$this->query($sql);
		return $this->num_rows();
	}

	function set_default_calendar($user_id, $calendar_id)
	{
		$sql = "UPDATE cal_settings SET default_cal_id='".$this->escape($calendar_id)."' WHERE user_id='".$this->escape($user_id)."'";
		return $this->query($sql);
	}

	function set_default_view($user_id, $calendar_id, $view_id, $merged_view = '')
	{
		$sql = "UPDATE cal_settings SET default_cal_id='".$this->escape($calendar_id)."', default_view_id='".$this->escape($view_id)."' ";

		if($merged_view != '')
		{
			$sql .= ",merged_view='".$this->escape($merged_view)."' ";
		}
		$sql .= "WHERE user_id='$user_id'";
		return $this->query($sql);
	}



	function add_calendar($calendar)
	{
		$calendar['id'] = $this->nextid("cal_calendars");

		global $GO_MODULES;
		if(isset($GO_MODULES->modules['files']))
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
				
			$files->check_share('events/'.File::strip_invalid_chars($calendar['name']),$calendar['user_id'], $calendar['acl_id']);
		}

		$this->insert_row('cal_calendars',$calendar);
		return $calendar['id'];
	}

	function delete_calendar($calendar_id)
	{
		global $GO_SECURITY, $GO_MODULES;
		$delete = new calendar;

		$calendar = $this->get_calendar($calendar_id);

		if(isset($GO_MODULES->modules['files']))
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
				
			$folder = $files->resolve_path('calendar/'.File::strip_invalid_chars($calendar['name']));
			if($folder){
				$files->delete_folder($folder);
			}
		}

		$sql = "SELECT * FROM cal_events WHERE calendar_id='".$this->escape($calendar_id)."'";
		$this->query($sql);

		while ($this->next_record())
		{
			$delete->delete_event($this->f('id'));
		}
		$sql = "DELETE FROM cal_views_calendars WHERE calendar_id='".$this->escape($calendar_id)."'";
		$this->query($sql);

		$sql= "DELETE FROM cal_calendars WHERE id='".$this->escape($calendar_id)."'";
		$this->query($sql);

		$this->query("DELETE FROM cal_visible_tasklists WHERE calendar_id=?", 'i', $calendar_id);
		
		if(isset($GO_MODULES->modules['summary']))
		{
			$this->query("DELETE FROM su_visible_calendars WHERE calendar_id=?", 'i', $calendar_id);
		}

		if(empty($calendar['shared_acl']))
		{
			$GO_SECURITY->delete_acl($calendar['acl_id']);
		}
	}

	function update_calendar($calendar, $old_calendar=false)
	{
		if(!$old_calendar)$old_calendar=$this->get_calendar($calendar['id']);

		global $GO_MODULES;
		if(isset($GO_MODULES->modules['files']) && $old_calendar &&  $calendar['name']!=$old_calendar['name'])
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();			
			$files->move_by_paths('events/'.File::strip_invalid_chars($old_calendar['name']), 'events/'.File::strip_invalid_chars($calendar['name']));
		}
		
		global $GO_SECURITY;
		//user id of the calendar changed. Change the owner of the ACL as well
		if(isset($calendar['user_id']) && $old_calendar['user_id'] != $calendar['user_id'])
		{
			$GO_SECURITY->chown_acl($old_calendar['acl_id'], $calendar['user_id']);
		}
		
		return $this->update_row('cal_calendars','id', $calendar);
	}

	
	function get_default_import_calendar($user_id)
	{
		$settings = $this->get_settings($user_id);
		$calendar_id = $settings['calendar_id'];
				
		if($calendar_id)
		{
			$this->query("SELECT * FROM cal_calendars WHERE user_id = ? AND id=?", 'ii', array($user_id, $calendar_id));
			if($this->next_record(DB_ASSOC))
			{
				return $this->record;
			}			
		}
		
		$this->get_user_calendars($user_id, 0, 1);			
		if($this->next_record(DB_ASSOC))
		{
			return $this->record;
		}

		return false;	
	}
	
	function get_default_calendar($user_id)
	{
		$this->get_user_calendars($user_id, 0, 1);		
		if($this->next_record(DB_ASSOC))
		{
			return $this->record;
		}else
		{
			global $GO_USERS, $GO_SECURITY;

			$calendar['user_id']=$user_id;
			$user = $GO_USERS->get_user($user_id);
			if(!$user){
				return false;
			}
			$calendar_name = String::format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'last_name');
			$calendar['name'] = $calendar_name;
			$calendar['acl_id']=$GO_SECURITY->get_new_acl();
			$x = 1;
			while($this->get_calendar_by_name($calendar['name']))
			{
				$calendar['name'] = $calendar_name.' ('.$x.')';
				$x++;
			}

			$calendar['name'] = $calendar['name'];
			if (!$calendar_id = $this->add_calendar($calendar))
			{
				throw new DatabaseInsertException();
			}else
			{
				return $this->get_calendar($calendar_id);
			}
		}
	}



	function get_calendar($calendar_id=0, $user_id=0)
	{
		if($calendar_id > 0)
		{
			$sql = "SELECT * FROM cal_calendars WHERE id='".$this->escape($calendar_id)."'";
			$this->query($sql);
			if ($this->next_record(DB_ASSOC))
			{
				return $this->record;
			}else
			{
				return false;
			}
		}else
		{
			global $GO_SECURITY;
			$user_id = !empty($user_id) ? $user_id : $GO_SECURITY->user_id;
			return $this->get_default_calendar($user_id);
		}
	}

	function get_calendar_by_name($name, $user_id=0)
	{
		$sql = "SELECT * FROM cal_calendars WHERE name='".$this->escape($name)."'";

		if($user_id>0)
		{
			$sql .= " AND user_id=".$this->escape($user_id);
		}
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->record;
		}else
		{
			return false;
		}
	}

	
	
	function get_user_calendars($user_id,$start=0,$offset=0, $group_id=1)
	{
		$sql = "SELECT * FROM cal_calendars WHERE user_id=".$this->escape($user_id);

		if($group_id>0){
			$sql .= ' AND group_id='.$group_id;
		}

		$sql .= " ORDER BY id ASC";
		$this->query($sql);
		$count= $this->num_rows();
		
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}
	
	function get_default_user_calendar($user_id)
	{
		$this->query("SELECT value FROM go_settings WHERE user_id=? AND name='calendar_default_calendar'", 'i', array($user_id));
		$deb = $this->next_record();
		$calendar_id = $this->f('value');
		if($calendar_id > 0)
		{
			$this->query("SELECT * FROM cal_calendars WHERE user_id = ? AND id=?", 'ii', array($user_id, $calendar_id));
			return $this->num_rows();
		}else
		{
			return $this->get_user_calendars($user_id, 0, 1);				
		}
		return false;
	}

	function get_calendars()
	{
		$sql = "SELECT * FROM cal_calendars ORDER BY name ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_authorized_calendars($user_id, $start=0, $offset=0, $resources=0, $group_id=1)
	{
		$sql = "SELECT DISTINCT c.* ";

		if($group_id<0){
			$sql .= ",g.name AS group_name ";
		}

		$sql .= "FROM cal_calendars c ";

		if($group_id<0){
			$sql .= " LEFT JOIN cal_groups g ON g.id=c.group_id ";
		}

		$sql .= "INNER JOIN go_acl a ON c.acl_id = a.acl_id ".
		"LEFT JOIN go_users_groups ug ON a.group_id = ug.group_id ".
		"WHERE (a.user_id=".$this->escape($user_id)." ".
		"OR ug.user_id=".$this->escape($user_id).")";
    
		if($resources)
		{
				$sql .= " AND c.group_id > 1";
		}elseif($group_id>-1)
		{
				$sql .= " AND c.group_id = ".$this->escape($group_id);
		}
		$sql .= " ORDER BY c.name ASC";

		$this->query($sql);

		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}

	function get_writable_calendars($user_id, $start=0, $offset=0, $resources=0, $groups=0, $group_id=-1, $show_all=0, $sort='name', $dir='ASC')
	{
		$sql = "SELECT DISTINCT cal_calendars.* ";
        if($groups)
            $sql .= ", cal_groups.fields ";
		$sql .= "FROM cal_calendars ".
		"INNER JOIN go_acl ON (cal_calendars.acl_id = go_acl.acl_id AND level>1) ".
		"LEFT JOIN go_users_groups ON go_acl.group_id = go_users_groups.group_id ";

        if($groups)
            $sql .= "LEFT JOIN cal_groups ON cal_calendars.group_id = cal_groups.id ";
        $sql .= "WHERE (go_acl.user_id=".$this->escape($user_id)." ".
		"OR go_users_groups.user_id=".$this->escape($user_id).")";

        if(!$show_all)
        {
            if($resources)
            {
                $sql .= " AND cal_calendars.group_id > 1";
            }else
                $group_id = 1;

            if($group_id>-1)
            {
                $sql .= " AND cal_calendars.group_id = ".$this->escape($group_id);
            }
            $sql .= " ORDER BY cal_calendars.".$this->escape($sort.' '.$dir);
        }else
        {
            $sql .= " ORDER BY cal_calendars.group_id ASC, cal_calendars.".$this->escape($sort.' '.$dir);
        }
        

		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}        
		return $count;
	}

    function get_calendars_by_group_id($group_id)
    {
        $sql = "SELECT * FROM cal_calendars WHERE group_id = ? ORDER BY name ASC";
        $this->query($sql, 'i', array($group_id));
    }
	/*
	 Times in GMT!
	 */
	
	function build_event_files_path($event, $calendar){
		return 'events/'.File::strip_invalid_chars($calendar['name']).'/'.date('Y', $event['start_time']).'/'.date('m', $event['start_time']).'/'.File::strip_invalid_chars($event['name']);
	}

	function add_event(&$event, $calendar=false)
	{
		if(empty($event['calendar_id']))
		{
			return false;
		}

		if (empty($event['user_id'])) {
			global $GO_SECURITY;
			$event['user_id'] = $GO_SECURITY->user_id;
		}

		if(empty($event['ctime']))
		{
			$event['ctime']  =  time();
		}

		if(empty($event['mtime']))
		{
			$event['mtime']  =  $event['ctime'];
		}

		if(empty($event['background']))
		{
			$settings = $this->get_settings($event['user_id']);
			$event['background']  =  $settings ? $settings['background'] : 'EBF1E2';
		}


		if(!isset($event['status']))
		{
			$event['status'] = 'ACCEPTED';
		}

		unset($event['acl_id']);



		if(!isset($event['start_time']))
		{
			$local_start_time = time();
			$year = date('Y', $local_start_time );
			$month = date('n', $local_start_time );
			$day = date('j', $local_start_time );
			$event['start_time'] = mktime(0,0,0,$month, $day, $year);
			$event['all_day_event']='1';
		}


		if(!isset($event['end_time']) || $event['end_time']<$event['start_time'])
		{
			$event['end_time']=$event['start_time']+3600;
		}
		$event['id'] = $this->nextid("cal_events");


		if(!isset($event['participants_event_id']))
		{
			$event['participants_event_id']=$event['id'];
		}

		global $GO_MODULES;
		if(!isset($event['files_folder_id']) && isset($GO_MODULES->modules['files']))
		{
			global $GO_CONFIG;
				
			if(!$calendar)
			{
				$calendar = $this->get_calendar($event['calendar_id']);
			}
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();

			$new_path = $this->build_event_files_path($event, $calendar);
			if($folder=$files->create_unique_folder($new_path))
			{
				$event['files_folder_id']=$folder['id'];
			}
		}


		$exceptions = isset($event['exceptions']) ? $event['exceptions'] : array();
		unset($event['exceptions']);

		if ($event['id'] > 0 &&  $this->insert_row('cal_events', $event))
		{
			foreach($exceptions as $exception_time)
			{
				if($exception_time!==false)
				{
					$exception['event_id']=$event['id'];
					$exception['time']=$exception_time;

					$this->add_exception($exception);
				}
			}

			$this->cache_event($event['id']);

			if(!empty($event['reminder']))
			{
				global $GO_CONFIG;

				require_once($GO_CONFIG->class_path.'base/reminder.class.inc.php');
				$rm = new reminder();

				if(!$calendar)
				{
					$calendar = $this->get_calendar($event['calendar_id']);
				}

				$reminder['user_id']=$calendar['user_id'];
				$reminder['name']=$event['name'];
				$reminder['link_type']=1;
				$reminder['link_id']=$event['id'];

				if(empty($event['rrule']))
					$reminder['vtime']=$event['start_time'];
				else
					$reminder['vtime'] = Date::get_next_recurrence_time($event['start_time'],time(), $event['rrule']);

				$reminder['time']=$reminder['vtime']-$event['reminder'];

				if($reminder['time']>time())
					$rm->add_reminder($reminder);
			}

			return $event['id'];
		}
		return false;
	}


	function is_duplicate_event($event)
	{
		$sql = "SELECT id FROM cal_events WHERE ".
		"name='".$this->escape($event['name'])."' AND ".
		"start_time='".$this->escape($event['start_time'])."' AND ".
		"end_time='".$this->escape($event['end_time'])."' AND ".
		"calendar_id='".$this->escape($event['calendar_id'])."' AND ".
		"user_id='".$this->escape($event['user_id'])."'";

		$this->query($sql);
		if($this->next_record())
		{
			return $this->f('id');
		}
		return false;
	}

	function update_event(&$event, $calendar=false, $old_event=false, $update_related=true, $update_related_status=true)
	{
		if(!$old_event)
		{
			$old_event = $this->get_event($event['id']);
		}

		
		unset($event['read_permission'], $event['write_permission']);
		if(empty($event['mtime']))
		{
			$event['mtime']  = time();
		}
		//for building files path we need this.
		if(empty($event['start_time']))
		{
			$event['start_time']  = $old_event['start_time'];
		}

		if(isset($event['completion_time']) && $event['completion_time'] > 0 && $this->copy_completed($event['id']))
		{
			$event['repeat_type'] = REPEAT_NONE;
			$event['repeat_end_time'] = 0;
		}

		if(isset($event['exceptions']))
		{
			$this->delete_exceptions($event['id']);
			foreach($event['exceptions'] as $exception_time)
			{
				if($exception_time!==false)
				{
					$exception['event_id']=$event['id'];
					$exception['time']=$exception_time;

					$this->add_exception($exception);
				}
			}
			unset($event['exceptions']);
		}

		
		
		global $GO_MODULES;
		if(isset($GO_MODULES->modules['files']))
		{
			
			if(!$calendar)
			{
				$calendar = $this->get_calendar($event['calendar_id']);				
			}
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
			
			if(!isset($event['ctime']))
			{
				$event['ctime']=$old_event['ctime'];
			}
			if(!isset($event['calendar_id']))
			{
				$event['calendar_id']=$old_event['calendar_id'];
			}
			if(!isset($event['name']))
			{
				$event['name']=$old_event['name'];
			}
			
			$new_path = $this->build_event_files_path($event, $calendar);			
			$event['files_folder_id']=$files->check_folder_location($old_event['files_folder_id'], $new_path);			
		}	

		$r = $this->update_row('cal_events', 'id', $event);

		$this->cache_event($event['id']);


		if(isset($event['start_time']))
		{

			if(!isset($event['reminder']))
			{
				$event['reminder']=$old_event['reminder'];
			}

			global $GO_CONFIG;

			require_once($GO_CONFIG->class_path.'base/reminder.class.inc.php');
			$rm = new reminder();

			$rm->get_reminders_by_link_id($event['id'], 1);
			$existing_reminder = $rm->next_record();

			if(empty($event['reminder']) && $existing_reminder)
			{
				$rm->delete_reminder($existing_reminder['id']);
			}

			if(!empty($event['reminder']))
			{
				if(!$calendar)
				{
					if(empty($event['calendar_id']))
					{
						$event['calendar_id']=$old_event['calendar_id'];
					}
					$calendar = $this->get_calendar($event['calendar_id']);
				}

				$reminder['id']=$existing_reminder['id'];
				$reminder['user_id']=$calendar['user_id'];
				if(isset($event['name']))
				$reminder['name']=$event['name'];

				$reminder['link_type']=1;
				$reminder['link_id']=$event['id'];							

				if(empty($event['rrule']))
					$reminder['vtime']=$event['start_time'];
				else
					$reminder['vtime'] = Date::get_next_recurrence_time($event['start_time'],time(), $event['rrule']);

				$reminder['time']=$reminder['vtime']-$event['reminder'];

				if($reminder['time']>time())
				{
					if($existing_reminder)
					{
						$rm->update_reminder($reminder);
					}else
					{
						$rm->add_reminder($reminder);
					}
				}elseif($existing_reminder)
				{
					$rm->delete_reminder($existing_reminder['id']);
				}
			}
		}

		if(!empty($old_event['rrule']) && $old_event['start_time']!=$event['start_time'])
		{
			$this->move_exceptions($event['id'], $event['start_time']-$old_event['start_time']);
		}

		if($update_related && !empty($event['id']))
		{
			$related_event = $event;
			unset($related_event['user_id'], $related_event['calendar_id'], $related_event['participants_event_id']);
			
			$cal = new calendar();
			/*if(!empty($old_event['participants_event_id'])){
				$sql = "SELECT * FROM cal_events WHERE id!=".$this->escape($event['id'])." AND (participants_event_id=".$this->escape($old_event['participants_event_id'])." OR id=".$this->escape($old_event['participants_event_id']).")";
			}else
			{
				$sql = "SELECT * FROM cal_events WHERE participants_event_id=".$this->escape($event['id']);
			 * $cal->query($sql);
			}*/

			$participants_event_id=!empty($old_event['participants_event_id']) ? $old_event['participants_event_id'] : $event['id'];
			$cal->get_participants_events($participants_event_id, $event['id']);
			
			while($old_event = $cal->next_record())
			{
				$related_event['id']=$cal->f('id');
				$related_event['calendar_id'] = $cal->f('calendar_id');

				if(!$update_related_status)
				{
					$related_event['status'] = $cal->f('status');
					$related_event['background'] = $cal->f('background');
				}

				$this->update_event($related_event, false, $old_event, false);
			}
		}		
	
		return $r;
	}

	function send_resource_notification($message_type, $resource, $calendar, $user_name, $recipient, $resource_group){
		global $GO_CONFIG, $GO_MODULES, $lang;

		if(!isset($lang['calendar'])){
			global $GO_LANGUAGE;
			$GO_LANGUAGE->require_language_file('calendar');
		}
		
		$url = create_direct_url('calendar', 'showEvent', array('values'=>array('event_id' => $resource['id'])));

		switch($message_type){
			case 'new':
				$body = sprintf($lang['calendar']['resource_modified_mail_body'],$user_name).'<br /><br />'
					. $this->event_to_html($resource, true)
					. '<br /><a href="'.$url.'">'.$lang['calendar']['open_resource'].'</a>';
				$subject = sprintf($lang['calendar']['resource_modified_mail_subject'],$calendar['name'], $resource['name'], date($_SESSION['GO_SESSION']['date_format'], $resource['start_time']));
			break;

			case 'modified_for_admin':
				$body = sprintf($lang['calendar']['resource_mail_body'],$user_name,$calendar['name']).'<br /><br />'
					. $this->event_to_html($resource, true)
					. '<br /><a href="'.$url.'">'.$lang['calendar']['open_resource'].'</a>';
				$subject = sprintf($lang['calendar']['resource_mail_subject'],$calendar['name'], $resource['name'], date($_SESSION['GO_SESSION']['date_format'], $resource['start_time']));
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

		require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
		$swift = new GoSwift($recipient, $subject);

		$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);

		$values = '';
		$labels = '';

		if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission']) {
			require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
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

	function get_participants_events($participants_event_id, $skip_event_id=0){
		$sql = "SELECT * FROM cal_events ".
			"WHERE participants_event_id=".$this->escape($participants_event_id);

		if(!empty($skip_event_id))
			$sql .= " AND id!=".$this->escape($skip_event_id);

		$this->query($sql);
		return $this->num_rows();
	}

	function add_exception_for_all_participants($participants_event_id, $exception){
		$cal = new calendar();

		$this->get_participants_events($participants_event_id);
		while($event = $this->next_record()){
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
	$offset)
	{

		$sql  = "SELECT * FROM cal_events WHERE ";

		if($view_id>0 || $calendar_id==0)
		{
			if($view_id>0)
			{
				$calendars = $this->get_view_calendars($view_id);
			}else {
				$calendars = array();
				$this->get_authorized_calendars($user_id);
				while($this->next_record())
				{
					$calendars[] = $this->f('id');
				}
			}

			if(!count($calendars))
			{
				return false;
			}else
			{
				foreach($calendars as $calendar)
				{
					$ids[]=$calendar['id'];
				}
			}
			$sql .= "calendar_id IN (".implode(',', $ids).")";
		}else
		{
			$sql .= "calendar_id=$calendar_id";
		}

		if ($start_time > 0)
		{
			$sql .= " AND ((repeat_type='".REPEAT_NONE."' AND (";
			if($end_time>0)
			{
				$sql .= "start_time<='$end_time' AND ";
			}
			$sql .= "end_time>='$start_time')) OR ".
			"(repeat_type!='".REPEAT_NONE."' AND ";
			if($end_time>0)
			{
				$sql .= "start_time<='$end_time' AND ";
			}
			$sql .= "(repeat_end_time>='$start_time' OR repeat_forever='1')))";
		}
		$sql .= " AND name LIKE '".$this->escape($query)."'";

		if($sort_field != '' && $sort_order != '')
		{
			$sql .=	" ORDER BY ".$this->escape($sort_field." ".$sort_order);
		}

		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
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
	$only_busy_events=false)
	{

		$sql  = "SELECT DISTINCT e.* FROM cal_events e";

		if($user_id > 0)
		{
			$sql .= " INNER JOIN cal_calendars c ON (e.calendar_id=c.id)";
		}

		$where=false;

		if($only_busy_events)
		{
			if($where)
			{
				$sql .= " AND ";
			}else
			{
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "busy='1'";
		}


		if($user_id > 0)
		{
			if($where)
			{
				$sql .= " AND ";
			}else
			{
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "c.user_id='$user_id' ";
		}else
		{
			if($where)
			{
				$sql .= " AND ";
			}else
			{
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "e.calendar_id IN (".implode(',', $calendars).")";
		}


		if ($interval_start > 0)
		{
			if($where)
			{
				$sql .= " AND ";
			}else
			{
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "((e.rrule='' AND (";
			if($interval_end>0)
			{
				$sql .= "e.start_time<'$interval_end' AND ";
			}
			$sql .= "e.end_time>'$interval_start')) OR ".
			"(e.rrule!='' AND ";
			if($interval_end>0)
			{
				$sql .= "e.start_time<'$interval_end' AND ";
			}
			$sql .= "(e.repeat_end_time>'$interval_start' OR e.repeat_end_time=0)))";
		}

		if($sort_field != '' && $sort_order != '')
		{
			$sql .=	" ORDER BY ".$this->escape($sort_field." ".$sort_order);
		}

		if($offset == 0)
		{
			$this->query($sql);
			return $this->num_rows();
		}else
		{
			$this->query($sql);
			$count = $this->num_rows();

			$sql .= " LIMIT ".$this->escape($start.",".$offset);

			$this->query($sql);

			return $count;
		}
	}

	function get_events_in_array(
	$calendars,
	$user_id,
	$interval_start_time,
	$interval_end_time,
	$only_busy_events=false)
	{
		$this->events = array();
		$this->events_sort=array();
		

		if($count = $this->get_events(
		$calendars,
		$user_id,
		$interval_start_time,
		$interval_end_time,
		'start_time','ASC',0,0,$only_busy_events))
		{
			while($this->next_record())
			{
				$this->calculate_event($this->record,
				$interval_start_time,
				$interval_end_time);
			}
		}

		asort($this->events_sort);

		//go_debug($this->events_sort);

		$sorted_events=array();
		foreach($this->events_sort as $key=>$value)
		{
			$sorted_events[] = &$this->events[$key];
		}
		//go_debug($sorted_events);
		return $sorted_events;
	}

	function calculate_event($event, $interval_start_time, $interval_end_time)
	{
		global $GO_SECURITY;


		if(empty($event['rrule']))
		{
			if($event['start_time'] < $interval_end_time && $event['end_time'] > $interval_start_time)
			{
				$this->events[] = $event;
				$this->events_sort[] = $event['start_time'].$event['name'];
			}
		}else
		{
			$cal = new calendar();
			$duration = $event['end_time'] - $event['start_time'];
			if($duration == 0) $duration = 3600;

			//go_log(LOG_DEBUG, date('r', $interval_start_time));

			$calculated_event=$event;

			$first_occurrence_time=$event['start_time'];
			$start_time=$interval_start_time;

			//calculate the next occurrence from the start_time minus one second because an event
			//may start exactly on the start of display.
			$calculated_event['start_time']=$interval_start_time-1;

			//echo date('Ymd G:i', $first_occurrence_time).'<br />';

			//go_debug($calculated_event['name'].': '.date('Ymd G:i', $first_occurrence_time));

			$loops = 0;
			while($calculated_event['start_time'] = Date::get_next_recurrence_time($first_occurrence_time, $calculated_event['start_time'], $event['rrule']))
			{
				$loops++;

				//echo date('Ymd G:i', $calculated_event['start_time']).'<br />';

				//go_debug($calculated_event['name'].': '.date('Ymd G:i', $calculated_event['start_time']));

				$calculated_event['end_time'] = $calculated_event['start_time']+$duration;

				if($calculated_event['start_time'] > $interval_end_time || $calculated_event['end_time'] < $interval_start_time)
				break;

				if(!$cal->is_exception($calculated_event['id'],$calculated_event['start_time']))
				{
					$this->events[] = $calculated_event;
					$this->events_sort[] = $calculated_event['start_time'].$calculated_event['name'];
				}

				if($loops==100)
				{
					global $GO_MODULES;
					echo '<a href="'.$GO_MODULES->modules['calendar']['url'].'event.php?event_id='.$calculated_event['id'].
					'>Warning: event looped 100 times '.
					date('Ymd G:i', $calculated_event['start_time']).'  '.
					$calculated_event['name'].' event_id='.$calculated_event['id'].'</a><br>';
					exit();
				}
			}
			//go_log(LOG_DEBUG, $calculated_event['name'].': eind');
		}
	}


	function has_participants_event($participants_event_id, $calendar_id){
		$sql = "SELECT * FROM cal_events WHERE participants_event_id=? AND calendar_id=?";
		$this->query($sql, 'ii', array($participants_event_id, $calendar_id));
		return $this->next_record();
	}

	function get_event($event_id)
	{
		$sql = "SELECT e.*, c.acl_id FROM cal_events e LEFT JOIN cal_calendars c ON c.id=e.calendar_id WHERE e.id='".$this->escape($event_id)."'";
		$this->query($sql);
		return $this->next_record(DB_ASSOC);
	}

	function get_events_for_period($user_id, $start_offset, $days, $index_hour=false)
	{
		$interval_end = mktime(0, 0, 0, date("m", $start_offset)  , date("d", $start_offset)+$days, date("Y", $start_offset));
		$year = date("Y", $start_offset);
		$month = date("m", $start_offset);
		$day = date("d", $start_offset);

		$events = $this->get_events_in_array(0, 0, $user_id, $start_offset, $interval_end, $day, $month, $year, 0, 'Ymd', $index_hour);

		return $events;
	}


	function delete_event($event_id, $delete_related=true)
	{
		if($event = $this->get_event($event_id))
		{
			$event_id = $this->escape($event_id);

			global $GO_MODULES,$GO_CONFIG;
			if(isset($GO_MODULES->modules['files']))
			{
				
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
				$files = new files();
				try{
					$files->delete_folder($event['files_folder_id']);
				}
				catch(Exception $e){}
			}		


			$sql = "DELETE FROM cal_events WHERE id='$event_id'";
			$this->query($sql);
			$sql = "DELETE FROM cal_participants WHERE event_id='$event_id'";
			$this->query($sql);
			$sql = "DELETE FROM cal_exceptions WHERE event_id='$event_id'";
			$this->query($sql);

			require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
			$search = new search();
			$search->delete_search_result($event_id, 1);

			require_once($GO_CONFIG->class_path.'base/reminder.class.inc.php');
			$rm = new reminder();
			$rm2 = new reminder();
			$rm->get_reminders_by_link_id($event_id, 1);
			while($r = $rm->next_record())
			{
				$rm2->delete_reminder($r['id']);
			}

			if($delete_related && !empty($event_id))
			{
				$cal = new calendar();
				$sql = "SELECT id FROM cal_events WHERE participants_event_id=".$this->escape($event_id);
				$cal->query($sql);
				while($cal->next_record())
				{
					$this->delete_event($cal->f('id'),false);
				}
			}

            if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
            {
                $this->query("DELETE FROM cf_1 WHERE link_id = ?", 'i', array($event_id));
            }
		}
	}

	function delete_exceptions($event_id)
	{
		$event_id = $this->escape($event_id);
		$sql = "DELETE FROM cal_exceptions WHERE event_id='$event_id'";
		return $this->query($sql);
	}

	function add_exception($exception)
	{
		$exception['id'] = $this->nextid('cal_exceptions');
		return $this->insert_row('cal_exceptions', $exception);
	}

	function move_exceptions($event_id, $diff)
	{
		$event_id = $this->escape($event_id);

		$sql = "UPDATE cal_exceptions SET time=time+".$this->escape($diff)." WHERE event_id=$event_id";
		return $this->query($sql);
	}

	function is_exception($event_id, $time)
	{
		$sql = "SELECT * FROM cal_exceptions WHERE event_id='".$this->escape($event_id)."' AND time='".$this->escape($time)."'";

		$this->query($sql);
		return $this->next_record();
	}

	function get_exceptions($event_id)
	{
		$sql = "SELECT * FROM cal_exceptions WHERE event_id='".$this->escape($event_id)."'";

		$this->query($sql);
		return $this->num_rows();
	}



	function get_view_color($view_id, $event_id)
	{
		$sql = "SELECT cal_views_calendars.background FROM cal_events_calendars ".
		"INNER JOIN cal_views_calendars ON cal_events_calendars.calendar_id=".
		"cal_views_calendars.calendar_id WHERE cal_events_calendars.event_id=".$this->escape($event_id)." AND cal_views_calendars.view_id=".$this->escape($view_id);

		$this->query($sql);
		if($this->num_rows() == 1 && $this->next_record())
		{
			return $this->f('background');
		}
		return 'FFFFCC';
	}


	function get_event_from_ical_object($object)
	{
		global $GO_MODULES, $GO_CONFIG;

		if(!isset($this->ical2array))
		{
			require_once($GO_CONFIG->class_path.'ical2array.class.inc');
			$this->ical2array = new ical2array();
		}

		$event['name'] = (isset($object['SUMMARY']['value']) && $object['SUMMARY']['value'] != '') ? trim($object['SUMMARY']['value']) : 'Unnamed';
		if(isset($object['SUMMARY']['params']['ENCODING']) && $object['SUMMARY']['params']['ENCODING'] == 'QUOTED-PRINTABLE')
		{
			$event['name'] = quoted_printable_decode($event['name']);
		}
		$event['description'] = isset($object['DESCRIPTION']['value']) ? trim($object['DESCRIPTION']['value']) : '';

		if(isset($object['DESCRIPTION']['params']['ENCODING']) && $object['DESCRIPTION']['params']['ENCODING'] == 'QUOTED-PRINTABLE')
		{
			$event['description'] = String::trim_lines(quoted_printable_decode($event['description']));
		}
		$event['location'] = isset($object['LOCATION']['value']) ? trim($object['LOCATION']['value']) : '';
		if(isset($object['LOCATION']['params']['ENCODING']) && $object['LOCATION']['params']['ENCODING'] == 'QUOTED-PRINTABLE')
		{
			$event['location'] = quoted_printable_decode($event['location']);
		}

		$event['status'] = isset($object['STATUS']['value']) ? $object['STATUS']['value'] : 'NEEDS-ACTION';

		$event['all_day_event'] = (isset($object['DTSTART']['params']['VALUE']) &&
		strtoupper($object['DTSTART']['params']['VALUE']) == 'DATE') ? '1' : '0';


		if(isset($object['DTSTART']))
		{
			$timezone_id = isset($object['DTSTART']['params']['TZID']) ? $object['DTSTART']['params']['TZID'] : '';
			$event['start_time'] = $this->ical2array->parse_date($object['DTSTART']['value'], $timezone_id);
		}

		if(empty($event['start_time'])){
			return false;
		}

		if(isset($object['DTEND']['value']))
		{
			$timezone_id = isset($object['DTEND']['params']['TZID']) ? $object['DTEND']['params']['TZID'] : '';
			$event['end_time'] = $this->ical2array->parse_date($object['DTEND']['value'],  $timezone_id);

		}elseif(isset($object['DURATION']['value']))
		{
			$duration = $this->ical2array->parse_duration($object['DURATION']['value']);
			$event['end_time'] = $event['start_time']+$duration;

		}elseif(isset($object['DUE']['value']))
		{
			$timezone_id = isset($object['DUE']['params']['TZID']) ? $object['DUE']['params']['TZID'] : '';
			$event['end_time'] = $this->ical2array->parse_date($object['DUE']['value'],  $timezone_id);
		}

		if($event['all_day_event']=='1')
		{
			$event['end_time']-=60;
		}

		//reminder
		if(isset($object['DALARM']['value']))
		{
			$dalarm = explode(';', $object['DALARM']['value']);
			if(isset($dalarm[0]) && $remind_time = $this->ical2array->parse_date($dalarm[0]))
			{
				$event['reminder'] = $event['start_time']-$remind_time;
			}
		}

		if(!isset($event['reminder']) && isset($object['AALARM']['value']))
		{
			$aalarm = explode(';', $object['AALARM']['value']);
			if(isset($aalarm[0]) && $remind_time = $this->ical2array->parse_date($aalarm[0]))
			{
				$event['reminder'] = $event['start_time']-$remind_time;
			}
		}

		if(isset($event['reminder']) && $event['reminder']<0)
		{
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

			if($event['end_time'] == $event['start_time'] || (($end_hour==23 || $end_hour==0) && date('G', $event['start_time'])==0))
			{
				$event['all_day_event'] = '1';

				//make sure times are 0 - 23

				$start_date = getdate($event['start_time']);
				$end_date = getdate($event['end_time']-60);

				$event['start_time']=mktime(0,0,0,$start_date['mon'], $start_date['mday'], $start_date['year']);
				$event['end_time']=mktime(23,59,0,$end_date['mon'], $end_date['mday'], $end_date['year']);
			}

			if(isset($object['CLASS']['value']) && $object['CLASS']['value'] == 'PRIVATE')
			{
				$event['private'] = '1';
			}else {
				$event['private']= '0';
			}


			$event['rrule'] = '';
			$event['repeat_end_time'] = 0;


			if (isset($object['RRULE']['value']) && $rrule = $this->ical2array->parse_rrule($object['RRULE']['value']))
			{
				$event['rrule'] = $object['RRULE']['value'];
				if (isset($rrule['UNTIL']))
				{
					if($event['repeat_end_time'] = $this->ical2array->parse_date($rrule['UNTIL']))
					{
						$event['repeat_end_time'] = mktime(0,0,0, date('n', $event['repeat_end_time']), date('j', $event['repeat_end_time'])+1, date('Y', $event['repeat_end_time']));
					}
				}

				if(isset($rrule['BYDAY']))
				{

					$month_time=1;
					if($rrule['FREQ']=='MONTHLY')
					{
						$month_time = $rrule['BYDAY'][0];
						$day = substr($rrule['BYDAY'], 1);
						$days_arr =array($day);
					}else
					{
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

					$event['rrule']=Date::build_rrule(Date::ical_freq_to_repeat_type($rrule), $rrule['INTERVAL'], $event['repeat_end_time'], $days, $month_time);
				}
			}



			if(isset($object['EXDATE']['value']))
			{
				$exception_dates = explode(';', $object['EXDATE']['value']);
				foreach($exception_dates as $exception_date)
				{
					$exception_time = $this->ical2array->parse_date($exception_date);
					if($exception_time>0)
					{
						$event['exceptions'][] = $exception_time;
					}
				}
			}

			//figure out end time of event
			if(isset($event_count))
			{
				$event['repeat_end_time']='0';
				$start_time=$event['start_time'];
				for($i=1;$i<$event_count;$i++)
				{
					$event['repeat_end_time']=$start_time=Date::get_next_recurrence_time($event['start_time'], $start_time, $event['rrule']);
				}
				if($event['repeat_end_time']>0)
				{
					$event['repeat_end_time']+=$event['end_time']-$event['start_time'];
				}
			}

			return $event;
		}
		return false;
	}

	function get_event_from_ical_file($ical_file)
	{
		global $GO_CONFIG;

		require_once($GO_CONFIG->class_path.'ical2array.class.inc');
		$this->ical2array = new ical2array();

		$vcalendar = $this->ical2array->parse_file($ical_file);

		while($object = array_shift($vcalendar[0]['objects']))
		{
			if($object['type'] == 'VEVENT' || $object['type'] == 'VTODO')
			{
				if($event = $this->get_event_from_ical_object($object))
				{
					return $event;
				}
			}
		}
		return false;
	}

	function import_ical_string($ical_string, $calendar_id=-1)
	{
		global $GO_MODULES, $GO_CONFIG;

		$count=0;

		require_once($GO_CONFIG->class_path.'ical2array.class.inc');
		$this->ical2array = new ical2array();

		$vcalendar = $this->ical2array->parse_string($ical_string);

		if(isset($vcalendar[0]['objects']))
		{
			while($object = array_shift($vcalendar[0]['objects']))
			{
				if($object['type'] == 'VEVENT')
				{
					if($event = $this->get_event_from_ical_object($object))
					{
						if ($calendar_id != -1) {
							$event['calendar_id']=$calendar_id;
							if ($event_id = $this->add_event($event))
							{
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
	function import_ical_file($ical_file, $calendar_id)
	{
		$data = file_get_contents($ical_file);
		return $this->import_ical_string($data, $calendar_id);
	}

    function get_conflicts($start_time, $end_time, $calendars)
	{
        $conflicts = array();
        
		$cal_events = $this->get_events_in_array($calendars, 0, $start_time, $end_time, false);
		foreach($cal_events as $event)
		{
			$conflicts[$event['id']]=$event;
		}

		return $conflicts;
	}


	function user_delete($user)
	{
		$cal = new calendar();

		$delete = new calendar();
		$sql = "SELECT * FROM cal_calendars WHERE user_id='".$cal->escape($user['id'])."'";
		$cal->query($sql);
		while($cal->next_record())
		{
			$delete->delete_calendar($cal->f('id'));
		}

		$sql = "DELETE FROM cal_settings WHERE user_id=".$cal->escape($user['id']);
		$cal->query($sql);


		$cal->get_user_views($user['id']);

		while($cal->next_record())
		{
			$delete->delete_view($cal->f('id'));
		}

	}

	public static function add_user($user)
	{
		global $GO_SECURITY, $GO_LANGUAGE, $GO_CONFIG, $GO_MODULES;

		$cal2 = new calendar();

		$cal = new calendar();

		$calendar['name']=String::format_name($user,'','','last_name');
		$calendar['user_id']=$user['id'];
		$calendar['acl_id']=$GO_SECURITY->get_new_acl('calendar', $user['id']);


		$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_internal, $calendar['acl_id'],2);

		$calendar_id = $cal->add_calendar($calendar);

		require($GO_LANGUAGE->get_language_file('calendar'));

		$sql = "SELECT * FROM cal_views WHERE name LIKE '".$cal->escape($lang['calendar']['groupView'])."'";
		$cal->query($sql);
		if($cal->next_record())
		{
			$view_id = $cal->f('id');

			$count = $cal2->get_view_calendars($view_id);

			if($count<=20)
			$cal2->add_calendar_to_view($calendar_id, '', $view_id);
		}

		if(isset($GO_MODULES->modules['summary'])){
			$cal2->add_visible_calendar(array('user_id'=>$user['id'], 'calendar_id'=>$calendar_id));
		}

	}



	function cache_event($event_id)
	{
		global $GO_CONFIG, $GO_LANGUAGE, $lang;

		require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
		$search = new search();

		$GO_LANGUAGE->require_language_file('calendar');

		$sql  = "SELECT DISTINCT cal_events.*, cal_calendars.acl_id FROM cal_events ".
		"INNER JOIN cal_calendars ON cal_events.calendar_id=cal_calendars.id ".
		"WHERE cal_events.id=?";

		$this->query($sql, 'i', $event_id);
		$record = $this->next_record();
		if($record && empty($record['private']))
		{
			$cache['id']=$this->f('id');
			$cache['user_id']=$this->f('user_id');
			$cache['name'] = htmlspecialchars($this->f('name').' ('.Date::get_timestamp($this->f('start_time'), true).')', ENT_QUOTES, 'utf-8');
			$cache['link_type']=1;
			$cache['module']='calendar';
			$cache['description']='';
			$cache['type']=$lang['link_type'][1];
			$cache['keywords']=$search->record_to_keywords($this->record).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			$cache['acl_id']=$this->f('acl_id');

			$search->cache_search_result($cache);
		}
	}
	public function build_search_index()
	{
		$cal = new calendar();
		$cal2 = new calendar();
		$sql = "SELECT id FROM cal_events";
		$cal->query($sql);

		while($record = $cal->next_record())
		{
			$cal2->cache_event($record['id']);
		}
		/* {ON_BUILD_SEARCH_INDEX_FUNCTION} */
	}



	function is_available($user_id, $start, $end, $ignore_event_id=0)
	{
		$events = $this->get_events_in_array(array(), $user_id, $start, $end, true);

		if($ignore_event_id>0)
		{
			$newevents=array();
			foreach($events as $event)
			{
				if($event['id']!=$ignore_event_id && $event['participants_event_id']!=$ignore_event_id)
				{
					$newevents[]=$event;
				}
			}
			$events = $newevents;
		}

		return count($events) > 0 ? false : true;
	}


	function get_free_busy($user_id, $date, $ignore_event_id=0)
	{
		$date=getdate($date);

		$daystart = mktime(0,0,0,$date['mon'], $date['mday'], $date['year']);
		$dayend = mktime(0,0,0,$date['mon'], $date['mday']+1, $date['year']);

		$freebusy=array();
		for($i=0;$i<1440;$i+=15)
		{
			$freebusy[$i]=0;
		}

		$events = $this->get_events_in_array(array(), $user_id, $daystart, $dayend, true);



		foreach($events as $event)
		{
			if($event['id']!=$ignore_event_id)
			{
				if($event['end_time'] > $dayend)
				{
					$event['end_time']=$dayend;
				}

				if($event['start_time'] < $daystart)
				{
					$event['start_time']=$daystart;
				}
				$event_start = getdate($event['start_time']);
				$event_end = getdate($event['end_time']);

				if($event_start['minutes']<15)
				{
					$minutes=0;
				}elseif($event_start['minutes']<30)
				{
					$minutes=15;
				}elseif($event_start['minutes']<45)
				{
					$minutes=30;
				}else
				{
					$minutes=45;
				}

				$start_minutes = $minutes+($event_start['hours']*60);
				$end_minutes = $event_end['minutes']+($event_end['hours']*60);

				//echo $start_minutes.' -> '.$end_minutes.'<br>';
				//go_log(LOG_DEBUG, $event['name'].' '.Date::get_timestamp($event['start_time']).' -> '.$end_minutes);

				for($i=$start_minutes;$i<$end_minutes;$i+=15)
				{
					$freebusy[$i]=1;
				}
			}
		}
		return $freebusy;

	}

	function clear_event_status($event_id, $accepted_email){
		$sql = "UPDATE cal_participants SET status='0' WHERE email!='".$this->escape($accepted_email)."' AND event_id='".$this->escape($event_id)."'";
		return $this->query($sql);
	}


	function set_event_status($event_id, $status, $email)
	{
		$sql = "UPDATE cal_participants SET status='".$this->escape($status)."' WHERE email='".$this->escape($email)."' AND event_id='".$this->escape($event_id)."'";
		return $this->query($sql);
	}

	function get_event_status($event_id, $email)
	{
		$sql = "SELECT status FROM cal_participants WHERE email='".$this->escape($email)."' AND event_id='".$this->escape($event_id)."'";
		if($this->query($sql))
		{
			if($this->next_record())
			{
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
	function add_group($group)
	{
        if(!$group['id'])
            $group['id']=$this->nextid('cal_groups');
		
		if($this->insert_row('cal_groups', $group))
		{
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
	function update_group($group)
	{
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
	function delete_group($group_id)
	{
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
	function get_group($group_id)
	{
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
	function get_group_by_name($name)
	{
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
	function get_groups($sortfield='name', $sortorder='ASC', $start=0, $offset=0, $hide_cal=1)
	{
		$sql = "SELECT ";
		if($offset>0)
		{
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}
		$sql .= "* FROM cal_groups ";
        if($hide_cal)
        {
            $sql .= "WHERE id > 1";
        }

		$sql .= " ORDER BY ".$this->escape($sortfield.' '.$sortorder);
		if($offset>0)
		{
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
    function get_event_resources($event_id)
	{
		if($event_id>0)
		{
			$sql = "SELECT cal_events.* FROM cal_events WHERE participants_event_id ='$event_id' OR id='$event_id'";
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
	function get_event_resource($event_id, $calendar_id)
	{
		if($event_id>0 && $calendar_id>0)
		{
			$sql = "SELECT cal_events.* FROM cal_events WHERE participants_event_id='$event_id' AND calendar_id='$calendar_id'";

			$this->query($sql);
			if($this->next_record())
			{
				return $this->record;
			}
		}
		return false;
	}

	function get_bdays($start_time,$end_time,$abooks=array())
	{
		global $response;
		
		$start = date('Y-m-d',$start_time);
		$end = date('Y-m-d',$end_time);
		
		$sql = "SELECT DISTINCT id, birthday, first_name, middle_name, last_name, "
			."IF (STR_TO_DATE(CONCAT(YEAR(?),'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e') >= ?, "
			."STR_TO_DATE(CONCAT(YEAR(?),'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e') , "
			."STR_TO_DATE(CONCAT(YEAR(?)+1,'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e')) "
			."as upcoming FROM ab_contacts "
			."WHERE birthday != '0000-00-00' ";


		if(count($abooks))
		{
			$sql .= "AND addressbook_id IN (".implode(',', $abooks).") ";
		}

		$sql .= "HAVING upcoming BETWEEN ? AND ? ORDER BY upcoming";
		
		$this->query($sql, 'ssssss', array($start,$start,$start,$start,$start,$end));

		return $this->num_rows();		
	}


	/**
	 * When a an item gets deleted in a panel with links. Group-Office attempts
	 * to delete the item by finding the associated module class and this function
	 *
	 * @param int $id The id of the linked item
	 * @param int $link_type The link type of the item. See /classes/base/links.class.inc
	 */

	function __on_delete_link($id, $link_type)
	{
		//echo $id.':'.$link_type;
		if($link_type==1)
		{
			return $this->delete_event($id);
		}
	}


	function event_to_json_response($event)
	{
		global $GO_USERS;

		if(!empty($event['user_id'])){
			$user = $GO_USERS->get_user($event['user_id']);
			$event['user_name']=String::format_name($user);
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

		if (!empty($event['rrule']) && $rrule = ical2array::parse_rrule($event['rrule']))
		{
			if(isset($rrule['FREQ']))
			{
				if (isset($rrule['UNTIL']))
				{
					$event['repeat_end_time'] = ical2array::parse_date($rrule['UNTIL']);
				}elseif(isset($rrule['COUNT']))
				{
					//go doesn't support this
				}else
				{
					$event['repeat_forever'] = 1;
				}

				$event['repeat_every'] = $rrule['INTERVAL'];
				switch($rrule['FREQ'])
				{
					case 'DAILY':
						$event['repeat_type'] = REPEAT_DAILY;
						break;

					case 'WEEKLY':
						$event['repeat_type'] = REPEAT_WEEKLY;

						$days = Date::byday_to_days($rrule['BYDAY']);
						$days = Date::shift_days_to_local($days, date('G', $start_time), Date::get_timezone_offset($start_time));

						$event['repeat_days_0'] = $days['sun'];
						$event['repeat_days_1'] = $days['mon'];
						$event['repeat_days_2'] = $days['tue'];
						$event['repeat_days_3'] = $days['wed'];
						$event['repeat_days_4'] = $days['thu'];
						$event['repeat_days_5'] = $days['fri'];
						$event['repeat_days_6'] = $days['sat'];
						break;

					case 'MONTHLY':
						if (isset($rrule['BYDAY']))
						{
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

						}else
						{
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

		if(isset($event['reminder']))
		{
			$event = array_merge($event, $this->reminder_seconds_to_form_input($event['reminder']));
		}
		return $event;
	}

	public function get_visible_calendars($user_id)
	{
		$this->query("SELECT * FROM su_visible_calendars WHERE user_id = $user_id");
		return $this->num_rows();
	}

	public function add_visible_calendar($calendar)
	{
		if($this->replace_row('su_visible_calendars', $calendar))
		{
			return $this->insert_id();
		}
		return false;
	}

	public function delete_visible_calendar($calendar_id, $user_id)
	{
		$this->query("DELETE FROM su_visible_calendars WHERE calendar_id = $calendar_id AND user_id = $user_id");
	}

	public function get_group_admins($group_id)
	{		
		$this->query("SELECT user_id FROM cal_group_admins WHERE group_id=?", 'i', $group_id);
		return $this->num_rows();		
	}

	public function group_admin_exists($group_id, $user_id)
	{
		$this->query("SELECT user_id FROM cal_group_admins WHERE group_id=? AND user_id=?", 'ii', array($group_id, $user_id));
		return ($this->num_rows() > 0) ? true : false;
	}

	public function add_group_admin($group_admin)
	{
		global $GO_SECURITY;

		$this->get_calendars_by_group_id($group_admin['group_id']);
		while($calendar = $this->next_record()){
			$GO_SECURITY->add_user_to_acl($group_admin['user_id'], $calendar['acl_id'], GO_SECURITY::MANAGE_PERMISSION);
		}

		return $this->insert_row('cal_group_admins', $group_admin);
	}

	public function delete_group_admin($group_id, $user_id)
	{
		return $this->query("DELETE FROM cal_group_admins WHERE group_id=? AND user_id=?" , 'ii', array($group_id, $user_id));
	}

	
	public function get_visible_tasklists($calendar_id)
	{
		$this->query("SELECT * FROM cal_visible_tasklists WHERE calendar_id = ?", 'i', $calendar_id);
		return $this->num_rows();
	}

	public function add_visible_tasklist($tasklist)
	{
		return $this->replace_row('cal_visible_tasklists', $tasklist);
	}

	public function delete_visible_tasklist($calendar_id, $tasklist_id)
	{
		return $this->query("DELETE FROM cal_visible_tasklists WHERE calendar_id = ? AND tasklist_id = ?", 'ii', array($calendar_id, $tasklist_id));
	}

}
