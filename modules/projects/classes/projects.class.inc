<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.50 $ $Date: 2006/11/28 10:42:45 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

class projects extends db
{
	function projects()
	{
		$this->db();
	}
	
	

	function get_project_users()
	{
		$users = array();
		$sql = "SELECT DISTINCT pmHours.user_id FROM pmHours";
		$this->query($sql);
		while($this->next_record())
		{
			$users[]=$this->f('user_id');
		}
		return $users;
	}

	function get_settings($user_id)
	{
		$this->query("SELECT * FROM pm_settings WHERE user_id='$user_id'");
		if ($this->next_record())
		{
			return $this->Record;
		}else
		{
			$this->query("INSERT INTO pm_settings ".
			"(user_id, show_projects) VALUES ".
			"('$user_id', '1')");
			return $this->get_settings($user_id);
		}
	}

	function get_statuses()
	{
		$sql = "SELECT * FROM pmStatuses ORDER BY id ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_status($status_id)
	{
		$sql = "SELECT * FROM pmStatuses WHERE id='$status_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}



	function set_sorting($user_id, $sort_field, $sort_order)
	{
		$sql = "UPDATE pm_settings SET sort_field='$sort_field' , ".
		"sort_order='$sort_order' WHERE user_id='$user_id'";
		return $this->query($sql);
	}

	function set_show_projects($user_id, $show_projects)
	{
		$sql = "UPDATE pm_settings SET show_projects='$show_projects'  WHERE user_id='$user_id'";
		return $this->query($sql);
	}

	function set_registration_method($user_id, $registration_method, $unit_value=false, $int_fee_value=false, $fee_time=false, $ext_fee_value=false, $fee_time=false)
	{
		$sql = "UPDATE pm_settings SET registration_method='$registration_method'";
		if($unit_value !==false)
		{
			$sql .= ", unit_value='$unit_value'";
		}
		if($int_fee_value !== false)
		{
			$sql .= ", int_fee_value='$int_fee_value', fee_time='$fee_time'";
			$sql .= ", ext_fee_value='$ext_fee_value', fee_time='$fee_time'";
		}
		$sql .= " WHERE user_id='$user_id'";

		return $this->query($sql);
	}

	function start_timer($user_id, $project_id)
	{
		$sql = "INSERT INTO pmTimers (user_id, start_time, project_id) ".
		"VALUES ('$user_id', '".get_gmt_time()."', '$project_id')";
		return $this->query($sql);
	}

	function stop_timer($user_id)
	{
		$sql = "DELETE FROM pmTimers WHERE user_id='$user_id'";
		return $this->query($sql);
	}

	function get_timer($user_id)
	{
		$sql = "SELECT start_time, project_id FROM pmTimers WHERE user_id='$user_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function _add_project($project)
	{
		$project['id'] = $this->nextid('pmProjects');

		if($project['id']>0)
		{
			$this->insert_row('pmProjects', $project);
			return $project['id'];
		}
		return false;
	}

	function _update_project($project)
	{
		return $this->update_row('pmProjects', 'id', $project);
	}


	function add_project($project)
	{
		$project['id'] = $this->nextid("pmProjects");

		$project['ctime'] = $project['mtime'] = get_gmt_time();

		if ($project['id'] > 0 && $this->insert_row('pmProjects', $project))
		{
			return $project['id'];
		}
		return false;
	}

	function update_project($project, $shift_events=false)
	{
		if($shift_events)
		{
			global $GO_MODULES, $GO_CONFIG, $GO_LINKS;

			$old_project = $this->get_project($project['id']);

			if($old_project['link_id']>0)
			{
				$offset = $project['start_date'] - $old_project['start_date'];

				require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
				$cal = new calendar();
				$cal2 = new calendar();

				$links = $GO_LINKS->get_links($old_project['link_id'], 1);

				if(count($links) > 0)
				{
					$cal->get_events(1, 1, 1, 0, 0, 0, $links);
					while($cal->next_record())
					{
						$event['id'] = $cal->f('id');
						$event['start_time'] = $cal->f('start_time') + $offset;
						$event['end_time'] = $cal->f('end_time') + $offset;
						$event['repeat_end_time'] = $cal->f('repeat_end_time') + $offset;

						$cal2->update_event($event);
					}
				}
			}
		}

		$project['mtime'] = get_gmt_time();
		return $this->update_row('pmProjects','id', $project);
	}

	function get_project($project_id)
	{
		$sql = "SELECT * FROM pmProjects WHERE id='$project_id'";
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
		$sql = "SELECT * FROM pmProjects WHERE name='$name'";
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
		global $GO_SECURITY, $GO_CONFIG, $GO_LINKS;

		if($project = $this->get_project($project_id))
		{
			if($project['calendar_id']> 0)
			{
				global $GO_MODULES;
				
				require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
				$cal = new calendar();
				$cal->delete_calendar($project['calendar_id']);
			}
			
		
			$GO_LINKS->delete_link($project['link_id']);
			
			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			$fs = new filesystem();
			$fs->delete($GO_CONFIG->file_storage_path.'projects/'.$project_id.'/');
			
			
			
			$GO_SECURITY->delete_acl($project['acl_read']);
			$GO_SECURITY->delete_acl($project['acl_write']);

			$sql = "DELETE FROM pmHours WHERE project_id='$project_id'";
			if ($this->query($sql))
			{
				$sql = "DELETE FROM pmProjects WHERE id='$project_id'";
				return $this->query($sql);
			}
		}
		return false;
	}

	function get_projects($sort='name', $direction='ASC', $project_id=0)
	{
		$sql = "SELECT * FROM pmProjects";
		if($project_id > 0)
		{
			$sql .= " WHERE project_id='$project_id'";
		}
		$sql .= " ORDER BY $sort $direction";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_projects_for_period($user_id, $start_time, $end_time)
	{
		$sql = "SELECT DISTINCT pmProjects.*, pmStatuses.name AS status_name FROM pmProjects ".
		"INNER JOIN pmStatuses ON pmStatuses.id=pmProjects.status ".
		"INNER JOIN acl ON (pmProjects.acl_read = acl.acl_id OR pmProjects.acl_write = acl.acl_id) ".
		"LEFT JOIN users_groups ON (acl.group_id = users_groups.group_id) WHERE ((".
		"users_groups.user_id = ".$user_id.") OR (".
		"acl.user_id = ".$user_id.")) AND pmProjects.start_date<$end_time AND pmProjects.end_date>$start_time";

		$this->query($sql);
		return $this->num_rows();
	}



	function get_authorized_projects($user_id, $links=false, $sort='name', $direction='ASC', $start=0, $offset=0,
	$status_id='', $search_field='', $search_keyword='', $start_time=0, $end_time=0)
	{
		$sql = "SELECT DISTINCT pmProjects.*, pmStatuses.name AS status_name FROM pmProjects ".
		"LEFT JOIN pmStatuses ON pmStatuses.id=pmProjects.status ".
		"INNER JOIN acl ON (pmProjects.acl_read = acl.acl_id OR pmProjects.acl_write = acl.acl_id OR pmProjects.acl_book=acl.acl_id) ".
		"LEFT JOIN users_groups ON (acl.group_id = users_groups.group_id) WHERE ((".
		"users_groups.user_id = ".$user_id.") OR (".
		"acl.user_id = ".$user_id."))";

		if(is_array($status_id) && count($status_id)>0)
		{
			if(count($status_id)>1)
			{
				$sql .= " AND status IN (".implode(',', $status_id).")";
			}else
			{
				$sql .= " AND status='".$status_id[0]."'";
			}
		}elseif($status_id>0)
		{
			$sql .= " AND status='$status_id'";
		}


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

		if($start_time>0)
		{
			$sql .= " AND start_date>=$start_time";
		}
		if($end_time>0)
		{
			$sql .= " AND end_date<$end_time";
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
		/*$sql = "SELECT DISTINCT pmProjects.* FROM pmProjects, acl, users_groups WHERE (".
		"pmProjects.acl_write = acl.acl_id".
		") AND ( ( acl.group_id = users_groups.group_id AND users_groups.user_id = ".$user_id." AND acl.user_id = 0 ) OR (".
		"acl.group_id = 0 AND acl.user_id = ".$user_id." ))";*/

		$sql = "SELECT DISTINCT pmProjects.* ".
		"FROM pmProjects ".
		"INNER JOIN acl ON pmProjects.acl_write = acl.acl_id ".
		"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
		"WHERE acl.user_id=$user_id ".
		"OR users_groups.user_id=$user_id";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_user_projects($user_id)
	{
		$this->query("SELECT * FROM pmProjects WHERE user_id='$user_id'");
		return $this->num_rows();
	}

	function get_client_projects($contact_id)
	{
		$this->query("SELECT * FROM pmProjects WHERE contact_id='$contact_id'");
		return $this->num_rows();
	}

	function add_booking($booking)
	{

		$booking['id'] = $this->nextid("pmHours");
		return $this->insert_row('pmHours',$booking);
	}

	function update_booking($booking)
	{
		return $this->update_row('pmHours', 'id', $booking);
		
	}

	function get_booking($booking_id)
	{
		$sql = "SELECT * FROM pmHours WHERE id='$booking_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function get_working_hours($working_hours_id)
	{
		$sql = "SELECT * FROM pmHours WHERE id='$working_hours_id'";
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function check_hours($user_id, $start_time, $end_time)
	{
		$sql = "SELECT id FROM pmHours WHERE user_id='$user_id' AND (".
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

	function get_total_hours($project_id, $start_time=0, $end_time=0)
	{
		$totals = array();

		$totals[0]['time'] =0;
		$totals[0]['int_fee'] =0;
		$totals[0]['ext_fee'] =0;

		$sql = "SELECT units, user_id, start_time, end_time, break_time, ext_fee_value, int_fee_value, fee_time FROM pmHours WHERE project_id=$project_id";
		
		if($start_time>0)
		{
			$sql .= " AND start_time>=$start_time";
		}
		if($end_time>0)
		{
			$sql .= " AND end_time<$end_time";
		}

		$this->query($sql);
		while($this->next_record())
		{
			
			
			if(!isset($totals[$this->f('user_id')]['time']))
			{
				$totals[$this->f('user_id')]['time'] =0;
			}
			if(!isset($totals[$this->f('user_id')]['int_fee']))
			{
				$totals[$this->f('user_id')]['int_fee'] =0;
			}
			if(!isset($totals[$this->f('user_id')]['ext_fee']))
			{
				$totals[$this->f('user_id')]['ext_fee'] =0;
			}

			if ($this->f('units') > 0)
			{
				$time = $this->f('units')*$this->f('fee_time')*60;
			}else
			{
				$time = $this->f('end_time')-$this->f('start_time')-$this->f('break_time');
			}
			
			
			$totals[$this->f('user_id')]['time'] += $time;
			$int_fee = $this->f('fee_time')> 0 ? ($this->f('int_fee_value')*$time/($this->f('fee_time')*60)) : 0;
			$totals[$this->f('user_id')]['int_fee'] += $int_fee;
			$ext_fee = $this->f('fee_time')> 0 ? ($this->f('ext_fee_value')*$time/($this->f('fee_time')*60)) : 0;
			$totals[$this->f('user_id')]['ext_fee'] += $ext_fee;

			$totals[0]['time'] += $time;
			$totals[0]['int_fee'] += $int_fee;
			$totals[0]['ext_fee'] += $ext_fee;
		}
		return $totals;
	}

	function get_hours($start_time=0, $end_time=0, $user_id=0, $project_id=0, $links=false)
	{
		if ($start_time == 0 && $end_time == 0)
		{
			$sql = "SELECT pmHours.*, pmProjects.name, ".
			"pmProjects.description, pmProjects.acl_read, pmProjects.acl_write, pmProjects.acl_book ".
			"FROM pmHours ".
			"INNER JOIN pmProjects ON (pmProjects.id=pmHours.project_id)";
			$where = false;
		}else
		{
			$sql = "SELECT pmHours.*, pmProjects.name, ".
			"pmProjects.description, pmProjects.acl_read, pmProjects.acl_write, pmProjects.acl_book ".
			"FROM pmHours INNER JOIN pmProjects ON (pmProjects.id=pmHours.project_id) ".
			"WHERE ".
			"pmHours.start_time >= '$start_time' AND pmHours.start_time < '$end_time'";

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
			$sql .= " pmHours.user_id='$user_id'";
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
			$sql .= " pmHours.project_id='$project_id'";
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

		$sql .= " ORDER BY pmHours.start_time ASC";

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
		$event['end_time'] = $booking['end_time'];
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
		global $GO_MODULES, $GO_CONFIG;

		$booking = $this->get_booking($hours_id);
		if($booking['event_id']>0)
		{
			require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
			$cal = new calendar();
			$cal->delete_event($booking['event_id']);
		}
		
		if ($hours_id > 0)
		{
			$sql = "DELETE FROM pmHours WHERE id='$hours_id'";
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
			$sql = "SELECT pmHours.*, pmFees.value AS fee_value, pmFees.time AS fee_time FROM pmHours INNER JOIN pmProjects ON (pmProjects.id=pmHours.project_id) LEFT JOIN pmFees ON (pmFees.id=pmProjects.fee_id) WHERE pmHours.project_id='$project_id' ORDER BY pmHours.start_time ASC";
		}else
		{
			$sql = "SELECT pmHours.*, pmFees.value AS fee_value, pmFees.time AS fee_time FROM pmHours INNER JOIN pmProjects ON (pmProjects.id=pmHours.project_id) LEFT JOIN pmFees ON (pmFees.id=pmProjects.fee_id)  WHERE pmHours.project_id='$project_id' AND pmHours.start_time >= '$start_time' AND pmHours.start_time < '$end_time' ORDER BY pmHours.start_time ASC";
		}
		$this->query($sql);
		return $this->num_rows();
	}

	function get_user_hours($user_id, $start_time=0, $end_time=0)
	{
		if ($start_time == 0 && $end_time == 0)
		{
			$sql = "SELECT pmHours.*, pmFees.value AS fee_value, pmFees.time AS fee_time, pmProjects.name AS project_name FROM pmHours LEFT JOIN pmProjects ON (pmHours.project_id = pmProjects.id) LEFT JOIN pmFees ON (pmFees.id=pmHours.fee_id)  WHERE pmHours.user_id='$user_id' ORDER BY start_time ASC";
		}else
		{
			$sql = "SELECT pmHours.*, pmFees.value AS fee_value, pmFees.time AS fee_time, pmProjects.name AS project_name FROM pmHours LEFT JOIN pmProjects ON (pmHours.project_id = pmProjects.id) LEFT JOIN pmFees ON (pmFees.id=pmHours.fee_id)  WHERE pmHours.user_id='$user_id' AND pmHours.start_time >= '$start_time' AND pmHours.start_time < '$end_time' ORDER BY pmHours.start_time ASC";
		}
		$this->query($sql);
		return $this->num_rows();
	}

	function get_fees()
	{
		$sql = "SELECT * FROM pmFees";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_authorized_fees($user_id)
	{
		$sql = "SELECT DISTINCT pmFees.* ".
		"FROM pmFees ".
		"INNER JOIN acl ON pmFees.acl_id = acl.acl_id ".
		"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
		"WHERE acl.user_id=$user_id OR users_groups.user_id=$user_id";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_fee($fee_id)
	{
		$sql = "SELECT * FROM pmFees WHERE id='$fee_id'";
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->Record;
		}else
		{
			return false;
		}
	}

	function add_fee($fee)
	{
		$fee['id'] = $this->nextid("pmFees");
		if ($fee['id']  > 0 && $this->insert_row('pmFees', $fee))
		{
			return $fee['id'];
		}
		return false;
	}

	function delete_fee($fee_id)
	{
		if($fee = $this->get_fee($fee_id))
		{
			global $GO_SECURITY;
			$GO_SECURITY->delete_acl($fee['acl_read']);
			$GO_SECURITY->delete_acl($fee['acl_write']);

			$sql = "DELETE FROM pmFees WHERE id='$fee_id'";
			return $this->query($sql);
		}
		return false;
	}

	function update_fee($fee)
	{
		return $this->update_row('pmFees','id', $fee);
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
		$template['id'] = $this->nextid("pm_templates");
		if ($template['id'] > 0 && $this->insert_row('pm_templates', $template))
		{
			return $template['id'];
		}
		return false;
	}

	function update_template($template)
	{
		return $this->update_row('pm_templates','id', $template);
	}

	function get_template($template_id)
	{
		$sql = "SELECT * FROM pm_templates WHERE id='$template_id'";
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
		$sql = "SELECT * FROM pm_templates WHERE name='$name'";
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

			$sql = "DELETE FROM pm_template_events WHERE template_id='$template_id'";
			if ($this->query($sql))
			{
				$sql = "DELETE FROM pm_templates WHERE id='$template_id'";
				return $this->query($sql);
			}
		}
		return false;
	}

	function get_authorized_templates($user_id)
	{

		$sql = "SELECT DISTINCT pm_templates.* ".
		"FROM pm_templates ".
		"INNER JOIN acl ON pm_templates.acl_write = acl.acl_id OR pm_templates.acl_read=acl.acl_id ".
		"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
		"WHERE acl.user_id=$user_id ".
		"OR users_groups.user_id=$user_id";

		$this->query($sql);
		return $this->num_rows();
	}

	function add_template_event($event)
	{
		$event['id'] = $this->nextid("pm_template_events");
		if ($event['id'] > 0 && $this->insert_row('pm_template_events', $event))
		{
			return $event['id'];
		}
		return false;
	}

	function update_template_event($event)
	{
		return $this->update_row('pm_template_events','id', $event);
	}

	function get_template_event($template_event_id)
	{
		$sql = "SELECT * FROM pm_template_events WHERE id='$template_event_id'";
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
		return $this->query("DELETE FROM pm_template_events WHERE id='$template_event_id'");
	}

	function get_template_events($template_id)
	{
		$sql = "SELECT * FROM pm_template_events WHERE template_id='$template_id' ORDER BY time_offset ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function apply_template($template_id, $project_id, $calendar_id)
	{
		global $GO_MODULES, $GO_SECURITY, $GO_USERS, $GO_CONFIG, $GO_LINKS;

		require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
		$cal = new calendar();

		$project = $this->get_project($project_id);
		if($project)
		{
			if(empty($project['link_id']))
			{
				$update_project['id'] = $project_id;
				$update_project['link_id'] = $project['link_id'] = $GO_LINKS->get_link_id();
				$this->update_project($update_project);
			}

			$this->get_template_events($template_id);
			while($this->next_record(MYSQL_ASSOC))
			{
				$event = $this->Record;
				$event['link_id']= $GO_LINKS->get_link_id();
				$event['start_time']		= get_gmt_time($project['start_date']+$event['time_offset']);
				$event['end_time'] = $event['start_time']+$event['duration'];

				unset($event['duration'], $event['time_offset'], $event['template_id'], $event['id']);

				$event_id = $cal->add_event($event);

				$cal->subscribe_event($event_id, $calendar_id);

				$calendar = $cal->get_calendar($calendar_id);

				if ($calendar && $event['reminder'] > 0 && $next_recurrence_time = $cal->get_next_recurrence_time($event_id)) {
					$remind_time = $next_recurrence_time - $event['reminder'];

					$reminder['user_id'] = $cal->f('user_id');
					$reminder['event_id'] = $event_id;
					$reminder['remind_time'] = $remind_time;
					$reminder['occurence_time'] = $next_recurrence_time;

					$cal->add_reminder($reminder);
				}

				$GO_LINKS->add_link($event['link_id'],1, $project['link_id'], 5);
			}
			return true;
		}
		return false;
	}

	function __on_user_delete($user_id)
	{
		$projects = new projects();
		$this->get_user_projects($user_id);
		while($this->next_record())
		{
			$projects->delete_project($this->f('id'));
		}

		$this->query("DELETE FROM pm_settings WHERE user_id='$user_id'");
	}
	
	function __on_search($user_id,$last_sync_time=0)
	{
		global $GO_MODULES, $GO_LANGUAGE;

		require($GO_LANGUAGE->get_language_file('projects'));

		$sql = "SELECT DISTINCT pmProjects.* FROM pmProjects ".
		"INNER JOIN acl ON (pmProjects.acl_read = acl.acl_id OR pmProjects.acl_write = acl.acl_id OR pmProjects.acl_book=acl.acl_id) ".
		"LEFT JOIN users_groups ON (acl.group_id = users_groups.group_id) WHERE ((".
		"users_groups.user_id = ".$user_id.") OR (".
		"acl.user_id = ".$user_id.")) AND mtime>$last_sync_time";

		$this->query($sql);

		$db = new db();
		while($this->next_record())
		{
			$cache['table']='pmProjects';
			$cache['id']=$this->f('id');
			$cache['user_id']=$user_id;
			$cache['name'] = addslashes($this->f('name'));
			$cache['link_id'] = $this->f('link_id');
			$cache['link_type']=5;
			$cache['description']=addslashes($this->f('description'));
			$cache['url']=$GO_MODULES->modules['projects']['url'].'project.php?project_id='.$this->f('id');
			$cache['type']=$pm_project;
			$cache['keywords']=addslashes(record_to_keywords($this->Record)).','.$pm_project;
			$cache['mtime']=$this->f('mtime');
			
			if($this->f('ctime')>$last_sync_time)
			{
				$db->insert_row('se_cache',$cache);
			}else {
				$db->update_row('se_cache',array('user_id','link_id'), $cache);
			}
		}
	}
}
