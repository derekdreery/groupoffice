<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.21 $ $Date: 2006/10/18 13:29:18 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
class events_list extends datatable
{
	var $count;
	
	function events_list($id, $todos=false, $events=false, $calendar_id=0, $view_id=0, $user_id=0, $link_id=0, $return_zero=true, $form_name='0', $return_to='', $show_completed=false)
	{
		global $GO_CONFIG, $GO_SECURITY, $GO_MODULES, $GO_LANGUAGE, $GO_LINKS;
		
		$this->datatable($id, false, $form_name);
		$this->set_attribute('style','width:100%;margin-bottom:20px;margin-top:0px;');
		
		$return_to = !empty($return_to) ? $return_to : $_SERVER['PHP_SELF'];
		
		require($GO_LANGUAGE->get_language_file('calendar'));

		require_once($GO_MODULES->modules['calendar']['class_path']."calendar.class.inc");
		$cal = new calendar();

		if($todos)
		{
			$now = get_gmt_time();
			$this->add_outerhtml_element(new input('hidden', $this->attributes['id'].'[completed_todo_id]','',false));
		}

		if($this->task == 'delete')
		{
			foreach($this->selected as $delete_event_id)
			{
				$delete_event_id =str_replace('1:', '', $delete_event_id);
				
				$event = $cal->get_event($delete_event_id);
				if(!$event['write_permission'])
				{
					$feedback = $strAccessDenied;
					break;
				}else
				{
					$cal->delete_event($delete_event_id);
				}
			}
		}elseif($this->task=='unlink')
		{
			global $GO_LINKS;
			
			foreach($this->selected as $event_id)
			{
				$event_id =str_replace('1:', '', $event_id);
				if($event = $cal->get_event($event_id))
				{
					$GO_LINKS->delete_link($link_id, $event['link_id']);
				}
			}
		}
		
		$links = $link_id > 0 ? $GO_LINKS->get_links($link_id, 1) : false;

		if(isset($_POST[$this->attributes['id']]['completed_todo_id']) && $_POST[$this->attributes['id']]['completed_todo_id'] > 0)
		{
			if($event = $cal->get_event($_POST[$this->attributes['id']]['completed_todo_id']))
			{			
				if(!$event['write_permission'])
				{
					$feedback = $strAccessDenied;					
				}else
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
					$update_event['id'] = $_POST[$this->attributes['id']]['completed_todo_id'];
					$cal->update_event($update_event);			
				}
			}
		}
		
		if (isset($feedback))
		{
			$p = new html_element('p', $feedback);
			$p->set_attribute('class','Error');
			$this->add_outerhtml_element($p);
		}		

		if($todos)
		{
			$th = new table_heading('');
			$th->set_attribute('style','width:20px');
			$this->add_column($th);
		}
		
		$this->add_column(new table_heading($GLOBALS['strName'], 'name'));
		$this->add_column(new table_heading($sc_start_at, 'start_time'));
		$this->add_column(new table_heading($sc_end_at,'end_time'));
		$this->add_column(new table_heading($GLOBALS['strOwner']));
		$this->add_column(new table_heading($sc_calendars));
		$this->add_column(new table_heading($sc_recur_section, 'repeat_type'));

		$th = new table_heading('&nbsp;');
		//$this->add_column($th);
		
		$this->count= $cal->get_events($todos,$events, $show_completed, $calendar_id, $view_id, $user_id, $links,
			0,0, $this->sort_index, $this->sql_sort_order,
			$this->start, $this->offset);
			$this->set_pagination($this->count);
		
		$cal2 = new calendar();
		
		if($this->count >0)
		{	
			while ($cal->next_record()) {	
				$row = new table_row('1:'.$cal->f('id'));
				$row->set_attribute('style','height:20px;');
				if($cal2->has_read_permission($GO_SECURITY->user_id, $cal->Record))
				{
					$row->set_attribute('ondblclick', 
						"javascript:window.location.href='".
						$GO_MODULES->modules['calendar']['url'].
						'event.php?event_id='.$cal->f('id').
						'&return_to='.urlencode($return_to)."';");	
				
					
					$div = new html_element('div','&nbsp;');
					$div->set_attribute('class','summary_icon');
					$div->set_attribute('style','background-color: #'.$cal->f('background'));
					
					$name_cell =new table_cell($div->get_html().htmlspecialchars($cal->f('name')));
					if($cal->f('todo')=='1')
					{
						$input = new input('checkbox', 'todo[]', $cal->f('id'));
						$input->set_attribute('class','');
						$input->set_attribute('onclick','set_completed(this.value);');
						if($cal->f('completion_time') > 0)
						{
							$input->set_attribute('checked','checked');
							$name_cell->set_attribute('class', 'event_completed');
						}elseif($cal->f('todo') == '1' && $now>$cal->f('end_time'))
						{
							$name_cell->set_attribute('class', 'event_late');
						}
						$cell = new table_cell($input->get_html());
						$cell->set_attribute('style','width:20px;');
						$row->add_cell($cell);
					}elseif($todos)
					{
						$cell = new table_cell('&nbsp;');
						$cell->set_attribute('style','width:20px;');
						$row->add_cell($cell);
					}				
				}else
				{
					$name_cell =new table_cell(htmlspecialchars($sc_private_event));
					$cell = new table_cell('&nbsp;');
					$row->add_cell($cell);
				}
				$row->add_cell($name_cell);
				
				if($cal->f('all_day_event') != '1')
				{
					$start_time = get_timestamp($cal->f('start_time'));
					$end_time = get_timestamp($cal->f('end_time'));
				}else
				{
					$start_time = date($_SESSION['GO_SESSION']['date_format'], gmt_to_local_time($cal->f('start_time')));
					$end_time = date($_SESSION['GO_SESSION']['date_format'], gmt_to_local_time($cal->f('end_time')));
				}
				$row->add_cell(new table_cell($start_time));
				$row->add_cell(new table_cell($end_time));
				$row->add_cell(new table_cell(show_profile($cal->f('user_id'))));
				
		
				$calendars = '';
				$event_cal_count = $cal2->get_calendars_from_event($cal->f('id'));
				$first = true;
				while ($cal2->next_record()) {
					if ($first) {
						$first = false;
					} else {
						$calendars .= ', ';
					}
					$calendars .= htmlspecialchars($cal2->f('name'));
				}	
				$row->add_cell(new table_cell($calendars));
				
				$cell = new table_cell($sc_types[$cal->f('repeat_type')]);
				
				if($cal->f('repeat_type') > 0)
				{
					if($cal->f('repeat_forever') == '1')
					{
						$cell->innerHTML .= ' '.$cal_forever;
					}else
					{
						$cell->innerHTML .= ' '.$cal_until.' '.date($_SESSION['GO_SESSION']['date_format'], gmt_to_local_time($cal->f('repeat_end_time')));
					}
				}
				$row->add_cell($cell);								
				$this->add_row($row);
			}
			
			if($todos)
			{
				$this->outerHTML .= 
				'<script type="text/javascript">
				function set_completed(todo_id)
				{
				  document.forms["'.$this->form_name.'"].elements["'.$this->attributes['id'].'[completed_todo_id]"].value=todo_id;
				  document.forms["'.$this->form_name.'"].submit();
				}
				</script>';
			}
		}
			
		if($this->count == 0 && $return_zero)
		{
			$row = new table_row();
			$cell = new table_cell($GLOBALS['sc_no_events']);
			$cell->set_attribute('colspan','99');
			$this->add_row($row);
		}else
		{
			$this->innerHTML = '';
			return false;
		}		
	}
}
