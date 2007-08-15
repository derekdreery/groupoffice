<?php
/**
 * @copyright Intermesh 2004
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.46 $ $Date: 2006/05/04 13:26:14 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 

 
if (isset ($GO_MODULES->modules['calendar']) && $GO_MODULES->modules['calendar']['read_permission']) {
	require_once ($GO_LANGUAGE->get_language_file('calendar'));	
	require_once ($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
	 require_once($GO_MODULES->modules['calendar']['class_path'].'event.class.inc');
	$cal = new calendar();
	

	
	$em_table = new table();
	$em_table->set_attribute('style', 'border:0px;margin-top:10px;');
	
	$img = new image('calendar');
	$img->set_attribute('style', 'border:0px;margin-right:10px;');
	
	$em_cell = new table_cell();
	$em_cell->set_attribute('valign','top');
	$em_cell->add_html_element($img);
	
	$em_row = new table_row();
	$em_row->add_cell($em_cell);
	
	$link = new hyperlink($GO_MODULES->modules['calendar']['url'], $lang_modules['calendar']);
	
	$h2 = new html_element('h2',$link->get_html());
	$em_row->add_cell(new table_cell($h2->get_html()));
	$em_table->add_row($em_row);

	require_once ($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
	$cal = new calendar();

	for ($show_days = 0; $show_days < 2; $show_days ++) {
		$em_row = new table_row();
		$em_row->add_cell(new table_cell('&nbsp;'));	
		$em_cell = new table_cell();
		
		$timezone_offset = get_timezone_offset(mktime(0, 0, 0, $month, $day+$show_days, $year))*3600;	
		$interval_start_time = mktime(0, 0, 0, $month, $day+$show_days, $year)-$timezone_offset;
		$interval_end_time = mktime(0, 0, 0, $month, $day+$show_days+1, $year)-$timezone_offset;
		//$interval_start_time = get_gmt_time(mktime(0, 0, 0, $month, $day+$show_days, $year));
		//$interval_end_time = get_gmt_time(mktime(0, 0, 0, $month, $day+$show_days+1, $year));
		
		//echo date('Y-m-d: G:i', $interval_start_time).' '.date('Y-m-d: G:i', $interval_end_time).'<br>';
	
		if($show_days == 0)
		{	
			$em_cell->add_html_element(new html_element('h3', $sum_today));
		}else
		{
			$h3 = new html_element('h3', $sum_tommorow);
			$h3->set_attribute('style','margin-top:10px');
			$em_cell->add_html_element($h3);
		}
	
		$events = $cal->get_events_in_array(0, 0, $GO_SECURITY->user_id, $interval_start_time, $interval_end_time);
		
		if(count($events) > 0)
		{
			while ($event = array_shift($events)) {			
		
				$event_obj = new event($event,$link_back);				
				$em_cell->add_html_element($event_obj);					
			}				
		} else {
			$span= new html_element('span', $sc_no_events);		
			$em_cell->add_html_element($span);				
		}			
		$em_row->add_cell($em_cell);
		$em_table->add_row($em_row);
	}

	$cell->add_html_element($em_table);

	
	
	
	
	//Todos
	
	$form->add_html_element(new input('hidden', 'completed_todo_id','',false));
	
	if(isset($_POST['completed_todo_id']) && $_POST['completed_todo_id'] > 0)
	{
		if($event = $cal->get_event($_POST['completed_todo_id']))
		{			
			if($cal->has_write_permission($GO_SECURITY->user_id, $event))
			{
				if($event['completion_time'] > 0)
				{
					$update_event['completion_time'] = 0;
					$update_event['status_id']=6;
				}else
				{
					$update_event['completion_time'] = get_gmt_time();						
					$update_event['status_id']=11;
				}
				$update_event['id'] = $_POST['completed_todo_id'];
				$cal->update_event($update_event);
			}else
			{
				$feedback = $GLOBALS['strAccessDenied'];
			}
		}
	}
		
	require_once ($GO_LANGUAGE->get_language_file('calendar'));

	$em_table = new table();
	$em_table->set_attribute('style', 'border:0px;margin-top:10px;');
	
	$img = new image('todos');
	$img->set_attribute('style', 'border:0px;margin-right:10px;');
	
	$em_cell = new table_cell();
	$em_cell->set_attribute('valign','top');
	$em_cell->add_html_element($img);
	
	$em_row = new table_row();
	$em_row->add_cell($em_cell);
	
	$link = new hyperlink($GO_MODULES->modules['todos']['url'], $lang_modules['todos']);
	
	$h2 = new html_element('h2',$link->get_html());
	$em_row->add_cell(new table_cell($h2->get_html()));
	$em_table->add_row($em_row);
	
	$em_row = new table_row();
	$em_row->add_cell(new table_cell('&nbsp;'));		
	
	$em_cell = new table_cell();
	
	
	$show = false;
	$timezone_offset = get_timezone_offset(mktime(0, 0, 0, $month, $day, $year))*3600;
	//$interval_start_time = mktime(0, 0, 0, $month, $day, $year) - ($timezone_offset * 3600);
	$interval_end_time = $tommorow_interval_start_time = mktime(0, 0, 0, $month, $day +	1, $year)-$timezone_offset;
	$tommorow_interval_end_time = mktime(0, 0, 0, $month, $day +	2, $year)-$timezone_offset;
	for($i=0;$i<2;$i++)
    {
        if($i == 0)
        {
                $h3 = new html_element('h3', $sum_unfinished);
                $cal->get_events(1,0,0,0,0,$GO_SECURITY->user_id,false,1,$interval_end_time);
                while($cal->next_record())
                {
                        $events[]=$cal->Record;
                }
        }else
        {
                $h3 = new html_element('h3', $sum_tommorow);
                $h3->set_attribute('style','margin-top:10px');
                $cal->get_events(1,0,0,0,0,$GO_SECURITY->user_id,false,$tommorow_interval_start_time,$tommorow_interval_end_time);
                while($cal->next_record())
                {
                        $events[]=$cal->Record;
                }
        }
        $cal2 = new calendar();
        if(count($events) > 0)
        {
        	$show=true;
			$em_cell->add_html_element($h3);
			while ($event = array_shift($events)) {
				$td_table = new table();
				$td_row = new table_row();
				$input = new input('checkbox', 'todo[]', $event['id']);
				$input->set_attribute('class','');
				$input->set_attribute('onclick','set_completed(this.value);');
				$td_cell = new table_cell($input->get_html());
				$td_row->add_cell($td_cell);
				$event_obj = new event($event,$link_back);
				$td_cell = new table_cell($event_obj->get_html());
				$td_row->add_cell($td_cell);
				$td_table->add_row($td_row);
				$em_cell->add_html_element($td_table);
			}
        }
    }	
    if($show)
    {
	    $em_row->add_cell($em_cell);
	    $em_table->add_row($em_row);
	    $cell->add_html_element($em_table);
	}
}
?>
<script type="text/javascript">

function set_completed(todo_id)
{
  document.forms[0].completed_todo_id.value=todo_id;
  document.forms[0].submit();
}

</script>
