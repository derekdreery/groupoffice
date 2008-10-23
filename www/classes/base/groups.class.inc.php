<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * This class is used to manage user groups
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id$
 * @copyright Copyright Intermesh
 * @package go.basic
 * @since Group-Office 2.05
 * 
 * @uses base_groups
 */


class GO_GROUPS extends db
{
	/**
	 * Constructor. Calls parent class base_groups constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct()
	{
		$this->db();
	}

	function groupnames_to_ids($groupnames)
	{

		$groupids = array();
		foreach($groupnames as $groupname)
		{
			if($group = $this->get_group_by_name($groupname))
			{
				$groupids[]=$group['id'];
			}
		}
		return $groupids;
	}

	/**
	 * Delete's a group
	 *
	 * @param	int			$group_id	The group ID to delete
	 * @access public
	 * @return bool		True on success
	 */
	function delete_group($group_id)
	{
		if($this->clear_group($group_id))
		{
			global $GO_SECURITY;
			if($GO_SECURITY->delete_group($group_id))
			{
				return $this->query("DELETE FROM go_groups WHERE id='".$this->escape($group_id)."'");
			}
		}
		return false;
	}

	/**
	 * Removes all go_users from a group
	 *
	 * @param	int			$group_id	The group ID to reset
	 * @access public
	 * @return bool		True on success
	 */
	function clear_group($group_id)
	{
		return $this->query("DELETE FROM go_users_groups WHERE group_id='".$this->escape($group_id)."'");
	}

	/**
	 * Add's a user to a group
	 *
	 * @param	int			$user_id	The user ID to add
	 * @param	int			$group_id	The group ID to add the user to
	 * @access public
	 * @return bool		True on success
	 */
	function add_user_to_group($user_id, $group_id)
	{
		if ( $user_id )
		{
			return $this->query("INSERT INTO go_users_groups (user_id,group_id)".
	 			 " VALUES ($user_id, $group_id)");
		}
		return false;
	}

	/**
	 * Delete's a user to a group
	 *
	 * @param	int			$user_id	The user ID to delete
	 * @param	int			$group_id	The group ID to remove the user from
	 * @access public
	 * @return bool		True on success
	 */

	function delete_user_from_group($user_id, $group_id)
	{
		return $this->query("DELETE FROM go_users_groups WHERE".
			" user_id='".$this->escape($user_id)."' AND group_id='".$this->escape($group_id)."'");
	}

	/**
	 * Get a group's properties in an array
	 *
	 * @param	int			$group_id	The group ID to query
	 * @access public
	 * @return mixed		Array with properties or false
	 */
	function get_group($group_id)
	{
		$this->query("SELECT * FROM go_groups WHERE id='".$this->escape($group_id)."'");

		if($this->next_record())
		return $this->Record;
		else
		return false;
	}

	/**
	 * Set the name of a group
	 *
	 * @param	string	$name			The new name of the group
	 * @param	int			$group_id	The group ID to query
	 * @access public
	 * @return mixed		Array with properties or false
	 */
	function update_group($group_id, $name)
	{
		return $this->query("UPDATE go_groups SET name='".$this->escape($name)."' WHERE id='".$this->escape($group_id)."'");
	}

	/**
	 * Get a group's properties in an array
	 *
	 * @param	int			$group_id	The group ID to query
	 * @access public
	 * @return mixed		Array with properties or false
	 */
	function get_group_by_name($name)
	{
		$this->query("SELECT * FROM go_groups WHERE name='".$this->escape($name)."'");
		if ($this->next_record())
		{
			return $this->Record;
		}else
		{
			return false;
		}
	}

	/**
	 * Add's a group
	 *
	 * @param	int			$user_id	The owner user ID
	 * @param	string	$name			The name of the new group
	 * @access public
	 * @return int			The new group ID or false;
	 */
	function add_group($user_id, $name)
	{
		$group['id'] = $this->nextid("go_groups");		
		$group['user_id']=$user_id;
		$group['name']=$name;
		
		$this->insert_row('go_groups', $group);
		return $group['id'];		
	}

	/**
	 * Check's if a user owns a group
	 *
	 * @param	int			$user_id	The user ID
	 * @param	int			$group_id	The group ID
	 * @access public
	 * @return bool

	 function user_owns_group($user_id, $group_id)
	 {
	 $this->query("SELECT user_id FROM go_groups WHERE user_id='$user_id' AND".
	 " id='$group_id'");
	 if ($this->num_rows() > 0)
	 {
	 return true;
	 }else
	 {
	 return false;
	 }
	 }*/

	/**
	 * Check's if a user is a member of a group
	 *
	 * @param	int			$user_id	The user ID
	 * @param	int			$group_id	The group ID
	 * @access public
	 * @return bool
	 */
	function is_in_group($user_id, $group_id)
	{
		$sql = "SELECT user_id FROM go_users_groups WHERE".
      " user_id='".$this->escape($user_id)."' AND group_id='".$this->escape($group_id)."'";
		$this->query($sql);

		if ($this->num_rows() > 0)
		return true;
		else
		return false;
	}

	/**
	 * Get's all members of a group
	 *
	 * @param	int			$group_id	The group
	 * @param	string	$sort			The field to sort on
	 * @param	string	$direction	The sort direction (ASC/DESC)
	 * @access public
	 * @return int			Number of go_users in the group
	 */

	//$query, $field, $user_id=0, $start=0, $offset=0, $sort="name", $sort_direction='ASC'
	function get_users_in_group($group_id, $start = 0, $offset = 0, $sort="name", $direction = "ASC")
	{
		if ($sort == 'name' || $sort == 'go_users.name')
		{
			if(!isset($_SESSION['GO_SESSION']['sort_name']) ||  $_SESSION['GO_SESSION']['sort_name'] == 'first_name')
			{
				$sort = 'first_name '.$direction.', last_name';
			}else
			{
				$sort = 'last_name '.$direction.', first_name';
			}
		}

		$sql = "SELECT go_users.id, go_users.email, go_users.first_name, go_users.middle_name , go_users.last_name FROM".
			" go_users LEFT JOIN go_users_groups ON (go_users.id = go_users_groups.user_id)".
			" WHERE go_users_groups.group_id='".$this->escape($group_id)."' ORDER BY ".		
		$sort." ".$direction;

		$this->query($sql);
		$count = $this->num_rows();

		if ($offset != 0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
			
		return $count;
	}

	/**
	 * Check's if a user is allowed to view the group.
	 *	The user must be owner of member to see it.
	 *
	 * @param	int			$user_id	The user ID
	 * @param	int			$group_id	The group ID
	 * @access public
	 * @return bool

	 function group_is_visible($user_id, $group_id)
	 {
	 if ($this->user_owns_group($user_id, $group_id)
	 || $this->is_in_group($user_id, $group_id))
	 return true;
	 else
	 return false;
	 }*/

	/**
	 * Get's all go_groups. If a user ID is specified it returns only the go_groups
	 *	that user is a member of.
	 *
	 * @access public
	 * @return int	Number of go_groups
	 */
	function get_groups($user_id=0, $start = 0, $offset = 0, $sort="name", $direction = "ASC")
	{
		$sql = "SELECT go_groups.*,go_users.username, go_users.first_name, go_users.middle_name, go_users.last_name FROM go_groups ".
  	"INNER JOIN go_users ON go_groups.user_id=go_users.id ";

		if($user_id > 0)
		{
			$sql .= "INNER JOIN go_users_groups ON go_groups.id=go_users_groups.group_id ".
							"AND go_users_groups.user_id='".$this->escape($user_id)."' ";
		}

		$sql .= 'ORDER BY '.$sort.' '.$direction;
		$this->query($sql);

		$this->query($sql);
		$count = $this->num_rows();

		if ($offset != 0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}

		return $this->num_rows();
	}

	/**
	 * Get's all authorised go_groups for a user. User's can only see go_groups if they
	 *	are owner or member of the group
	 *
	 * @param	int	$user_id
	 * @access public
	 * @return int	Number of go_groups

	 function get_authorised_groups($user_id)
	 {
	 $sql = "SELECT go_groups.* FROM go_groups, go_users_groups".
	 " WHERE ((groups.user_id='$user_id')".
	 " OR (go_users_groups.user_id='$user_id'".
	 " AND go_users_groups.group_id=groups.id))".
	 " GROUP BY go_groups.id ORDER BY go_groups.id ASC";
	 $this->query($sql);
	 return $this->num_rows();
	 } */

	/**
	 * Search for a visible user for another user.
	 *
	 * @param	string	$query	The keyword to search on
	 *	@param	string	$field	The database field to search on
	 * @param	int			$user_id	The user_id to search for (Permissions)
	 * @param	int			$start	The first record to return
	 * @param	int			$offset	The number of records to return
	 * @access public
	 * @return int			The number of records returned
	 */
	function search($query, $field, $user_id, $start=0, $offset=0)
	{
		$sql = "SELECT go_users.* FROM go_users, go_users_groups INNER ".
			"JOIN go_acl ON go_users.go_acl_id= go_acl.go_acl_id WHERE ".
			"((go_acl.group_id = go_users_groups.group_id ".
			"AND go_users_groups.user_id = ".$this->escape($user_id).") OR (".
			"go_acl.user_id = ".$this->escape($user_id)." )) AND $field ".
			"LIKE '".$this->escape($query)."' ".
			"GROUP BY go_users.id ORDER BY name ASC";

		if ($offset != 0)	$sql .= " LIMIT ".$this->escape($start.",".$offset);

		$this->query($sql);
		return $this->num_rows();
	}

	/**
	 * Called when a user is deleted
	 *
	 * @param	int			$user_id	The user ID that is about to be deleted
	 * @access private
	 * @return bool		True on success
	 */

	function __on_user_delete($user)
	{					
		$sql = "DELETE FROM go_users_groups WHERE user_id='".$this->escape($user['id'])."'";
		$this->query($sql);
		$sql = "SELECT id FROM go_groups WHERE user_id='".$this->escape($user['id'])."'";
		$this->query($sql);
		$del = new GO_GROUPS();
		while ($this->next_record())
		{
			$del->delete_group($this->f("id"));
		}
	}
}
