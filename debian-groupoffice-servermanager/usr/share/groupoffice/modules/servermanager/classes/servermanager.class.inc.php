<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: class.tpl 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
class servermanager extends db {
	public function __on_load_listeners($events){
			$events->add_listener('update_user', __FILE__, 'servermanager', 'update_user');
	}
	
	function update_user($user)
	{		
		if($user['id']==1 && !empty($user['password']))
		{
			global $GO_CONFIG, $GO_MODULES;
			 
			$cmd='sudo '.$GO_MODULES->modules['servermanager']['path'].'sudo.php '.
				$GO_CONFIG->get_config_file().' change_admin_password "'.$user['password'].'"';

			system($cmd);
		}
	}
	
	function server_users_available($installation_id=0)
	{
		$this->get_installations();
		
		$current_server_users=0;
		
		while($this->next_record())
		{
			if($this->f('id') != $installation_id)
			{
				$config_file = '/etc/groupoffice/'.$this->f('name').'/config.php';
				if(file_exists($config_file))
				{
					require($config_file);
					//0 is not allowed
					if(intval($config['max_users'])<1)
					{
						return -1;
					}
	
					$current_server_users += $config['max_users'];
				}			
			}
		}
		
		require('/etc/groupoffice/servermanager/license.inc.php');
		
		if($secret!='ditishetgeheimewachtwoordvoornetgebruik')
		{
			return -1;
		}		
		return $max_server_users-$current_server_users;
	}
	
/**
	 * Add a New trial
	 *
	 * @param Array $new_trial Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_new_trial($new_trial)
	{
		
		$new_trial['ctime']=time();
		
		$time = Date::date_add(time(), -2);
		$sql = "DELETE FROM sm_new_trials WHERE ctime<$time";
		$this->query($sql);		
		
		if($this->insert_row('sm_new_trials', $new_trial))
		{
			return true;
		}
		return false;
	}
	/**
	 * Update a New trial
	 *
	 * @param Array $new_trial Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_new_trial($new_trial)
	{
		return $this->update_row('sm_new_trials', 'id', $new_trial);
	}

	/**
	 * Delete a New trial
	 *
	 * @param Int $new_trial_id ID of the new_trial
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_new_trial($name)
	{
		return $this->query("DELETE FROM sm_new_trials WHERE name='".$this->escape($name)."'");
	}


	
	function get_new_trial_by_name($name)
	{
		$this->query("SELECT * FROM sm_new_trials WHERE name='".$this->escape($name)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;		
	}
	
	function get_new_trial_by_key($key)
	{
		$this->query("SELECT * FROM sm_new_trials WHERE `key`='".$this->escape($key)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;		
	}
	
	
	function write_config($file, $values)
	{
		
		require($file);
		
		if(!isset($config))
			$config=array();
		
		foreach($values as $key=>$value)
		{
			$config[$key]=$value;
		}
		
		$config_data = "<?php\n";
		foreach($config as $key=>$value)
		{
			if($value===true)
			{
					$config_data .= '$config[\''.$key.'\']=true;'."\n";
			}elseif($value===false)
			{
				$config_data .= '$config[\''.$key.'\']=false;'."\n";
			}else
			{
				$config_data .= '$config[\''.$key.'\']="'.str_replace('"','\\"', $value).'";'."\n";							
			}				
		}			
		return file_put_contents($file, $config_data);		
	}
	
	/**
	 * Add a Report
	 *
	 * @param Array $report Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_report($report)
	{
		if($this->insert_row('sm_reports', $report))
		{
			return true;
		}
		return false;
	}
	/**
	 * Update a Report
	 *
	 * @param Array $report Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_report($report)
	{
		return $this->update_row('sm_reports', 'name', $report);
	}

	/**
	 * Delete a Report
	 *
	 * @param Int $report_id ID of the report
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_report($name)
	{
		return $this->query("DELETE FROM sm_reports WHERE name='".$this->escape($name)."'");
	}

	/**
	 * Gets a Report record
	 *
	 * @param Int $report_id ID of the report
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_report($name)
	{
		$this->query("SELECT * FROM sm_reports WHERE name='".$this->escape($name)."'");
		if($this->next_record())
		{
			return $this->record;
		}else
		{
			return false;
		}
	}



	/**
	 * Gets all Reports
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_reports($query, $sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT sm_reports.*, (file_storage_usage+mailbox_usage+database_usage) AS total_usage FROM sm_reports ";
		
		if(!empty($query))
 		{
 			$sql .= " WHERE name LIKE '".$this->escape($query)."'";
 		} 		
		
		$sql .= " ORDER BY ".$this->escape($sortfield." ".$sortorder);
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}
		
	/**
	 * Add a Installation
	 *
	 * @param Array $installation Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_installation($installation)
	{		
		$installation['ctime']=$installation['mtime']=time();
				
		$installation['id']=$this->nextid('sm_installations');
		if($this->insert_row('sm_installations', $installation))
		{
			return $installation['id'];
		}
		return false;
	}
	/**
	 * Update a Installation
	 *
	 * @param Array $installation Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_installation($installation)
	{		
		$installation['mtime']=time();
		
		return $this->update_row('sm_installations', 'id', $installation);
	}

	/**
	 * Delete a Installation
	 *
	 * @param Int $installation_id ID of the installation
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_installation($installation_id)
	{		
		global $GO_CONFIG;		
		
		return $this->query("DELETE FROM sm_installations WHERE id=".$this->escape($installation_id));
	}

	/**
	 * Gets a Installation record
	 *
	 * @param Int $installation_id ID of the installation
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_installation($installation_id)
	{
		$this->query("SELECT * FROM sm_installations WHERE id=".$this->escape($installation_id));
		if($this->next_record())
		{
			return $this->record;
		}else
		{
			throw new DatabaseSelectException();
		}
	}
	/**
	 * Gets a Installation record by the name field
	 *
	 * @param String $name Name of the installation
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_installation_by_name($name)
	{
		$this->query("SELECT * FROM sm_installations WHERE name='".$this->escape($name)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets all Installations
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_installations($query='', $sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT * FROM sm_installations ";
		
		if(!empty($query))
 		{
 			$sql .= " WHERE name LIKE '".$this->escape($query)."'";
 		} 		
		
		$sql .= " ORDER BY ".$this->escape($sortfield." ".$sortorder);
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}
	
	
	
	/* {CLASSFUNCTIONS} */
	
	
	/**
	 * When a an item gets deleted in a panel with links. Group-Office attempts
	 * to delete the item by finding the associated module class and this function
	 *
	 * @param int $id The id of the linked item
	 * @param int $link_type The link type of the item. See /classes/base/links.class.inc
	 */
	
	function __on_delete_link($id, $link_type)
	{		
		
		if($link_type==13)
		{
			$this->delete_installation($id);
		}
		
		/* {ON_DELETE_LINK_FUNCTION} */	
	}

	
	/**
	 * When a global search action is performed this function will be called for each module
	 *
	 * @param int $last_sync_time The time this function was called last
	 
	
	public function __on_search($last_sync_time=0)
	{
		global $GO_MODULES, $GO_LANGUAGE;
		
		require($GO_LANGUAGE->get_language_file('servermanager'));
		
		$sql = "SELECT * FROM sm_installations WHERE mtime>".$this->escape($last_sync_time);
		
		
		$this->query($sql);
		$search = new search();
		while($this->next_record())
		{
			$cache['id']=$this->f('id');
			$cache['user_id']=$this->f('user_id');
			$cache['module']='servermanager';
			$cache['name'] = htmlspecialchars($this->f('name'), ENT_QUOTES, 'utf-8');
			$cache['link_type']=13;
			$cache['description']='';			
			$cache['type']=$lang['servermanager']['installation'];
			$cache['keywords']=$search->record_to_keywords($this->record).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			$cache['acl_read']=$this->f('acl_read');
 			$cache['acl_write']=$this->f('acl_write');	
			$search->cache_search_result($cache);
		}
		
		
	}*/
	
}