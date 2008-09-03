<?php
class projects extends db
{
	function __construct()
	{
		$this->db();
	}
	
	function get_report($group_by='project_id', $user_id, $start_date=0, $end_date=0, $sort='units', $dir='DESC', $start=0, $limit=0)
	{
		
		$sql = "SELECT COUNT(DISTINCT h.date) AS days, ".
				"SUM(h.units) AS units, ".
				"SUM(h.units*h.int_fee_value) AS int_fee_value, ".
				"SUM(h.units*h.int_fee_value) AS ext_fee_value, ".
				"h.user_id, h.project_id, p.customer, p.name AS project_name ".				
				"FROM pm_hours h ".
				"INNER JOIN pm_projects p ON p.id=h.project_id ".
				"WHERE EXISTS(".
				"SELECT * FROM go_acl a ".
				"LEFT JOIN go_users_groups ug ON ug.group_id=a.group_id ".
				"WHERE (a.acl_id=p.acl_read OR a.acl_id=p.acl_write) AND (a.user_id=$user_id OR ug.user_id=$user_id)".
				") ";
		
		//echo $start;
		
		$where = true;
		if($start_date>0)
		{			
			$sql .= "AND h.date>=$start_date ";
		}
		
		if($end_date>0)
		{
			if($where)
			{
				$sql .= "AND ";
			}else
			{
				$sql .= "WHERE ";
			}
			$sql .= "h.date<$end_date ";
		}		
		$sql .= "GROUP BY $group_by ORDER BY $sort $dir";
		$this->query($sql);				
		$count = $this->num_rows();
				
		if($limit>0)
		{
			$sql .= " LIMIT $start, $limit";
			$this->query($sql);
		}
		
		return $count;
						
	}

	function get_statuses()
	{
		$sql = "SELECT * FROM pm_statuses ORDER BY id ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_status($status_id)
	{
		$sql = "SELECT * FROM pm_statuses WHERE id='$status_id'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}



	function add_project($project)
	{
		$project['id'] = $this->nextid("pm_projects");

		$project['ctime'] = $project['mtime'] = gmmktime();

		if ($project['id'] > 0 && $this->insert_row('pm_projects', $project))
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

		$project['mtime'] = gmmktime();
		return $this->update_row('pm_projects','id', $project);
	}

	function get_project($project_id)
	{
		$sql = "SELECT * FROM pm_projects WHERE id='$project_id'";
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
		$sql = "SELECT * FROM pm_projects WHERE name='$name'";
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

			$sql = "DELETE FROM pm_hours WHERE project_id='$project_id'";
			if ($this->query($sql))
			{
				$sql = "DELETE FROM pm_projects WHERE id='$project_id'";
				return $this->query($sql);
			}
		}
		return false;
	}
	
	
	function get_active_milestones($user_id, $sort, $dir)
	{
		$sql = "SELECT DISTINCT m.*, p.name AS project_name, p.customer FROM pm_milestones m ".
			"INNER JOIN pm_projects p ON m.project_id=p.id ".
 			"INNER JOIN go_acl a ON ".
			"(p.acl_read = a.acl_id OR ".
				"p.acl_write = a.acl_id OR ".
				"p.acl_book=a.acl_id) ".
			"LEFT JOIN go_users_groups ug ON (a.group_id = ug.group_id) WHERE ((".
 			"ug.user_id = ".$user_id.") OR (".
 			"a.user_id = ".$user_id.")) AND p.archived='0' ORDER BY $sort $dir";
		
		$this->query($sql);
		
		return $this->num_rows();
		
	}




	function get_authorized_projects($auth_type, $user_id, $sort='name', $direction='ASC', $start=0, $offset=0, $search_field='', $search_keyword='')
	{
		
		$sql = "SELECT DISTINCT pm_projects.* FROM pm_projects ".
 		"INNER JOIN go_acl ON ";
		
		switch($auth_type)
		{
			case 'read':
				$sql .= "(pm_projects.acl_read = go_acl.acl_id OR pm_projects.acl_write = go_acl.acl_id OR pm_projects.acl_book=go_acl.acl_id) ";	
				break;
			case 'book':
				$sql .= "(pm_projects.acl_write = go_acl.acl_id OR pm_projects.acl_book=go_acl.acl_id) ";
				break;
			case 'write':
				$sql .= "pm_projects.acl_write = go_acl.acl_id ";
				break;
		}
		
		
 		$sql .= "LEFT JOIN go_users_groups ON (go_acl.group_id = go_users_groups.group_id) WHERE ((".
 		"go_users_groups.user_id = ".$user_id.") OR (".
 		"go_acl.user_id = ".$user_id."))";


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

	function get_writable_projects($user_id, $sort, $direction, $start, $offset)
	{	
		$sql = "SELECT DISTINCT pm_projects.* ".
 		"FROM pm_projects ".
 		"INNER JOIN go_acl ON pm_projects.acl_write = go_acl.acl_id ".
 		"LEFT JOIN go_users_groups ON go_acl.group_id = go_users_groups.group_id ".
 		"WHERE go_acl.user_id=$user_id ".
 		"OR go_users_groups.user_id=$user_id";
		
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

	function get_user_projects($user_id)
	{
		$this->query("SELECT * FROM pm_projects WHERE user_id='$user_id'");
		return $this->num_rows();
	}



	function add_hours($hours)
	{
		$hours['id'] = $this->nextid("pm_hours");
		return $this->insert_row('pm_hours',$hours);
	}

	function update_hours($hours)
	{
		return $this->update_row('pm_hours', 'id', $hours);
	}


	function get_hours($hours_id)
	{
		$sql = "SELECT * FROM pm_hours WHERE id='$hours_id'";
		$this->query($sql);
		if($this->next_record(MYSQL_ASSOC))
		{
			return $this->Record;
		}
		return false;
	}

	function delete_hours($hours_id)
	{
		global $GO_MODULES, $GO_CONFIG;

		if ($hours_id > 0)
		{
			$sql = "DELETE FROM pm_hours WHERE id='$hours_id'";
			return $this->query($sql);
		}else
		{
			return false;
		}
	}


	function select_hours($start_time=0, $end_time=0, $user_id=0, $project_id=0, $start=0, $offset=0, $sort='date', $direction='DESC')
	{
		$sql = "SELECT h.*,p.name AS project_name, f.name AS fee_name FROM pm_hours h ".
			"INNER JOIN pm_projects p ON h.project_id=p.id ".
			"LEFT JOIN pm_fees f ON h.fee_id=f.id ";
		
		
		if ($start_time == 0 && $end_time == 0)
		{
			$where = false;
		}else
		{
			$sql .= "WHERE date >= '$start_time' AND date < '$end_time'";
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
			$sql .= " h.user_id='$user_id'";
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
			$sql .= " h.project_id='$project_id'";
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



	function get_fees()
	{
		$sql = "SELECT * FROM pm_fees";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_authorized_fees($user_id)
	{
		$sql = "SELECT DISTINCT pm_fees.* ".
 		"FROM pm_fees ".
 		"INNER JOIN go_acl ON pm_fees.acl_id = go_acl.acl_id ".
 		"LEFT JOIN go_users_groups ON go_acl.group_id = go_users_groups.group_id ".
 		"WHERE go_acl.user_id=$user_id OR go_users_groups.user_id=$user_id";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_fee($fee_id)
	{
		$sql = "SELECT * FROM pm_fees WHERE id='$fee_id'";
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->Record;
		}else
		{
			throw new DatabaseSelectException();
		}
	}

	function add_fee($fee)
	{
		$fee['id'] = $this->nextid("pm_fees");
		if ($fee['id']  > 0 && $this->insert_row('pm_fees', $fee))
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
			$GO_SECURITY->delete_acl($fee['acl_id']);			

			$sql = "DELETE FROM pm_fees WHERE id='$fee_id'";
			return $this->query($sql);
		}
		return false;
	}

	function update_fee($fee)
	{
		return $this->update_row('pm_fees','id', $fee);
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



	function __on_user_delete($user)
	{
		$projects = new projects();
		$this->get_user_projects($user['id']);
		while($this->next_record())
		{
			$projects->delete_project($this->f('id'));
		}
	}

	function __on_search($last_sync_time=0)
	{
		global $GO_MODULES, $GO_LANGUAGE;

		//require($GO_LANGUAGE->get_language_file('projects'));

		$pm_project = 'Project';

		$sql = "SELECT * FROM pm_projects WHERE mtime>$last_sync_time";

		$this->query($sql);
			
		$search = new search();

		$db = new db();
		while($this->next_record())
		{

			$project_name = ($this->f('description') == '') ? $this->f('name') : $this->f('name').' ('.$this->f('description').')';

			$cache['id']=$this->f('id');
			$cache['user_id']=$this->f('user_id');
			$cache['name'] = addslashes($project_name);
			$cache['link_type']=5;
			$cache['description']=addslashes($this->f('comments'));
			$cache['type']=$pm_project;
			$cache['keywords']=addslashes($search->record_to_keywords($this->Record)).','.$pm_project;
			$cache['mtime']=$this->f('mtime');
			$cache['acl_read']=$this->f('acl_read');
			$cache['acl_write']=$this->f('acl_write');

			//var_dump($cache);
			$search->cache_search_result($cache);

		}
	}
	
	
	
	
	
	
	
		/**
	 * Add a ms
	 *
	 * @param Array $ms Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_milestone($milestone)
	{
		$milestone['id']=$this->nextid('pm_milestones');
		if($this->insert_row('pm_milestones', $milestone))
		{
			return $milestone['id'];
		}
		return false;
	}

	/**
	 * Update a milestone
	 *
	 * @param Array $milestone Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_milestone($milestone)
	{
		return $this->update_row('pm_milestones', 'id', $milestone);
	}


	/**
	 * Delete a milestone
	 *
	 * @param Int $milestone_id ID of the milestone
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_milestone($milestone_id)
	{
		return $this->query("DELETE FROM pm_milestones WHERE id=$milestone_id");
	}


	/**
	 * Gets a milestone record
	 *
	 * @param Int $milestone_id ID of the milestone
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_milestone($milestone_id)
	{
		$this->query("SELECT * FROM pm_milestones WHERE id=$milestone_id");
		if($this->next_record(MYSQL_ASSOC))
		{
			return $this->Record;
		}else
		{
			throw new DatabaseSelectException();
		}
	}

	/**
	 * Gets a milestone record by the name field
	 *
	 * @param String $name Name of the milestone
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_milestone_by_name($name)
	{
		$this->query("SELECT * FROM pm_milestones WHERE name='$name'");
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}


	/**
	 * Gets all milestones
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_milestones($project_id, $start=0, $offset=0, $sortfield='id', $sortorder='ASC')
	{
		$sql = "SELECT m.*,p.name AS project_name FROM pm_milestones m ".
			"INNER JOIN pm_projects p ON p.id=m.project_id WHERE project_id=$project_id ORDER BY $sortfield $sortorder";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}
		return $count;
	}
	


}