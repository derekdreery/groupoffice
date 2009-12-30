<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

class tasks extends db
{
	public function __on_load_listeners($events){
		$events->add_listener('load_settings', __FILE__, 'tasks', 'load_settings');
		$events->add_listener('save_settings', __FILE__, 'tasks', 'save_settings');
		$events->add_listener('user_delete', __FILE__, 'tasks', 'user_delete');
		$events->add_listener('add_user', __FILE__, 'tasks', 'add_user');
		$events->add_listener('build_search_index', __FILE__, 'tasks', 'build_search_index');
		$events->add_listener('check_database', __FILE__, 'tasks', 'check_database');
	}

	public static function check_database(){
		global $GO_CONFIG, $GO_MODULES, $GO_LANGUAGE;

		$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

		echo 'Task folders'.$line_break;

		if(isset($GO_MODULES->modules['files']))
		{
			$ta = new tasks();
			$db = new db();

			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();

			$sql = "SELECT * FROM ta_lists";
			$db->query($sql);
			while($tasklist = $db->next_record())
			{
				try{
					$files->check_share('tasks/'.$tasklist['name'], $tasklist['user_id'], $tasklist['acl_id'], false);
				}
				catch(Exception $e){
					echo $e->getMessage().$line_break;
				}
			}

			$db->query("SELECT c.*,a.name AS tasklist_name,a.acl_id FROM ta_tasks c INNER JOIN ta_lists a ON a.id=c.tasklist_id");
			while($task = $db->next_record())
			{
				try{
					$path = $ta->build_task_files_path($task, array('name'=>$task['tasklist_name']));
                    echo $path.$line_break;
					$up_task['files_folder_id']=$files->check_folder_location($task['files_folder_id'], $path);

					if($up_task['files_folder_id']!=$task['files_folder_id']){
						$up_task['id']=$task['id'];
						$ta->update_row('ta_tasks', 'id', $up_task);
					}
					$files->set_readonly($up_task['files_folder_id']);
				}
				catch(Exception $e){
					echo $e->getMessage().$line_break;
				}
			}
		}

		if($GO_MODULES->modules['customfields']){
			$db = new db();
			echo "Deleting non existing custom field records".$line_break.$line_break;
			$db->query("delete from cf_12 where link_id not in (select id from ta_lists);");
		}
		echo 'Done'.$line_break.$line_break;
	}

	
	function load_settings($response)
	{
		global $GO_MODULES;

		if($GO_MODULES->has_module('tasks'))
		{
			$tasks = new tasks();
			
			$settings = $tasks->get_settings($_POST['user_id']);

			$tasklist = $tasks->get_tasklist($settings['default_tasklist_id']);

			if($tasklist)
			{
				$settings['default_tasklist_id']=$tasklist['id'];
				$settings['default_tasklist_name']=$tasklist['name'];
			}
			$response['data']=array_merge($response['data'], $settings);
		}
	}

	function save_settings(){

		global $GO_MODULES;

		if($GO_MODULES->has_module('tasks'))
		{
			$tasks = new tasks();
			
			$settings['remind']=isset($_POST['remind']) ? '1' : '0';
			$settings['user_id']=$_POST['user_id'];
			if(isset($_POST['reminder_days']))					
				$settings['reminder_days']=$_POST['reminder_days'];
				
			if(isset($_POST['reminder_time']))
				$settings['reminder_time']=$_POST['reminder_time'];

			$settings['default_tasklist_id']=$_POST['default_tasklist_id'];
			
			$tasks->update_settings($settings);
		}
	}
	
	function get_settings($user_id)
	{
		$this->query("SELECT * FROM ta_settings WHERE user_id='".$this->escape($user_id)."'");
		if ($this->next_record(DB_ASSOC))
		{
			return $this->record;
		}else
		{			
			$this->query("INSERT INTO ta_settings (user_id, reminder_time) VALUES ('".$this->escape($user_id)."', '".date($_SESSION['GO_SESSION']['time_format'],mktime(8,0))."')");
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
		return $this->update_row('ta_settings', 'user_id', $settings);
	}

	
	function is_duplicate_task($task, $tasklist_id)
	{
		unset($task['exceptions']);
		$record = $task;

		if(!isset($record['due_time'])){
			$record['due_time']=0;
		}

		$sql = "SELECT id FROM ta_tasks WHERE ".
		"name='".$this->escape($record['name'])."' AND ".
		"due_time='".$this->escape($record['due_time'])."' AND ".
		"tasklist_id='".$this->escape($tasklist_id)."'";

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
		unset($dst_task['id'], $dst_task['acl_id']);

		foreach($new_values as $key=>$value)
		{
			$dst_task[$key] = $value;
		}

		$dst_task = $dst_task;
		return $this->add_task($dst_task);
	}

	function add_tasklist($list)
	{
		global $GO_MODULES;
		if(isset($GO_MODULES->modules['files']))
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
			
			$files->check_share('tasks/'.File::strip_invalid_chars($list['name']),$list['user_id'], $list['acl_id']);
		}	
		
		$list['id'] = $this->nextid("ta_lists");	
		$this->insert_row('ta_lists',$list);
		return $list['id'];
	}

	function delete_tasklist($list_id)
	{
		global $GO_SECURITY;
		$delete = new tasks();

		$tasklist = $this->get_tasklist($list_id);

		$sql = "SELECT * FROM ta_tasks WHERE tasklist_id='".$this->escape($list_id)."'";
		$this->query($sql);

		while ($this->next_record())
		{
			$delete->delete_task($this->f('id'));
		}
		
		$sql= "DELETE FROM ta_lists WHERE id='".$this->escape($list_id)."'";
		$this->query($sql);

		if(empty($tasklist['shared_acl'])){
			$GO_SECURITY->delete_acl($tasklist['acl_id']);
		}

		$this->query("DELETE FROM cal_visible_tasklists WHERE tasklist_id=?", 'i', $list_id);
		$this->query("DELETE FROM su_visible_lists WHERE tasklist_id=?", 'i', $list_id);
		
		global $GO_MODULES;
		if(isset($GO_MODULES->modules['files']))
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
			
			$folder = $files->resolve_path('tasks/'.File::strip_invalid_chars($tasklist['name']));			
			if($folder){
				$files->delete_folder($folder);
			}
		}	

	}

	function update_tasklist($tasklist, $old_tasklist=false)
	{
		if(!$old_tasklist)$old_tasklist=$this->get_tasklist($tasklist['id']);

		global $GO_MODULES;
		if(isset($GO_MODULES->modules['files']) && $old_tasklist &&  $tasklist['name']!=$old_tasklist['name'])
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();			
			$files->move_by_paths('tasks/'.File::strip_invalid_chars($old_tasklist['name']), 'tasks/'.File::strip_invalid_chars($tasklist['name']));
		}
		
		global $GO_SECURITY;
		//user id of the tasklist changed. Change the owner of the ACL as well
		if(isset($tasklist['user_id']) && $old_tasklist['user_id'] != $tasklist['user_id'])
		{
			$GO_SECURITY->chown_acl($old_tasklist['acl_id'], $tasklist['user_id']);
		}
		
		return $this->update_row('ta_lists','id', $tasklist);
	}
	
	function get_user_tasklists($user_id)
	{
		$sql = "SELECT * FROM ta_lists WHERE user_id='".$this->escape($user_id)."'";

		$this->query($sql);
		return $this->num_rows();
	}
	
	function get_default_tasklist($user_id)
	{
		$sql = "SELECT * FROM ta_lists WHERE user_id='".$this->escape($user_id)."' LIMIT 0,1";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
		
	}

	function get_tasklist($list_id=0, $user_id=0)
	{
		if($list_id > 0)
		{
			$sql = "SELECT * FROM ta_lists WHERE id='".$this->escape($list_id)."'";
			$this->query($sql);
			if ($this->next_record(DB_ASSOC))
			{
				return $this->record;
			}else
			{
				return false;
			}
		}else
		{
			global $GO_SECURITY;

			$user_id = !empty($user_id) ? $user_id : $GO_SECURITY->user_id;

			$tasklist = $this->get_default_tasklist($user_id);
			if ($tasklist)
			{
				return $tasklist;
			}else
			{
				global $GO_USERS;

				$list['user_id']=$user_id;
				$user = $GO_USERS->get_user($user_id);
				if(!$user){
					return false;
				}
				$task_name = String::format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'last_name');
				$list['name'] = $task_name;
				$list['acl_id']=$GO_SECURITY->get_new_acl('',$user_id);
				$x = 1;
				while($this->get_tasklist_by_name($list['name']))
				{
					$list['name'] = $task_name.' ('.$x.')';
					$x++;
				}

				if (!$list_id = $this->add_tasklist($list))
				{
					throw new DatabaseInsertException();
				}else
				{
					$this->update_settings(array('user_id'=>$GO_SECURITY->user_id, 'default_tasklist_id'=>$list_id));
					return $this->get_tasklist($list_id);
				}
			}
		}
	}

	function get_tasklist_by_name($name, $user_id=0)
	{
		$sql = "SELECT * FROM ta_lists WHERE name='".$this->escape($name)."'";

		if($user_id>0)
		{
			$sql .= " AND user_id=".$this->escape($user_id);
		}
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->record;
		}else
		{
			return false;
		}
	}


	
	function get_authorized_tasklists($auth_type='read', $query='', $user_id=0, $start=0, $offset=0, $sort='name', $direction='ASC')
	{
                global $GO_SECURITY;

                if(!$user_id)
                        $user_id = $GO_SECURITY->user_id;
        
		$sql = "SELECT DISTINCT l.* ".
		"FROM ta_lists l ";
		if($auth_type=='read')
		{
			$sql .= "INNER JOIN go_acl a ON l.acl_id = a.acl_id ";
		}else
		{
			$sql .= "INNER JOIN go_acl a ON (l.acl_id=a.acl_id AND a.level>".GO_SECURITY::READ_PERMISSION.") ";
		}
		$sql .= "LEFT JOIN go_users_groups ug ON a.group_id = ug.group_id ".
		"WHERE (a.user_id=".$this->escape($user_id)." OR ug.user_id=".$this->escape($user_id).")";
		
		
		if(!empty($query))
		{
			$sql .= " AND name LIKE '".$this->escape($query)."'";
		}
		
		$sql .= " ORDER BY ".$this->escape($sort." ".$direction);
		

		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.','.$offset);
			$this->query($sql);
		}
		return $count;
	}

	function add_task($task, $tasklist=false)
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
		
		global $GO_MODULES;
		if(!isset($task['files_folder_id']) && isset($GO_MODULES->modules['files']))
		{
			global $GO_CONFIG;
			
			if(!$tasklist)
			{
				$tasklist = $this->get_tasklist($task['tasklist_id']);				
			}
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();			

			$new_path = $this->build_task_files_path($task,$tasklist);			
			if($folder=$files->create_unique_folder($new_path))
			{
				$task['files_folder_id']=$folder['id'];
			}
		}

		$task['id'] = $this->nextid("ta_tasks");		
		$this->insert_row('ta_tasks', $task);		
		
		$this->cache_task($task['id']);
		
		$this->set_reminder($task);
		
		return $task['id'];
	}
	
	function set_reminder($task) {
		global $GO_CONFIG;

		$tasklist = $this->get_tasklist($task['tasklist_id']);

		require_once($GO_CONFIG->class_path.'base/reminder.class.inc.php');
		$rm = new reminder();
		$existing_reminder = $rm->get_reminder_by_link_id($tasklist['user_id'], $task['id'], 12);

		if(empty($task['reminder']) && $existing_reminder) {
			$rm->delete_reminder($existing_reminder['id']);
		}

		if(!empty($task['reminder'])) {
			$reminder['user_id']=$tasklist['user_id'];
			$reminder['name']=$task['name'];
			$reminder['link_type']=12;
			$reminder['link_id']=$task['id'];
			$reminder['time']=$task['reminder'];

			if($existing_reminder) {
				$reminder['id']=$existing_reminder['id'];
				$rm->update_reminder($reminder);
			}else {
				$rm->add_reminder($reminder);
			}
		}
	}

	function build_task_files_path($task, $tasklist) {
		return 'tasks/'.File::strip_invalid_chars($tasklist['name']).'/'.date('Y', $task['due_time']).'/'.File::strip_invalid_chars($task['name']);
	}


	function update_task($task, $tasklist=false, $old_task=false) {
		if(!isset($task['mtime']) || $task['mtime'] == 0) {
			$task['mtime']  = time();
		}

		if(isset($task['completion_time']) && $task['completion_time'] > 0 && $this->copy_recurring_completed($task['id'])) {
			$task['rrule'] = '';
			$task['repeat_end_time'] = 0;
		}

		if(isset($task['reminder'])) {
			$this->set_reminder($task);
		}



		global $GO_MODULES;
		if(isset($GO_MODULES->modules['files'])) {
			if(!$old_task) {
				$old_task = $this->get_task($task['id']);
			}

			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();

			if(!isset($task['due_time'])) {
				$task['due_time']=$old_task['due_time'];
			}
			if(!isset($task['name'])) {
				$task['name']=$old_task['name'];
			}
			if(!isset($task['tasklist_id'])) {
				$task['tasklist_id']=$old_task['tasklist_id'];
			}
			if(!$tasklist) {
				$tasklist = $this->get_tasklist($task['tasklist_id']);
			}

			$new_path = $this->build_task_files_path($task, $tasklist);
			$task['files_folder_id']=$files->check_folder_location($old_task['files_folder_id'], $new_path);
		}

		$r = $this->update_row('ta_tasks', 'id', $task);


		$this->cache_task($task['id']);
		return $r;
	}


	function copy_recurring_completed($task_id) {
		global $GO_LINKS;
		/*
		 If a recurring task is completed we copy it to a new task and recur that again
		 */

		$task = $this->get_task($task_id);
		$old_start_time = $task['start_time'];

		if(!empty($task['rrule']) && $next_recurrence_time = Date::get_next_recurrence_time($task['start_time'], $task['start_time'], $task['rrule'])) {
			$old_id = $task['id'];
			unset($task['completion_time'], $task['id'], $task['acl_id']);
			$task['start_time'] = $next_recurrence_time;

			$diff = $next_recurrence_time-$old_start_time;

			$task['due_time']+=$diff;
			$task['reminder']+=$diff;

			$task['status']='IN-PROCESS';

			$task=array_map('addslashes',$task);
			if($new_task_id = $this->add_task($task)) {
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
			$show_inactive=false,
			$search_query='') {

		global $GO_MODULES;

		$sql  = "SELECT DISTINCT t.*";

		if($GO_MODULES->has_module('customfields')) {
			$sql .= " ,cf_12.*";
		}

		$sql .= " FROM ta_tasks t";

		if($user_id > 0) {
			$sql .= " INNER JOIN ta_lists l ON (t.tasklist_id=l.id)";
		}

		if($GO_MODULES->has_module('customfields')) {
			$sql .= " LEFT JOIN cf_12 ON cf_12.link_id=t.id";
		}

		$where=false;

		if(empty($show_completed)) {
			$where=true;
			$sql .= ' WHERE completion_time=0';

		}


		if($user_id > 0) {
			if($where) {
				$sql .= " AND ";
			}else {
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "l.user_id='".$this->escape($user_id)."' ";
		}else {
			if($where) {
				$sql .= " AND ";
			}else {
				$sql .= " WHERE ";
				$where=true;
			}

			$sql .= "t.tasklist_id IN (".$this->escape(implode(',',$lists)).")";

		}

		if(empty($show_inactive)) {
			$now = mktime(0,0,0);
			if($where) {
				$sql .= " AND ";
			}else {
				$sql .= " WHERE ";
				$where=true;
			}
			$sql .= "t.start_time<=".$now." AND (t.due_time>=".$now." OR t.completion_time=0)";
		}

		if(!empty($search_query)) {
			if($where) {
				$sql .= " AND ";
			}
			else {
				$where=true;
				$sql .= " WHERE ";
			}
			$query = $this->escape($search_query);
			$sql .= "(t.name LIKE '".$query."' OR t.description LIKE '".$query."')";
		}

		if($sort_field != '' && $sort_order != '') {
			$sql .=	" ORDER BY ".$this->escape($sort_field)." ".$this->escape($sort_order)."";
		}

		$_SESSION['GO_SESSION']['export_queries']['get_tasks']=array(
				'query'=>$sql,
				'method'=>'format_task_record',
				'class'=>'tasks',
				'require'=>__FILE__);

		if($offset == 0) {
			$this->query($sql);
			return $this->num_rows();
		}else {
			$this->query($sql);
			$count = $this->num_rows();

			$sql .= " LIMIT ".$this->escape($start).", ".$this->escape($offset)."";

			$this->query($sql);

			return $count;
		}
	}

	function format_task_record(&$record) {
		$record['start_time']=Date::get_timestamp($record['start_time']);
		$record['due_time']=Date::get_timestamp($record['due_time']);
		$record['completion_time']=Date::get_timestamp($record['completion_time']);
		$record['ctime']=Date::get_timestamp($record['ctime']);
		$record['mtime']=Date::get_timestamp($record['mtime']);
	}

	function get_task($task_id) {
		$sql = "SELECT t.*, tl.acl_id FROM ta_tasks t INNER JOIN ta_lists tl ON tl.id=t.tasklist_id WHERE t.id='".$this->escape($task_id)."'";
		$this->query($sql);
		return $this->next_record(DB_ASSOC);
	}

	function delete_task($task_id)
	{
		if($task = $this->get_task($task_id))
		{
			global $GO_CONFIG,$GO_MODULES;

			if(isset($GO_MODULES->modules['files']))
			{
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
				$files = new files();
				try{
					$files->delete_folder($task['files_folder_id']);
				}
				catch(Exception $e){}
			}				

			$sql = "DELETE FROM ta_tasks WHERE id='".$this->escape($task_id)."'";
			$this->query($sql);
			
			global $GO_CONFIG;
				
			require_once($GO_CONFIG->class_path.'base/reminder.class.inc.php');
			$rm = new reminder();
			$rm2 = new reminder();
			$rm->get_reminders_by_link_id($task_id, 12);
			while($r = $rm->next_record())
			{
				$rm2->delete_reminder($r['id']);
			}
						
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
		
		if(empty($task['start_time'])){
			$task['start_time']=!empty($task['due_time']) ? $task['due_time'] : time();
		}

		if(empty($task['due_time'])){
			$task['due_time']=$task['start_time'];
		}


		if(!empty($object['COMPLETED']['value']))
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
		if(!empty($object['DALARM']['value']))
		{
			$dalarm = explode(';', $object['DALARM']['value']);
			if(isset($dalarm[0]))
			{
				$task['reminder']= $this->ical2array->parse_date($dalarm[0]);
			}
		}

		if(!isset($task['reminder']) && isset($object['AALARM']['value']))
		{
			$aalarm = explode(';', $object['AALARM']['value']);
			if(isset($aalarm[0]))
			{
				$task['reminder']= $this->ical2array->parse_date($aalarm[0]);
			}
		}

		if(isset($task['reminder']) && $task['reminder']<0)
		{
			//If we have a negative reminder value default to half an hour before
			$task['reminder'] = 1800;
		}

		if($task['name'] != '')
		{
			$task['rrule'] = '';
			$task['repeat_end_time'] = 0;

			if (isset($object['RRULE']['value']) && $rrule = $this->ical2array->parse_rrule($object['RRULE']['value']))
			{
				$task['rrule'] = $object['RRULE']['value'];
				if (isset($rrule['UNTIL']))
				{
					if($task['repeat_end_time'] = $this->ical2array->parse_date($rrule['UNTIL']))
					{
						$task['repeat_end_time'] = mktime(0,0,0, date('n', $task['repeat_end_time']), date('j', $task['repeat_end_time'])+1, date('Y', $task['repeat_end_time']));
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
					
					$task['rrule']=Date::build_rrule(Date::ical_freq_to_repeat_type($rrule), $rrule['INTERVAL'], $task['repeat_end_time'], $days, $month_time);					
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
			if($object['type'] == 'VTODO')
			{
				if($task = $this->get_task_from_ical_object($object))
				{
					return $task;
				}
			}
		}
		return false;
	}

	function import_ical_string($ical_string, $tasklist_id)
	{
		global $GO_MODULES, $GO_CONFIG;
		$count = 0;

		require_once($GO_CONFIG->class_path.'ical2array.class.inc');
		$this->ical2array = new ical2array();

		$vcalendar = $this->ical2array->parse_string($ical_string);
			
		if(isset($vcalendar[0]['objects']))
		{			
			while($object = array_shift($vcalendar[0]['objects']))
			{
				if($object['type'] == 'VTODO')
				{
					if($task = $this->get_task_from_ical_object($object))
					{
						$task['tasklist_id']=$tasklist_id;	
						if($task_id = $this->add_task($task))
						{
							$count++;
						}
					}
				}
			}
		}
		return $count;
	}


	//TODO: attendee support
	function import_ical_file($ical_file, $tasklist_id)
	{
		$data = file_get_contents($ical_file);
		return $this->import_ical_string($data, $tasklist_id);
	}
	
	
	public static function add_user($user)
	{
		global $GO_SECURITY, $GO_MODULES;
		
		$tasks = new tasks();

		$tasklist['name']=String::format_name($user);
		$tasklist['user_id']=$user['id'];
		$tasklist['acl_id']=$GO_SECURITY->get_new_acl('tasks', $user['id']);

		$tasklist_id = $tasks->add_tasklist($tasklist);
		
		if(isset($GO_MODULES->modules['summary'])){
			$tasks->add_visible_tasklist(array('user_id'=>$user['id'], 'tasklist_id'=>$tasklist_id));
		}
	}




	public static function user_delete($user)
	{
		$tasks = new tasks();
		$delete = new tasks();

		$sql = "DELETE FROM ta_settings WHERE user_id=".$tasks->escape($user['id']);
		$tasks->query($sql);

		$sql = "SELECT * FROM ta_lists WHERE user_id='".$tasks->escape($user['id'])."'";
		$tasks->query($sql);
		while($tasks->next_record())
		{
			$delete->delete_tasklist($tasks->f('id'));
		}
	}
	
	/**
	 * When a global search action is performed this function will be called for each module
	 *
	 * @param int $last_sync_time The time this function was called last
	 */

	public static function build_search_index()
	{
		$tasks = new tasks();
		
		$sql = "SELECT id FROM ta_tasks";
		$tasks->query($sql);
		
		$tasks2 = new tasks();
		while($record=$tasks->next_record())
		{
			$tasks2->cache_task($record['id']);
		}
	}

	function __on_delete_link($id, $link_type)
	{
		//echo $id.':'.$link_type;
		if($link_type==12)
		{
			return $this->delete_task($id);
		}
	}
	

	private function cache_task($task_id)
	{
		global $GO_CONFIG, $GO_LANGUAGE;
		
		require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
		$search = new search();
		require($GO_LANGUAGE->get_language_file('tasks'));

		$sql  = "SELECT DISTINCT t.*, tl.acl_id FROM ta_tasks t ".
		"INNER JOIN ta_lists tl ON t.tasklist_id=tl.id ".
		"WHERE t.id=?";

		$this->query($sql, 'i', $task_id);
		$record = $this->next_record();
		if($record)
		{		
			$now = time();

			$class = '';
			
			if($this->f('due_time')<$now)
			{
				$class = 'tasks-late';
			}
			
			if($this->f('completion_time')>0)
			{
				$class .= ' tasks-completed';
			}
			
			$status = isset($lang['tasks']['statuses'][$this->f('status')]) ? $lang['tasks']['statuses'][$this->f('status')] : $lang['tasks']['statuses']['NEEDS-ACTION']; 
			
			//$cache['table']='cal_tasks';
			$cache['id']=$this->f('id');
			$cache['user_id']=$this->f('user_id');
			$cache['name'] = '<span class="'.$class.'">'.htmlspecialchars($this->f('name'), ENT_QUOTES, 'utf-8').' ['.$status.']</span>';
			//$cache['link_id'] = $this->f('link_id');
			$cache['link_type']=12;
			$cache['module']='tasks';
			$cache['description']=sprintf($lang['tasks']['dueAtdate'], Date::get_timestamp($record['due_time'],false));
			$cache['type']=$lang['link_type'][12];
			$cache['keywords']=$search->record_to_keywords($this->record).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			$cache['acl_id']=$this->f('acl_id');
				
			$search->cache_search_result($cache);
		}
	}

	public function get_visible_tasklists($user_id)
	{
		$this->query("SELECT * FROM su_visible_lists WHERE user_id = $user_id");
		return $this->num_rows();
	}

	public function add_visible_tasklist($tasklist)
	{
		if($this->replace_row('su_visible_lists', $tasklist))
		{
			return $this->insert_id();
		}
		return false;
	}

	public function delete_visible_tasklist($tasklist_id, $user_id)
	{
		$this->query("DELETE FROM su_visible_lists WHERE tasklist_id = $tasklist_id AND user_id = $user_id");
	}
}