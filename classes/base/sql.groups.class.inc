<?php
/**
* @copyright Intermesh 2003
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.15 $ $Date: 2006/07/12 13:01:27 $
*
*  This program is free software; you can redistribute it and/or modify it
*  under the terms of the GNU General Public License as published by the
*  Free Software Foundation; either version 2 of the License, or (at your
*  option) any later version.
*/

require_once ($GO_CONFIG->class_path.'base/base.groups.class.inc');

/**
* This is the SQL group management class.
*
* @package  Framework
* @author   Merijn Schering <mschering@intermesh.nl>
* @since    Group-Office 2.05
*/

class sql_groups extends base_groups
{
	/**
	* Constructor. Calls parent class base_groups constructor
	*
	* @access public
	* @return void
	*/
  function sql_groups()
  {
    $this->base_groups();    
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
 		  	return $this->query("DELETE FROM groups WHERE id='$group_id'");
 		  }
  	}
  	return false;
  }
  
  /**
	* Removes all users from a group
	*
	* @param	int			$group_id	The group ID to reset
	* @access public
	* @return bool		True on success
	*/
  function clear_group($group_id)
  {
    return $this->query("DELETE FROM users_groups WHERE group_id='$group_id'");
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
      return $this->query("INSERT INTO users_groups (user_id,group_id)".
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
    return $this->query("DELETE FROM users_groups WHERE".
			" user_id='$user_id' AND group_id='$group_id'");
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
    $this->query("SELECT * FROM groups WHERE id='$group_id'");

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
    return $this->query("UPDATE groups SET name='$name' WHERE id='$group_id'");
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
     $this->query("SELECT * FROM groups WHERE name='$name'");
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
    $group_id = $this->nextid("groups");
    if ($group_id > 0)
    {
      $this->query("INSERT INTO groups (id, user_id, name) VALUES".
	  		" ('$group_id','$user_id','$name')");
      return $group_id;
    }else
    {
      return false;
    }
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
    $this->query("SELECT user_id FROM groups WHERE user_id='$user_id' AND".
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
	  $sql = "SELECT user_id FROM users_groups WHERE".
      " user_id='$user_id' AND group_id='$group_id'";
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
	* @return int			Number of users in the group
	*/
  function get_users_in_group($group_id, $sort="name", $direction="ASC")
  {
		if ($sort == 'name' || $sort == 'users.name')
	  {
			$sort = 'users.first_name '.$direction.', users.last_name';
	  }
	  $sql = "SELECT users.id, users.first_name, users.middle_name , users.last_name FROM".
			" users LEFT JOIN users_groups ON (users.id = users_groups.user_id)".
			" WHERE users_groups.group_id='$group_id' ORDER BY ".
			$sort." ".$direction;
	
	  $this->query($sql);
	  return $this->num_rows();
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
	* Get's all groups. If a user ID is specified it returns only the groups
	*	that user is a member of.
	*
	* @access public
	* @return int	Number of groups
	*/
  function get_groups($user_id=0)
  {
  	$sql = "SELECT groups.*,users.username FROM groups ".
  	"INNER JOIN users ON groups.user_id=users.id ";
					 
		if($user_id > 0)
		{
			$sql .= "INNER JOIN users_groups ON groups.id=users_groups.group_id ".
							"AND users_groups.user_id='$user_id' ";
		}					

	$sql .= "ORDER BY groups.id ASC";
    $this->query($sql);
	return $this->num_rows();
  }
  
  /**
	* Get's all authorised groups for a user. User's can only see groups if they
	*	are owner or member of the group 
	*
	* @param	int	$user_id
	* @access public
	* @return int	Number of groups
	
  function get_authorised_groups($user_id)
  {
 		$sql = "SELECT groups.* FROM groups, users_groups".
			" WHERE ((groups.user_id='$user_id')".
			" OR (users_groups.user_id='$user_id'".
			" AND users_groups.group_id=groups.id))".
			" GROUP BY groups.id ORDER BY groups.id ASC";
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
    $sql = "SELECT users.* FROM users, users_groups INNER ".
			"JOIN acl ON users.acl_id= acl.acl_id WHERE ".
			"((acl.group_id = users_groups.group_id ".
			"AND users_groups.user_id = ".$user_id.") OR (".
			"acl.user_id = ".$user_id." )) AND $field ".
			"LIKE '$query' ".
			"GROUP BY users.id ORDER BY name ASC";

    if ($offset != 0)	$sql .= " LIMIT $start, $offset";

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
	
  function delete_user($user_id)
  {
  	global $go_groups_class;
  	
    $sql = "DELETE FROM users_groups WHERE user_id='$user_id'";
    $this->query($sql);
    $sql = "SELECT id FROM groups WHERE user_id='$user_id'";
    $this->query($sql);
    $del = new $go_groups_class();
    while ($this->next_record())
    {
      $del->delete_group($this->f("id"));
    }
  }
}
