<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.63 $ $Date: 2006/11/22 15:10:01 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once($GO_MODULES->modules['calendar']['class_path'].'event.class.inc');

class calendar_view extends table
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
	
	var $_offset;
	var $offset;
	
	var $start_time;
	var $end_time;
	
	var $calendar_start_time;
	var $calendar_end_time;
	
	var $events;
	var $all_day_events;
	var $colspans;
	
	var $time_interval;
	
	var $return_to;
	
	var $id;
	
	var $form_name;
	
	var $timezone_offset;
	
	var $weeks = 1;
	
	var $read_only=false;
	
	var $background_colors=array();
	
	function calendar_view($id, $day_start_hour, $day_end_hour, $time_interval, $form_name='0', $read_only=false)
	{	
		global $GO_CONFIG, $GO_SECURITY;
		
		$this->id = $id;
		$this->form_name = $form_name;		
		
		$this->current_day = date('j', get_time());
		$this->current_month = date('n', get_time());
		$this->current_year = date('Y', get_time());
			
		$this->calendar_start_time = $day_start_hour*3600;
		$this->calendar_end_time = $day_end_hour*3600+3600;
		
		$this->read_only=$read_only;

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
		$this->_offset = $this->offset;
		//echo date('Ymd G:i', $browse_time);

		$this->year = $this->clicked_year = date("Y", $browse_time);
		$this->month = $this->clicked_month = date("n", $browse_time);
		$this->day = $this->clicked_day = date("j", $browse_time);
		$this->hour = date("H", $browse_time);
		$this->min = date("i", $browse_time);
		
		if ($this->_offset > 4)
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
		
		
		
		if($this->_offset > 7)
		{
			$this->weeks = ceil($this->_offset/7);
			$this->_offset = 7;
		}
		
		$this->time_interval = $time_interval;
		
		$this->start_time = mktime(0,0,0,$this->month, $this->day, $this->year);
		$this->end_time = $this->start_time+($this->_offset*$this->weeks*86400);		
		
		$this->return_to = $_SERVER['HTTP_REFERER'];
	}
	
	function set_return_to($return_to)
	{
		$this->return_to = $return_to;
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
			$event['display_start_time'] = floor($event['start_time']/$this->time_interval)*$this->time_interval;
			$event['display_end_time'] = ceil($event['end_time']/$this->time_interval)*$this->time_interval;			
			
			//echo date('Ymd G:i', $event['display_start_time']);
			
			//Maybe event is out of display range
			if($event['display_start_time'] < $this->calendar_start_time)
			{
				$event['display_start_time'] = $this->calendar_start_time;
			}				
			if($event['display_end_time'] > $this->end_time)
			{
				$event['display_end_time'] = $this->end_time;
			}		
			
			if($event['all_day_event'] == '1')
			{
				for($time=$event['display_start_time'];$time<$event['display_end_time'];$time+=86400)
				{				
					$this->all_day_events[date('Ymd', $time)][] = &$event;							
				}
			}else
			{		
					
				for($time=$event['display_start_time'];$time<$event['display_end_time'];$time+=$this->time_interval)
				{				
					$this->events[$time][] = &$event;							
				}
			}
		}						
	}
	
	function calculate_colspans()
	{
		for($time=$this->calendar_start_time;$time<=$this->calendar_end_time;$time+=$this->time_interval)
		{
			for($display_day=0;$display_day<$this->_offset*$this->weeks;$display_day++)
			{			
				$current_time = mktime(0,0,$time,$this->month, $this->day+$display_day, $this->year);
				
				$weekday = date('w', $current_time);
				
				if(!isset($this->colspans[$weekday]))
				{
					$this->colspans[$weekday] = 1;
				}
				if(isset($this->events[$current_time]))
				{					
					for($n=0;$n<count($this->events[$current_time]);$n++)
					{
						if(!isset($this->events[$current_time][$n]['days'][$display_day]['position']))
						{
							if(isset($this->events[$current_time][$n-1]['days'][$display_day]['position']))
							{			
								$this->events[$current_time][$n-1]['days'][$display_day]['neighbour'] = true;					
								$this->events[$current_time][$n]['days'][$display_day]['position'] = $this->events[$current_time][$n-1]['days'][$display_day]['position']+1;								
							}else
							{
								$this->events[$current_time][$n]['days'][$display_day]['position'] = 0;
							}
						}
						if($this->events[$current_time][$n]['days'][$display_day]['position'] >= $this->colspans[$weekday])
						{
							$this->colspans[$weekday] = $this->events[$current_time][$n]['days'][$display_day]['position']+1;	
						}						
					}									
				}
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
		$_offset = isset($_offset) ? $_offset : $this->_offset;
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
	
	function set_headings($week=0)
	{		
		$heading_width = ceil(100/$this->_offset).'%';

		$row = new table_row();
		$th = new table_heading('&nbsp;');
		//$th->set_attribute('style','width:5%');
		$row->add_cell($th);		
		
		$end = $this->_offset+($week*7);
		
		for($i=($week*7);$i<$end;$i++)
		{
			$current_time = mktime(0,0,0,$this->month, $this->day+$i, $this->year);
			$weekday = date('w', $current_time);
			
			$link = new hyperlink(
				'javascript:'.$this->get_change_view_handler($this->day+$i, $this->month, $this->year, 1), 
				$GLOBALS['full_days'][$weekday].'<br />'.	date($_SESSION['GO_SESSION']['date_format'], $current_time));
				
			if(isset($this->holidays[$current_time]))
			{
				$link->innerHTML .= '<br />'.htmlspecialchars($this->holidays[$current_time]);
			}			
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
			
			$th = new table_heading($link->get_html());	
			$th->set_attribute('style','width:'.$heading_width);
			
			$weekday = date('w', $new_time);
			
			if(isset($this->colspans[$weekday]) && $this->colspans[$weekday]>1)
			{
				$th->set_attribute('colspan',$this->colspans[$weekday]);
			}
			$row->add_cell($th);
		}	
		$this->add_row($row);
	}
	
	function set_background_color($color, $weekday, $start_time, $end_time)
	{
		for($i=$start_time;$i<$end_time;$i+=$this->time_interval)
		{
			$this->background_colors[$weekday.gmdate('Gi', $i)]=$color;
		}
	}
	
	function set_rows()
	{
		global $GO_MODULES, $GO_LINKS;
		
		for($week=0;$week<$this->weeks;$week++)
		{
			$this->set_headings($week);
			
			$week_start_day = ($week*7);
			$week_end_day = ($week*7)+$this->_offset;
			
			if(isset($this->all_day_events))
			{
				$all_day_events=false;
				$row = new table_row();
				$row->set_attribute('class','all_day_event_row');
				$row->add_cell(new table_cell('&nbsp;'));
				
				for($display_day=$week_start_day;$display_day<$week_end_day ;$display_day++)
				{
					$current_time = mktime(0,0,0,$this->month, $this->day+$display_day, $this->year);
					$index = date('Ymd', $current_time);
					$cell = new table_cell();
					
					$weekday = date('w', $current_time);
					
					if($this->colspans[$weekday] > 1)
					{
						$cell->set_attribute('colspan',$this->colspans[$weekday]);
					}
					
					if(isset($this->all_day_events[$index]))
					{
						$all_day_events=true;
						foreach($this->all_day_events[$index] as $event)
						{
							$cell->add_html_element(new event($event, $this->return_to, true));
						}
					}
					$row->add_cell($cell);
				}
				if($all_day_events)
				{
					$this->add_row($row);
				}
			}	
				
			$time_cell_rowspan = 3600/$this->time_interval;
			$row_height = 30/$time_cell_rowspan;
			$time = $this->calendar_start_time;
			
			for($time=$this->calendar_start_time;$time<$this->calendar_end_time;$time+=$this->time_interval)
			{
				$row = new table_row();			
				$row->set_attribute('style','height:'.$row_height.'px');
				$row->set_attribute('onmouseover','javascript:glow(this);');
				$row->set_attribute('onmouseout','javascript:unglow(this);');
				
				if(gmdate('i',$time) == '00')
				{
					$time_cell = new table_cell(gmdate($_SESSION['GO_SESSION']['time_format'], $time));
					$time_cell->set_attribute('class','cal_hour');
		
					
					$rowspan = $time_cell_rowspan;				
					while(($time+$rowspan*$this->time_interval) > $this->calendar_end_time)
					{
						$rowspan--;
					}
					$time_cell->set_attribute('rowspan',$rowspan);
					$row->add_cell($time_cell);
				}
				
				for($display_day=$week_start_day;$display_day<$week_end_day;$display_day++)
				{			
					$day_start_time = mktime(0,0,$this->calendar_start_time,$this->month, $this->day+$display_day, $this->year);
					//$day_end_time = get_time(mktime(0,0,$this->calendar_end_time,$this->month, $this->day+$display_day, $this->year));
					$day_end_time = $day_start_time + $this->calendar_end_time-$this->calendar_start_time;
		
					$current_time = mktime(0,0,$time,$this->month, $this->day+$display_day, $this->year);
					//echo date('Ymd G:i',$current_time).'<br />';
									
					$hour = date('G', $current_time);
					$min = date('i', $current_time);
					$day = date('j', $current_time);
					$month = date('n', $current_time);
					$year = date('Y', $current_time);
					$weekday = date('w', $current_time);
					
					if(isset($this->events[$current_time]))
					{
						$cells=array();
						for($n=0;$n<count($this->events[$current_time]);$n++)
						{									
							if(!isset($this->events[$current_time][$n]['days'][$display_day]['added']))
							{
								$this->events[$current_time][$n]['days'][$display_day]['added']=true;
								$event_cell = new table_cell();
								if(date('Ymd', $this->events[$current_time][$n]['start_time']) == 
									date('Ymd', $current_time))
								{	
								if(!$this->events[$current_time][$n]['read_permission'])
								{						
									$event_str = cut_string(date($_SESSION['GO_SESSION']['time_format'],
										$this->events[$current_time][$n]['start_time']).' '.
										htmlspecialchars($GLOBALS['sc_private_event']), 30, false);
										
									$event_cell->innerHTML .= $event_str;
								}else
								{
									$event_str = cut_string(date($_SESSION['GO_SESSION']['time_format'],
										$this->events[$current_time][$n]['start_time']).' '.
										$this->events[$current_time][$n]['name'], 30, false);
										
									$link = 'event.php?event_id='.$this->events[$current_time][$n]['id'].
										'&gmt_start_time='.local_to_gmt_time($this->events[$current_time][$n]['start_time']).
										'&return_to='.urlencode($this->return_to);
									
									$links = $GO_LINKS->get_links($this->events[$current_time][$n]['link_id']);
									if(count($links))
									{
										$img = new image('links_small');
										$img->set_attribute('style','margin-right:1px;margin-left:2px;border:0');
										$img->set_attribute('align','absmiddle');
										
										$link_obj = new hyperlink(add_params_to_url($link, 'event_strip=links&create_exception=false'), $img->get_html());							
										$event_str .= $link_obj->get_html();					
									}
									
									$event_cell->innerHTML .= $event_str;	
									
									$event_cell->set_attribute('onclick', "javascript:document.location='".
										$GO_MODULES->modules['calendar']['url'].$link."';");
								}
								}else
								{
									$event_cell->innerHTML .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
								}
								$event_cell->set_tooltip(new event_tooltip($this->events[$current_time][$n], true));								
								$event_cell ->set_attribute('style','background-color: #'.$this->events[$current_time][$n]['background']);			
								

								
								$this->events[$current_time][$n]['days'][$display_day]['colspan']=1;
								if($this->colspans[$weekday]>1)
								{
									if(!isset($this->events[$current_time][$n]['days'][$display_day]['neighbour']))
									{
											$this->events[$current_time][$n]['days'][$display_day]['colspan']= 
											$this->colspans[$weekday]-$this->events[$current_time][$n]['days'][$display_day]['position'];										
							
										$event_cell->set_attribute('colspan',$this->events[$current_time][$n]['days'][$display_day]['colspan']);
									}
								}
								
								//$event_cell->innerHTML .= $this->events[$current_time][$n]['days'][$display_day]['colspan'];
								//$event_cell->innerHTML .= $this->events[$current_time][$n]['name'];
								
								$rowspan_start_time = 
									$this->events[$current_time][$n]['display_start_time'] < $day_start_time ? 
									$day_start_time : $this->events[$current_time][$n]['display_start_time'];
								
								$rowspan_end_time = 
									$this->events[$current_time][$n]['display_end_time'] > $day_end_time ? 
									$day_end_time : $this->events[$current_time][$n]['display_end_time'];

								$tz_start = get_timezone_offset($rowspan_start_time)*3600;
								$tz_end = get_timezone_offset($rowspan_end_time)*3600;
								
								$rowspan = ($rowspan_end_time - $rowspan_start_time+ $tz_end- $tz_start) / $this->time_interval ;							

								if($rowspan>1)
								{
									$event_cell->set_attribute('rowspan', $rowspan);
								}								
								
			
								$last_position = $this->events[$current_time][$n]['days'][$display_day]['colspan']+$this->events[$current_time][$n]['days'][$display_day]['position'];
								$first_position = $this->events[$current_time][$n]['days'][$display_day]['position']+1;						
								

								
								$cells[$this->events[$current_time][$n]['days'][$display_day]['position']] = $event_cell;
							}else
							{
								$first_position = $this->events[$current_time][$n]['days'][$display_day]['position']+1;
								$last_position = $this->events[$current_time][$n]['days'][$display_day]['colspan']+$this->events[$current_time][$n]['days'][$display_day]['position'];
								$cells[$this->events[$current_time][$n]['days'][$display_day]['position']] ='skip';
							}
							
																	
						}
						for($position=$first_position;$position<$last_position;$position++)
						{
							$cells[$position] = 'skip';
						}	
						for($n=0;$n<$this->colspans[$weekday];$n++)
						{						
							if(isset($cells[$n]))
							{
								//if($cells[$n] != 'skip' )
								if(is_object($cells[$n]))
								{								
									$row->add_cell($cells[$n]);
								}
							}else
							{							
								$next_cell = $n+1;
								$colspan = 1;
								
								while($next_cell <$this->colspans[$weekday] && !isset($cells[$next_cell]))
								{
									$next_cell++;
									$colspan++;
									$n++;								
								}
								$cell = new table_cell();
								$cell->set_attribute('colspan', $colspan);						
								$cell->set_attribute('onclick', $this->get_new_event_handler($year,$month,$day,$hour,$min));
								
								$bgcolor_index = date('wGi', $current_time);
								if(isset($this->background_colors[$bgcolor_index]))
								{
									$cell->set_attribute('style', 'background-color:#'.$this->background_colors[$bgcolor_index]);			
								}
								
								//$cell->innerHTML .= $colspan;
								
								$row->add_cell($cell);								
							}
						}			
					}else
					{
						$cell = new table_cell();
						if($this->colspans[$weekday] > 1)
						{
							$cell->set_attribute('colspan',$this->colspans[$weekday]);	
						}
						$cell->set_attribute('onclick', $this->get_new_event_handler($year,$month,$day,$hour,$min));
						
						$bgcolor_index = date('wGi', $current_time);
						if(isset($this->background_colors[$bgcolor_index]))
						{
							$cell->set_attribute('style', 'background-color:#'.$this->background_colors[$bgcolor_index]);			
						}
						
						//$cell->innerHTML .= $this->colspans[$display_day];
						
						$row->add_cell($cell);
					}
				}			
				$this->add_row($row);
			}		
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

/*		
		//$this->set_attribute('bordercolor','#7d7d7d;');
		$this->set_attribute('cellpadding','0');
		$this->set_attribute('cellspacing','0');
		//$this->set_attribute('style','border:1px solid #7d7d7d;');*/
		$this->set_attribute('class','cal');
		$this->set_attribute('id',$this->id);
		$this->set_attribute('cellpadding','0');
		$this->set_attribute('cellspacing','0');
		//$this->set_attribute('style','border-collapse:collapse');
		$this->calculate_colspans();
		
		$this->set_rows();	
		
		return parent::get_html().$javascript;		
	}
}
?>
