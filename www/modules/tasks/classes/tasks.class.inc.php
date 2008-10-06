<?php

class tasks extends db
{

	function tasks()
	{
		$this->db();
	}
	
	function is_duplicate_task($task, $tasklist_id)
	{
		unset($task['exceptions']);
		$record = array_map('addslashes', $task);

		$sql = "SELECT id FROM ta_tasks WHERE ".
		"name='".$record['name']."' AND ".
		"due_time='".$record['due_time']."' AND ".
		"tasklist_id='".$tasklist_id."'";

		$this->query($sql);
		if($this->next_record())
		{
			return $this->f('id');
		}
		return false;
	}


	function copy_task($task_id, $new_values=array())
	{
		global $GO_SECURITY;

		$src_task = $dst_task = $this->get_task($task_id);
		unset($dst_task['id'], $dst_task['acl_write'], $dst_task['acl_read']);

		foreach($new_values as $key=>$value)
		{
			$dst_task[$key] = $value;
		}

		$dst_task = array_map('addslashes', $dst_task);

		return $this->add_task($dst_task);

	}



	function add_tasklist($list)
	{
		$list['id'] = $this->nextid("ta_lists");
		$this->insert_row('ta_lists',$list);
		return $list['id'];
	}

	function delete_tasklist($list_id)
	{
		global $GO_SECURITY;
		$delete = new tasks();

		$tasklist = $this->get_tasklist($list_id);

		if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $tasklist['acl_write']))
		{
			throw new AccessDeniedException();
		}

		$sql = "SELECT * FROM ta_tasks WHERE tasklist_id='$list_id'";
		$this->query($sql);

		while ($this->next_record())
		{
			$delete->delete_task($this->f('id'));
		}
		
		$sql= "DELETE FROM ta_lists WHERE id='$list_id'";
		$this->query($sql);
		
		$GO_SECURITY->delete_acl($tasklist['acl_read']);
		$GO_SECURITY->delete_acl($tasklist['acl_write']);

	}

	function update_tasklist($tasklist)
	{
		return $this->update_row('ta_lists','id', $tasklist);
	}
	
	function get_user_tasklists($user_id)
	{
		$sql = "SELECT * FROM ta_lists WHERE user_id=$user_id";
		$this->query($sql);
		return $this->num_rows();
	}
	
	function get_default_tasklist($user_id)
	{
		$sql = "SELECT * FROM ta_lists WHERE user_id=$user_id LIMIT 0,1";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
		
	}

	function get_tasklist($list_id=0)
	{
		if($list_id > 0)
		{
			$sql = "SELECT * FROM ta_lists WHERE id='$list_id'";
			$this->query($sql);
			if ($this->next_record(MYSQL_ASSOC))
			{
				return $this->Record;
			}else
			{
				return $this->get_tasklist();
			}
		}else
		{
			global $GO_SECURITY;

			$tasklist = $this->get_default_tasklist($GO_SECURITY->user_id);
			if ($tasklist)
			{
				return $tasklist;
			}else
			{
				global $GO_USERS;

				$list['user_id']=$GO_SECURITY->user_id;
				$user = $GO_USERS->get_user($GO_SECURITY->user_id);
				$task_name = String::format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'last_name');
				$list['name'] = $task_name;
				$list['acl_read']=$GO_SECURITY->get_new_acl();
				$list['acl_write']=$GO_SECURITY->get_new_acl();
				$x = 1;
				while($this->get_tasklist_by_name(addslashes($list['name'])))
				{
					$list['name'] = $task_name.' ('.$x.')';
					$x++;
				}

				$list['name'] = addslashes($list['name']);
				if (!$list_id = $this->add_tasklist($list))
				{
					throw new DatabaseInsertException();
				}else
				{
					return $this->get_tasklist($list_id);
				}
			}
		}
	}

	function get_tasklist_by_name($name, $user_id=0)
	{
		$sql = "SELECT * FROM ta_lists WHERE name='$name'";

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


	
	function get_authorized_tasklists($auth_type='read', $query='', $user_id, $start=0, $offset=0, $sort='name', $direction='ASC')
	{
		$sql = "SELECT DISTINCT l.* ".
		"FROM ta_lists l ";
		if($auth_type=='read')
		{
			$sql .= "INNER JOIN go_acl a ON (l.acl_read = a.acl_id OR l.acl_write = a.acl_id ) ";
		}else
		{
			$sql .= "INNER JOIN go_acl a ON (l.acl_write = a.acl_id ) ";
		}
		$sql .= "LEFT JOIN go_users_groups ug ON a.group_id = ug.group_id ".
		"WHERE (a.user_id=$user_id OR ug.user_id=$user_id) ";
		
		if(!empty($query))
		{
			$sql .= " AND name LIKE '$query'";
		}
		
		$sql .= "ORDER BY $sort $direction";
		

		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
		}
		return $count;
	}




	/*
	 Times in GMT!
	 */

	function add_task($task)
	{
		

		if(empty($task['tasklist_id']))
		{
			throw new Exception();
		}

		if (!isset($task['user_id']) || $task['user_id'] == 0) {
			global $GO_SECURITY;
			$task['user_id'] = $GO_SECURITY->user_id;
		}

		if(!isset($task['ctime']) || $task['ctime'] == 0)
		{
			$task['ctime']  =  time();
		}

		if(!isset($task['mtime']) || $task['mtime'] == 0)
		{
			$task['mtime']  =  $task['ctime'];
		}

		

		
		if(!isset($task['status']))
		{
			$task['status'] = 'ACCEPTED';
		}	


		$task['id'] = $this->nextid("ta_tasks");
		
		$this->insert_row('ta_tasks', $task);
		
		
		
		$this->set_reminder($task);
		
		return $task['id'];
	}
	
	function set_reminder($task)
	{
		global $GO_CONFIG;
		
		$tasklist = $this->get_tasklist($task['tasklist_id']);
		
		require_once($GO_CONFIG->class_path.'base/reminder.class.inc.php');
		$rm = new reminder();
		$existing_reminder = $rm->get_reminder_by_link_id($tasklist['user_id'], $task['id'], 12);

		if(empty($event['reminder']) && $existing_reminder)
		{
			$rm->delete_reminder($existing_reminder['id']);
		}
		
		if(!empty($task['reminder']))
		{			
			$reminder['user_id']=$tasklist['user_id'];
			$reminder['name']=$task['name'];
			$reminder['link_type']=12;
			$reminder['link_id']=$task['id'];
			$reminder['time']=$task['reminder'];
			
			if($existing_reminder)
			{
				$rm->update_reminder($reminder);
			}else
			{
				$rm->add_reminder($reminder);
			}
		}
	}


	function update_task($task)
	{
		if(!isset($task['mtime']) || $task['mtime'] == 0)
		{
			$task['mtime']  = time();
		}

		if(isset($task['completion_time']) && $task['completion_time'] > 0 && $this->copy_recurring_completed($task['id']))
		{
			$task['rrule'] = '';
			$task['repeat_end_time'] = 0;
		}

		if(isset($task['reminder']))
		{
			$this->set_reminder($task);
		}
		
		return $this->update_row('ta_tasks', 'id', $task);
	}
	
	
	function copy_recurring_completed($task_id)
	{
		global $GO_LINKS;
		/*
		 If a recurring task is completed we copy it to a new task and recur that again
		 */

		 $task = $this->get_task($task_id);
		 $old_start_time = $task['start_time'];

		 if(!empty($task['rrule']) && $next_recurrence_time = Date::get_next_recurrence_time($task['start_time'], $task['start_time'], $task['rrule']))
		 {
		 	$old_id = $task['id'];
		 	unset($task['completion_time'], $task['id'], $task['acl_read'], $task['acl_write']);
		 	$task['start_time'] = $next_recurrence_time;
		 	
		 	$diff = $next_recurrence_time-$old_start_time;
		 	
		 	$task['due_time']+=$diff;
		 	$task['reminder']+=$diff;
		 		 	
		 	$task['status']='IN-PROCESS';
		 	
		 	$task=array_map('addslashes',$task);
		 	if($new_task_id = $this->add_task($task))
		 	{
		 		//$GO_LINKS->copy_links($old_id, $new_task_id, 11, 11);	
		 	}
		 }
		 return true;
	}

	function get_tasks(
	$lists,
	$user_id=0,
	$show_completed=false,
	$sort_field='due_time',
	$sort_order='ASC',
	$start=0,
	$offset=0,
	$show_inactive=false)
	{

		$sql  = "SELECT DISTINCT t.* FROM ta_tasks t";

		if($user_id > 0)
		{
			$sql .= " INNER JOIN ta_lists l ON (t.tasklist_id=l.id)";
		}

		$where=false;
		
		if(!$show_completed)
		{
			$where=true;
			
			$sql .= ' WHERE completion_time=0';
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
			$sql .= "l.user_id='$user_id' ";
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
			$sql .= "t.tasklist_id IN (".implode(',', $lists).")";
		}
		
		if(!$show_inactive)
		{
			$now = time();
			if($where)
			{
				$sql .= " AND ";
			}else
			{
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "t.start_time<=".$now." AND (t.due_time>=".$now." OR t.completion_time=0)";
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



	function get_task($task_id)
	{
		$sql = "SELECT t.*, tl.acl_read, tl.acl_write FROM ta_tasks t INNER JOIN ta_lists tl ON tl.id=t.tasklist_id WHERE t.id='$task_id'";
		$this->query($sql);
		if($this->next_record(MYSQL_ASSOC))
		{
			return $this->Record;
		}else
		{
			throw new DatabaseSelectException();
		}
	}



	function delete_task($task_id)
	{
		if($task = $this->get_task($task_id))
		{
			global $GO_CONFIG;
			
			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			$fs = new filesystem();
			if(file_exists($GO_CONFIG->file_storage_path.'tasks/'.$task_id.'/'))
			{
				$fs->delete($GO_CONFIG->file_storage_path.'tasks/'.$task_id.'/');
			}

			$sql = "DELETE FROM ta_tasks WHERE id='$task_id'";
			$this->query($sql);
						
			require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
			$search = new search();
			$search->delete_search_result($task_id, 12);
			}
	}



	
	
	
	function get_task_from_ical_object($object)
	{
		global $GO_MODULES, $GO_CONFIG;

		if(!isset($this->ical2array))
		{
			require_once($GO_CONFIG->class_path.'ical2array.class.inc');
			$this->ical2array = new ical2array();
		}


		$task['name'] = (isset($object['SUMMARY']['value']) && $object['SUMMARY']['value'] != '') ? trim($object['SUMMARY']['value']) : 'Unnamed';
		if(isset($object['SUMMARY']['params']['ENCODING']) && $object['SUMMARY']['params']['ENCODING'] == 'QUOTED-PRINTABLE')
		{
			$task['name'] = quoted_printable_decode($task['name']);
		}
		$task['description'] = isset($object['DESCRIPTION']['value']) ? trim($object['DESCRIPTION']['value']) : '';

		if(isset($object['DESCRIPTION']['params']['ENCODING']) && $object['DESCRIPTION']['params']['ENCODING'] == 'QUOTED-PRINTABLE')
		{
			$task['description'] = String::trim_lines(quoted_printable_decode($task['description']));
		}


		$task['status'] = isset($object['STATUS']['value']) ? $object['STATUS']['value'] : 'NEEDS-ACTION';
		

		if(isset($object['DTSTART']))
		{
			$timezone_id = isset($object['DTSTART']['params']['TZID']) ? $object['DTSTART']['params']['TZID'] : '';
			$task['start_time'] = $this->ical2array->parse_date($object['DTSTART']['value']);
		}

		if(isset($object['DTEND']['value']))
		{
			$timezone_id = isset($object['DTEND']['params']['TZID']) ? $object['DTEND']['params']['TZID'] : '';
			$task['due_time'] = $this->ical2array->parse_date($object['DTEND']['value'],  $timezone_id);

		}elseif(isset($object['DURATION']['value']))
		{
			$duration = $this->ical2array->parse_date($object['DURATION']['value']);
			$task['due_time'] = $task['start_time']+$duration;

		}elseif(isset($object['DUE']['value']))
		{
			$timezone_id = isset($object['DUE']['params']['TZID']) ? $object['DUE']['params']['TZID'] : '';
			$task['due_time'] = $this->ical2array->parse_date($object['DUE']['value'],  $timezone_id);
		}
		
		if(isset($object['DUE']['value']))
		{
			$timezone_id = isset($object['DUE']['params']['TZID']) ? $object['DUE']['params']['TZID'] : '';
			$task['due_time'] = $this->ical2array->parse_date($object['DUE']['value'],  $timezone_id);
		}


		if(isset($object['COMPLETED']['value']))
		{
			$timezone_id = isset($object['COMPLETED']['params']['TZID']) ? $object['COMPLETED']['params']['TZID'] : '';
			$task['completion_time'] = $this->ical2array->parse_date($object['COMPLETED']['value'], $timezone_id);
			$task['status']='COMPLETED';
		}elseif(isset($task['status']) && $task['status']=='COMPLETED')
		{
			$task['completion_time']=time();
		}else
		{
			$task['completion_time']=0;
		}
		

		//reminder
		if(isset($object['DALARM']['value']))
		{
			$dalarm = explode(';', $object['DALARM']['value']);
			if(isset($dalarm[0]) && $remind_time = $this->ical2array->parse_date($dalarm[0]))
			{
				$task['reminder'] = $task['start_time']-$remind_time;
			}
		}

		if(!isset($task['reminder']) && isset($object['AALARM']['value']))
		{
			$aalarm = explode(';', $object['AALARM']['value']);
			if(isset($aalarm[0]) && $remind_time = $this->ical2array->parse_date($aalarm[0]))
			{
				$task['reminder'] = $task['due_time']-$remind_time;
			}
		}

		if(isset($task['reminder']) && $task['reminder']<0)
		{
			//If we have a negative reminder value default to half an hour before
			$task['reminder'] = 1800;
		}

		if($task['name'] != '')// && $task['start_time'] > 0 && $task['end_time'] > 0)
		{
			//$task['all_day_task'] = (isset($object['DTSTART']['params']['VALUE']) &&
			//strtoupper($object['DTSTART']['params']['VALUE']) == 'DATE') ? true : false;

			//for Nokia. It doesn't send all day task in any way. If the local times are equal and the
			//time is 0:00 hour then this is probably an all day task.

			/*if(isset($object['CLASS']['value']) && $object['CLASS']['value'] == 'PRIVATE')
			{
				$task['private'] = '1';
			}else {
				$task['private']= '0';
			}*/


			$task['rrule'] = '';
			$task['repeat_end_time'] = 0;


			if (isset($object['RRULE']['value']) && $rrule = $this->ical2array->parse_rrule($object['RRULE']['value']))
			{
				$task['rrule'] = $object['RRULE']['value'];
				if (isset($rrule['UNTIL']))
				{
					if($task['repeat_end_time'] = $this->ical2array->parse_date($rrule['UNTIL']))
					{
						$task['repeat_end_time'] = date(0,0,0, adodb_date('n', $task['repeat_end_time']), adodb_date('j', $task['repeat_end_time'])+1, adodb_date('Y', $task['repeat_end_time']));
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
	
					$days=Date::shift_days_to_gmt($days, date('G', $task['start_time']), Date::get_timezone_offset($task['start_time']));					
					
					$task['rrule']=Date::build_rrule(Date::ical_freq_to_repeat_type($rrule['FREQ']), $rrule['INTERVAL'], $task['repeat_end_time'], $days, $month_time);					
				}				
			}
			
			//figure out end time of task
			if(isset($task_count))
			{
				$task['repeat_end_time']='0';
				$start_time=$task['start_time'];
				for($i=1;$i<$task_count;$i++)
				{
					$task['repeat_end_time']=$start_time=Date::get_next_recurrence_time($task['start_time'], $start_time, $task['rrule']);
				}
				if($task['repeat_end_time']>0)
				{
					$task['repeat_end_time']+=$task['end_time']-$task['start_time'];
				}
			}

			return $task;
		}
		return false;
	}
	
	
	

	function get_task_from_ical_file($ical_file)
	{
		global $GO_MODULES;

		require_once($GO_MODULES->modules['task']['class_path'].'ical2array.class.inc');
		$this->ical2array = new ical2array();

		$vtask = $this->ical2array->parse_file($ical_file);

		while($object = array_shift($vtask[0]['objects']))
		{
			if($object['type'] == 'Vtask' || $object['type'] == 'VTODO')
			{
				if($task = $this->get_task_from_ical_object($object))
				{
					return $task;
				}
			}
		}
		return false;
	}

	function import_ical_string($ical_string, $task_id)
	{
		global $GO_MODULES;

		require_once($GO_MODULES->modules['task']['class_path'].'ical2array.class.inc');
		$this->ical2array = new ical2array();

		$vtask = $this->ical2array->parse_string($ical_string);

		while($object = array_shift($vtask[0]['objects']))
		{
			if($object['type'] == 'Vtask' || $object['type'] == 'VTODO')
			{
				if($task = $this->get_task_from_ical_object($object))
				{
					$exceptions=isset($task['exceptions']) ? $task['exceptions'] : array();
					unset($task['exceptions']);
					$task = array_map('addslashes', $task);
					$task = array_map('trim', $task);
					$task['exceptions']=$exceptions;

					if ($task_id = $this->add_task($task))
					{
						$this->subscribe_task($task_id, $task_id);
						return $task_id;
					}
				}
			}
		}
		return false;
	}


	//TODO: attendee support
	function import_ical_file($user_id, $ical_file, $task_id, $return_task_id=false)
	{
		global $GO_CONFIG, $GO_MODULES;
		$count = 0;

		$cal_module = $GO_MODULES->get_module('task');

		if ($task = $this->get_task($task_id) && $cal_module)
		{
			require_once($cal_module['class_path'].'ical2array.class.inc');
			$this->ical2array = new ical2array();

			$vtask = $this->ical2array->parse_file($ical_file);

			while($object = array_shift($vtask[0]['objects']))
			{
				if($object['type'] == 'Vtask' || $object['type'] == 'VTODO')
				{
					if($task = $this->get_task_from_ical_object($object))
					{

						$exceptions=isset($task['exceptions']) ? $task['exceptions'] : array();
						unset($task['exceptions']);
						$task = array_map('addslashes', $task);
						$task = array_map('trim', $task);
						$task['exceptions']=$exceptions;

						if ($task_id = $this->add_task($task))
						{
							$count++;
							$this->subscribe_task($task_id, $task_id);
						}
					}
				}
			}
		}
		return $count;
	}
	
	
	function __on_add_user($params)
	{
		global $GO_SECURITY;

		$user = $params['user'];

		$tasklist['name']=addslashes(String::format_name($user));
		$tasklist['user_id']=$user['id'];
		$tasklist['acl_read']=$GO_SECURITY->get_new_acl('tasks', $user['id']);
		$tasklist['acl_write']=$GO_SECURITY->get_new_acl('tasks', $user['id']);

		$tasklist_id = $this->add_tasklist($tasklist);
	}




	function __on_user_delete($user)
	{
		$delete = new tasks();
		$sql = "SELECT * FROM ta_lists WHERE user_id='".$user['id']."'";
		$this->query($sql);
		while($this->next_record())
		{
			$delete->delete_tasklist($this->f('id'));
		}
	}

	function __on_search($last_sync_time=0)
	{
		global $GO_MODULES, $GO_LANGUAGE;

		require($GO_LANGUAGE->get_language_file('tasks'));

		$sql  = "SELECT DISTINCT t.*, tl.acl_read, tl.acl_write FROM ta_tasks t ".
		"INNER JOIN ta_lists tl ON t.tasklist_id=tl.id ".
		"WHERE mtime>$last_sync_time";


		$this->query($sql);

		$search = new search();
		
		$now = gmmktime();

		while($this->next_record())
		{
			$class = '';
			
			if($this->f('due_time')<$now)
			{
				$class = 'tasks-late';
			}
			
			if($this->f('completion_time')>0)
			{
				$class .= ' tasks-completed';
			}
			
			$description = addslashes($this->f('description'));
			
			if(!empty($description))
			{
				$description .= '<br />';
			}
			
			$description .= $lang['tasks']['status'].': '.$this->f('status');
			
			//$cache['table']='cal_tasks';
			$cache['id']=$this->f('id');
			$cache['user_id']=$this->f('user_id');
			$cache['name'] = '<span class="'.$class.'">'.addslashes($this->f('name')).'</span>';
			//$cache['link_id'] = $this->f('link_id');
			$cache['link_type']=12;
			$cache['description']=$description;
			$cache['type']='Task';
			$cache['keywords']=addslashes($search->record_to_keywords($this->Record)).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			$cache['acl_read']=$this->f('acl_read');
			$cache['acl_write']=$this->f('acl_write');
				
			$search->cache_search_result($cache);
		}
	}
}