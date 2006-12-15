<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.32 $ $Date: 2006/11/22 15:10:00 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once($GO_MODULES->modules['calendar']['class_path'].'event.class.inc');
require_once($GO_LANGUAGE->get_language_file('calendar'));

class calendar_monthview extends table
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
		
	var $start_time;
	var $end_time;
	
	var $events;

	var $return_to;
	
	var $id;
	
	var $form_name;
	
	var $offset = '31';
	
	var $max_events = 4;
	
	var $read_only=false;
		
	function calendar_monthview($id, $form_name='0', $read_only=false)
	{	
		global $GO_CONFIG, $GO_SECURITY;
		
		$this->id = $id;
		$this->form_name = $form_name;		
		
		$this->read_only=$read_only;
		
		$this->current_day = date('j', get_time());
		$this->current_month = date('n', get_time());
		$this->current_year = date('Y', get_time());
		
		if(isset($_POST[$this->id]['day']))
		{
			$GO_CONFIG->save_setting(
			'calendar_offset', 
			31, 
			$GO_SECURITY->user_id);

			
			$browse_time = mktime(
				$_POST[$this->id]['hour'], 
				$_POST[$this->id]['min'], 
				0, 
				$_POST[$this->id]['month'], 
				1, 
				$_POST[$this->id]['year']);
				
			$_SESSION[$this->id]['browse_time'] = $browse_time;	
		}else
		{
			if(!isset($_SESSION[$this->id]['browse_time']))
			{				
				$browse_time = mktime(0,0,0,$this->current_month, 1, $this->current_year);
			}else
			{
				$browse_time = $_SESSION[$this->id]['browse_time'];
			}	
		}

		//calculate the first day to display in the week view.
		
		

		$this->year = date("Y", $browse_time);
		$this->month =  date("n", $browse_time);
		$this->day = date('j', $browse_time);;
		$this->hour = date("H", $browse_time);
		$this->min = date("i", $browse_time);
		
		$this->clicked_year = isset($_POST[$this->id]['year']) ? $_POST[$this->id]['year'] : $this->year;
		$this->clicked_month = isset($_POST[$this->id]['month']) ? $_POST[$this->id]['month'] : $this->month;
		$this->clicked_day = isset($_POST[$this->id]['day']) ? $_POST[$this->id]['day'] : $this->day;
		
		$clicked_time = mktime(0,0,0,$this->clicked_month, $this->clicked_day, $this->clicked_year);
		$this->clicked_year = date("Y", $clicked_time);
		$this->clicked_month = date("n", $clicked_time);
		$this->clicked_day = date("j", $clicked_time);

		$weekday = date("w", $browse_time);
		
		$tmpday = $this->day - $weekday + $_SESSION['GO_SESSION']['first_weekday'];
		if ($tmpday > $this->day)
		{
			$tmpday -= 7;
		}

		$this->start_time = mktime(0,0,0,$this->month, $tmpday, $this->year);

		$this->day = date("j", $this->start_time);
		$this->month = date('n', $this->start_time);
		$this->year = date('Y', $this->start_time);
		

		$this->end_time = mktime(0,0,0,$this->clicked_month+1, 1, $this->clicked_year);		
		$weekday = date("w", $this->end_time);		
		$end_day = date('j', $this->end_time);
		$end_month = date('n', $this->end_time);
		$end_year = date('Y', $this->end_time);

		$tmpday = $end_day - $weekday + $_SESSION['GO_SESSION']['first_weekday'];
		
		if ($tmpday < $end_day)
		{
			$tmpday +=7;
		}
		
		$this->end_time = mktime(0,0,0,$end_month, $tmpday, $end_year);		
		
		//echo date('Ymd G:i', $this->start_time).' '.date('Ymd G:i', $this->end_time);

		$this->return_to = $_SERVER['HTTP_REFERER'];
	}
	
	function set_return_to($return_to)
	{
		$this->return_to = $return_to;
	}
	
	function get_header()
	{
		global $GO_MODULES;
		return '<script type="text/javascript" src="'.$GO_MODULES->modules['calendar']['url'].'calendar.js"></script>';
	}
	
	function set_background_color($color, $weekday, $start_time, $end_time)
	{

	}
	
	function add_event($event)
	{	

		$timezone_offset = get_timezone_offset($event['start_time'])*3600;
		$event['start_time'] += $timezone_offset;
		$timezone_offset = get_timezone_offset($event['end_time'])*3600;
		$event['end_time'] += $timezone_offset;
		$timezone_offset = get_timezone_offset($event['repeat_end_time'])*3600;
		$event['repeat_end_time'] += $timezone_offset;
		
		
		if($event['start_time'] < $this->end_time && $event['end_time'] > $this->start_time)
		{		
			$event['start_day'] = date('j', $event['start_time']);
			$event['start_month'] = date('n', $event['start_time']);
			$event['start_year'] = date('Y', $event['start_time']);
			$event['display_start_time'] = mktime(0,0,0,$event['start_month'],$event['start_day'], $event['start_year']);
			
			$event['end_day'] = date('j', $event['end_time']);
			$event['end_month'] = date('n', $event['end_time']);
			$event['end_year'] = date('Y', $event['end_time']);			
			$event['display_end_time'] = mktime(0,0,0,$event['end_month'],$event['end_day']+1, $event['end_year']);
			
			$event_obj = new event($event, $this->return_to, true);
			$event_obj->set_maxlength(20);
			
			//Maybe event is out of display range
			if($event['display_start_time'] < $this->start_time)
			{
				$event['display_start_time'] = $this->start_time;
			}				
			if($event['display_end_time'] > $this->end_time)
			{
				$event['display_end_time'] = $this->end_time;
			}

			$time = $event['display_start_time'];
			
			while($time<$event['display_end_time'])
			{
				$this->events[$time][] = $event_obj;
				
				$day = date('j', $time);			
				$month = date('n', $time);
				$year = date('Y', $time);
				$time = mktime(0,0,0,$month, $day+1, $year);
			}
			return true;
		}else
		{
			return false;
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
		
		require_once($GO_MODULES->modules['calendar']['class_path'].'cal_holidays.class.inc');
		$holidays = new holidays();
		$current_time =$this->start_time;
		
		$holidays->get_holidays_for_period($user_id, $this->start_time, $this->end_time);
		
		while($holidays->next_record())
		{
			$this->holidays[$holidays->f('date')]=$holidays->f('name');
		}
	}
	
	function set_headings()
	{
		$this->add_column(new table_heading('&nbsp;'));		
		
		for($i=0;$i<7;$i++)
		{
			$current_time = mktime(0,0,0,$this->month, $this->day+$i, $this->year);
			$weekday = date('w', $current_time);
			
			$td = new table_heading($GLOBALS['full_days'][$weekday]);
			
			$td->set_attribute('class','heading');
			$this->add_column($td);
		}	
	}
	
	function set_rows()
	{
		global $GO_MODULES;
		$time =$this->start_time;
		while($time<$this->end_time)
		{
			$day = date('j', $time);			
			$month = date('n', $time);
			$year = date('Y', $time);
			$weekday = date('w', $time);
							
			if($weekday == $_SESSION['GO_SESSION']['first_weekday'])
			{
				if(isset($row))
				{
					$this->add_row($row);
				}		
				$row = new table_row();
				
				$cell = new table_cell();
				$cell->add_html_element(new hyperlink('javascript:'.$this->get_change_view_handler($day, $month, $year, 7),date("W", $time)));

			
				$cell->set_attribute('class','heading');
				$cell->set_attribute('style','height:80px;width:20px;');
				$row->add_cell($cell);
			}			
			
			
			$div = new html_element('div');
			$link = new hyperlink('javascript:'.$this->get_change_view_handler($day, $month, $year, 1),$day);
			if(isset($this->holidays[$time]))
			{
				$link->innerHTML .= '<br />'.htmlspecialchars($this->holidays[$time]);
			}	
			$div->add_html_element($link);
			$div->set_attribute('style','text-align:center');			
			
			$cell = new table_cell($div->get_html());
			
			if(isset($this->events[$time]))
			{
				$event_count = 0;
				foreach($this->events[$time] as $event)
				{
					if($event_count >= $this->max_events)
					{
						$div = new html_element('div', '...');
						$div->set_attribute('style','text-align: center');
						
						$cell->add_html_element($div);
						break;
					}
					
					$cell->add_html_element($event);
					$event_count++;
				}			
			}

			if($month == $this->current_month && $day==$this->current_day && $year == $this->current_year)
			{
				$cell->set_attribute('class','cell_selected');			
			}elseif($month != $this->clicked_month)
			{
				$cell->set_attribute('class', 'cell_other_month');
			}elseif($weekday == 0 || $weekday == 6 || !empty($holiday_name))
			{
				$cell->set_attribute('class','cell_holiday');
			}else
			{
				$cell->set_attribute('class','cell_current_month');
			}						
			$row->add_cell($cell);
			$time = mktime(0,0,0,$month, $day+1, $year);
			
		}
		if(isset($row))
		{
			$this->add_row($row);
		}		
	}
	
	function get_html()
	{
		global $GO_MODULES;
		
		$this->add_outerhtml_element(new input('hidden',$this->id.'[day]', $this->clicked_day, false));
		$this->add_outerhtml_element(new input('hidden',$this->id.'[month]', $this->clicked_month, false));		
		$this->add_outerhtml_element(new input('hidden',$this->id.'[year]', $this->clicked_year, false));	
		$this->add_outerhtml_element(new input('hidden',$this->id.'[hour]', $this->hour, false));	
		$this->add_outerhtml_element(new input('hidden',$this->id.'[min]', $this->min, false));	
		$this->add_outerhtml_element(new input('hidden',$this->id.'[offset]', $this->offset, false));
		
		$javascript = "		
		<script type=\"text/javascript\">
		function new_event(year, month, day, hour, min)
		{
			document.location='".$GO_MODULES->modules['calendar']['url'].
				"event.php?hour='+hour+'&min='+min+'&day='+day+
				'&month='+month+'&year='+year+'&todo='+
				document.forms['".$this->form_name."'].todo.value+'&return_to=".urlencode($this->return_to)."';
		}
		document.getElementById('".$this->id."').style.borderCollapse='collapse';
		</script>";

		
		$this->set_attribute('class','cal');
		$this->set_attribute('id',$this->id);
		$this->set_headings();
		$this->set_rows();		
		
		return parent::get_html().$javascript;		
	}
}
?>
