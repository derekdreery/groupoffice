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

define('REPEAT_NONE', 0);
define('REPEAT_DAILY', 1);
define('REPEAT_WEEKLY', 2);
define('REPEAT_MONTH_DATE', 3);
define('REPEAT_MONTH_DAY', 4);
define('REPEAT_YEARLY', 5);

define('DB_DATETIME_FORMAT', 'Y-m-d H:i:00');
define('DB_DATE_FORMAT', 'Y-m-d');
define('DB_TIME_FORMAT', 'H:i:00');

define('PARTICIPANTS_WRITE', '0');
define('EVERYBODY_READ', '1');
define('EVERYBODY_WRITE', '2');
define('PRIVATE_EVENT', '3');
define('CUSTOM', '4');


require_once($GLOBALS['GO_CONFIG']->class_path.'xml/xml.class.inc');

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
	
	

	function get_backgrounds()
	{
		$sql = "SELECT * FROM cal_backgrounds";
		$this->query($sql);
		return $this->num_rows();
	}

	function add_background($background)
	{
		$background['id'] = $this->nextid('cal_backgrounds');
		if($this->insert_row('cal_backgrounds', $background))
		{
			return $background['id'];
		}
		return false;
	}

	function update_background($background)
	{
		return $this->update_row('cal_backgrounds', 'id', $background);
	}

	function get_background($background_id)
	{
		$sql = "SELECT * FROM cal_backgrounds WHERE id='$background_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function delete_background($background_id)
	{
		$sql = "DELETE FROM cal_calendar_backgrounds WHERE background_id='$background_id'";
		$this->query($sql);

		$sql = "DELETE FROM cal_backgrounds WHERE id='$background_id'";
		return $this->query($sql);
	}

	function get_calendar_backgrounds($calendar_id)
	{
		$sql = "SELECT cal_calendar_backgrounds.*, cal_backgrounds.color, ".
		"cal_backgrounds.name FROM cal_calendar_backgrounds INNER JOIN cal_backgrounds ON ".
		"cal_backgrounds.id=cal_calendar_backgrounds.background_id ".
		"WHERE cal_calendar_backgrounds.calendar_id=$calendar_id ".
		"ORDER BY weekday ASC, start_time ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function add_calendar_background($calendar_background)
	{
		$calendar_background['id'] = $this->nextid('cal_calendar_backgrounds');
		if($this->insert_row('cal_calendar_backgrounds', $calendar_background))
		{
			return $calendar_background['id'];
		}
		return false;
	}

	function update_calendar_background($calendar_background)
	{
		return $this->update_row('cal_calendar_backgrounds', 'id',$calendar_background);
	}

	function get_calendar_background($calendar_background_id)
	{
		$sql = "SELECT * FROM cal_calendar_backgrounds WHERE id='$calendar_background_id'";

		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function delete_calendar_background($calendar_background_id)
	{
		$sql = "DELETE FROM cal_calendar_backgrounds WHERE id='$calendar_background_id'";
		return $this->query($sql);
	}

	function event_to_html($event)
	{
		global $GO_LANGUAGE;

		require($GO_LANGUAGE->get_language_file('calendar'));

		$table = new table();
		$row = new table_row();
		$row->add_cell(new table_cell($cal_subject.':'));
		$row->add_cell(new table_cell($event['name']));
		$table->add_row($row);

		$cal = new calendar();
		$status=$cal->get_status($event['status_id']);
		$row = new table_row();
		$row->add_cell(new table_cell($cal_status.':'));
		$row->add_cell(new table_cell($cal_statuses[$status['name']]));
		$table->add_row($row);

		

		if (!empty($event['location'])) {
			$row = new table_row();
			$cell = new table_cell($sc_location.':');
			$cell->set_attribute('valign','top');
			$row->add_cell($cell);
			$row->add_cell(new table_cell(text_to_html($event['location'])));
			$table->add_row($row);
		}



		//don't calculate timezone offset for all day events
		$timezone_offset_string = get_timezone_offset($event['start_time']);
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
		//$event_time_format = $_SESSION['GO_SESSION']['time_format'].' '.$gmt_string;
		
		$row = new table_row();
		$cell = new table_cell('&nbsp;');
		$cell->set_attribute('colspan','2');
		$row->add_cell($cell);
		$table->add_row($row);

		$row = new table_row();
		$row->add_cell(new table_cell($sc_start_at.':'));
		$row->add_cell(new table_cell(date($event_datetime_format, gmt_to_local_time($event['start_time']))));
		$table->add_row($row);

		$row = new table_row();
		$row->add_cell(new table_cell($sc_end_at.':'));
		$row->add_cell(new table_cell(date($event_datetime_format, gmt_to_local_time($event['end_time']))));
		$table->add_row($row);



		if($event['repeat_type']!=REPEAT_NONE)
		{
			$row = new table_row();

			$cell = new table_cell();
			$cell->set_attribute('colspan','2');
			switch($event['repeat_type'])
			{
				case REPEAT_WEEKLY:
					$event = $this->shift_days_to_local($event);

					$days=array();
					if($event['sun']=='1')
					{
						$days[]=$GLOBALS['full_days'][0];
					}
					if($event['mon']=='1')
					{
						$days[]=$GLOBALS['full_days'][1];
					}
					if($event['tue']=='1')
					{
						$days[]=$GLOBALS['full_days'][2];
					}
					if($event['wed']=='1')
					{
						$days[]=$GLOBALS['full_days'][3];
					}
					if($event['thu']=='1')
					{
						$days[]=$GLOBALS['full_days'][4];
					}
					if($event['fri']=='1')
					{
						$days[]=$GLOBALS['full_days'][5];
					}
					if($event['sat']=='1')
					{
						$days[]=$GLOBALS['full_days'][6];
					}

					if(count($days)==1)
					{
						$daysStr=$days[0];
					}else
					{
						$daysStr = ' '.$cal_and.' '.array_pop($days);
						$daysStr = implode(', ', $days).$daysStr;
					}

					if($event['repeat_every']>1)
					{
						$cell->innerHTML .= sprintf($cal_repeats_at_not_every,
						$event['repeat_every'], $sc_weeks,
						$daysStr);
					}else
					{
						$cell->innerHTML .= sprintf($cal_repeats_at,
						$sc_week,
						$daysStr);
					}

					break;

				case REPEAT_DAILY:
					if($event['repeat_every']>1)
					{
						$cell->innerHTML .= sprintf($cal_repeats_not_every,
						$event['repeat_every'], $sc_days);
					}else
					{
						$cell->innerHTML .= sprintf($cal_repeats,
						$sc_day);
					}
					break;

				case REPEAT_MONTH_DATE:
					if($event['repeat_every']>1)
					{
						$cell->innerHTML .= sprintf($cal_repeats_not_every,
						$event['repeat_every'], $sc_months);
					}else
					{
						$cell->innerHTML .= sprintf($cal_repeats,
						$sc_month);
					}
					break;

				case REPEAT_MONTH_DAY:

					$event = $this->shift_days_to_local($event);

					$days=array();
					if($event['sun']=='1')
					{
						$days[]=$GLOBALS['full_days'][0];
					}
					if($event['mon']=='1')
					{
						$days[]=$GLOBALS['full_days'][1];
					}
					if($event['tue']=='1')
					{
						$days[]=$GLOBALS['full_days'][2];
					}
					if($event['wed']=='1')
					{
						$days[]=$GLOBALS['full_days'][3];
					}
					if($event['thu']=='1')
					{
						$days[]=$GLOBALS['full_days'][4];
					}
					if($event['fri']=='1')
					{
						$days[]=$GLOBALS['full_days'][5];
					}
					if($event['sat']=='1')
					{
						$days[]=$GLOBALS['full_days'][6];
					}

					if(count($days)==1)
					{
						$daysStr=$days[0];
					}else
					{
						$daysStr = ' '.$cal_and.' '.array_pop($days);
						$daysStr = implode(', ', $days).$daysStr;
					}

					if($event['repeat_every']>1)
					{
						$cell->innerHTML .= sprintf($cal_repeats_at_not_every,
						$event['repeat_every'], $sc_months, $daysStr);
					}else
					{
						$cell->innerHTML .= sprintf($cal_repeats_at,
						$sc_month, $daysStr);
					}
					break;

				case REPEAT_YEARLY:
					if($event['repeat_every']>1)
					{
						$cell->innerHTML .= sprintf($cal_repeats_not_every,
						$event['repeat_every'], $sc_years);
					}else
					{
						$cell->innerHTML .= sprintf($cal_repeats,
						$sc_year);
					}
					break;
			}

			if ($event['repeat_forever'] != '1') {
				$cell->innerHTML .= ' '.$cal_until.' '.date($_SESSION['GO_SESSION']['date_format'], $event['repeat_end_time']);
			}
			$row->add_cell($cell);
			$table->add_row($row);
		}
		
		$row = new table_row();
		$cell = new table_cell('&nbsp;');
		$cell->set_attribute('colspan','2');
		$row->add_cell($cell);
		$table->add_row($row);

		if(!empty($event['custom_fields']))
		{
			$fieldsNode =text_to_xml($event['custom_fields']);
			$fields = $fieldsNode->children();

			if(count($fields) > 0 && isset($fields[0]->_name))
			{
				$row = new table_row();
				$cell = new table_cell('&nbsp;');
				$cell->set_attribute('colspan','2');
				$row->add_cell($cell);
				$table->add_row($row);
				foreach(	$fields as $inputNode)
				{
					$row = new table_row();
					$type = $inputNode->get_attribute('type') ? $inputNode->get_attribute('type') : 'text';
					switch($type)
					{
						default:
							$row->add_cell(new table_cell($inputNode->get_attribute('name').':'));
							$row->add_cell(new table_cell($inputNode->get_attribute('value')));
							break;

						case 'checkbox':
							$row->add_cell(new table_cell($inputNode->get_attribute('name').':'));
							$value = $inputNode->get_attribute('value')=='1' ? $GLOBALS['cmdYes'] : $GLOBALS['cmdNo'];
							$row->add_cell(new table_cell($value));
							break;
					}
					$table->add_row($row);
				}
				
				$row = new table_row();
				$cell = new table_cell('&nbsp;');
				$cell->set_attribute('colspan','2');
				$row->add_cell($cell);
				$table->add_row($row);
			}
		}
		
		
		$calendars=array();
		$this->get_calendars_from_event($event['id']);
		while($this->next_record())
		{
			$calendars[]=$this->f('name');
		}
		
		$row = new table_row();
		$cell = new table_cell($cal_event_calendars.':');
		$cell->set_attribute('valign','top');
		$row->add_cell($cell);
		$row->add_cell(new table_cell(implode(', ', $calendars)));
		$table->add_row($row);
		
		
		
		if (!empty($event['description'])) {
			$row = new table_row();
			$cell = new table_cell($GLOBALS['strDescription'].':');
			$cell->set_attribute('valign','top');
			$row->add_cell($cell);
			$row->add_cell(new table_cell(text_to_html($event['description'])));
			$table->add_row($row);
		}
		
		
		
		
		return $table->get_html();
	}



	function get_statuses($type='VEVENT')
	{
		$sql = "SELECT * FROM cal_statuses WHERE type='$type'";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_status($status_id)
	{
		$sql = "SELECT * FROM cal_statuses WHERE id='$status_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function get_status_id($name)
	{
		$sql = "SELECT * FROM cal_statuses WHERE name='$name'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->f('id');
		}
		return false;
	}

	function add_custom_field($group_id, $field)
	{

		$existing_group = $this->get_group($group_id);
		if(trim($existing_group['custom_fields']) == '' || trim($existing_group['custom_fields']) == '<fields></fields>')
		{
			$existing_group ['custom_fields'] = '<fields></fields>';
			$field['id']='1';
		}else
		{
			$field['id']='0';
		}
		$fieldsNode = text_to_xml ($existing_group['custom_fields']);


		for($i=0;$i<count($fieldsNode->_children);$i++)
		{
			if(isset($fieldsNode->_children[$i]->_attribs['name']))
			{
				$fieldsNode->_children[$i]->_attribs['name']=htmlspecialchars($fieldsNode->_children[$i]->_attribs['name']);
			}
		}
		if($field['id'] == '0')
		{
			$field['id'] = count($fieldsNode->_children)+1;
		}
		$fieldsNode->new_child('input', $field);
		$group['id'] = $group_id;
		$group['custom_fields'] = mysql_escape_string($fieldsNode->get_xml());
		if($this->update_group($group))
		{
			return $field['id'];
		}
		return false;
	}


	function group_values_to_xml($values, $group_id)
	{
		$has_values=false;
		$doc = text_to_xml('<fields></fields>');
		$custom_fields = $this->get_custom_fields($group_id);
		foreach(	$custom_fields as $inputNode)
		{
			if($inputNode->get_attribute('name')!='')
			{
				$has_values=true;
				$types[$inputNode->get_attribute('name')] = $inputNode->get_attribute('type');

				$value = isset($values[$inputNode->get_attribute('name')]) ? $values[$inputNode->get_attribute('name')] : '';
				$doc->new_child('input', array('name'=>htmlspecialchars($inputNode->get_attribute('name')), 'type'=>$inputNode->get_attribute('type'), 'value'=>htmlspecialchars($value)));
			}
		}
		if($has_values)
		{
			return $doc->get_xml();
		}
		return '';
	}

	function group_xml_to_values($xml)
	{
		$values = array();
		if(!empty($xml))
		{
			$doc = text_to_xml($xml);
			foreach($doc->children() as $input)
			{
				$value = array('name'=>$input->get_attribute('name'), 'type'=>$input->get_attribute('type'), 'value'=>$input->get_attribute('value'));
				$values[] = $value;
			}
		}
		return $values;
	}

	function get_custom_fields($group_id)
	{
		$group = $this->get_group($group_id);
		if($group['custom_fields'] == '')
		{
			$group ['custom_fields'] = '<fields></fields>';
		}
		$fieldsNode = text_to_xml ($group['custom_fields']);
		return $fieldsNode->children();
	}
	function delete_custom_field($group_id, $custom_field_id)
	{
		$newxml = text_to_xml('<fields></fields>');

		$fields = $this->get_custom_fields($group_id);

		$id=1;
		foreach($fields as $input)
		{
			if($input->get_attribute('id') != $custom_field_id)
			{
				$newxml->new_child('input', array('id'=>$id, 'name'=>htmlspecialchars($input->get_attribute('name')), 'type'=>$input->get_attribute('type')));
				$id++;
			}
		}
		$group['id'] = $group_id;
		$group['custom_fields'] = mysql_escape_string($newxml->get_xml());
		return $this->update_group($group);
	}

	function update_custom_field($group_id, $field)
	{
		$existing_group = $this->get_group($group_id);
		if($existing_group['custom_fields'] == '')
		{
			$existing_group ['custom_fields'] = '<fields></fields>';
		}
		$xml = text_to_xml ($existing_group['custom_fields']);

		for($i=0;$i<count($xml->_children);$i++)
		{
			if($xml->_children[$i]->get_attribute('id') == $field['id'])
			{
				$xml->_children[$i]->_attribs=$field;
				$group['id'] = $group_id;
				$group['custom_fields'] = mysql_escape_string($xml->get_xml());
				return $this->update_group($group);
			}else
			{
				$xml->_children[$i]->_attribs['name']=htmlspecialchars($xml->_children[$i]->_attribs['name']);
			}
		}
		return false;
	}

	function get_custom_field($group_id, $field_id)
	{
		$fields = $this->get_custom_fields($group_id);
		for($i=0;$i<$fields;$i++)
		{
			if($fields[$i]->get_attribute('id') == $field_id)
			{
				return $fields[$i]->_attribs;
			}
		}
		return false;
	}

	function add_group_admin($group_id, $user_id)
	{
		$sql = "INSERT INTO cal_group_admins VALUES('$group_id', '$user_id');";
		return $this->query($sql);
	}

	function delete_group_admin($group_id, $user_id)
	{
		$sql = "DELETE FROM cal_group_admins WHERE group_id='$group_id' AND user_id='$user_id';";
		return $this->query($sql);
	}

	function get_resource_group_admins($group_id)
	{
		$sql = "SELECT * FROM cal_group_admins WHERE group_id='$group_id'";
		$this->query($sql);
		return $this->num_rows();
	}

	function is_resource_group_admin($user_id, $group_id)
	{
		$sql = "SELECT * FROM cal_group_admins WHERE group_id='$group_id' AND user_id='$user_id'";
		$this->query($sql);
		return $this->next_record();
	}


	function get_resource_groups()
	{
		$sql = "SELECT * FROM cal_groups ORDER BY name ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_writable_resource_groups($user_id)
	{
		$sql = "SELECT DISTINCT cal_groups.* FROM cal_groups ".
		"INNER JOIN acl ON cal_groups.acl_write = acl.acl_id ".
		"LEFT JOIN users_groups ON acl.group_id=users_groups.group_id ".
		"WHERE (acl.user_id=$user_id ".
		"OR users_groups.user_id=$user_id) ".
		"ORDER BY name ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_groups()
	{
		$sql = "SELECT * FROM cal_groups ORDER BY name ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function add_group($group)
	{
		$group['id'] = $this->nextid('cal_groups');
		if($group['id'] && $this->insert_row('cal_groups',$group))
		{
			return $group['id'];
		}
	}

	function get_event_resources($event_id)
	{
		if($event_id>0)
		{
			$sql = "SELECT cal_events.*,cal_events_calendars.calendar_id FROM cal_events ".
			"INNER JOIN cal_events_calendars ON cal_events.id=cal_events_calendars.event_id ".
			"WHERE cal_events.event_id='$event_id'";
			$this->query($sql);
			return $this->num_rows();
		}
		return false;
	}

	function get_event_resource($event_id, $calendar_id)
	{
		if($event_id>0 && $calendar_id>0)
		{
			$sql = "SELECT cal_events.*,cal_events_calendars.calendar_id FROM cal_events ".
			"INNER JOIN cal_events_calendars ON cal_events.id=cal_events_calendars.event_id ".
			"WHERE cal_events.event_id='$event_id' AND cal_events_calendars.calendar_id='$calendar_id'";

			$this->query($sql);
			if($this->next_record())
			{
				return $this->Record;
			}
		}
		return false;
	}

	function update_group($group)
	{
		return $this->update_row('cal_groups', 'id', $group);
	}

	function delete_group($group_id)
	{
		$cal = new calendar();
		$this->get_calendars($group_id);
		while($this->next_record())
		{
			$cal->delete_calendar($this->f('id'));
		}
		return $this->query("DELETE FROM cal_groups WHERE id='$group_id'");
	}

	function get_group($group_id)
	{
		$sql = "SELECT * FROM cal_groups WHERE id='$group_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function get_group_by_name($group_name)
	{
		$sql = "SELECT * FROM cal_groups WHERE name='$group_name'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function copy_completed($event_id)
	{
		global $GO_LINKS;
		/*
		If a recurring task is completed we copy it to a new task and recur that again
		*/

		$event = $this->get_event($event_id);

		if($event['repeat_type'] > REPEAT_NONE && $next_recurrence_time = $this->get_next_recurrence_time(0,$event['start_time'] ,$event))
		{
			unset($event['completion_time'], $event['id']);

			$old_link_id = $event['link_id'];
			$event['link_id'] = $GO_LINKS->get_link_id();

			$GO_LINKS->copy_links($old_link_id, $event['link_id'] , 1);


			$duration = $event['end_time']-$event['start_time'];
			$event['start_time'] = $next_recurrence_time;
			$event['end_time'] = $next_recurrence_time+$duration;


			if(!isset($event['todo']) || $event['todo'] == '')
			{
				$event['todo'] = '0';
			}

			$event=array_map('addslashes',$event);
			if($new_event_id = $this->add_event($event))
			{
				$cal = new calendar();
				$this->get_event_subscribtions($event_id);
				while($this->next_record())
				{
					$cal->subscribe_event($new_event_id, $this->f('calendar_id'));
				}
			}
		}
		return true;
	}

	function copy_event($event_id, $new_values=array())
	{
		global $GO_SECURITY;

		if($src_event = $dst_event = $this->get_event($event_id))
		{
			unset($dst_event['id']);

			foreach($new_values as $key=>$value)
			{
				$dst_event[$key] = $value;
			}

			$dst_event = array_map('addslashes', $dst_event);

			return $this->add_event($dst_event);
		}
		return false;
	}

	/*
	takes a sting YYYY-MM-DD HH:MM in GMT time and converts it to an array with
	hour, min etc. with	a timezone offset. If 0000 or 00 is set in a date
	(not time) then it will be replaced with current locale	date.
	*/
	function explode_datetime($datetime_stamp, $timezone_offset)
	{
		$local_time = get_time();

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

	function add_view($user_id, $name, $start_hour, $end_hour, $event_colors_override, $time_interval, $acl_read, $acl_write)
	{
		$view_id = $this->nextid("cal_views");

		if ($view_id > 0)
		{
			$sql = "INSERT INTO cal_views (id, user_id, name, start_hour, end_hour, event_colors_override, time_interval, acl_read, acl_write) ".
			"VALUES ('$view_id', '$user_id', '$name', '$start_hour', '$end_hour', '$event_colors_override', '$time_interval', '$acl_read', '$acl_write')";
			$this->query($sql);
			return $view_id;
		}
		return false;
	}

	function update_view($view_id, $name, $start_hour, $end_hour, $event_colors_override, $time_interval)
	{
		$sql = "UPDATE cal_views SET name='$name', start_hour='$start_hour', ".
		"end_hour='$end_hour', event_colors_override='$event_colors_override', time_interval='$time_interval' WHERE id='$view_id'";
		return $this->query($sql);
	}

	function delete_view($view_id)
	{
		if($this->query("DELETE FROM cal_views_calendars WHERE view_id='$view_id'"))
		{
			return $this->query("DELETE FROM cal_views WHERE id='$view_id'");
		}
	}

	function get_user_views($user_id)
	{
		$sql = "SELECT * FROM cal_views WHERE user_id='$user_id'";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_authorized_views($user_id, $start=0, $offset=0)
	{
		$sql = "SELECT DISTINCT cal_views . * ".
		"FROM cal_views ".
		"	INNER JOIN acl ON ( cal_views.acl_read = acl.acl_id ".
		"OR cal_views.acl_write = acl.acl_id ) ".
		"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
		"WHERE acl.user_id=$user_id ".
		"OR users_groups.user_id=$user_id".
		"	ORDER BY cal_views.name ASC";

		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
		}
		return $count;
	}

	function get_writable_views($user_id)
	{

		$sql = "SELECT DISTINCT cal_views . * ".
		"FROM cal_views ".
		"	INNER JOIN acl ON cal_views.acl_write = acl.acl_id ".
		"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
		"WHERE acl.user_id=$user_id ".
		"OR users_groups.user_id=$user_id".
		"	ORDER BY cal_views.name ASC";

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
		return false;
	}

	function get_view_calendars($view_id)
	{
		$sql = "SELECT cal_calendars.name, cal_calendars.user_id, cal_calendars.id, cal_views_calendars.background FROM cal_calendars ".
		"INNER JOIN cal_views_calendars ON cal_calendars.id=cal_views_calendars.calendar_id ".
		"WHERE cal_views_calendars.view_id='$view_id' ORDER BY cal_calendars.name ASC";

		$this->query($sql);
		$calendars = array();
		while($this->next_record())
		{
			$calendars[] = $this->Record;
		}

		if(isset($calendars))
		{
			return $calendars;
		}
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

	function get_view_calendar($calendar_id, $view_id)
	{
		$sql = "SELECT * FROM cal_views_calendars WHERE calendar_id='$calendar_id' AND view_id='$view_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
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

	function add_participant($event_id, $name, $email, $user_id = 0)
	{
		$id = $this->nextid("cal_participants");

		if ($id > 0)
		{
			$sql = 	"INSERT INTO cal_participants (id, event_id, user_id, name, email) ".
			"VALUES ('$id', '$event_id', '$user_id',  '$name', '$email')";
			if ($this->query($sql))
			{
				return $id;
			}else
			{
				return -1;
			}
		}else
		{
			return -1;
		}
	}

	function delete_participant($event_id, $email)
	{
		$sql = "DELETE FROM cal_participants WHERE event_id='$event_id' AND email='$email'";
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

	function add_calendar($user_id, $name, $start_hour, $end_hour, $background='FFFFCC',  $group_id=0, $time_interval=1800)
	{
		global $GO_SECURITY;
		$acl_read = $GO_SECURITY->get_new_acl('calendar read: '.$name, $user_id);
		$acl_write = $GO_SECURITY->get_new_acl('calendar write: '.$name, $user_id);
		if ($acl_read > 0 && $acl_write > 0)
		{
			$calendar_id = $this->nextid("cal_calendars");
			if ($calendar_id > 0)
			{
				$sql  = "INSERT INTO cal_calendars (id, user_id, name, acl_read, acl_write, start_hour, end_hour, background, group_id, time_interval) ";
				$sql .= "VALUES ('$calendar_id', '$user_id', '$name', '$acl_read', '$acl_write', '$start_hour', '$end_hour', '$background', '$group_id', '$time_interval')";
				if ($this->query($sql))
				{
					$GO_SECURITY->add_user_to_acl($user_id,$acl_write);
					return $calendar_id;
				}
			}else
			{
				$GO_SECURITY->delete_acl($acl_read);
				$GO_SECURITY->delete_acl($acl_write);
			}
		}
		return false;
	}

	function delete_calendar($calendar_id)
	{
		global $GO_SECURITY;
		$delete = new calendar;

		if($calendar = $this->get_calendar($calendar_id))
		{
			$sql = "SELECT * FROM cal_events_calendars WHERE calendar_id='$calendar_id'";
			$this->query($sql);

			while ($this->next_record())
			{
				$sql = "SELECT * FROM cal_events_calendars WHERE event_id='".$this->f("event_id")." '";
				$delete->query($sql);
				if ($delete->num_rows() < 2)
				{
					$delete->delete_event($this->f('event_id'));
				}
			}
			$sql = "DELETE FROM cal_events_calendars WHERE calendar_id='$calendar_id'";
			$this->query($sql);

			$sql = "DELETE FROM cal_views_calendars WHERE calendar_id='$calendar_id'";
			$this->query($sql);

			$sql= "DELETE FROM cal_calendars WHERE id='$calendar_id'";
			$this->query($sql);

			$GO_SECURITY->delete_acl($calendar['acl_read']);
			$GO_SECURITY->delete_acl($calendar['acl_write']);
			return true;
		}
		return false;
	}

	function update_calendar($calendar_id, $user_id,  $name, $start_hour, $end_hour, $background, $group_id=0, $time_interval=1800)
	{
		$sql = "UPDATE cal_calendars SET background='$background', user_id='$user_id',".
		"name='$name', start_hour='$start_hour', end_hour='$end_hour', group_id='$group_id', time_interval='$time_interval' WHERE id='$calendar_id'";
		return $this->query($sql);
	}


	function get_calendar($calendar_id=0)
	{
		if($calendar_id > 0)
		{
			$sql = "SELECT * FROM cal_calendars WHERE id='$calendar_id'";
			$this->query($sql);
			if ($this->next_record())
			{
				return $this->Record;
			}else
			{
				
				return $this->get_calendar();
			}
		}else
		{
			global $GO_SECURITY;

			$this->get_user_calendars($GO_SECURITY->user_id);
			if ($this->next_record())
			{
				return $this->Record;
			}else
			{
				global $GO_USERS;

				$user = $GO_USERS->get_user($GO_SECURITY->user_id);
				$calendar_name = format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'last_name');
				$new_calendar_name = $calendar_name;
				$x = 1;
				while($this->get_calendar_by_name(addslashes($new_calendar_name)))
				{
					$new_calendar_name = $calendar_name.' ('.$x.')';
					$x++;
				}
				if (!$calendar_id = $this->add_calendar($GO_SECURITY->user_id, addslashes($new_calendar_name), 7, 20))
				{
					return false;
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

	function get_user_calendars($user_id, $group_id=-1)
	{
		$sql = "SELECT * FROM cal_calendars WHERE user_id='$user_id'";
		if($group_id>-1)
		{
			$sql .= " AND group_id=$group_id";
		}
		$sql .=" ORDER BY name ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_calendars($group_id=-1)
	{
		$sql = "SELECT * FROM cal_calendars";
		if($group_id>-1)
		{
			$sql .= " WHERE group_id='$group_id'";
		}
		$sql .= " ORDER BY name ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_authorized_calendars($user_id, $group_id=-1, $start=0, $offset=0)
	{
		$sql = "SELECT DISTINCT cal_calendars. * ".
		"FROM cal_calendars ".
		"	INNER JOIN acl ON ( cal_calendars.acl_read = acl.acl_id ".
		"OR cal_calendars.acl_write = acl.acl_id ) ".
		"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
		"WHERE (acl.user_id=$user_id ".
		"OR users_groups.user_id=$user_id)";
		if($group_id > -1)
		{
			$sql .= " AND cal_calendars.group_id='$group_id'";
		}
		$sql .= "	ORDER BY cal_calendars.name ASC";

		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
		}
		return $count;
	}

	function get_authorized_resources($user_id, $start=0, $offset=0)
	{
		$sql = "SELECT DISTINCT cal_calendars . * ".
		"FROM cal_calendars ".
		"	INNER JOIN acl ON ( cal_calendars.acl_read = acl.acl_id ".
		"OR cal_calendars.acl_write = acl.acl_id ) ".
		"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
		"WHERE (acl.user_id=$user_id ".
		"OR users_groups.user_id=$user_id) AND cal_calendars.group_id!='1'";

		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
		}
		return $count;
	}

	function get_writable_calendars($user_id, $group_id=-1, $start=0, $offset=0)
	{
		$sql = "SELECT DISTINCT cal_calendars . * ".
		"FROM cal_calendars ".
		"INNER JOIN acl ON cal_calendars.acl_write = acl.acl_id ".
		"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
		"WHERE (acl.user_id=$user_id ".
		"OR users_groups.user_id=$user_id)";
		if($group_id > -1)
		{
			$sql .= " AND cal_calendars.group_id='$group_id'";
		}
		$sql .= "	ORDER BY cal_calendars.name ASC";

		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
		}
		return $count;
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


	/*
	Times in GMT!
	*/

	function add_event($event)
	{
		unset($event['read_permission'], $event['write_permission']);

		if (!isset($event['user_id']) || $event['user_id'] == 0) {
			global $GO_SECURITY;
			$event['user_id'] = $GO_SECURITY->user_id;
		}

		if(!isset($event['ctime']) || $event['ctime'] == 0)
		{
			$event['ctime']  =  get_gmt_time();
		}

		if(!isset($event['mtime']) || $event['mtime'] == 0)
		{
			$event['mtime']  =  $event['ctime'];
		}

		if(!isset($event['background']) || $event['background'] == '')
		{
			$event['background']  =  'FFFFCC';
		}

		if(!isset($event['todo']) || $event['todo'] != '1')
		{
			$event['todo']='0';
		}

		if(!isset($event['timezone']))
		{
			$event['timezone']=$_SESSION['GO_SESSION']['timezone'];
		}

		if(!isset($event['DST']))
		{
			$event['DST']=$_SESSION['GO_SESSION']['DST'];
		}

		if(!isset($event['status_id']))
		{
			$event['status_id'] = $event['todo'] == '1' ? 6 : 1;
		}
		
		if(!isset($event['link_id']))
		{
			global $GO_LINKS;
			$event['link_id'] = $GO_LINKS->get_link_id();
		}

		$event['id'] = $this->nextid("cal_events");

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
		$record = array_map('addslashes', $event);

		$sql = "SELECT id FROM cal_events INNER JOIN cal_events_calendars ON cal_events.id=cal_events_calendars.event_id WHERE ".
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
			$event['mtime']  = get_gmt_time();
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

	function clear_subscriptions($event_id)
	{
		$sql = "DELETE FROM cal_events_calendars WHERE event_id='$event_id'";
		return $this->query($sql);
	}

	function subscribe_event($event_id, $calendar_id)
	{
		$sql = "INSERT INTO cal_events_calendars (calendar_id, event_id) VALUES ('$calendar_id','$event_id')";
		$this->query($sql);

		$update_event['id'] = $event_id;
		$update_event['mtime'] = get_gmt_time();
		return $this->update_event($update_event);
	}

	function unsubscribe_event($event_id, $calendar_id)
	{
		$sql = "DELETE FROM cal_events_calendars WHERE calendar_id='$calendar_id' AND event_id='$event_id'";
		return $this->query($sql);
	}

	function event_is_subscribed($event_id, $calendar_id)
	{
		$sql = "SELECT * FROM cal_events_calendars WHERE event_id='$event_id' AND calendar_id='$calendar_id'";
		$this->query($sql);
		return $this->next_record();
	}

	function get_event_subscribtions($event_id)
	{
		$sql = "SELECT * FROM cal_events_calendars WHERE event_id='$event_id'";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_calendars_from_event($event_id)
	{
		$sql = "SELECT id,name,background FROM cal_calendars INNER JOIN cal_events_calendars ".
		"ON cal_events_calendars.calendar_id=cal_calendars.id ".
		"WHERE cal_events_calendars.event_id='$event_id'";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_remind_time($user_id, $event_id)
	{
		$sql = "SELECT remind_time FROM cal_reminders WHERE event_id='$event_id' AND user_id='$user_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->f('remind_time');
		}
		return false;
	}


	function add_reminder($reminder)
	{
		return $this->insert_row('cal_reminders', $reminder);
	}

	function update_reminder($user_id, $event_id, $remind_time)
	{
		return $this->query("UPDATE cal_reminders SET remind_time='$remind_time',email_sent='0' WHERE user_id='$user_id' AND event_id='$event_id'");
	}

	function reminder_mail_sent($user_id, $event_id)
	{
		return $this->query("UPDATE cal_reminders SET email_sent='1' WHERE user_id='$user_id' AND event_id='$event_id'");
	}

	function delete_reminder($user_id, $event_id)
	{
		return $this->query("DELETE FROM cal_reminders WHERE user_id='$user_id' AND event_id='$event_id'");
	}
	
	function delete_reminders($user_id)
	{
		return $this->query("DELETE FROM cal_reminders WHERE user_id='$user_id'");
	}
	/*
	returns next starting time of an event in GMT timezone
	*/

	function get_next_recurrence_time($event_id, $gmt_time=0, $event='')
	{



		//echo date('Ymd G:i', $gmt_time);
		if ($event == '')
		{
			if(!$event = $this->get_event($event_id))
			{
				return false;
			}
		}

		if ($gmt_time == 0)
		{
			$gmt_time = get_gmt_time();
		}

		if($gmt_time<0)
		{
			$gmt_time=0;
		}

		$event_tzo = $event['timezone'];
		//Daily event going into other dst zone fail
		//if ($event['DST'] > 0 && date('I', $event['start_time']) != 0) {
		if ($event['DST'] > 0 && date('I', $gmt_time) != 0) {
			$event_tzo += $event['DST'];
		}
		$event = $this->shift_days_to_local($event, $event_tzo);

		$event_tzo=$event_tzo*3600;

		$event['start_time'] += $event_tzo;
		$gmt_time += $event_tzo;

		//echo ' -> '.date('Ymd G:i', $gmt_time).'<br />';


		if ($event['repeat_forever'] == '0' && ($event['repeat_end_time'] < $gmt_time && $event['repeat_type'] != REPEAT_NONE))
		{
			return 0;
		}
		$duration = 0;//$event['end_time']-$event['start_time'];
		if ($event['start_time'] > $gmt_time && $event['repeat_type'] != REPEAT_NONE)
		{
			$start_time = $event['start_time']-$duration;

		}else
		{
			$start_time = $gmt_time;
		}


		$original_tzo = get_timezone_offset($event['start_time']);

		$year = date('Y', $start_time);
		$month = date('n', $start_time);
		$day = date('j', $start_time);



		$event['start_year'] = date('Y', $event['start_time']);
		$event['start_month'] = date('n', $event['start_time']);
		$event['start_day'] = date('j', $event['start_time']);
		$event['start_hour'] = date('G', $event['start_time']);
		$event['start_min'] = date('i', $event['start_time']);

		$day_db_field[0] = 'sun';
		$day_db_field[1] = 'mon';
		$day_db_field[2] = 'tue';
		$day_db_field[3] = 'wed';
		$day_db_field[4] = 'thu';
		$day_db_field[5] = 'fri';
		$day_db_field[6] = 'sat';

		switch($event['repeat_type'])
		{
			case REPEAT_NONE:
				if($event['start_time'] > $gmt_time)
				{
					return $event['start_time']-$event_tzo;
				}else
				{
					return 0;
				}
				break;

			case REPEAT_WEEKLY:
				if ($event['repeat_every'] > 1)
				{
					$interval = $start_time - $event['start_time'];
					$interval_weeks = floor($interval/604800);
					$devided = $interval_weeks/$event['repeat_every'];
					$rounded = ceil($devided);
					$last_repeat_week = $event['repeat_every']*$rounded*604800;

					$last_occurence_week = $event['start_time']+$last_repeat_week;
					$year = date('Y', $last_occurence_week);
					$month = date('n', $last_occurence_week);
					$day = date('j', $last_occurence_week);
				}else
				{
					$last_occurence_week= $start_time;
				}

				$weekday = date("w", $last_occurence_week);

				for ($loop=0;$loop<2;$loop++)
				{
					$day_add=$loop*$event['repeat_every']*7;

					for ($i=0;$i<7;$i++)
					{
						if ($weekday>6)
						{
							$weekday -= 7;
						}

						if ($event[$day_db_field[$weekday]] == '1')
						{
							$occurence_time = mktime($event['start_hour'], $event['start_min'], 0, $month, $day+$i+$day_add, $year);
							$new_tzo = get_timezone_offset($occurence_time);
							$diff = ($original_tzo-$new_tzo)*3600;
							$occurence_time += $diff;

							if ($event['repeat_forever'] == '0' && ($occurence_time > $event['repeat_end_time']))
							{
								return 0;
							}elseif ($occurence_time > $gmt_time)
							{
								return $occurence_time-$event_tzo;
							}
						}
						$weekday++;
					}
				}

				break;

			case REPEAT_DAILY:

				if ($event['repeat_every'] > 1)
				{
					$interval = $start_time - $event['start_time'];
					$interval_days = floor($interval/86400);
					$devided = $interval_days/$event['repeat_every'];
					$rounded = ceil($devided);
					$last_repeat_day = $event['repeat_every']*$rounded*86400;
					$last_occurence_day = $event['start_time']+$last_repeat_day;
					$year = date('Y', $last_occurence_day);
					$month = date('n', $last_occurence_day);
					$day = date('j', $last_occurence_day);
				}


				$occurence_time = mktime($event['start_hour'], $event['start_min'], 0, $month, $day, $year);

				//echo date('Ymd G:i', $occurence_time).'<br>';

				$new_tzo = get_timezone_offset($occurence_time);
				$diff = ($original_tzo-$new_tzo)*3600;
				$occurence_time += $diff;

				//echo date('Ymd G:i', $occurence_time).'  '.date('Ymd G:i', $start_time).'<br />';
				if ($occurence_time <= $gmt_time)
				{
					$occurence_time =  mktime($event['start_hour'], $event['start_min'], 0, $month, $day+$event['repeat_every'], $year);
					$new_tzo = get_timezone_offset($occurence_time);
					$diff = ($original_tzo-$new_tzo)*3600;
					$occurence_time += $diff;
				}
				//echo date('Ymd', $occurence_time).'  '.date('Ymd', $start_time).'<br />';
				if ($event['repeat_forever'] == '0' && $occurence_time > $event['repeat_end_time'])
				{
					return 0;
				}else
				{
					return $occurence_time-$event_tzo;
				}


				break;

			case REPEAT_MONTH_DATE:
				if ($event['repeat_every'] > 1)
				{
					$interval_years = $year-date('Y', $event['start_time']);
					$interval_months = $month - date('n', $event['start_time']);
					$interval_months = 12*$interval_years+$interval_months;
					$devided = $interval_months/$event['repeat_every'];
					$rounded = ceil($devided);
					$last_repeat_month = $event['repeat_every']*$rounded;


					$repeat_year = date('Y', $event['start_time']);
					$repeat_month = date('n', $event['start_time']);
					$repeat_day = date('j', $event['start_time']);

					$last_occurence_month = mktime(0,0,0, $repeat_month+$last_repeat_month, $repeat_day, $repeat_year);
				}else
				{
					$last_occurence_month = $start_time;
				}
				$year = date('Y', $last_occurence_month);
				$month = date('n', $last_occurence_month);

				$occurence_time = 0;//mktime($event['start_hour'], $event['start_min'], 0, $month, $event['start_day'], $year);
				while ($occurence_time <= $gmt_time)
				{
					$occurence_time = mktime($event['start_hour'], $event['start_min'], 0, $month, $event['start_day'], $year);
					$new_tzo = get_timezone_offset($occurence_time);
					$diff = ($original_tzo-$new_tzo)*3600;
					$occurence_time += $diff;

					/*echo '&nbsp;&nbsp;&nbsp;'.date('j', $occurence_time).' '.$event['start_day'].' '.date('Ymd G:i', $occurence_time).'<br>';
					//while(date('j', $occurence_time) != $event['start_day'])
					{
					$month+=$event['repeat_every'];
					$occurence_time = mktime($event['start_hour'], $event['start_min'], 0, $month, $event['start_day'], $year);
					$new_tzo = get_timezone_offset($occurence_time);
					$diff = ($original_tzo-$new_tzo)*3600;
					$occurence_time += $diff;
					echo '&nbsp;&nbsp;&nbsp;'.date('j', $occurence_time).' '.$event['start_day'].' '.date('Ymd G:i', $occurence_time).'<br>';
					}	*/
					$month+=$event['repeat_every'];
				}

				if ($event['repeat_forever'] == '0' && $occurence_time > $event['repeat_end_time'])
				{
					return 0;
				}else
				{
					return $occurence_time-$event_tzo;
				}
				break;

			case REPEAT_MONTH_DAY:



				if ($event['repeat_every'] > 1)
				{
					$interval_years = $year-date('Y', $event['start_time']);
					$interval_months = $month - date('n', $event['start_time']);
					$interval_months = 12*$interval_years+$interval_months;

					$devided = $interval_months/$event['repeat_every'];
					$rounded = ceil($devided);
					$last_repeat_month = $event['repeat_every']*$rounded;


					$repeat_year = date('Y', $event['start_time']);
					$repeat_month = date('n', $event['start_time']);
					$repeat_day = date('j', $event['start_time']);

					$last_occurence_month = mktime(0,0,0, $repeat_month+$last_repeat_month, $repeat_day, $repeat_year);
					//echo '&nbsp;&nbsp;&nbsp;'.date('Ymd', $last_occurence_month).' == '.$month.'<br>';
				}else
				{
					$last_occurence_month = $start_time;
				}

				$year = date('Y', $last_occurence_month);
				$month = date('n', $last_occurence_month);
				$day = date('j', $last_occurence_month);


				for($m=$month;$m<=($month+$event['repeat_every']);$m+=$event['repeat_every'])
				{
					//echo $m.'<br>';
					for($d=0;$d<31;$d++)
					{
						//$occurence_time = mktime($event['start_hour'], $event['start_min'], 0, $m, $day+$d, $year);
						$occurence_time = mktime($event['start_hour'], $event['start_min'], 0, $m, $d, $year);
						$new_tzo = get_timezone_offset($occurence_time);
						$diff = ($original_tzo-$new_tzo)*3600;
						$occurence_time += $diff;
						//echo date('Ymd', $occurence_time).' == '.$month.'<br>';
						$weekday = date("w", $occurence_time);
						//echo date('Ymd', $occurence_time).' == '.$weekday.'<br>';

						if ($event[$day_db_field[$weekday]] == '1')
						{
							//echo ceil(date('j',$occurence_time)/7).' = '.$event['month_time'].'<br>';
							if (ceil(date('j',$occurence_time)/7) == $event['month_time'])
							{
								if ($event['repeat_forever'] == '0' && $occurence_time > $event['repeat_end_time'])
								{
									return 0;
								}elseif ($occurence_time > $gmt_time)
								{
									//echo 'FOUND: '.date('Ymd G:i', $occurence_time).'<br>';
									return $occurence_time-$event_tzo;
								}
							}
						}
					}
				}


				break;

			case REPEAT_YEARLY;
			if ($event['repeat_every'] > 1)
			{
				$interval_years = $year-date('Y', $event['start_time']);
				$devided = $interval_years/$event['repeat_every'];
				$rounded = ceil($devided);
				$last_repeat_year = $event['repeat_every']*$rounded;

				$repeat_year = date('Y', $event['start_time']);
				$repeat_month = date('n', $event['start_time']);
				$repeat_day = date('j', $event['start_time']);

				$last_occurence_year = mktime(0,0,0, $repeat_month, $repeat_day, $repeat_year+$last_repeat_year);
			}else
			{
				$last_occurence_year = $start_time;
			}
			$year = date('Y', $last_occurence_year);

			$occurence_time = mktime($event['start_hour'], $event['start_min'], 0, $event['start_month'], $event['start_day'], $year);
			$new_tzo = get_timezone_offset($occurence_time);
			$diff = ($original_tzo-$new_tzo)*3600;
			$occurence_time += $diff;
			if ($occurence_time <= $gmt_time)
			{
				$occurence_time = mktime($event['start_hour'], $event['start_min'], 0, $event['start_month'], $event['start_day'], $year+$event['repeat_every']);
				$new_tzo = get_timezone_offset($occurence_time);
				$diff = ($original_tzo-$new_tzo)*3600;
				$occurence_time += $diff;
			}
			//echo $event['repeat_every']. ': '.$event['name'].' '.date('Ymd G:i', $occurence_time).' '.date('Ymd G:i', $start_time).' <br />';
			if ($event['repeat_forever'] == '0' && $occurence_time > $event['repeat_end_time'])
			{
				return 0;
			}else
			{
				return $occurence_time-$event_tzo;
			}
			break;
		}
	}
	
	

	
	

	function search_events($user_id, $calendar_id=0, $view_id=0, $query, $start_time, $end_time, $sort_field='start_time',
	$sort_order='ASC', $start, $offset)
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




		$sql  = "SELECT DISTINCT cal_events.* FROM cal_events ".
		"INNER JOIN cal_events_calendars ON cal_events.id=cal_events_calendars.event_id ".
		"WHERE ";

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
			$sql .= "cal_events_calendars.calendar_id IN (".implode(',', $ids).")";
		}else
		{
			$sql .= "cal_events_calendars.calendar_id=$calendar_id";
		}

		if ($start_time > 0)
		{
			$sql .= " AND ((cal_events.repeat_type='".REPEAT_NONE."' AND (";
			if($end_time>0)
			{
				$sql .= "cal_events.start_time<='$end_time' AND ";
			}
			$sql .= "cal_events.end_time>='$start_time')) OR ".
			"(cal_events.repeat_type!='".REPEAT_NONE."' AND ";
			if($end_time>0)
			{
				$sql .= "cal_events.start_time<='$end_time' AND ";
			}
			$sql .= "(cal_events.repeat_end_time>='$start_time' OR cal_events.repeat_forever='1')))";
		}
		$sql .= " AND name LIKE '$query'";

		if($sort_field != '' && $sort_order != '')
		{
			$sql .=	" ORDER BY $sort_field $sort_order";
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

	function get_events($todos, $events, $completed, $calendar_id=0, $view_id=0, $user_id=0, $links=false, $interval_start=0,
	$interval_end=0, $sort_field='start_time',
	$sort_order='ASC', $start=0, $offset=0, $only_busy_events=false)
	{
		$todos = $todos ? '1' : '0';
		$events = $events ? '1' : '0';

		$sql  = "SELECT DISTINCT cal_events.*";

		$sql .=   " FROM cal_events";

		if ($view_id > 0)
		{
			$sql .= " INNER JOIN cal_events_calendars ON (cal_events.id=cal_events_calendars.event_id)";
			$sql .= " INNER JOIN cal_views_calendars ON (cal_views_calendars.calendar_id=cal_events_calendars.calendar_id)";
		}elseif ($calendar_id > 0)
		{
			$sql .= " INNER JOIN cal_events_calendars ON (cal_events.id=cal_events_calendars.event_id)";
		}elseif($user_id > 0)
		{
			$sql .= " INNER JOIN cal_events_calendars ON (cal_events.id=cal_events_calendars.event_id)";
			$sql .= " INNER JOIN cal_calendars ON (cal_events_calendars.calendar_id=cal_calendars.id)";
		}

		$where=false;

		if($events=='0' || $todos=='0')
		{
			$sql .= " WHERE todo='$todos' ";
			$where=true;
		}
		
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
		

		if(!$completed && $todos=='1')
		{
			if($where)
			{
				$sql .= " AND ";
			}else
			{
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "completion_time=0";
		}

		if ($view_id > 0)
		{
			if($where)
			{
				$sql .= " AND ";
			}else
			{
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "cal_views_calendars.view_id='$view_id'";
		}elseif($user_id > 0)
		{
			if($where)
			{
				$sql .= " AND ";
			}else
			{
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "cal_calendars.user_id='$user_id' AND cal_calendars.group_id=0";
		}elseif ($calendar_id > 0)
		{
			if($where)
			{
				$sql .= " AND ";
			}else
			{
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "cal_events_calendars.calendar_id='$calendar_id'";
		}

		if(is_array($links))
		{
			if(!count($links))
			{
				return 0;
			}
			if($where)
			{
				$sql .= " AND ";
			}else
			{
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "cal_events.link_id IN (".implode(',', $links).")";
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
			$sql .= "((cal_events.repeat_type='".REPEAT_NONE."' AND (";
			if($interval_end>0)
			{
				$sql .= "cal_events.start_time<'$interval_end' AND ";
			}
			$sql .= "cal_events.end_time>'$interval_start')) OR ".
			"(cal_events.repeat_type!='".REPEAT_NONE."' AND ";
			if($interval_end>0)
			{
				$sql .= "cal_events.start_time<'$interval_end' AND ";
			}
			$sql .= "(cal_events.repeat_end_time>'$interval_start' OR cal_events.repeat_forever='1')))";
		}

		if($sort_field != '' && $sort_order != '')
		{
			$sql .=	" ORDER BY $sort_field $sort_order";
		}

		if($offset == 0)
		{
			$this->query($sql);
			return $this->num_rows();
		}else
		{
			$this->query($sql);
			$count = $this->num_rows();

			$sql .= " LIMIT $start, $offset";

			$this->query($sql);

			return $count;
		}
	}

	function get_events_in_array($calendar_id, $view_id, $user_id,
	$interval_start_time, $interval_end_time, $show_todos=false, $completed=false, $show_events=true, $links=false, $with_calendars=false,$only_busy_events=false)
	{
		$this->events = array();
		$this->events_sort=array();


		if($count = $this->get_events($show_todos, $show_events, $completed, $calendar_id,
		$view_id,
		$user_id,
		$links,
		$interval_start_time,
		$interval_end_time,'start_time','ASC',0,0,$only_busy_events))
		{

			while($this->next_record())
			{

				$this->calculate_event($this->Record,
				$interval_start_time,
				$interval_end_time,
				$with_calendars);

			}
		}
		//return $this->events;

		asort($this->events_sort);
		$sorted_events=array();
		foreach($this->events_sort as $key=>$value)
		{
			$sorted_events[] = &$this->events[$key];
		}
		return $sorted_events;
	}

	function calculate_event($event, $interval_start_time, $interval_end_time, $with_calendars=false)
	{
		global $GO_SECURITY;


		$cal = new calendar();
		$cal->set_permissions($event);

		$duration = $event['end_time'] - $event['start_time'];
		if($duration == 0) $duration = 3600;

		if($with_calendars)
		{
			$event_cal_count = $cal->get_calendars_from_event($event['id']);
			while($cal->next_record())
			{
				$event['calendars'][]=$cal->Record;
			}
		}

		$calculated_event = $event;
		$calculated_event['original_start_time'] = $event['start_time'];
		$calculated_event['original_end_time'] = $event['end_time'];
		$calculated_event['original_repeat_end_time'] = $event['repeat_end_time'];


		/*$exceptions=array();

		$cal->get_exceptions($event['id']);
		while($cal->next_record())
		{
		$exceptions[]=$this->f('time');
		}*/

		//echo 'Ik ga presenteren '.date('Ymd G:i', $interval_start_time).' tot '.date('Ymd G:i', $interval_end_time).'<br>';




		//echo 'Gevraagd eerst volgende keer na '.date('Ymd G:i', $interval_start_time-$duration);

		if($calculated_event['start_time'] = $this->get_next_recurrence_time(0, $interval_start_time-$duration, $event))
		{

			$calculated_event['end_time'] = $calculated_event['start_time']+$duration;			
			
			//echo 'Gevonden '.date('Ymd G:i', $calculated_event['start_time']).' tot '.date('Ymd G:i', $calculated_event['end_time']).' <br />';

			//echo date('Ymd G:i', $calculated_event['start_time']).' '.date('Ymd G:i', $calculated_event['end_time'])." ".$calculated_event['name']."<br><hr /><br />";
			$loops = 0;

			while($calculated_event['start_time'] < $interval_end_time && $calculated_event['end_time'] > $interval_start_time)
			{
				$loops++;
				if(!$cal->is_exception($calculated_event['id'],$calculated_event['start_time']))
				{
					//echo 'Ik voeg toe '.date('Ymd G:i', $calculated_event['start_time']).'<br />';
					$this->events[] = $calculated_event;
					//echo date('Ymd G:i', $interval_start_time).' '.date('Ymd G:i', $calculated_event['start_time']).' '.date('Ymd G:i', $calculated_event['end_time'])." ".$calculated_event['name']."<br><hr /><br />";
					//$timezone_offset = get_timezone_offset($calculated_event['original_start_time'])*3600;
					$this->events_sort[] = $calculated_event['start_time'];
				}

				if($loops==100)
				{
					global $GO_MODULES;
					echo '<a href="'.$GO_MODULES->modules['calendar']['url'].'event.php?event_id='.$calculated_event['id'].
					'>Warning: event looped 1000 times '.
					date('Ymd G:i', $calculated_event['start_time']).'  '.
					$calculated_event['name'].' event_id='.$calculated_event['id'].'</a><br>';
					exit();
				}

				$calculated_event['original_start_time'] = $event['start_time'];
				$calculated_event['original_end_time'] = $event['end_time'];
				$calculated_event['original_repeat_end_time'] = $event['repeat_end_time'];

				//echo 'Gevraagd eerst volgende keer na '.date('Ymd G:i', $calculated_event['start_time']);

				if($calculated_event['start_time'] = $this->get_next_recurrence_time(0, $calculated_event['start_time'], $event))
				{

					$calculated_event['end_time'] = $calculated_event['start_time']+$duration;

					//echo 'Gevonden '.date('Ymd G:i', $calculated_event['start_time']).' tot '.date('Ymd G:i', $calculated_event['end_time']).' <br />';
				}else
				{
					$calculated_event['end_time'] = 0;
				}
				//echo $calculated_event['end_time']. '  > '.$interval_start_time.' '.date('Ymd G:i', $calculated_event['start_time']).' '.date('Ymd G:i', $calculated_event['end_time'])." ".$calculated_event['name']."<br><hr /><br />";
			}
		}

	}


	function get_events_to_remind($user_id, $events=true,$todos=true, $formail=false)
	{
		$todos = $todos ? '1' : '0';

		$gmt_time = get_gmt_time();

		$sql = "SELECT * FROM cal_reminders INNER JOIN ".
		"cal_events ON cal_reminders.event_id=cal_events.id WHERE ".
		"cal_reminders.user_id='$user_id' AND remind_time<='$gmt_time'";
		if(!$events)
		{
			$sql .= " AND cal_events.todo='1'";
		}elseif(!$todos)
		{
			$sql .= " AND cal_events.todo='0'";
		}

		if($formail)
		{
			$sql .= " AND cal_reminders.email_sent='0'";
		}

		$this->query($sql);
		return $this->num_rows();
	}

	function has_read_permission($user_id, $event, $check_write_permission=true)
	{
		global $GO_SECURITY, $GO_MODULES;

		if($user_id==0)
		{
			return false;
		}elseif($event['user_id']==$user_id || $GO_SECURITY->has_permission($user_id, $GO_MODULES->modules['calendar']['acl_write']))
		{
			return true;
		}elseif($event['permissions']==PRIVATE_EVENT)
		{
			return false;
		}else
		{
			$sql = "SELECT cal_calendars.id FROM cal_events_calendars ".
			"INNER JOIN cal_calendars ON cal_events_calendars.calendar_id=cal_calendars.id ".
			"INNER JOIN acl ON (cal_calendars.acl_read=acl.acl_id OR cal_calendars.acl_write=acl.acl_id) ".
			"LEFT JOIN users_groups ON users_groups.group_id=acl.group_id ".
			"WHERE (acl.user_id=$user_id OR users_groups.user_id=$user_id) AND ".
			"cal_events_calendars.event_id=".$event['id'];
			$this->query($sql);

			if($this->next_record())
			{
				return true;
			}elseif($check_write_permission)
			{
				return $this->has_write_permission($user_id, $event);
			}else
			{
				return false;
			}
		}
	}

	function get_resource_group_id_by_event_id($event_id)
	{
		$this->get_event_subscribtions($event_id);
		if($this->next_record())
		{
			if($calendar = $this->get_calendar($this->f('calendar_id')))
			{
				if($calendar['group_id']>1)
				{
					return $calendar['group_id'];
				}
			}
		}
		return false;
	}

	function has_write_permission($user_id, $event)
	{
		global $GO_SECURITY, $GO_MODULES;

		if($user_id==0)
		{
			return false;
		}elseif($event['user_id']==$user_id || $GO_SECURITY->has_permission($user_id, $GO_MODULES->modules['calendar']['acl_write']))
		{
			return true;
		}else
		{

			switch($event['permissions'])
			{
				case PARTICIPANTS_WRITE:
					$sql = "SELECT * FROM cal_events_calendars ".
					"INNER JOIN cal_calendars ON cal_events_calendars.calendar_id=cal_calendars.id ".
					"WHERE cal_events_calendars.event_id=".$event['id']." AND cal_calendars.user_id=$user_id";
					$this->query($sql);
					if($this->next_record())
					{
						return true;
					}else {
						$this->is_resource_event_admin($user_id, $event['id']);
					}
					break;

				case PRIVATE_EVENT:
					return false;
					break;

				case EVERYBODY_WRITE:
					return true;
					break;

				case EVERYBODY_READ:

					return $this->is_resource_event_admin($user_id, $event['id']);
					break;
			}
		}
	}

	function is_resource_event_admin($user_id, $event_id)
	{
		$sql = "SELECT cal_group_admins.group_id FROM cal_group_admins ".
		"INNER JOIN cal_calendars on cal_calendars.group_id=cal_group_admins.group_id ".
		"INNER JOIN cal_events_calendars ON cal_events_calendars.calendar_id=cal_calendars.id WHERE cal_events_calendars.event_id=$event_id AND cal_group_admins.user_id=$user_id";

		$this->query($sql);
		//echo $sql.'<hr>';
		return $this->next_record();

		if($resource_group_id = $this->get_resource_group_id_by_event_id($event_id))
		{
			if($this->is_resource_group_admin($user_id, $resource_group_id))
			{
				return true;
			}
		}
		return false;
	}
	function set_permissions(&$event)
	{
		global $GO_SECURITY;

		if(!$event['write_permission'] = $event['read_permission'] = $this->has_write_permission($GO_SECURITY->user_id, $event, false))
		{
			$event['read_permission'] = $this->has_read_permission($GO_SECURITY->user_id, $event);
		}
	}

	function get_event($event_id)
	{
		$sql = "SELECT * FROM cal_events WHERE id='$event_id'";
		$this->query($sql);
		if($this->next_record(MYSQL_ASSOC))
		{
			$event =  $this->Record;
			$this->set_permissions($event);

			return $event;
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
			$GO_LINKS->delete_link($event['link_id']);
			
			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			$fs = new filesystem();
			$fs->delete($GO_CONFIG->file_storage_path.'events/'.$event_id.'/');
		

			$sql = "DELETE FROM cal_events WHERE id='$event_id'";
			$this->query($sql);
			$sql = "DELETE FROM cal_events_calendars WHERE event_id='$event_id'";
			$this->query($sql);
			$sql = "DELETE FROM cal_reminders WHERE event_id='$event_id'";
			$this->query($sql);
			$sql = "DELETE FROM cal_participants WHERE event_id='$event_id'";
			$this->query($sql);
			$sql = "DELETE FROM cal_exceptions WHERE event_id='$event_id'";
			$this->query($sql);

		}
		return false;
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
		global $GO_MODULES;

		if(!isset($this->ical2array))
		{
			require_once($GO_MODULES->modules['calendar']['class_path'].'ical2array.class.inc');
			$this->ical2array = new ical2array();
		}


		$event['todo'] = $object['type'] == 'VTODO' ? '1' : '0';

		$event['name'] = (isset($object['SUMMARY']['value']) && $object['SUMMARY']['value'] != '') ? trim($object['SUMMARY']['value']) : 'Unnamed';
		if(isset($object['SUMMARY']['params']['ENCODING']) && $object['SUMMARY']['params']['ENCODING'] == 'QUOTED-PRINTABLE')
		{
			$event['name'] = quoted_printable_decode($event['name']);
		}
		$event['description'] = isset($object['DESCRIPTION']['value']) ? trim($object['DESCRIPTION']['value']) : '';

		if(isset($object['DESCRIPTION']['params']['ENCODING']) && $object['DESCRIPTION']['params']['ENCODING'] == 'QUOTED-PRINTABLE')
		{
			$event['description'] = trim_lines(quoted_printable_decode(trim_lines($event['description'])));
		}
		$event['location'] = isset($object['LOCATION']['value']) ? trim($object['LOCATION']['value']) : '';
		if(isset($object['LOCATION']['params']['ENCODING']) && $object['LOCATION']['params']['ENCODING'] == 'QUOTED-PRINTABLE')
		{
			$event['location'] = quoted_printable_decode($event['location']);
		}

		if(isset($object['STATUS']['value']))
		{
			$event['status_id'] = $this->get_status_id($object['STATUS']['value']);
		}

		$event['all_day_event'] = (isset($object['DTSTART']['params']['VALUE']) &&
		strtoupper($object['DTSTART']['params']['VALUE']) == 'DATE') ? true : false;

		/*if($event['all_day_event'])
		{
		$timezone_id='';
		}else
		{
		$timezone_id = isset($object['DTSTART']['params']['TZID']) ? $object['DTSTART']['params']['TZID'] : '';
		}*/

		if(isset($object['DTSTART']))
		{
			$timezone_id = isset($object['DTSTART']['params']['TZID']) ? $object['DTSTART']['params']['TZID'] : '';
			$event['start_time'] = $this->ical2array->parse_date($object['DTSTART']['value'], $timezone_id);
		}elseif(isset($object['DUE']['value']))
		{
			$timezone_id = isset($object['DUE']['params']['TZID']) ? $object['DUE']['params']['TZID'] : '';
			$event['start_time'] = $this->ical2array->parse_date($object['DUE']['value'],  $timezone_id);
		}else
		{
			$local_start_time = get_time();
			$year = date('Y', $local_start_time );
			$month = date('n', $local_start_time );
			$day = date('j', $local_start_time );
			$event['start_time'] = local_to_gmt_time(adodb_mktime(0,0,0,$month, $day, $year));
			$event['all_day_event']='1';
		}

		if(isset($object['DTEND']['value']))
		{
			$timezone_id = isset($object['DTEND']['params']['TZID']) ? $object['DTEND']['params']['TZID'] : '';
			$event['end_time'] = $this->ical2array->parse_date($object['DTEND']['value'],  $timezone_id);
			//echo 'End:'.date('Ymd G:i', $event['end_time']).'<br />';
		}elseif(isset($object['DURATION']['value']))
		{
			$duration = $this->ical2array->parse_date($object['DURATION']['value']);
			$event['end_time'] = $event['start_time']+$duration;
			
			
		}elseif(isset($object['DUE']['value']))
		{
			$timezone_id = isset($object['DUE']['params']['TZID']) ? $object['DUE']['params']['TZID'] : '';
			$event['end_time'] = $this->ical2array->parse_date($object['DUE']['value'],  $timezone_id);
		}else
		{
			$event['end_time'] = $event['start_time']+3600;
		}

		if(isset($object['COMPLETED']['value']))
		{
			$timezone_id = isset($object['COMPLETED']['params']['TZID']) ? $object['COMPLETED']['params']['TZID'] : '';
			$event['completion_time'] = $this->ical2array->parse_date($object['COMPLETED']['value'], $timezone_id);
		}elseif(isset($event['status_id']) && $event['status_id']==11)
		{
			$event['completion_time']=get_gmt_time();
		}else
		{
			$event['completion_time']=0;
		}


		//Shift dates before 1970 to this year because php can't handle dates before 1970
		/*if($event['start_time'] <= 0)
		{
		go_log(LOG_DEBUG, 'Date is before 1970. Shifting it to 1970');
		$year=1970;
		$month=date('n', $event['start_time']);
		$day = date('j', $event['start_time']);
		$hour = date('G', $event['start_time']);
		$min = date('i', $event['start_time']);

		$original_start_time = $event['start_time'];
		$event['start_time'] = mktime($hour, $min ,0 , $month, $day, $year);

		$offset = $event['start_time'] - $original_start_time;
		$event['end_time'] +=$offset;
		}*/

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
			
			
			if(date('G', gmt_to_local_time($event['start_time']))==0 && ($event['end_time'] == $event['start_time'] || date('G', gmt_to_local_time($event['end_time']))==23 || date('G', gmt_to_local_time($event['end_time']))==0))
			{
				$event['all_day_event'] = '1';
				//$event['start_time'] = gmt_to_local_time($event['start_time']);
			}

			if($event['all_day_event'])
			{
				//TODO DST!
				//$event['end_time'] = $event['end_time']+86340;
				//dont do this for symbian
				
				//calc duration in days:
				$duration = $event['end_time']-$event['start_time'];
				$duration_days = ceil($duration/86400);
				
				$year = adodb_date('Y', $event['start_time']);
				$month = adodb_date('n', $event['start_time']);
				$day = adodb_date('j', $event['start_time']);
				$event['end_time'] = local_to_gmt_time(adodb_mktime(0,-1,0,$month, $day+$duration_days+1, $year));
				
			}
			
			

			if(isset($object['CLASS']['value']) && $object['CLASS']['value'] == 'PRIVATE')
			{	
				$event['permissions'] = PRIVATE_EVENT;				
			}else {
				global $GO_SECURITY;
				
				$cal_settings = $this->get_settings($GO_SECURITY->user_id);
				$event['permissions'] = $cal_settings['permissions'];
			}


			$event['sun'] = 0;
			$event['mon'] = 0;
			$event['tue'] = 0;
			$event['wed'] = 0;
			$event['thu'] = 0;
			$event['fri'] = 0;
			$event['sat'] = 0;

			$event['repeat_every'] = 0;
			$event['repeat_forever'] = 0;
			$event['repeat_type'] = REPEAT_NONE;
			$event['repeat_end_time'] = 0;
			$event['month_time'] = 0;

			if (isset($object['RRULE']['value']))
			{
				if(!$rrule = $this->ical2array->parse_rrule($object['RRULE']['value']))
				{
					//Recurrence rule is not understood by GO, abort import
					return false;
				}

				if(isset($rrule['FREQ']))
				{
					if (isset($rrule['UNTIL']))
					{
						if($event['repeat_end_time'] = $this->ical2array->parse_date($rrule['UNTIL']))
						{
							$event['repeat_end_time'] = adodb_mktime(0,0,0, adodb_date('n', $event['repeat_end_time']), adodb_date('j', $event['repeat_end_time'])+1, adodb_date('Y', $event['repeat_end_time']));
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

					$event['repeat_every'] = $rrule['INTERVAL'];
					switch($rrule['FREQ'])
					{
						case 'DAILY':
							$event['repeat_type'] = REPEAT_DAILY;
							break;

						case 'WEEKLY':
							$event['repeat_type'] = REPEAT_WEEKLY;

							$days = explode(',', $rrule['BYDAY']);

							$event['sun'] = in_array('SU', $days) ? '1' : '0';
							$event['mon'] = in_array('MO', $days) ? '1' : '0';
							$event['tue'] = in_array('TU', $days) ? '1' : '0';
							$event['wed'] = in_array('WE', $days) ? '1' : '0';
							$event['thu'] = in_array('TH', $days) ? '1' : '0';
							$event['fri'] = in_array('FR', $days) ? '1' : '0';
							$event['sat'] = in_array('SA', $days) ? '1' : '0';
							break;

						case 'MONTHLY':
							if (isset($rrule['BYDAY']))
							{
								$event['repeat_type'] = REPEAT_MONTH_DAY;

								$event['month_time'] = $rrule['BYDAY'][0];
								$day = substr($rrule['BYDAY'], 1);

								switch($day)
								{
									case 'MO':
										$event['mon'] = 1;
										break;

									case 'TU':
										$event['tue'] = 1;
										break;

									case 'WE':
										$event['wed'] = 1;
										break;

									case 'TH':
										$event['thu'] = 1;
										break;

									case 'FR':
										$event['fri'] = 1;
										break;

									case 'SA':
										$event['sat'] = 1;
										break;

									case 'SUN':
										$event['sun'] = 1;
										break;
								}
							}else
							{
								$event['repeat_type'] = REPEAT_MONTH_DATE;
								if (isset($rrule['BYMONTHDAY']))
								{
									//GO Supports only one single monthday
									$bymonthday = explode(',',$rrule['BYMONTHDAY']);

									if($bymonthday[0] > 0 && date('j', $event['start_time']) != $bymonthday[0])
									{
										go_log(LOG_DEBUG, 'ByMonthDay: '.$bymonthday[0]);
										$duration = $event['end_time'] - $event['start_time'];
										$event['start_time'] = mktime(
										date('G',$event['start_time']),
										date('i', $event['start_time']),
										0,
										date('n',$event['start_time']),
										$bymonthday[0]-$this->get_shift_day($event),
										date('Y',$event['start_time']));

										$event['end_time'] = $event['start_time']+$duration;
									}
								}
							}
							break;

						case 'YEARLY':
							$event['repeat_type'] = REPEAT_YEARLY;
							if (isset($rrule['BYMONTH']))
							{
								//GO Supports only one single month
								$bymonth = explode(',',$rrule['BYMONTH']);

								if($bymonth[0] > 0 && date('n', $event['start_time']) != $bymonth[0])
								{
									$duration = $event['end_time'] - $event['start_time'];
									$event['start_time'] = mktime(
									date('G',$event['start_time']),
									date('i', $event['start_time']),
									0,
									$bymonth[0]-$this->get_shift_day($event),
									date('j',$event['start_time']),
									date('Y',$event['start_time']));

									$event['end_time'] = $event['start_time']+$duration;
								}
							}
							break;
					}
				}
			}

			if (isset($object['X-GO-BGCOLOR']['value']))
			{
				$event['background'] = $object['X-GO-BGCOLOR']['value'];
			}

			if(isset($object['DTSTART']['value']))
			{
				$timezone_offset=	$this->ical2array->get_timezone_offset($object['DTSTART']['value'], $timezone_id);
			}else
			{
				$timezone_offset = get_timezone_offset();
			}

			$event=$this->shift_days_to_gmt($event, $timezone_offset);

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
			
			if(!isset($event['timezone']))
			{
				$event['timezone']=$_SESSION['GO_SESSION']['timezone'];
			}
	
			if(!isset($event['DST']))
			{
				$event['DST']=$_SESSION['GO_SESSION']['DST'];
			}
			
			//figure out end time of event
			if(isset($event_count))
			{				
				$event['repeat_forever']='1';
				$start_time=$event['start_time'];
				for($i=1;$i<$event_count;$i++)
				{
					$event['repeat_end_time']=$start_time=$this->get_next_recurrence_time(0, $start_time, $event);

				}
				if($event['repeat_end_time']>0)
				{
					$event['repeat_end_time']+=$event['end_time']-$event['start_time'];
					$event['repeat_forever']='0';
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

				if($member_profile = $GO_USERS->get_user_by_email(smart_addslashes($participants_array[$i])))
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

	function get_shift_day($event, $timezone_offset=null)
	{
		if(!isset($timezone_offset))
		{
			$timezone_offset = get_timezone_offset($event['start_time']);
		}

		//shift the selected weekdays to local time
		$local_start_hour = date("G", $event['start_time']) + $timezone_offset;

		if ($local_start_hour > 23) {
			$local_start_hour = $local_start_hour -24;
			$shift_day = 1;
		}
		elseif ($local_start_hour < 0) {
			$local_start_hour = 24 + $local_start_hour;
			$shift_day = -1;
		} else {
			$shift_day = 0;
		}
		return $shift_day;
	}
	function get_to_gmt_shift_day($event, $timezone_offset)
	{
		//shift the selected weekdays to local time
		$local_start_hour = date("G", $event['start_time']) + $timezone_offset;


		if ($local_start_hour > 23) {
			$local_start_hour = $local_start_hour -24;
			$shift_day = -1;
		}
		elseif ($local_start_hour < 0) {
			$local_start_hour = 24 + $local_start_hour;
			$shift_day = 1;
		} else {
			$shift_day = 0;
		}
		return $shift_day;
	}
	function shift_days_to_gmt($event, $timezone_offset)
	{



		$shift_day = $this->get_to_gmt_shift_day($event, $timezone_offset);

		switch ($shift_day) {
			case 1 :
				$mon = $event['sun'];
				$tue = $event['mon'];
				$wed = $event['tue'];
				$thu = $event['wed'];
				$fri = $event['thu'];
				$sat = $event['fri'];
				$sun = $event['sat'];
				break;

			case -1 :
				$mon = $event['tue'];
				$tue = $event['wed'];
				$wed = $event['thu'];
				$thu = $event['fri'];
				$fri = $event['sat'];
				$sat = $event['sun'];
				$sun = $event['mon'];
				break;
		}
		if ($shift_day != 0) {
			$event['sun'] = $sun;
			$event['mon'] = $mon;
			$event['tue'] = $tue;
			$event['wed'] = $wed;
			$event['thu'] = $thu;
			$event['fri'] = $fri;
			$event['sat'] = $sat;
		}
		return $event;
	}


	function shift_days_to_local($event, $timezone_offset=null)
	{
		if(!isset($timezone_offset))
		{
			$timezone_offset = get_timezone_offset($event['start_time']);
		}

		$shift_day = $this->get_shift_day($event, $timezone_offset);
		switch ($shift_day) {
			case 1 :
				$mon = $event['sun'];
				$tue = $event['mon'];
				$wed = $event['tue'];
				$thu = $event['wed'];
				$fri = $event['thu'];
				$sat = $event['fri'];
				$sun = $event['sat'];
				break;

			case -1 :
				$mon = $event['tue'];
				$tue = $event['wed'];
				$wed = $event['thu'];
				$thu = $event['fri'];
				$fri = $event['sat'];
				$sat = $event['sun'];
				$sun = $event['mon'];
				break;
		}
		if ($shift_day != 0) {
			$event['sun'] = $sun;
			$event['mon'] = $mon;
			$event['tue'] = $tue;
			$event['wed'] = $wed;
			$event['thu'] = $thu;
			$event['fri'] = $fri;
			$event['sat'] = $sat;
		}
		return $event;
	}
	
	
	function __on_user_delete($user_id)
	{
		$delete = new calendar();
		$sql = "SELECT * FROM cal_calendars WHERE user_id='$user_id'";
		$this->query($sql);
		while($this->next_record())
		{
			$delete->delete_calendar($this->f('id'));
		}

		$sql = "SELECT * FROM cal_events WHERE user_id='$user_id'";
		$this->query($sql);

		while($this->next_record())
		{
			$delete->delete_event($this->f('id'));
		}

		$this->get_user_views($user_id);

		while($this->next_record())
		{
			$delete->delete_view($this->f('id'));
		}
		$this->query("DELETE FROM cal_settings WHERE user_id='$user_id'");
	}
	
	function __on_search($user_id,$last_sync_time=0)
	{
		global $GO_MODULES, $GO_LANGUAGE;

		require($GO_LANGUAGE->get_language_file('calendar'));

		$sql  = "SELECT DISTINCT cal_events.* FROM cal_events ".
		"INNER JOIN cal_events_calendars ON cal_events.id=cal_events_calendars.event_id ".
		"WHERE ";


		$calendars = array();
		$this->get_authorized_calendars($user_id);
		while($this->next_record())
		{
			$calendars[] = $this->f('id');
		}
		
		$sql .= "cal_events_calendars.calendar_id IN (".implode(',', $calendars).")  AND mtime>$last_sync_time";


		$this->query($sql);

		$db = new db();
		while($this->next_record())
		{
			$cache['table']='cal_events';
			$cache['id']=$this->f('id');
			$cache['user_id']=$user_id;
			$cache['name'] = addslashes($this->f('name'));
			$cache['link_id'] = $this->f('link_id');
			$cache['link_type']=1;
			$cache['description']=addslashes($this->f('description'));
			$cache['url']=$GO_MODULES->modules['calendar']['url'].'event.php?event_id='.$this->f('id');
			$cache['type']=$this->f('todo')=='1' ? $cal_todo : $cal_event;
			$cache['keywords']=addslashes(record_to_keywords($this->Record)).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			
			if($this->f('ctime')>$last_sync_time)
			{
				$db->insert_row('se_cache',$cache);
			}else {
				$db->update_row('se_cache',array('user_id','link_id'), $cache);
			}
		}
	}
}
