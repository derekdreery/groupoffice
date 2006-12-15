<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.26 $ $Date: 2006/11/22 15:10:00 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
require_once($GO_MODULES->modules['calendar']['class_path'].'event.class.inc');

class calendar_groupview extends table
{	
	var $id;
	
	var $day;
	var $month;
	var $year;
	var $hour;
	var $min;

	var $clicked_day;	
	var $clicked_month;
	var $clicked_year;
	
	var $offset;
	
	var $start_time;
	var $end_time;
	
	var $calendars = array();
	
	var $return_to;
	
	var $holidays=array();
	
	var $background_colors=array();
	
	var $read_only=false;
	
	function calendar_groupview($id, $form_name='0', $read_only=false)
	{	
		global $GO_CONFIG, $GO_SECURITY;
		
		$this->current_day = date('j', get_time());
		$this->current_month = date('n', get_time());
		$this->current_year = date('Y', get_time());
		
		$this->id = $id;
		$this->form_name = $form_name;		
		//$this->offset = $offset;
		$this->read_only=$read_only;
		
		if(isset($_POST[$this->id]['offset']) && $this->offset != $_POST[$this->id]['offset'])
		{
			$GO_CONFIG->save_setting(
			'calendar_offset', 
			smart_addslashes($_POST[$this->id]['offset']), 
			$GO_SECURITY->user_id);
		}
		
		
		$this->offset = $GO_CONFIG->get_setting('calendar_offset', $GO_SECURITY->user_id);
		if(!$this->offset || $this->offset>7)
		{
			$this->offset = 7;
		}
		
			
		if(isset($_POST[$this->id]['day']))
		{
			$browse_time = mktime(
				$_POST[$this->id]['hour'], 
				$_POST[$this->id]['min'], 
				0, 
				$_POST[$this->id]['month'], 
				$_POST[$this->id]['day'], 
				$_POST[$this->id]['year']);

			$_SESSION[$this->id]['browse_time'] = $browse_time;	
		}else
		{
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
		

		//calculate the first day to display in the week view.
		if($this->offset>4)
		{
			$weekday = date("w", mktime(0,0,0,$this->month, $this->day, $this->year));

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
		$this->return_to = $_SERVER['HTTP_REFERER'];
		
		$this->start_time = get_time(mktime(0,0,0,$this->month, $this->day, $this->year));
		$this->end_time = $this->start_time+($this->offset*86400);	
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
		
		$heading_width = ceil(100/$this->offset).'%';
		
		for($i=0;$i<$this->offset;$i++)
		{
			$current_time = mktime(0,0,0,$this->month, $this->day+$i, $this->year);
			$weekday = date('w', $current_time);
			
			$link = new hyperlink(
				'javascript:'.$this->get_change_view_handler($this->day+$i, $this->month, $this->year, 1), 
				$GLOBALS['full_days'][$weekday].'<br />'.	date($_SESSION['GO_SESSION']['date_format'], $current_time));
			
//			$link =new hyperlink('#', $GLOBALS['full_days'][$weekday].'<br />'.
	//			date($_SESSION['GO_SESSION']['date_format'], $current_time));
			
			$new_time = mktime(0,0,0,$this->month, $this->day+$i, $this->year);
			$new_day = date('j', $new_time);
			$new_month = date('n', $new_time);
			$new_year = date('Y', $new_time);
			
			if($new_day==$this->current_day &&
			$new_month==$this->current_month &&
			$new_year==$this->current_year)			
			{
				$link->set_attribute('class', 'HoverLinkSelected');
			}else
			{
				$link->set_attribute('class', 'HoverLink');
			}
			
			if(isset($this->holidays[$current_time]))
			{
				$link->innerHTML .= '<br />'.htmlspecialchars($this->holidays[$current_time]);
			}			
			
			$th =new table_heading($link->get_html());
			$th->set_attribute('style','width:'.$heading_width);
			$this->add_column($th);
		}	
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
	
	function add_calendar($calendar)
	{
		$this->calendars[$calendar['id']] = $calendar;
		$this->calendars[$calendar['id']]['events'] = array();
	}
	
	function add_event($calendar_id, $event)
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
				$this->calendars[$calendar_id]['events'][$time][] = $event_obj;
				
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
	/*
	function set_background_color($calendar_id, $color, $weekday, $start_time, $end_time)
	{
		$top = ceil($start_time*100/86400);
		$height = ceil($end_time*100/86400)-$top;
		
		$div = new html_element('div', '&nbsp;');
		$div->set_attribute('style','display:block;width:100%:height:'.$height.'%;margin-top:'.$top.'%;background-color:#'.$color);
//echo $calendar_id.' '.$weekday.'position:relative;width:100%:height:'.$height.'%;margin-top:'.$top.'%;background-color:#'.$color.'<br>';
		$this->background_colors[$calendar_id][$weekday][]=$div;
	}*/
	
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
				"event.php?hour='+hour+'&min='+min+'&day='+day+
				'&month='+month+'&year='+year+
				'&todo='+document.forms['".$this->form_name."'].todo.value+
				'&view_id='+document.forms['".$this->form_name."'].view_id.value+
				'&calendar_id='+document.forms['".$this->form_name."'].calendar_id.value+		
				'&return_to=".urlencode($this->return_to)."';
		}
		document.getElementById('".$this->id."').style.borderCollapse='collapse';
		</script>";
		
		$this->set_attribute('id',$this->id);
		
		$this->set_headings();
		
		foreach($this->calendars as $calendar)
		{		
			$row = new table_row();			
			$row->set_attribute('style','height: 50px;');
			
			$link = new hyperlink($GO_MODULES->modules['calendar']['url'].'?calendar_id='.$calendar['id'].'&view_id=0', $calendar['name']);
			$link->set_attribute('class','HoverLink');
			
			$row->add_cell(new table_cell($link->get_html()));
			
			$time =$this->start_time;
			$day = date('j', $time);			
			$month = date('n', $time);
			$year = date('Y', $time);
			
			for($i=0;$i<$this->offset;$i++)
			{		
				$time = mktime(0,0,0,$month, $day+$i, $year);
				$weekday = date('w', $time);

				
				$cell = new table_cell();
				/*if(isset($this->background_colors[$calendar['id']][$weekday]))
				{
					foreach($this->background_colors[$calendar['id']][$weekday] as $div)
					{
						$cell->add_html_element($div);
					}
				}*/
				
				if(isset($calendar['events'][$time]))
				{
					//ksort($calendar['events']);
					
					foreach($calendar['events'][$time] as $event)
					{
						$cell->add_html_element($event); 
					}
				}
				$row->add_cell($cell);				
			}
			$this->add_row($row);
		}
		$this->set_attribute('class','cal');

		return parent::get_html().$javascript;
	}	
}
?>
