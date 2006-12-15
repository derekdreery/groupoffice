<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.1 $ $Date: 2006/11/28 12:36:17 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

class timeregistration extends db
{
	function timeregistration()
	{
		$this->db();
	}

	function get_project_users()
	{
		$users = array();
		$sql = "SELECT DISTINCT tr_hours.user_id FROM tr_hours";
		$this->query($sql);
		while($this->next_record())
		{
			$users[]=$this->f('user_id');
		}
		return $users;
	}

	function get_settings($user_id)
	{
		$this->query("SELECT * FROM tr_settings WHERE user_id='$user_id'");
		if ($this->next_record())
		{
			return $this->Record;
		}else
		{
			$this->query("INSERT INTO tr_settings ".
			"(user_id) VALUES ".
			"('$user_id')");
			return $this->get_settings($user_id);
		}
	}




	function start_timer($user_id, $project_id)
	{
		$sql = "INSERT INTO tr_timers (user_id, start_time, project_id) ".
		"VALUES ('$user_id', '".get_gmt_time()."', '$project_id')";
		return $this->query($sql);
	}

	function stop_timer($user_id)
	{
		$sql = "DELETE FROM tr_timers WHERE user_id='$user_id'";
		return $this->query($sql);
	}

	function get_timer($user_id)
	{
		$sql = "SELECT start_time, project_id FROM tr_timers WHERE user_id='$user_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function _add_project($project)
	{
		$project['id'] = $this->nextid('tr_projects');

		if($project['id']>0)
		{
			$this->insert_row('tr_projects', $project);
			return $project['id'];
		}
		return false;
	}

	function _update_project($project)
	{
		return $this->update_row('tr_projects', 'id', $project);
	}


	function add_project($project)
	{
		$project['id'] = $this->nextid("tr_projects");

		$project['ctime'] = $project['mtime'] = get_gmt_time();

		if ($project['id'] > 0 && $this->insert_row('tr_projects', $project))
		{
			return $project['id'];
		}
		return false;
	}

	function update_project($project, $shift_events=false)
	{
		$project['mtime'] = get_gmt_time();
		return $this->update_row('tr_projects','id', $project);
	}

	function get_project($project_id)
	{
		$sql = "SELECT * FROM tr_projects WHERE id='$project_id'";
		$this->query($sql);
		if ($this->next_record(MYSQL_ASSOC))
		{
			return $this->Record;
		}else
		{
			return false;
		}
	}

	function get_project_by_name($name)
	{
		$sql = "SELECT * FROM tr_projects WHERE name='$name'";
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->Record;
		}else
		{
			return false;
		}
	}

	function delete_project($project_id)
	{
		global $GO_SECURITY, $GO_CONFIG;

		if($project = $this->get_project($project_id))
		{
			if($project['calendar_id']> 0)
			{
				global $GO_MODULES;
				require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
				$cal = new calendar();
				$cal->delete_calendar($project['calendar_id']);
			}
			
			
			
			$GO_SECURITY->delete_acl($project['acl_read']);
			$GO_SECURITY->delete_acl($project['acl_write']);

			$sql = "DELETE FROM tr_hours WHERE project_id='$project_id'";
			if ($this->query($sql))
			{
				$sql = "DELETE FROM tr_projects WHERE id='$project_id'";
				return $this->query($sql);
			}
		}
		return false;
	}

	function get_projects($sort='name', $direction='ASC', $project_id=0)
	{
		$sql = "SELECT * FROM tr_projects";
		if($project_id > 0)
		{
			$sql .= " WHERE project_id='$project_id'";
		}
		$sql .= " ORDER BY $sort $direction";
		$this->query($sql);
		return $this->num_rows();
	}





	function get_authorized_projects($user_id, $links=false, $sort='name', $direction='ASC', $start=0, $offset=0, $search_field='', $search_keyword='')
	{
		$sql = "SELECT DISTINCT tr_projects.* FROM tr_projects ".
		"INNER JOIN acl ON (tr_projects.acl_read = acl.acl_id OR tr_projects.acl_write = acl.acl_id OR tr_projects.acl_book=acl.acl_id) ".
		"LEFT JOIN users_groups ON (acl.group_id = users_groups.group_id) WHERE ((".
		"users_groups.user_id = ".$user_id.") OR (".
		"acl.user_id = ".$user_id."))";

		if (is_array($links))
		{
			if(!count($links))
			{
				return 0;
			}
			$sql .= " AND link_id IN (".implode(',', $links).")";
		}

		if($search_field != '' && $search_keyword != '')
		{
			$sql .= " AND $search_field LIKE '$search_keyword'";
		}

		$sql .= " ORDER BY $sort $direction";

		if ($offset > 0)
		{
			$sql2 = $sql ." LIMIT $start, $offset";

			$this->query($sql);

			$count = $this->num_rows();
			if ($count > 0)
			{
				$this->query($sql2);
			}
			return $count;

		}else
		{
			$this->query($sql);
			return $this->num_rows();
		}
	}

	function get_writable_projects($user_id)
	{
		/*$sql = "SELECT DISTINCT tr_projects.* FROM tr_projects, acl, users_groups WHERE (".
		"tr_projects.acl_write = acl.acl_id".
		") AND ( ( acl.group_id = users_groups.group_id AND users_groups.user_id = ".$user_id." AND acl.user_id = 0 ) OR (".
		"acl.group_id = 0 AND acl.user_id = ".$user_id." ))";*/

		$sql = "SELECT DISTINCT tr_projects.* ".
		"FROM tr_projects ".
		"INNER JOIN acl ON tr_projects.acl_write = acl.acl_id ".
		"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
		"WHERE acl.user_id=$user_id ".
		"OR users_groups.user_id=$user_id";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_user_projects($user_id)
	{
		$this->query("SELECT * FROM tr_projects WHERE user_id='$user_id'");
		return $this->num_rows();
	}

	function get_client_projects($contact_id)
	{
		$this->query("SELECT * FROM tr_projects WHERE contact_id='$contact_id'");
		return $this->num_rows();
	}

	function add_booking($booking)
	{

		$booking['id'] = $this->nextid("tr_hours");
		return $this->insert_row('tr_hours',$booking);
	}

	function update_booking($booking)
	{
		return $this->update_row('tr_hours', 'id', $booking);
		
	}

	function get_booking($booking_id)
	{
		$sql = "SELECT * FROM tr_hours WHERE id='$booking_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function get_working_hours($working_hours_id)
	{
		$sql = "SELECT * FROM tr_hours WHERE id='$working_hours_id'";
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function check_hours($user_id, $start_time, $end_time)
	{
		$sql = "SELECT id FROM tr_hours WHERE user_id='$user_id' AND (".
		"(start_time <= '$start_time' AND end_time > '$start_time') OR ".
		"(start_time < '$end_time' AND end_time > '$end_time'))";

		$this->query($sql);
		if ($this->next_record())
		{
			return $this->f('id');
		}
		return false;
	}

	function get_related_projects($project_id)
	{
		$rel_projects = array();

		$projects = new projects();
		$projects->get_projects('name', 'ASC', $project_id);
		while($projects->next_record())
		{
			$rel_projects = array_merge($rel_projects, $this->get_related_projects($projects->f('id')));
		}
		$rel_projects[] = $project_id;
		return $rel_projects;
	}

	function get_total_hours($project_id)
	{
		$totals = array();

		$totals[0]['time'] =0;

		$sql = "SELECT user_id, start_time, end_time, break_time FROM tr_hours WHERE project_id=$project_id";
		$this->query($sql);
		while($this->next_record())
		{
			if(!isset($totals[$this->f('user_id')]['time']))
			{
				$totals[$this->f('user_id')]['time'] =0;
			}
			
			$time = $this->f('end_time')-$this->f('start_time')-$this->f('break_time');
			$totals[$this->f('user_id')]['time'] += $time;
			
			$totals[0]['time'] += $time;
		}
		return $totals;
	}

	function get_hours($start_time=0, $end_time=0, $user_id=0, $project_id=0, $links=false)
	{
		if ($start_time == 0 && $end_time == 0)
		{
			$sql = "SELECT tr_hours.*, tr_projects.name, ".
			"tr_projects.acl_read, tr_projects.acl_write ".
			"FROM tr_hours ".
			"INNER JOIN tr_projects ON (tr_projects.id=tr_hours.project_id)";
			$where = false;
		}else
		{
			$sql = "SELECT tr_hours.*, tr_projects.name, ".
			" tr_projects.acl_read, tr_projects.acl_write ".
			"FROM tr_hours INNER JOIN tr_projects ON (tr_projects.id=tr_hours.project_id) ".
			"WHERE ".
			"tr_hours.start_time >= '$start_time' AND tr_hours.start_time < '$end_time'";

			$where = true;
		}


		if ($user_id > 0)
		{
			if ($where)
			{
				$sql .= " AND";
			}else
			{
				$sql .= " WHERE";
				$where = true;
			}
			$sql .= " tr_hours.user_id='$user_id'";
		}

		if ($project_id > 0)
		{
			if ($where)
			{
				$sql .= " AND";
			}else
			{
				$sql .= " WHERE";
				$where = true;
			}
			$sql .= " tr_hours.project_id='$project_id'";
		}

		if (is_array($links))
		{
			if(!count($links))
			{
				return 0;
			}

			if ($where)
			{
				$sql .= " AND";
			}else
			{
				$sql .= " WHERE";
				$where = true;
			}
			$sql .= " link_id IN (".implode(',', $links).")";
		}

		$sql .= " ORDER BY tr_hours.start_time ASC";

		$this->query($sql);
		return $this->num_rows();
	}
	
	function  add_booking_to_calendar($booking, $calendar_id)
	{
		global $GO_USERS, $GO_LINKS, $GO_MODULES, $GO_CONFIG;
		
		require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
		$cal = new calendar();
		
		$user = $GO_USERS->get_user($booking['user_id']);

		$event['name'] = addslashes(format_name($user['last_name'], $user['first_name'], $user['middle_name'],'first_name'));
		$event['description'] = addslashes($booking['comments']);
 		$event['start_time'] = $booking['start_time'];
		$event['end_time'] = $booking['end_time']-60;
		$event['all_day_event']='1';
		$event['background'] = 'FFFFCC';
		$event['user_id'] = $booking['user_id'];		
		$event['timezone']=$_SESSION['GO_SESSION']['timezone'];
		$event['DST']=$_SESSION['GO_SESSION']['DST'];
		
		if(isset($booking['event_id']) && $booking['event_id']>0)
		{
			$event['id']=$booking['event_id'];
			$cal->update_event($event);
			return $booking['event_id'];	
		}else {
			
			$event['link_id'] = $GO_LINKS->get_link_id();
			$event_id = $cal->add_event($event);
			$cal->subscribe_event($event_id, $calendar_id);		
		
			return $event_id;	
		}	
	}

	function delete_hours($hours_id)
	{
		$booking = $this->get_booking($hours_id);
		if($booking['event_id']>0)
		{
			require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
			$cal = new calendar();
			$cal->delete_event($booking['event_id']);
		}
		
		if ($hours_id > 0)
		{
			$sql = "DELETE FROM tr_hours WHERE id='$hours_id'";
			return $this->query($sql);
		}else
		{
			return false;
		}
	}

	function get_project_hours($project_id, $start_time=0, $end_time=0)
	{
		if ($start_time == 0 && $end_time == 0)
		{
			$sql = "SELECT tr_hours.* FROM tr_hours INNER JOIN tr_projects ON (tr_projects.id=tr_hours.project_id) WHERE tr_hours.project_id='$project_id' ORDER BY tr_hours.start_time ASC";
		}else
		{
			$sql = "SELECT tr_hours.* FROM tr_hours INNER JOIN tr_projects ON (tr_projects.id=tr_hours.project_id) WHERE tr_hours.project_id='$project_id' AND tr_hours.start_time >= '$start_time' AND tr_hours.start_time < '$end_time' ORDER BY tr_hours.start_time ASC";
		}
		$this->query($sql);
		return $this->num_rows();
	}

	function get_user_hours($user_id, $start_time=0, $end_time=0)
	{
		if ($start_time == 0 && $end_time == 0)
		{
			$sql = "SELECT tr_hours.*, tr_projects.name AS project_name FROM tr_hours LEFT JOIN tr_projects ON (tr_hours.project_id = tr_projects.id)  WHERE tr_hours.user_id='$user_id' ORDER BY start_time ASC";
		}else
		{
			$sql = "SELECT tr_hours.*, tr_projects.name AS project_name FROM tr_hours LEFT JOIN tr_projects ON (tr_hours.project_id = tr_projects.id)  WHERE tr_hours.user_id='$user_id' AND tr_hours.start_time >= '$start_time' AND tr_hours.start_time < '$end_time' ORDER BY tr_hours.start_time ASC";
		}
		$this->query($sql);
		return $this->num_rows();
	}


	function copy_project($project_id)
	{
		global $GO_SECURITY;

		if($src_project = $dst_project = $this->get_project($project_id))
		{
			unset($dst_project['id']);

			$dst_project['name'].' ('.$GLOBALS['strCopy'].')';
			$x = 1;
			while($this->get_project_by_name($dst_project['name']))
			{
				$dst_project['name'] = $src_project['name'].' ('.$GLOBALS['strCopy'].' '.$x.')';
				$x++;
			}

			$dst_project['acl_read'] = $GO_SECURITY->get_new_acl('project read');
			$dst_project['acl_write'] = $GO_SECURITY->get_new_acl('project write');

			$GO_SECURITY->copy_acl($src_project['acl_read'], $dst_project['acl_read']);
			$GO_SECURITY->copy_acl($src_project['acl_write'], $dst_project['acl_write']);

			$dst_project = array_map('addslashes', $dst_project);

			return $this->_add_project($dst_project);
		}
		return false;
	}



	function add_template($template)
	{
		$template['id'] = $this->nextid("tr_templates");
		if ($template['id'] > 0 && $this->insert_row('tr_templates', $template))
		{
			return $template['id'];
		}
		return false;
	}

	function update_template($template)
	{
		return $this->update_row('tr_templates','id', $template);
	}

	function get_template($template_id)
	{
		$sql = "SELECT * FROM tr_templates WHERE id='$template_id'";
		$this->query($sql);
		if ($this->next_record(MYSQL_ASSOC))
		{
			return $this->Record;
		}else
		{
			return false;
		}
	}

	function get_template_by_name($name)
	{
		$sql = "SELECT * FROM tr_templates WHERE name='$name'";
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->Record;
		}else
		{
			return false;
		}
	}

	function delete_template($template_id)
	{
		global $GO_SECURITY;

		if($template = $this->get_template($template_id))
		{
			$GO_SECURITY->delete_acl($template['acl_read']);
			$GO_SECURITY->delete_acl($template['acl_write']);

			$sql = "DELETE FROM tr_template_events WHERE template_id='$template_id'";
			if ($this->query($sql))
			{
				$sql = "DELETE FROM tr_templates WHERE id='$template_id'";
				return $this->query($sql);
			}
		}
		return false;
	}

	function get_authorized_templates($user_id)
	{

		$sql = "SELECT DISTINCT tr_templates.* ".
		"FROM tr_templates ".
		"INNER JOIN acl ON tr_templates.acl_write = acl.acl_id OR tr_templates.acl_read=acl.acl_id ".
		"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
		"WHERE acl.user_id=$user_id ".
		"OR users_groups.user_id=$user_id";

		$this->query($sql);
		return $this->num_rows();
	}

	function add_template_event($event)
	{
		$event['id'] = $this->nextid("tr_template_events");
		if ($event['id'] > 0 && $this->insert_row('tr_template_events', $event))
		{
			return $event['id'];
		}
		return false;
	}

	function update_template_event($event)
	{
		return $this->update_row('tr_template_events','id', $event);
	}

	function get_template_event($template_event_id)
	{
		$sql = "SELECT * FROM tr_template_events WHERE id='$template_event_id'";
		$this->query($sql);
		if ($this->next_record(MYSQL_ASSOC))
		{
			return $this->Record;
		}else
		{
			return false;
		}
	}

	function delete_template_event($template_event_id)
	{
		return $this->query("DELETE FROM tr_template_events WHERE id='$template_event_id'");
	}

	function get_template_events($template_id)
	{
		$sql = "SELECT * FROM tr_template_events WHERE template_id='$template_id' ORDER BY time_offset ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	

	function delete_user($user_id)
	{
		$projects = new projects();
		$this->get_user_projects($user_id);
		while($this->next_record())
		{
			$projects->delete_project($this->f('id'));
		}

		$this->query("DELETE FROM tr_settings WHERE user_id='$user_id'");
	}
}
?>
