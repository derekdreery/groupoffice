<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.21 $ $Date: 2006/11/22 15:10:01 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
class event extends html_element
{
	var $event;
	var $link_back;
	var $maxlength;
	var $times_local=false;
	
	function event($event, $link_back='', $times_local=false)
	{
		if(!isset($event['read_permission']))
		{
			$cal = new calendar();
			$cal->set_permissions($event);
		}
		$this->event=$event;
		$this->link_back=$link_back;
		$this->times_local=$times_local;
	}
	
	function set_maxlength($maxlength)
	{
		$this->maxlength=$maxlength;
	}
	
	function get_html()
	{
		global $GO_MODULES, $GO_LANGUAGE, $GO_LINKS;		
		
		$div = new html_element('div', '&nbsp;');
		$div->set_attribute('class', 'summary_icon');
		$div->set_attribute('style', 'background-color: #'.$this->event['background']);
		
		
		
		if(!$this->times_local)
		{
			$gmt_start_time=$this->event['start_time'];
			
			$timezone_offset = get_timezone_offset($this->event['start_time'])*3600;
			$this->event['start_time'] += $timezone_offset;
			$timezone_offset = get_timezone_offset($this->event['end_time'])*3600;
			$this->event['end_time'] += $timezone_offset;
			$timezone_offset = get_timezone_offset($this->event['repeat_end_time'])*3600;
			$this->event['repeat_end_time'] += $timezone_offset;
			
		}else
		{
			$gmt_start_time= local_to_gmt_time($this->event['start_time']);
		}
		
		if($this->event['all_day_event'] == '1')
		{
			$date_format = '';//$_SESSION['GO_SESSION']['date_format'];
		}else
		{
			if (date($_SESSION['GO_SESSION']['date_format'], $this->event['start_time']) != date($_SESSION['GO_SESSION']['date_format'], $this->event['end_time'])) {
				$date_format = $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'].' ';
			} else {
				$date_format = $_SESSION['GO_SESSION']['time_format'].' ';
			}
		}
		
		if(!$this->event['read_permission'])
		{
			$div = new html_element('div', $div->get_html().date($date_format, $this->event['start_time']).
				htmlspecialchars($GLOBALS['sc_private_event']));
			$div->set_tooltip(new event_tooltip($this->event, true));	
			return $div->get_html();
		}else
		{
			$link =  $GO_MODULES->modules['calendar']['url'].'event.php?event_id='.
				$this->event['id'].'&return_to='.	rawurlencode($this->link_back).
				'&gmt_start_time='.$gmt_start_time;
			
			if(isset($this->maxlength))
			{
				 $event_name = cut_string($this->event['name'], $this->maxlength, false);
			}else
			{
				$event_name = $this->event['name'];
			}
			
			
				
			
			$event_link = new hyperlink($link, 
				$div->get_html().date($date_format, $this->event['start_time']).
				htmlspecialchars($event_name));
			
			$event_link->set_tooltip(new event_tooltip($this->event, true));					
			$event_str = $event_link->get_html();
			
			$links = $GO_LINKS->get_links($this->event['link_id']);
			if(count($links))
			{
				$img = new image('links_small');
				$img->set_attribute('style','margin-right:1px;margin-left:2px;border:0');
				$img->set_attribute('align','absmiddle');
				
				$link_obj = new hyperlink(add_params_to_url($link, 'event_strip=links&create_exception=false'), $img->get_html());
				$event_str .= $link_obj->get_html();						
			}
			
			$event_div = new html_element('div',$event_str);
			$event_div->set_attribute('style','display:block');	
			return $event_div->get_html();
		}
	}
}

class event_tooltip extends tooltip
{
	function event_tooltip($event, $times_local=false)
	{
		if(!$times_local)
		{
			$timezone_offset = get_timezone_offset($this->event['original_start_time'])*3600;
			$event['start_time'] += $timezone_offset;
			$timezone_offset = get_timezone_offset($this->event['original_end_time'])*3600;
			$event['end_time'] += $timezone_offset;
			$timezone_offset = get_timezone_offset($this->event['original_repeat_end_time'])*3600;
			$event['repeat_end_time'] += $timezone_offset;
		}
		
		global $GO_MODULES, $GO_LANGUAGE;	
		
		if($event['all_day_event'] == '1')
		{
			$date_format = $_SESSION['GO_SESSION']['date_format'];
		}else
		{
			if (date($_SESSION['GO_SESSION']['date_format'], $event['start_time']) != date($_SESSION['GO_SESSION']['date_format'], $event['end_time'])) {
				$date_format = $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'];
			} else {
				$date_format = $_SESSION['GO_SESSION']['time_format'];
			}
		}
		$title = $GLOBALS['sc_start_at'].': '.date($date_format, $event['start_time']).
		'<br />'.$GLOBALS['sc_end_at'].': '.date($date_format, $event['end_time']);
		
		if(!$event['read_permission'])
		{
			$caption = htmlspecialchars($GLOBALS['sc_private_event']);
		}else
		{
			if ($event['location'] != '') {
				$title .= "<br />".$GLOBALS['sc_location'].": ".htmlspecialchars($event['location']);
			}		
			
			if(isset($event['calendars']))
			{
				$title .= '<br />'.$GLOBALS['cal_event_calendars'].': ';
				$first = true;
				foreach($event['calendars'] as $calendar) {
					if ($first) {
						$first = false;
					} else {
						$title .= ', ';
					}
					$title .= htmlspecialchars($calendar['name']);
				}	
			}
			
			
			if ($event['description'] != '') {
				$title .= "<br /><br />". text_to_html(htmlspecialchars($event['description']));
			}
			$caption = htmlspecialchars($event['name']);
		}
		
		$this->tooltip($title, $caption);
	}	
}
