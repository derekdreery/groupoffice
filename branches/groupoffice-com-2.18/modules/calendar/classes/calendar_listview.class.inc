<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.14 $ $Date: 2006/11/22 15:26:39 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once($GO_MODULES->modules['calendar']['class_path'].'event.class.inc');

class calendar_listview extends table
{

	var $current_day;
	var $current_month;
	var $current_year;

	var $clicked_day;	
	var $clicked_month;
	var $clicked_year;
	
	var $day;
	var $month;
	var $year;
	var $hour;
	var $min;
		
	var $offset;	
	var $start_time;
	var $end_time;	
	var $events;		
	var $return_to;	
	var $id;	
	var $form_name;	
	var $timezone_offset;
	var $read_only=false;
		
	
	function calendar_listview($id, $form_name='0', $read_only=false)
	{	
		global $GO_CONFIG, $GO_SECURITY;
		
		$this->id = $id;
		$this->form_name = $form_name;		
		$this->read_only=$read_only;
		$this->current_time = get_time();
		$this->current_day = date('j', $this->current_time);
		$this->current_month = date('n', $this->current_time);
		$this->current_year = date('Y', $this->current_time);
			
		if(isset($_POST[$this->id]['day']))
		{
			if($this->offset != $_POST[$this->id]['offset'])
			{
				$GO_CONFIG->save_setting(
				'calendar_offset', 
				smart_addslashes($_POST[$this->id]['offset']), 
				$GO_SECURITY->user_id);
			}
			$browse_time = mktime(
				$_POST[$this->id]['hour'], 
				$_POST[$this->id]['min'], 
				0, 
				$_POST[$this->id]['month'], 
				$_POST[$this->id]['day'], 
				$_POST[$this->id]['year']);
				
			$this->offset = $_POST[$this->id]['offset'];
			
			$_SESSION[$this->id]['browse_time'] = $browse_time;	
		}else
		{
			$this->offset = $GO_CONFIG->get_setting('calendar_offset', $GO_SECURITY->user_id);
			if(!$this->offset)
			{
				$this->offset = 7;
			}	
			if(!isset($_SESSION[$this->id]['browse_time']))
			{
				$browse_time = get_time();
			}else
			{
				$browse_time = $_SESSION[$this->id]['browse_time'];
			}	
		}

		$this->year = $this->clicked_year = date("Y", $browse_time);
		$this->month = $this->clicked_month = date("n", $browse_time);
		$this->day = $this->clicked_day = date("j", $browse_time);
		$this->hour = date("H", $browse_time);
		$this->min = date("i", $browse_time);
		
		if ($this->offset > 4)
		{
			//calculate the first day to display in the week view.
			$weekday = date("w", $browse_time);

			$tmpday = $this->day - $weekday + $_SESSION['GO_SESSION']['first_weekday'];

			if ($tmpday > $this->day)
			{
				$tmpday = $tmpday -7;
			}
			$new_time = mktime(0,0,0,$this->month, $tmpday, $this->year);
			$this->day = date("j", $new_time);
			$this->month = date('n', $new_time);
			$this->year = date('Y', $new_time);
		}
		
		$this->start_time = mktime(0,0,0,$this->month, $this->day, $this->year);
		$this->end_time = $this->start_time+($this->offset*86400);		
		
		$this->return_to = $_SERVER['HTTP_REFERER'];
	}
	
	function set_return_to($return_to)
	{
		$this->return_to = $return_to;
	}
	
	function set_background_color($color, $weekday, $start_time, $end_time)
	{

	}
	
	function add_holiday($name, $time)
	{
		$this->holidays[$time]=$name;
	}
	
	function get_header()
	{
		global $GO_MODULES;
		return '<script type="text/javascript" src="'.$GO_MODULES->modules['calendar']['url'].'calendar.js"></script>';
	}
	
	function add_event($event)
	{
		$timezone_offset = get_timezone_offset($event['start_time'])*3600;
		$event['start_time'] += $timezone_offset;
		$timezone_offset = get_timezone_offset($event['end_time'])*3600;
		$event['end_time'] += $timezone_offset;
		$timezone_offset = get_timezone_offset($event['repeat_end_time'])*3600;
		$event['repeat_end_time'] += $timezone_offset;

		
		if($event['start_time'] == $event['end_time'])
		{
			$event['end_time'] += 3600;
		}
		
		
		if($event['start_time'] < $this->end_time && $event['end_time'] > $this->start_time)
		{
			for($time=$event['start_time'];$time<$event['end_time'];$time+=86400)
			{			
					$this->events[date('Ymd', $time)][] = &$event;							
			}
		}						
	}
	
	function get_new_event_handler($year,$month,$day,$hour,$min)
	{
		if($this->read_only)
		{
			return "alert('".htmlspecialchars(addslashes($GLOBALS['strAccessDenied']))."');";
		}else
		{
			return "new_event($year,$month,$day,$hour,$min)";
		}
	}
	
	function get_change_view_handler($day, $month, $year, $offset=null)
	{
		$offset = isset($offset) ? $offset : $this->offset;
		return "change_view('".$this->id."','".$this->form_name."', $offset, $day, $month, $year);";
	}
	
	function get_date_handler($day, $month, $year)
	{
		return "goto_date('".$this->id."','".$this->form_name."', $day, $month, $year);";
	}
	
	function load_holidays($user_id)
	{
		global $GO_MODULES;
		
	}
	
	function set_headings()
	{
		global $GO_LANGUAGE;
		
		require($GO_LANGUAGE->get_language_file('calendar'));
		
		$row = new table_row();
		$row->set_attribute('class', 'headingRow');
		
		
		$row->add_cell(new table_heading('&nbsp;'));
		$row->add_cell(new table_heading($sc_start_at, 'start_time'));
		$row->add_cell(new table_heading($sc_end_at,'end_time'));
		$row->add_cell(new table_heading($GLOBALS['strDescription']));
		$row->add_cell(new table_heading($GLOBALS['strOwner']));
		$row->add_cell(new table_heading($sc_calendars));
		$this->add_row($row);
	}
	
	function set_rows()
	{
		global $GO_MODULES, $GO_LINKS;
		
		$cal = new calendar();
		
		for($i=0;$i<$this->offset;$i++)
		{
			$current_time = mktime(0,0,0,$this->month, $this->day+$i, $this->year);
			
			$index = date('Ymd', $current_time);
			
			$rowspan = isset($this->events[$index]) ? count($this->events[$index])+1 : '2';
			
			$row = new table_row();
			
			$cell = new table_cell(date('j', $current_time));
			
			$weekday = date('w', $current_time);
			$div = new html_element('div', $GLOBALS['full_days'][$weekday]);
			if(date('Ymd', $current_time) == date('Ymd', $this->current_time))
			{
				$cell->set_attribute('class', 'currentDayCell');
			}else
			{
				$cell->set_attribute('class', 'dayCell');	
			}			
			$div->set_attribute('class','dayname');
			$cell->add_html_element($div);
			
			$cell->set_attribute('rowspan', $rowspan);
			$row->add_cell($cell);
			
			$cell = new table_cell();			
			$cell = new table_cell();
			$cell->set_attribute('colspan','99');
			$cell->set_attribute('style', 'height:0px;');
			$row->add_cell($cell);
			$this->add_row($row);
			
			if(isset($this->events[$index]))
			{
				foreach($this->events[$index] as $event)
				{	
					$row = new table_row($event['id']);	
					$row->set_attribute('style', 'background-color: #'.$event['background']);
					

						
					if(date('Ymd', $event['start_time']) != $index)
					{
						$date_format = $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'];
					}else
					{
						$date_format = $_SESSION['GO_SESSION']['time_format'];
					}
					
					$cell = new table_cell(date($date_format, $event['start_time']));
					$cell->set_attribute('class','eventCell');
					$row->add_cell($cell);
					
					if(date('Ymd', $event['end_time']) != $index)
					{
						$date_format = $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'];
					}else
					{
						$date_format = $_SESSION['GO_SESSION']['time_format'];
					}
					$cell = new table_cell(date($date_format, $event['end_time']));
					$cell->set_attribute('class','eventCell');
					$row->add_cell($cell);
					

					if(!$event['read_permission'])					
					{
						$cell = new table_cell($GLOBALS['sc_private_event']);
					}else
					{
						$p = new html_element('div');
						$p->set_attribute('class', 'eventDetails');
						
						if($event['location'] != '')
						{
							$event['name'] .= ' @ '. $event['location'];
						}	
						
						if($event['description'] != '')
						{
							$p->innerHTML .= text_to_html($event['description']).'<br /><br />';
						}				
						
						$custom_fields = $cal->group_xml_to_values($event['custom_fields']);
						if(count($custom_fields))
						{
							$cf_table = new table();
							$cf_table->set_attribute('cellspacing', '0');
							$cf_table->set_attribute('class', 'eventDetails');
							
							foreach($custom_fields as $custom_field)
							{
								$cf_row = new table_row();
								$cf_cell = new table_cell($custom_field['name'].': ');
								$cf_cell->set_attribute('valign','top');
								$cf_cell->set_attribute('style', 'font-size:9px;');
								$cf_row->add_cell($cf_cell);
								
								$cf_cell = new table_cell();
								$cf_cell->set_attribute('valign','top');
								
								switch($custom_field['type'])
								{
									case 'checkbox':
										if($custom_field['value'] == '1')
										{
											$cf_cell->innerHTML .= $GLOBALS['cmdYes'];
										}else
										{
											$cf_cell->innerHTML .= $GLOBALS['cmdNo'];
										}
									break;
									
									default:							
										$cf_cell->innerHTML .= htmlspecialchars($custom_field['value']);
									break;
								}
								$cf_row->add_cell($cf_cell);
								$cf_table->add_row($cf_row);
							}
							$p->add_html_element($cf_table);
						}
						$link = 'event.php?event_id='.$event['id'].'&gmt_start_time='.local_to_gmt_time($event['start_time']).'&return_to='.urlencode($this->return_to);									
						$row->set_attribute('onclick', "javascript:document.location='".
							$GO_MODULES->modules['calendar']['url'].$link."';");
						$cell = new table_cell($event['name']);
						
						$links = $GO_LINKS->get_links($event['link_id']);
						if(count($links))
						{
							$img = new image('links_small');
							$img->set_attribute('style','margin-right:1px;margin-left:2px;border:0');
							$img->set_attribute('align','middle');
							
							$link_obj = new hyperlink(add_params_to_url($link, 'event_strip=links&create_exception=false'), $img->get_html());
							$cell->add_html_element($link_obj);			
						}
		
						if(!empty($p->innerHTML))
						{
							$cell->add_html_element($p);
						}
						
					}
					$cell->set_attribute('class','eventCell');
					$row->add_cell($cell);
					$cell = new table_cell(show_profile($event['user_id']));
					$cell->set_attribute('class','eventCell');
					$row->add_cell($cell);
					
					$calendars = '';
					$event_cal_count = $cal->get_calendars_from_event($event['id']);
					$first = true;
					while ($cal->next_record()) {
						if ($first) {
							$first = false;
						} else {
							$calendars .= ', ';
						}
						$calendars .= htmlspecialchars($cal->f('name'));
					}	
					$cell = new table_cell($calendars);
					$cell->set_attribute('class','eventCell');
					$row->add_cell($cell);
					$this->add_row($row);									
				}					
			}else
			{
				$row = new table_row();
				$cell = new table_cell();
				$cell->set_attribute('colspan','99');
				$row->add_cell($cell);
				$this->add_row($row);
			}	
			
			$row = new table_row();
			if(date('Ymd', $current_time) == date('Ymd', $this->current_time))
			{
				$cell = new table_cell();
				$cell->set_attribute('class', 'currentDayCell');
				$row->add_cell($cell);
			}
			$cell = new table_cell();
			$cell->set_attribute('colspan','99');
			$cell->set_attribute('class','daySeperator');
			$row->add_cell($cell);
			$this->add_row($row);
		}
	}
	
	

	function get_html()
	{
		global $GO_MODULES;
		
		$this->add_outerhtml_element(new input('hidden',$this->id.'[day]', $this->day, false));
		$this->add_outerhtml_element(new input('hidden',$this->id.'[month]', $this->month, false));		
		$this->add_outerhtml_element(new input('hidden',$this->id.'[year]', $this->year, false));	
		$this->add_outerhtml_element(new input('hidden',$this->id.'[hour]', $this->hour, false));	
		$this->add_outerhtml_element(new input('hidden',$this->id.'[min]', $this->min, false));	
		$this->add_outerhtml_element(new input('hidden',$this->id.'[offset]', $this->offset, false));
		
		$javascript = "		
		<script type=\"text/javascript\">
		function new_event(year, month, day, hour, min)
		{
			document.location='".$GO_MODULES->modules['calendar']['url'].
				"event.php?hour='+hour+				
				'&min='+min+
				'&day='+day+
				'&month='+month+
				'&year='+year+
				'&view_id='+document.forms['".$this->form_name."'].view_id.value+
				'&calendar_id='+document.forms['".$this->form_name."'].calendar_id.value+				
				'&todo='+document.forms['".$this->form_name."'].todo.value+				
				'&return_to=".urlencode($this->return_to)."';
		}
		document.getElementById('".$this->id."').style.borderCollapse='collapse';
		</script>";

		$this->set_attribute('class','cal_list');
		$this->set_attribute('id',$this->id);
		$this->set_attribute('cellpadding','0');
		$this->set_attribute('cellspacing','0');
		$this->set_headings();
		$this->set_rows();	
		
		
		return parent::get_html().$javascript;		
	}
}
