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
class postfixadmin extends db {
	
	function get_installation_mailboxes($go_installation_id)
	{
		$sql = "SELECT * FROM pa_mailboxes WHERE go_installation_id='".$this->escape($go_installation_id)."'";
		$this->query($sql);
		return $this->num_rows();
	}
	/**
	 * Add a Alias
	 *
	 * @param Array $alias Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_alias($alias)
	{
		
		$alias['ctime']=$alias['mtime']=time();
		
		
		$alias['id']=$this->nextid('pa_aliases');
		if($this->insert_row('pa_aliases', $alias))
		{
			return $alias['id'];
		}
		return false;
	}
	/**
	 * Update a Alias
	 *
	 * @param Array $alias Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_alias($alias)
	{
		
		$alias['mtime']=time();
		
		return $this->update_row('pa_aliases', 'id', $alias);
	}

	/**
	 * Delete a Alias
	 *
	 * @param Int $alias_id ID of the alias
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_alias($alias_id)
	{
				
		
		return $this->query("DELETE FROM pa_aliases WHERE id=".intval($alias_id));
	}

	/**
	 * Gets a Alias record
	 *
	 * @param Int $alias_id ID of the alias
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_alias($alias_id)
	{
		$this->query("SELECT * FROM pa_aliases WHERE id=".intval($alias_id));
		if($this->next_record())
		{
			return $this->record;
		}else
		{
			throw new DatabaseSelectException();
		}
	}
	
/**
	 * Gets a Alias record
	 *
	 * @param Int $alias_id ID of the alias
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_alias_by_address($address)
	{
		$this->query("SELECT * FROM pa_aliases WHERE address='".$this->escape($address)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}
	
	/**
	 * Gets a Alias record by the name field
	 *
	 * @param String $name Name of the alias
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_alias_by_name($name)
	{
		$this->query("SELECT * FROM pa_aliases WHERE name='".$this->escape($name)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets all Aliases
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_aliases($domain_id, $query='', $sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT * FROM pa_aliases WHERE domain_id=".intval($domain_id);
		
		if(!empty($query))
 		{
 			$sql .= "AND name LIKE '".$this->escape($query)."' ";
 		} 		
		
		$sql .= " ORDER BY ".$this->escape($sortfield." ".$sortorder);
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			$this->query($sql);
		}
		return $count;
	}
	
	
	
		
	/**
	 * Add a Domain
	 *
	 * @param Array $domain Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_domain($domain)
	{
		
		$domain['ctime']=$domain['mtime']=time();
		
		
		$domain['id']=$this->nextid('pa_domains');
		if($this->insert_row('pa_domains', $domain))
		{
			return $domain['id'];
		}
		return false;
	}
	/**
	 * Update a Domain
	 *
	 * @param Array $domain Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_domain($domain)
	{
		
		$domain['mtime']=time();
		
		return $this->update_row('pa_domains', 'id', $domain);
	}

	/**
	 * Delete a Domain
	 *
	 * @param Int $domain_id ID of the domain
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_domain($domain_id)
	{

		$this->query("DELETE FROM pa_mailboxes WHERE domain_id=".intval($domain_id));
		$this->query("DELETE FROM pa_aliases WHERE domain_id=".intval($domain_id));
		
		return $this->query("DELETE FROM pa_domains WHERE id=".intval($domain_id));
	}

	/**
	 * Gets a Domain record
	 *
	 * @param Int $domain_id ID of the domain
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_domain($domain_id)
	{
		$this->query("SELECT * FROM pa_domains WHERE id=".intval($domain_id));
		if($this->next_record())
		{
			return $this->record;
		}else
		{
			throw new DatabaseSelectException();
		}
	}
	/**
	 * Gets a Domain record by the name field
	 *
	 * @param String $name Name of the domain
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_domain_by_domain($domain)
	{
		$this->query("SELECT * FROM pa_domains WHERE domain='".$this->escape($domain)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets all Domains
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_domains($query='', $sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT * FROM pa_domains ";
		
		if(!empty($query))
 		{
 			$sql .= " WHERE name LIKE '".$this->escape($query)."'";
 		} 		
		
		$sql .= " ORDER BY `".$this->escape($sortfield)."` ".$this->escape($sortorder);
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			$this->query($sql);
		}
		return $count;
	}
	
	function get_domain_info($domain_id)
	{
		$sql ="SELECT COUNT(*) AS count, SUM(`usage`) AS `usage` FROM pa_mailboxes WHERE domain_id=".intval($domain_id);
		$this->query($sql);
		$this->next_record();
		
		$domain['mailbox_count']=intval($this->f('count'));
		$domain['usage']=intval($this->f('usage'));
		
		$sql ="SELECT COUNT(*) AS count FROM pa_aliases WHERE domain_id=".intval($domain_id);
		$this->query($sql);
		$this->next_record();
		
		$domain['alias_count']=intval($this->f('count'));
		
		return $domain;
	}
	
	
	/**
	 * Gets all Domains where the user has access for
	 *
	 * @param String $auth_type Can be 'read' or 'write' to fetch readable or writable Domains
	 * @param Int $user_id First record of the total record set to return
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */

	function get_authorized_domains($auth_type, $user_id, $query, $sort='name', $direction='ASC', $start=0, $offset=0, $no_backup_mx_domains=false)
	{
		$user_id=intval($user_id);
		
		$sql = "SELECT DISTINCT pa_domains.* FROM pa_domains ".
 		"INNER JOIN go_acl a ON ";
		
		switch($auth_type)
		{
			case 'read':
				$sql .= "pa_domains.acl_id = a.acl_id ";
				break;
				
			case 'write':
				$sql .= "(pa_domains.acl_id = a.acl_id AND a.level > ".GO_SECURITY::READ_PERMISSION.") ";
				break;
		}
		
		
 		$sql .= "LEFT JOIN go_users_groups ug ON (a.group_id = ug.group_id) WHERE ((".
 		"ug.user_id = ".$user_id.") OR (a.user_id = ".$user_id.")) ";
 		
 		if(!empty($query))
 		{
 			$sql .= " AND domain LIKE '".$this->escape($query)."'";
 		}

		if($no_backup_mx_domains){
			$sql .= " AND backupmx='0'";
		}

		$sql .= " ORDER BY `".$this->escape($sort)."` ".$this->escape($direction);
		
		$this->query($sql);
		$count = $this->num_rows();
		if ($offset > 0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			$this->query($sql);
			return $count;
		}else
		{
			return $count;
		}
	}
	
	
		
	/**
	 * Add a FetchmailConfig
	 *
	 * @param Array $fetchmail_config Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_fetchmail_config($fetchmail_config)
	{		
		$fetchmail_config['id']=$this->nextid('pa_fetchmail_configs');
		if($this->insert_row('pa_fetchmail_configs', $fetchmail_config))
		{
			return $fetchmail_config['id'];
		}
		return false;
	}
	/**
	 * Update a FetchmailConfig
	 *
	 * @param Array $fetchmail_config Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_fetchmail_config($fetchmail_config)
	{		
		return $this->update_row('pa_fetchmail_configs', 'id', $fetchmail_config);
	}

	/**
	 * Delete a FetchmailConfig
	 *
	 * @param Int $fetchmail_config_id ID of the fetchmail_config
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_fetchmail_config($fetchmail_config_id)
	{		
		return $this->query("DELETE FROM pa_fetchmail_configs WHERE id=".intval($fetchmail_config_id));
	}

	/**
	 * Gets a FetchmailConfig record
	 *
	 * @param Int $fetchmail_config_id ID of the fetchmail_config
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_fetchmail_config($fetchmail_config_id)
	{
		$this->query("SELECT * FROM pa_fetchmail_configs WHERE id=".intval($fetchmail_config_id));
		if($this->next_record())
		{
			return $this->record;
		}else
		{
			throw new DatabaseSelectException();
		}
	}
	/**
	 * Gets a FetchmailConfig record by the name field
	 *
	 * @param String $name Name of the fetchmail_config
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_fetchmail_config_by_name($name)
	{
		$this->query("SELECT * FROM pa_fetchmail_configs WHERE name='".$this->escape($name)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * Gets all FetchmailConfigs
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_fetchmail_configs($query, $sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT * FROM pa_fetchmail_configs ";
		
		if(!empty($query))
 		{
 			$sql .= " WHERE name LIKE '".$this->escape($query)."'";
 		} 		
		
		$sql .= " ORDER BY ".$this->escape($sortfield." ".$sortorder);
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			$this->query($sql);
		}
		return $count;
	}
	
	
	
		
	/**
	 * Add a Mailbox
	 *
	 * @param Array $mailbox Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_mailbox($mailbox)
	{
		
		$mailbox['ctime']=$mailbox['mtime']=time();
		
		
		$mailbox['id']=$this->nextid('pa_mailboxes');
		if($this->insert_row('pa_mailboxes', $mailbox))
		{
			return $mailbox['id'];
		}
		return false;
	}
	/**
	 * Update a Mailbox
	 *
	 * @param Array $mailbox Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_mailbox($mailbox)
	{
		
		$mailbox['mtime']=time();
		
		return $this->update_row('pa_mailboxes', 'id', $mailbox);
	}

	/**
	 * Delete a Mailbox
	 *
	 * @param Int $mailbox_id ID of the mailbox
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_mailbox($mailbox_id)
	{		
		$mailbox = $this->get_mailbox($mailbox_id);
		
		$deleted_user = $this->escape($mailbox['username']);
		
		if(!empty($deleted_user))
		{
			$sql = "UPDATE pa_aliases SET goto=replace(goto, '$deleted_user,','');";
			$this->query($sql);
			$sql = "UPDATE pa_aliases SET goto=replace(goto, ',$deleted_user','');";
			$this->query($sql);
			$sql = "DELETE FROM pa_aliases WHERE goto='' OR goto='$deleted_user'";
			$this->query($sql);
		}
		
		return $this->query("DELETE FROM pa_mailboxes WHERE id=".intval($mailbox_id));
	}

	/**
	 * Gets a Mailbox record
	 *
	 * @param Int $mailbox_id ID of the mailbox
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_mailbox($mailbox_id)
	{
		$this->query("SELECT * FROM pa_mailboxes WHERE id=".intval($mailbox_id));
		if($this->next_record())
		{
			return $this->record;
		}else
		{
			throw new DatabaseSelectException();
		}
	}
	/**
	 * Gets a Mailbox record by the name field
	 *
	 * @param String $name Name of the mailbox
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_mailbox_by_username($username)
	{
		$this->query("SELECT * FROM pa_mailboxes WHERE username='".$this->escape($username)."'");
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}
	
	function sum_quota($domain_id)
	{
		$sql = "SELECT SUM(quota) AS totalquota FROM pa_mailboxes WHERE domain_id=?";

		$this->query($sql,'i', $domain_id);
		$r = $this->next_record();
		return $r['totalquota'];
	}

	/**
	 * Gets all Mailboxes
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_mailboxes($domain_id=0, $query='', $sortfield='id', $sortorder='ASC', $start=0, $offset=0)
	{
		$sql = "SELECT * FROM pa_mailboxes ";
		
		$where=false;
		if($domain_id>0)
		{
			$where=true;
			$sql .= "WHERE domain_id=".intval($domain_id);
		}
		
		if(!empty($query))
 		{
 			if($where)
 			{
 				$sql .= "AND ";
 			}else
 			{
 				$sql .= "WHERE ";
 			}
 			$sql .= "name LIKE '".$this->escape($query)."' ";
 		} 		
		
		$sql .= " ORDER BY `".$this->escape($sortfield)."` ".$this->escape($sortorder);
		$this->query($sql);
		$count = $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			$this->query($sql);
		}
		return $count;
	}	
	
	function remove_notifications($email)
	{
		$sql = "DELETE FROM pa_vacation_notification WHERE on_vacation='".$this->escape($email)."'";
		
		return $this->query($sql);
	}	
	
}