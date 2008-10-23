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

	function calendar()
	{
		$this->db();
	}

	function event_to_html($event)
	{
		global $GO_LANGUAGE, $GO_CONFIG, $lang;

		require($GO_LANGUAGE->get_language_file('calendar'));

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
				$days = explode(',', $rrule['BYDAY']);

				$event['sun'] = in_array('SU', $days) ? '1' : '0';
				$event['mon'] = in_array('MO', $days) ? '1' : '0';
				$event['tue'] = in_array('TU', $days) ? '1' : '0';
				$event['wed'] = in_array('WE', $days) ? '1' : '0';
				$event['thu'] = in_array('TH', $days) ? '1' : '0';
				$event['fri'] = in_array('FR', $days) ? '1' : '0';
				$event['sat'] = in_array('SA', $days) ? '1' : '0';
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


						$event = Date::shift_days_to_local($event);

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
							$event['repeat_every'], $lang['common']['months'], $daysStr);
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

		$html .= '</table>';



		return $html;
	}



	function copy_event($event_id, $new_values=array())
	{
		global $GO_SECURITY;

		$src_event = $dst_event = $this->get_event($event_id);
		unset($dst_event['id']);

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
		if($this->query("DELETE FROM cal_views_calendars WHERE view_id='".$this->query($view_id)."'"))
		{
			return $this->query("DELETE FROM cal_views WHERE id='".$this->query($view_id)."'");
		}
	}

	function get_user_views($user_id)
	{
		$sql = "SELECT * FROM cal_views WHERE user_id='".$this->query($user_id)."'";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_authorized_views($user_id, $start=0, $offset=0)
	{
		$sql = "SELECT DISTINCT cal_views . * ".
		"FROM cal_views ".
		"INNER JOIN go_acl ON ( cal_views.acl_read = go_acl.acl_id ".
		"OR cal_views.acl_write = go_acl.acl_id ) ".
		"LEFT JOIN go_users_groups ON go_acl.group_id = go_users_groups.group_id ".
		"WHERE go_acl.user_id=".$this->escape($user_id)." ".
		"OR go_users_groups.user_id=".$this->escape($user_id)." ".
		"ORDER BY cal_views.name ASC";

		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}

	function get_writable_views($user_id)
	{

		$sql = "SELECT DISTINCT cal_views . * ".
		"FROM cal_views ".
		"INNER JOIN go_acl ON cal_views.acl_write = go_acl.acl_id ".
		"LEFT JOIN go_users_groups ON go_acl.group_id = go_users_groups.group_id ".
		"WHERE go_acl.user_id=$user_id ".
		"OR go_users_groups.user_id=$user_id ".
		"ORDER BY cal_views.name ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_view($view_id)
	{
		$sql = "SELECT * FROM cal_views WHERE id='$view_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		throw new DatabaseSelectException();
	}

	function get_view_calendars($view_id)
	{
		$sql = "SELECT cal_calendars.name, cal_calendars.user_id, cal_calendars.id, cal_views_calendars.background FROM cal_calendars ".
		"INNER JOIN cal_views_calendars ON cal_calendars.id=cal_views_calendars.calendar_id ".
		"WHERE cal_views_calendars.view_id='$view_id' ORDER BY cal_calendars.name ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function add_calendar_to_view($calendar_id, $background, $view_id)
	{
		$sql = "INSERT INTO cal_views_calendars (view_id, background, calendar_id) ".
		"VALUES ('$view_id' ,'$background', '$calendar_id')";
		return $this->query($sql);
	}

	function remove_calendar_from_view($calendar_id, $view_id)
	{
		$sql = "DELETE FROM cal_views_calendars WHERE calendar_id='$calendar_id' AND view_id='$view_id'";
		return $this->query($sql);
	}

	function remove_calendars_from_view($view_id)
	{
		$sql = "DELETE FROM cal_views_calendars WHERE view_id='$view_id'";
		return $this->query($sql);
	}

	function is_view_calendar($calendar_id, $view_id)
	{
		$sql = "SELECT * FROM cal_views_calendars WHERE calendar_id='$calendar_id' AND view_id='$view_id'";
		$this->query($sql);
		return $this->next_record();
	}

	function get_view_by_name($user_id, $name)
	{
		$sql = "SELECT * FROM cal_views WHERE user_id='$user_id' AND name='$name'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function user_has_calendar($user_id)
	{
		$sql = "SELECT id FROM cal_calendars WHERE user_id='$user_id'";
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
		$sql = "DELETE FROM cal_participants WHERE id='$participant_id'";
		return $this->query($sql);
	}

	function remove_participants($event_id)
	{
		$sql = "DELETE FROM cal_participants WHERE event_id='$event_id'";
		return $this->query($sql);
	}

	function is_participant($event_id, $email)
	{
		$sql = "SELECT id FROM cal_participants WHERE event_id='$event_id' AND email='$email'";
		$this->query($sql);
		return $this->next_record();
	}

	function get_participants($event_id)
	{
		$sql = "SELECT * FROM cal_participants WHERE event_id='$event_id' ORDER BY email ASC" ;
		$this->query($sql);
		return $this->num_rows();
	}

	function set_default_calendar($user_id, $calendar_id)
	{
		$sql = "UPDATE cal_settings SET default_cal_id='$calendar_id' WHERE user_id='$user_id'";
		return $this->query($sql);
	}

	function set_default_view($user_id, $calendar_id, $view_id, $merged_view = '')
	{
		$sql = "UPDATE cal_settings SET default_cal_id='$calendar_id', default_view_id='$view_id' ";

		if($merged_view != '')
		{
			$sql .= ",merged_view='$merged_view' ";
		}
		$sql .= "WHERE user_id='$user_id'";
		return $this->query($sql);
	}

	function get_settings($user_id)
	{
		$this->query("SELECT * FROM cal_settings WHERE user_id='$user_id'");
		if ($this->next_record(MYSQL_ASSOC))
		{
			return $this->Record;
		}else
		{
			$this->query("INSERT INTO cal_settings (user_id) VALUES ('$user_id')");
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

	function add_calendar($calendar)
	{
		$calendar['id'] = $this->nextid("cal_calendars");
		$this->insert_row('cal_calendars',$calendar);
		return $calendar['id'];
	}

	function delete_calendar($calendar_id)
	{
		global $GO_SECURITY;
		$delete = new calendar;

		$calendar = $this->get_calendar($calendar_id);

		if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_write']))
		{
			throw new AccessDeniedException();
		}

		$sql = "SELECT * FROM cal_events WHERE calendar_id='$calendar_id'";
		$this->query($sql);

		while ($this->next_record())
		{
			$delete->delete_event($this->f('id'));
		}
		$sql = "DELETE FROM cal_views_calendars WHERE calendar_id='$calendar_id'";
		$this->query($sql);

		$sql= "DELETE FROM cal_calendars WHERE id='$calendar_id'";
		$this->query($sql);

		$GO_SECURITY->delete_acl($calendar['acl_read']);
		$GO_SECURITY->delete_acl($calendar['acl_write']);


	}

	function update_calendar($calendar)
	{
		return $this->update_row('cal_calendars','id', $calendar);
	}


	function get_calendar($calendar_id=0)
	{
		if($calendar_id > 0)
		{
			$sql = "SELECT * FROM cal_calendars WHERE id='$calendar_id'";
			$this->query($sql);
			if ($this->next_record(MYSQL_ASSOC))
			{
				return $this->Record;
			}else
			{
				return false;
			}
		}else
		{
			global $GO_SECURITY;

			$this->get_user_calendars($GO_SECURITY->user_id);
			if ($this->next_record(MYSQL_ASSOC))
			{
				return $this->Record;
			}else
			{
				global $GO_USERS;

				$calendar['user_id']=$GO_SECURITY->user_id;
				$user = $GO_USERS->get_user($GO_SECURITY->user_id);
				$calendar_name = String::format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'last_name');
				$calendar['name'] = $calendar_name;
				$calendar['acl_read']=$GO_SECURITY->get_new_acl();
				$calendar['acl_write']=$GO_SECURITY->get_new_acl();
				$x = 1;
				while($this->get_calendar_by_name(addslashes($calendar['name'])))
				{
					$calendar['name'] = $calendar_name.' ('.$x.')';
					$x++;
				}

				$calendar['name'] = addslashes($calendar['name']);
				if (!$calendar_id = $this->add_calendar($calendar))
				{
					throw new DatabaseInsertException();
				}else
				{
					return $this->get_calendar($calendar_id);
				}
			}
		}
	}

	function get_calendar_by_name($name, $user_id=0)
	{
		$sql = "SELECT * FROM cal_calendars WHERE name='$name'";

		if($user_id>0)
		{
			$sql .= " AND user_id=$user_id";
		}
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->Record;
		}else
		{
			return false;
		}
	}

	function get_user_calendars($user_id )
	{
		$sql = "SELECT * FROM cal_calendars WHERE user_id='$user_id' ORDER BY name ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_calendars()
	{
		$sql = "SELECT * FROM cal_calendars  ORDER BY name ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_authorized_calendars($user_id, $start=0, $offset=0)
	{
		$sql = "SELECT DISTINCT cal_calendars.* ".
		"FROM cal_calendars ".
		"INNER JOIN go_acl ON ( cal_calendars.acl_read = go_acl.acl_id ".
		"OR cal_calendars.acl_write = go_acl.acl_id ) ".
		"LEFT JOIN go_users_groups ON go_acl.group_id = go_users_groups.group_id ".
		"WHERE (go_acl.user_id=$user_id ".
		"OR go_users_groups.user_id=$user_id) ORDER BY cal_calendars.name ASC";

		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}

	function get_writable_calendars($user_id, $start=0, $offset=0)
	{
		$sql = "SELECT DISTINCT cal_calendars . * ".
		"FROM cal_calendars ".
		"INNER JOIN go_acl ON cal_calendars.acl_write = go_acl.acl_id ".
		"LEFT JOIN go_users_groups ON go_acl.group_id = go_users_groups.group_id ".
		"WHERE (go_acl.user_id=$user_id ".
		"OR go_users_groups.user_id=$user_id) ORDER BY cal_calendars.name ASC";

		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}




	/*
	 Times in GMT!
	 */

	function add_event($event)
	{

		if(empty($event['calendar_id']))
		{
			return false;
		}

		if (!isset($event['user_id']) || $event['user_id'] == 0) {
			global $GO_SECURITY;
			$event['user_id'] = $GO_SECURITY->user_id;
		}

		if(!isset($event['ctime']) || $event['ctime'] == 0)
		{
			$event['ctime']  =  gmmktime();
		}

		if(!isset($event['mtime']) || $event['mtime'] == 0)
		{
			$event['mtime']  =  $event['ctime'];
		}

		/*if(!isset($event['background']) || $event['background'] == '')
		 {
			$event['background']  =  'FFFFCC';
			}*/


		if(!isset($event['status']))
		{
			$event['status'] = 'ACCEPTED';
		}

		unset($event['acl_read'], $event['acl_write']);



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

			return $event['id'];
		}
		return false;
	}


	function is_duplicate_event($event, $calendar_id)
	{
		unset($event['exceptions']);
		$record = array_map('addslashes', $event);

		$sql = "SELECT id FROM cal_events WHERE ".
		"name='".$record['name']."' AND ".
		"start_time='".$record['start_time']."' AND ".
		"end_time='".$record['end_time']."' AND ".
		"calendar_id='".$calendar_id."' AND ".
		"user_id='".$record['user_id']."'";

		$this->query($sql);
		if($this->next_record())
		{
			return $this->f('id');
		}
		return false;
	}

	function update_event($event)
	{
		unset($event['read_permission'], $event['write_permission']);
		if(!isset($event['mtime']) || $event['mtime'] == 0)
		{
			$event['mtime']  = gmmktime();
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

		return $this->update_row('cal_events', 'id', $event);
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

		/*
		 $calendars = array();
		 if(!$this->get_authorized_calendars($user_id))
		 {
		 return false;
		 }

		 while($this->next_record())
		 {
		 $calendars[] = $this->f('id');
		 }*/




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
		$sql .= " AND name LIKE '$query'";

		if($sort_field != '' && $sort_order != '')
		{
			$sql .=	" ORDER BY ".$this->replace($sort_field." ".$sort_order);
		}

		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
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
			$sql .=	" ORDER BY ".$this->replace($sort_field." ".$sort_order);
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
				$this->calculate_event($this->Record,
				$interval_start_time,
				$interval_end_time);
			}
		}

		asort($this->events_sort);
		$sorted_events=array();
		foreach($this->events_sort as $key=>$value)
		{
			$sorted_events[] = &$this->events[$key];
		}
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
				$this->events_sort[] = $event['start_time'];
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
				
		//go_log(LOG_DEBUG, $calculated_event['name'].': '.date('Ymd G:i', $first_occurrence_time));

			$loops = 0;
			while($calculated_event['start_time'] = Date::get_next_recurrence_time($first_occurrence_time, $calculated_event['start_time'], $event['rrule']))
			{
				$loops++;

				//echo date('Ymd G:i', $calculated_event['start_time']).'<br />';

			//	go_log(LOG_DEBUG, $calculated_event['name'].': '.date('Ymd G:i', $calculated_event['start_time']));

				$calculated_event['end_time'] = $calculated_event['start_time']+$duration;

				if($calculated_event['start_time'] > $interval_end_time || $calculated_event['end_time'] < $interval_start_time)
				break;
					

					
					

				if(!$cal->is_exception($calculated_event['id'],$calculated_event['start_time']))
				{
					$this->events[] = $calculated_event;
					$this->events_sort[] = $calculated_event['start_time'];
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




	function get_event($event_id)
	{
		$sql = "SELECT e.*, c.acl_read, c.acl_write FROM cal_events e INNER JOIN cal_calendars c ON c.id=e.calendar_id WHERE e.id='$event_id'";
		$this->query($sql);
		if($this->next_record(MYSQL_ASSOC))
		{
			return $this->Record;
		}else
		{
			return false;
		}
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


	function delete_event($event_id)
	{
		if($event = $this->get_event($event_id))
		{
			global $GO_LINKS, $GO_CONFIG;
			$GO_LINKS->delete_link($event['id'],1);

				
			if(file_exists($GO_CONFIG->file_storage_path.'events/'.$event_id.'/'))
			{
				require_once($GO_CONFIG->class_path.'filesystem.class.inc');
				$fs = new filesystem();
				$fs->delete($GO_CONFIG->file_storage_path.'events/'.$event_id.'/');
			}


			$sql = "DELETE FROM cal_events WHERE id='$event_id'";
			$this->query($sql);
			$sql = "DELETE FROM cal_participants WHERE event_id='$event_id'";
			$this->query($sql);
			$sql = "DELETE FROM cal_exceptions WHERE event_id='$event_id'";
			$this->query($sql);

		}
	}

	function delete_exceptions($event_id)
	{
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
		$sql = "UPDATE cal_exceptions SET time=time+$diff WHERE event_id=$event_id";
		return $this->query($sql);
	}

	function is_exception($event_id, $time)
	{
		$sql = "SELECT * FROM cal_exceptions WHERE event_id='$event_id' AND time='$time'";

		$this->query($sql);
		return $this->next_record();
	}

	function get_exceptions($event_id)
	{
		$sql = "SELECT * FROM cal_exceptions WHERE event_id='$event_id'";

		$this->query($sql);
		return $this->num_rows();
	}



	function get_view_color($view_id, $event_id)
	{
		$sql = "SELECT cal_views_calendars.background FROM cal_events_calendars ".
		"INNER JOIN cal_views_calendars ON cal_events_calendars.calendar_id=".
		"cal_views_calendars.calendar_id WHERE cal_events_calendars.event_id=$event_id AND cal_views_calendars.view_id=$view_id";

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
			strtoupper($object['DTSTART']['params']['VALUE']) == 'DATE') ? true : false;


		if(isset($object['DTSTART']))
		{
			$timezone_id = isset($object['DTSTART']['params']['TZID']) ? $object['DTSTART']['params']['TZID'] : '';
			$event['start_time'] = $this->ical2array->parse_date($object['DTSTART']['value']);
		}

		if(isset($object['DTEND']['value']))
		{
			$timezone_id = isset($object['DTEND']['params']['TZID']) ? $object['DTEND']['params']['TZID'] : '';
			$event['end_time'] = $this->ical2array->parse_date($object['DTEND']['value'],  $timezone_id);

		}elseif(isset($object['DURATION']['value']))
		{
			$duration = $this->ical2array->parse_date($object['DURATION']['value']);
			$event['end_time'] = $event['start_time']+$duration;

		}elseif(isset($object['DUE']['value']))
		{
			$timezone_id = isset($object['DUE']['params']['TZID']) ? $object['DUE']['params']['TZID'] : '';
			$event['end_time'] = $this->ical2array->parse_date($object['DUE']['value'],  $timezone_id);
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

			
			
			if($event['end_time'] == $event['start_time'] || (date('G', $event['end_time'])==23 && date('G', $event['end_time'])==0))
			{
				$event['all_day_event'] = '1';
				
				//make sure times are 0 - 23
				
				$start_date = getdate($event['start_time']);
				$end_date = getdate($event['end_time']);
				
				$event['start_time']=mktime(0,0,0,$start_date['mon'], $start_date['mday'], $start_date['year']);
				$event['end_time']=mktime(23,59,0,$end_date['mon'], $end_date['mday'], $end_date['year']);
				
				//$event['start_time'] = $event['start_time']);
			}

			/*if($event['all_day_event'])
			{
				//TODO DST!
				//$event['end_time'] = $event['end_time']+86340;
				//dont do this for symbian

				//calc duration in days:
				$duration = $event['end_time']-$event['start_time'];
				$duration_days = ceil($duration/86400);

				$local_start_time = $event['start_time'];

				$year = date('Y', $local_start_time);
				$month = date('n', $local_start_time);
				$day = date('j', $local_start_time);
				$event['end_time'] = mktime(0,-1,0,$month, $day+$duration_days+1, $year);

			}*/
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
					
					$event['rrule']=Date::build_rrule(Date::ical_freq_to_repeat_type($rrule['FREQ']), $rrule['INTERVAL'], $event['repeat_end_time'], $days, $month_time);					
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
		global $GO_MODULES;

		require_once($GO_MODULES->modules['calendar']['class_path'].'ical2array.class.inc');
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

	function import_ical_string($ical_string, $calendar_id)
	{
		global $GO_MODULES;

		require_once($GO_MODULES->modules['calendar']['class_path'].'ical2array.class.inc');
		$this->ical2array = new ical2array();

		$vcalendar = $this->ical2array->parse_string($ical_string);

		while($object = array_shift($vcalendar[0]['objects']))
		{
			if($object['type'] == 'VEVENT' || $object['type'] == 'VTODO')
			{
				if($event = $this->get_event_from_ical_object($object))
				{
					$exceptions=isset($event['exceptions']) ? $event['exceptions'] : array();
					unset($event['exceptions']);
					$event = array_map('addslashes', $event);
					$event = array_map('trim', $event);
					$event['exceptions']=$exceptions;

					if ($event_id = $this->add_event($event))
					{
						$this->subscribe_event($event_id, $calendar_id);
						return $event_id;
					}
				}
			}
		}
		return false;
	}


	//TODO: attendee support
	function import_ical_file($user_id, $ical_file, $calendar_id, $return_event_id=false)
	{
		global $GO_CONFIG, $GO_MODULES;
		$count = 0;

		$cal_module = $GO_MODULES->get_module('calendar');

		if ($calendar = $this->get_calendar($calendar_id) && $cal_module)
		{
			require_once($cal_module['class_path'].'ical2array.class.inc');
			$this->ical2array = new ical2array();

			$vcalendar = $this->ical2array->parse_file($ical_file);

			while($object = array_shift($vcalendar[0]['objects']))
			{
				if($object['type'] == 'VEVENT' || $object['type'] == 'VTODO')
				{
					if($event = $this->get_event_from_ical_object($object))
					{

						$exceptions=isset($event['exceptions']) ? $event['exceptions'] : array();
						unset($event['exceptions']);
						$event = array_map('addslashes', $event);
						$event = array_map('trim', $event);
						$event['exceptions']=$exceptions;

						if ($event_id = $this->add_event($event))
						{
							$count++;
							$this->subscribe_event($event_id, $calendar_id);
						}
					}
				}
			}
		}
		return $count;
	}

	function get_conflicts($start_time, $end_time, $calendars, $participants)
	{
		global $GO_USERS, $RFC822;

		$conflicts=array();

		$participants_array = $RFC822->explode_address_list($participants);

		for($i=0;$i<sizeof($participants_array);$i++)
		{
			if(!empty($participants_array[$i]))
			{
				$id = 0;

				if($member_profile = $GO_USERS->get_user_by_email(($participants_array[$i])))
				{
					$id = $member_profile["id"];

					$member_events = false;
					if($id)
					{
						$member_events = $this->get_events_in_array(0, 0, $id,
						$start_time, $end_time,false,false,true,false,false,true);
						foreach($member_events as $event)
						{
							$conflicts[$event['id']]=$event;
						}
					}
				}
			}
		}

		foreach($calendars as $calendar_id)
		{
			$cal_events = $this->get_events_in_array($calendar_id, 0, 0,
			$start_time, $end_time,false,false,true,false,false,true);
			foreach($cal_events as $event)
			{
				$conflicts[$event['id']]=$event;
			}
		}

		return $conflicts;
	}


	function __on_user_delete($user)
	{
		$delete = new calendar();
		$sql = "SELECT * FROM cal_calendars WHERE user_id='".$user['id']."'";
		$this->query($sql);
		while($this->next_record())
		{
			$delete->delete_calendar($this->f('id'));
		}


		$this->get_user_views($user['id']);

		while($this->next_record())
		{
			$delete->delete_view($this->f('id'));
		}

	}

	function __on_add_user($params)
	{
		global $GO_SECURITY, $GO_LANGUAGE, $GO_CONFIG;

		$user = $params['user'];

		$calendar['name']=addslashes(String::format_name($user));
		$calendar['user_id']=$user['id'];
		$calendar['acl_read']=$GO_SECURITY->get_new_acl('category', $user['id']);
		$calendar['acl_write']=$GO_SECURITY->get_new_acl('category', $user['id']);
		
		$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_internal, $calendar['acl_write']);

		$calendar_id = $this->add_calendar($calendar);

		require($GO_LANGUAGE->get_language_file('calendar'));

		$sql = "SELECT * FROM cal_views WHERE name LIKE '".addslashes($lang['calendar']['groupView'])."'";
		$this->query($sql);
		if($this->next_record())
		{
			$view_id = $this->f('id');
				
			$count = $this->get_view_calendars($view_id);
				
			if($count<=20)
			$this->add_calendar_to_view($calendar_id, '', $view_id);
		}
	}

	function __on_search($last_sync_time=0)
	{
		global $GO_MODULES, $GO_LANGUAGE;

		require($GO_LANGUAGE->get_language_file('calendar'));

		$sql  = "SELECT DISTINCT cal_events.*, cal_calendars.acl_read, cal_calendars.acl_write FROM cal_events ".
		"INNER JOIN cal_calendars ON cal_events.calendar_id=cal_calendars.id ".
		"WHERE mtime>$last_sync_time";


		$this->query($sql);

		$search = new search();

		while($this->next_record())
		{
			//$cache['table']='cal_events';
			$cache['id']=$this->f('id');
			$cache['user_id']=$this->f('user_id');
			$cache['name'] = addslashes($this->f('name'));
			//$cache['link_id'] = $this->f('link_id');
			$cache['link_type']=1;
			$cache['module']='calendar';
			$cache['description']=addslashes($this->f('description'));
			//$cache['url']=$GO_MODULES->modules['calendar']['url'].'event.php?event_id='.$this->f('id');
			$cache['type']=$this->f('todo')=='1' ? $cal_todo : $lang['calendar']['event'];
			$cache['keywords']=addslashes($search->record_to_keywords($this->Record)).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			$cache['acl_read']=$this->f('acl_read');
			$cache['acl_write']=$this->f('acl_write');

			$search->cache_search_result($cache);
		}
	}
	
	function __on_reminder_dismissed($reminder)
	{
		$event = $this->get_event($reminder['link_id']);
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

	function is_available($user_id, $start, $end)
	{
		$events = $this->get_events_in_array(array(), $user_id, $start, $end, true);
		return count($events) > 1 ? false : true;
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


	function set_event_status($event_id, $status, $email)
	{
		$sql = "UPDATE cal_participants SET status='$status' WHERE email='$email' AND event_id='$event_id'";
		return $this->query($sql);
	}

	function get_event_status($event_id, $email)
	{
		$sql = "SELECT status FROM cal_participants WHERE email='$email' AND event_id='$event_id'";
		if($this->query($sql))
		{
			if($this->next_record())
			{
				return $this->f('status');
			}
		}
		return false;
	}
	
	function __on_check_database(){
		global $GO_CONFIG, $GO_MODULES, $GO_LANGUAGE;
		
		echo 'Checking calendar folder permissions<br />';

		if(isset($GO_MODULES->modules['files']))
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc');
			$fs = new files();

			$sql = "SELECT e.name,e.id, c.acl_read, c.acl_write, c.user_id FROM cal_events e INNER JOIN cal_calendars c ON c.id=e.calendar_id";
			$this->query($sql);
			while($this->next_record())
			{
				echo 'Checking '.$this->f('name').'<br />';				
				$full_path = $GO_CONFIG->file_storage_path.'events/'.$this->f('id');
				$fs->check_share($full_path, $this->f('user_id'), $this->f('acl_read'), $this->f('acl_write'));
			}
		}
		echo 'Done<br /><br />';
	}

}
