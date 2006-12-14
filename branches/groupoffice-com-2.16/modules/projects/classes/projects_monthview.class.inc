<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.1 $ $Date: 2005/07/20 10:06:43 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
require_once($GO_MODULES->modules['calendar']['class_path'].'calendar_monthview.class.inc');
require_once($GO_MODULES->modules['projects']['class_path']."project.class.inc");

class projects_monthview extends calendar_monthview
{
	function projects_monthview($id, $form_name='0')
	{	
		$this->calendar_monthview($id, $form_name);
	}
	
	function get_new_event_handler($day,$month, $year,$hour,$min)
	{
		return false;
	}
	
	function get_change_view_handler($day, $month, $year, $offset=null)
	{
		return false;
	}
	
	function add_project($event)
	{	
		$event['start_time'] = gmt_to_local_time($event['start_date']);
		$event['end_time'] = gmt_to_local_time($event['end_date']);
		$event['repeat_end_time'] = 0;
		$event['repeat_type']=0;
		$event['todo']='0';
		
		
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
			
			$event_obj = new project($event, $this->return_to);
			$event_obj->set_maxlength(20);

			for($time = $event['display_start_time'];$time<$event['display_end_time'];$time+=86400)
			{
				$this->events[$time][] = $event_obj;
			}
			return true;
		}else
		{
			return false;
		}
	}		
}


?>
