<?php
/**
* @copyright Intermesh 2003
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.46 $ $Date: 2006/11/22 15:09:46 $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or (at your
* option) any later version.
*
*  
* Do not modify this class unless you're bugfixing or inventing some
* truely great new stuff :). Instead extend this class with new
* authentication ans security management stuff.
*/

/**
* This is the base class of the user group management files.
* Don't use this class directly it should be extended by a security manager.
* For example sql.security.class.inc.
*
* @package Framework
* @author   Merijn Schering <mschering@intermesh.nl>
* @since    Group-Office 2.05
* @access private
*/

class base_security extends db
{
	/**
	* The user_id of the current logged in user
	*
	* @var     int
	* @access  public
	*/
	var $user_id = 0;
	
	/**
	* True if admin user
	*
	* @var     int
	* @access  private
	*/
	var $is_admin;

	/**
	* Constructor. Initialised database connection and user ID
	*
	* @access public
	* @return void
	*/
	function base_security()
	{
		global $GO_CONFIG;
		$this->db();
		
		if(isset($_SESSION['GO_SESSION']['user_id']) && $_SESSION['GO_SESSION']['user_id']>0)
		{
			$this->user_id=$_SESSION['GO_SESSION']['user_id'];
		}
	}

	/**
	* Set's a user as logged in. This does NOT log a user in. $GO_AUTH->login()
	* does that.
	*
	* @param	int	$user_id	The ID of the logged in user.
	* @access public
	* @return void
	*/
	function logged_in( $user_id=null ) {
		global $GO_USERS;
		
		if(isset($user_id))
		{
			$GO_USERS->update_session( $user_id );
			$this->user_id = $user_id;
		}else
		{
			return ($this->user_id > 0);
		}
	}

	/**
	* Log the current user out.
	*
	* @access public
	* @return void
	*/
	function logout()
	{
		$username = isset($_SESSION['GO_SESSION']['username']) ? $_SESSION['GO_SESSION']['username'] : 'notloggedin';
		go_log(LOG_DEBUG, 'LOGOUT Username: '.$username.'; IP: '.$_SERVER['REMOTE_ADDR']);
		
		SetCookie("GO_UN","",time()-3600,"/","",0);
		SetCookie("GO_PW","",time()-3600,"/","",0);
		unset($_SESSION);
		unset($_COOKIE);
		session_destroy();
		$this->user_id = 0;
	}

	/**
	* Checks if a user is logged in. if not it attempts to log in
  * based on stored cookies. On failure it redirects the user to the login page.
	*
	* @param	bool	$admin	Check for administrator privileges too.
	* @access public
	* @return void
	*/
	function authenticate($admin = false, $load_frames=true)
	{
		global $GO_CONFIG, $GO_AUTH, $auth_sources;

		$GO_AUTH_SOURCE_KEY = 
			isset($_COOKIE['GO_AUTH_SOURCE_KEY']) ? 
			$_COOKIE['GO_AUTH_SOURCE_KEY'] : 0;

		if (!$this->logged_in())
		{
			$params = isset( $auth_sources[$GO_AUTH_SOURCE_KEY]) ?  
				$auth_sources[$GO_AUTH_SOURCE_KEY] : false;			
			
			if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
			{
				$username = $_SERVER['PHP_AUTH_USER'];
				$password = $_SERVER['PHP_AUTH_PW'];
			}elseif(isset($_COOKIE['GO_UN']) && isset($_COOKIE['GO_PW']) &&
					$_COOKIE['GO_UN'] !='' && $_COOKIE['GO_PW'] != '')
			{
				$username = $_COOKIE['GO_UN'];
				$password = $_COOKIE['GO_PW'];
			}
				
			if (!isset($username) || !isset($password) ||
					!$GO_AUTH->login($username, $password, $params))
			{
				$location = $GO_CONFIG->host.
					'index.php?return_to='.urlencode($_SERVER['REQUEST_URI']);
				
				if($load_frames)
				{
					$location .= '&load_frames=true';
				}else
				{
					$location .= '&load_frames=false';
				}
					
				header('Location: '.$location);
				exit();
			}
		}

		if ($admin && !$this->has_admin_permission($this->user_id))
		{
			header("Location: ".$GO_CONFIG->host."error_docs/403.php");
			exit();
		}
	}
	
	function http_authenticate() {
	
		global $GO_CONFIG;
		
		header('WWW-Authenticate: Basic realm="'.$GO_CONFIG->title.'"');
		header( "HTTP/1.0 401 Unauthorized");
		exit();
	}

	/**
	* Creates and returns a new Access Control List to secure an object
	*
	* @param	string	$description	Description of the ACL
	* @param	int			$user_id	The owner of the ACL and the one who can modify it
	*									default is the current logged in user.
	* @access public
	* @return int			The ID of the new Access Control List
	*/
	function get_new_acl($description='', $user_id=-1)
	{
		global $GO_CONFIG;
		
		if ($user_id == -1)
		{
			$user_id = $this->user_id;
		}
		$id = $this->nextid("acl_items");
		if ($id > 0)
		{
			$this->query("INSERT INTO acl_items (id, description, user_id) ".
			"VALUES ('$id', '$description', '$user_id')");
			
			$this->add_group_to_acl($GO_CONFIG->group_root, $id);
			
			return $id;
		}else
		{
			return false;
		}
	}

	/**
	* Checks if a user owns the Access Control List
	*
	* @param	int			$user_id	The owner of the ACL and the one who can modify it
	* @param	int			$acl_id	The ID of the Access Control List
	* @access public
	* @return bool
	*/
	function user_owns_acl($user_id, $acl_id)
	{
		$this->query("SELECT user_id FROM acl_items WHERE id='$acl_id'");
		if ($this->next_record())
		{
			if ($user_id == $this->f('user_id'))
			{
				return true;
			}elseif($this->f('user_id') == '0')
			{
				return $this->has_admin_permission($user_id);
			}
		}
		return false;
	}
	
	/**
	* Change ownership of an ACL
	*
	* @param	int			$acl_id	The ID of the Access Control List
	* @param	int			$user_id	The owner of the ACL and the one who can modify it
	* @access public
	* @return bool
	*/	
	function chown_acl($acl_id, $user_id)
	{
		$sql = "UPDATE acl_items SET user_id='$user_id' WHERE id='$acl_id'";
		return $this->query($sql);
	}

	/**
	* Deletes an Access Control List
	*
	* @param	int			$acl_id	The ID of the Access Control List
	* @access public
	* @return bool		True on succces
	*/
	function delete_acl($acl_id)
	{
		if($this->query("DELETE FROM acl WHERE acl_id='$acl_id'"))
		{
			return $this->query("DELETE FROM acl_items WHERE id='$acl_id'");
		}
		return false;
	}

	/**
	* Adds a user to an Access Control List
	*
	* @param	int			$user_id	The user_id to add to the ACL
	* @param	int			$acl_id		The ID of the Access Control List
	* @access public
	* @return bool		True on success
	*/
	function add_user_to_acl($user_id,$acl_id)
	{
		return $this->query("INSERT INTO acl (acl_id,user_id) ".
		"VALUES ('$acl_id','$user_id')");
	}

	/**
	* Deletes a user from an Access Control List
	*
	* @param	int			$user_id	The user_id to delete from the ACL
	* @param	int			$acl_id		The ID of the Access Control List
	* @access public
	* @return bool		True on success
	*/
	function delete_user_from_acl($user_id, $acl_id)
	{
		$sql = "DELETE FROM acl WHERE user_id='$user_id' AND acl_id='$acl_id'";
		return $this->query($sql);
	}

	/**
	* Add's a user group to an Access Control List
	*
	* @param	int			$group_id	The group_id to add to the ACL
	* @param	int			$acl_id		The ID of the Access Control List
	* @access public
	* @return bool		True on success
	*/
	function add_group_to_acl($group_id,$acl_id)
	{
		return $this->query("INSERT INTO acl (acl_id,group_id) ".
		"VALUES ('$acl_id','$group_id')");
	}

	/**
	* Deletes a user group from an Access Control List
	*
	* @param	int			$group_id	The group_id to add to the ACL
	* @param	int			$acl_id		The ID of the Access Control List
	* @access public
	* @return bool		True on success
	*/
	function delete_group_from_acl($group_id, $acl_id)
	{
		global $GO_CONFIG;
		if($group_id != $GO_CONFIG->group_root)
		{
			$sql = "DELETE FROM acl WHERE group_id='$group_id' AND acl_id='$acl_id'";
			return $this->query($sql);
		}
	}

	/**
	* Remove all users and user groups from an ACL
	*
	* @param	int			$acl_id		The ID of the Access Control List
	* @access public
	* @return bool		True on success
	*/
	function clear_acl($acl_id)
	{
		global $GO_CONFIG;
		
		if($this->query("DELETE FROM acl WHERE acl_id='$acl_id'"))
		{
			return $this->add_group_to_acl($GO_CONFIG->group_root, $acl_id);
		}
	}

	/**
	* Set's the owner of an access control list
	*
	* @param	int			$acl_id		The ID of the Access Control List
	* @param	int			$user_id	The user ID of the new owner
	* @access public
	* @return bool		True on success
	*/
	function set_acl_owner($acl_id, $user_id)
	{
		return $this->query("UPDATE acl_items SET user_id='$user_id' WHERE id='$acl_id'");
	}

	/**
	* Checks if a user is in the special admins group
	*
	* @param	int			$user_id	The user ID
	* @access public
	* @return bool		True on success
	*/
	function has_admin_permission($user_id)
	{
		global $GO_CONFIG, $GO_GROUPS;
		if(!isset($this->is_admin))
			$this->is_admin = $GO_GROUPS->is_in_group($user_id, $GO_CONFIG->group_root);
			
		return $this->is_admin;
	}

	/**
	* Get's all groups from an ACL
	*
	* @param	int			$acl_id	The ACL ID
	* @access public
	* @return int			Number of groups in the acl
	*/
	function get_groups_in_acl($acl_id)
	{
		global $GO_CONFIG, $auth_sources;

		$sql = "SELECT groups.* FROM groups INNER JOIN acl ON".
		" acl.group_id=groups.id WHERE acl.acl_id='$acl_id'".
		" ORDER BY groups.name";
		$this->query($sql);
		return $this->num_rows();
	}

	/**
	 * Get all groups that are connected to a given acl.
	 * 
	 * This function fetches all groups that have permissions for the given acl,
	 * and returns an array of IDs.
	 * 
	 * @access public
	 * 
	 * @param Integer $acl_id is the ID whose groups should be fetched.
	 * 
	 * @return Array of the group IDs.
	 */
	function get_group_ids_from_acl( $acl_id ) {
		trigger_error(
			'get_group_ids_from_acl() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

	/**
	* Get's all users from an ACL
	*
	* @param	int			$acl_id	The ACL ID
	* @access public
	* @return int			Number of users in the acl
	*/
	function get_users_in_acl($acl_id)
	{
		$sql = "SELECT user_id FROM acl WHERE acl_id='$acl_id' AND user_id!=0";
		$this->query($sql);
		return $this->num_rows();
	}
	
	/**
	* Get's all authorized users from an ACL
	*
	* @param	int			$acl_id	The ACL ID
	* @access public
	* @return Array			The user id's
	*/
	function get_authorized_users_in_acl($acl_id)
	{
		$users=array();
		$sql = "SELECT user_id FROM acl WHERE acl_id='$acl_id' AND user_id!=0";
		
		$this->query($sql);
		while($this->next_record())
		{
			$users[] =$this->f('user_id');
		}
		
		$sql = "SELECT users_groups.user_id FROM users_groups INNER JOIN acl ON ".
			"acl.group_id=users_groups.group_id WHERE acl.acl_id=$acl_id AND users_groups.user_id!=0";
		$this->query($sql);
		while($this->next_record())
		{
			if(!in_array($this->f('user_id'), $users))
			{
				$users[] =$this->f('user_id');
			}
		}
		return $users;
	}

	/**
	* Checks presence of a user in an ACL
	*
	* @param	int			$user_id	The user ID
	* @param	int			$acl_id	The ACL ID
	* @access public
	* @return int			True if the user is in the ACL
	*/
	function user_in_acl($user_id, $acl_id)
	{
		$sql = "SELECT user_id FROM acl WHERE acl_id='$acl_id' AND".
		" user_id='$user_id'";
		$this->query($sql);
		if ($this->num_rows() > 0)
		{
			return true;
		}
		return false;
	}

	/**
	* Checks presence of a group in an ACL
	*
	* @param	int			$group_id	The group ID
	* @param	int			$acl_id	The ACL ID
	* @access public
	* @return int			True if the group is in the ACL
	*/
	function group_in_acl($group_id, $acl_id)
	{
		$sql = "SELECT group_id FROM acl WHERE acl_id='$acl_id' AND group_id='$group_id'";
		$this->query($sql);
		if ($this->num_rows() > 0)
		{
			return true;
		}else
		{
			return false;
		}
	}

	/**
	* Get's an ACL id based on the desciption. Use carefully.
	*
	* @param	string			$description	The description of an ACL
	* @access public
	* @return int			True if the group is in the ACL
	*/
	function get_acl_id($description)
	{
		$sql = "SELECT id FROM acl_items WHERE description='$description'";
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->f('id');
		}
		return false;
	}

	/**
   * Checks if an ACL exists in acl_items. Use carefully!
   * 
   * Returns:
   *	false if the acl does not exist
   *	true if the acl does exist in acl_items
   * 
   * @param int $acl_id
   * @access public
   * @return bool
   */
	function acl_exists( $acl_id )
	{
		$sql = "SELECT * FROM acl_items WHERE id='$acl_id'";
		$this->query($sql);
		if ( $this->num_rows() != 0 ) {
			return true;
		}
		#    $sql = "SELECT * FROM acl WHERE acl_id='$acl_id'";
		#    $this->query($sql);
		#    if ( $this->num_rows() != 0 ) {
		#      $retval += 2;
		#    }
		return false;
	}

	/**
	* Copy the user and group permissions of one acl to another
	*
	* @param	int			$sAcl	The source ACL to copy
	* @param	int			$dAcl	The destination ACL to copy to.
	* @access public
	* @return void
	*/
	function copy_acl($sAcl, $dAcl=0)
	{
		global $GO_CONFIG, $GO_GROUPS, $go_security_class;

		if($dAcl > 0)
		{
			$this->clear_acl($dAcl);
		}else
		{
			$dAcl = $this->get_new_acl();
		}

		$sql = "SELECT * FROM acl WHERE acl_id='$sAcl'";

		$security = new $go_security_class();
		$this->query($sql);
		while($this->next_record())
		{
			$new_security = new $go_security_class();
			if ($this->f("group_id") != 0 && $this->f('group_id') != $GO_CONFIG->group_root)
			{
				$new_security->add_group_to_acl($this->f("group_id"), $dAcl);
			}

			if ($this->f("user_id") != 0 && ($security->user_is_visible($this->f("user_id")) || $this->f("user_id") == $this->user_id))
			{
				$new_security->add_user_to_acl($this->f("user_id"), $dAcl);
			}
		}
		return $dAcl;
	}

	/**
	* Checks if a user is visible to the current logged in user
	*
	* @param	int			$user_id	The user ID to check
	* @access public
	* @return int			True if the user is visible
	*/

	function user_is_visible($user_id)
	{
		if ($this->user_id == $user_id)
		return true;

		$sql = "SELECT acl_id FROM users WHERE id='$user_id'";
		$this->query($sql);
		$this->next_record();
		return $this->has_permission($this->user_id, $this->f("acl_id"));
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
		/*$sql = "DELETE FROM acl WHERE user_id='$user_id'";
		return $this->query($sql);*/
	}

	/**
	* Called when a group is deleted
	*
	* @param	int			$group_id	The group ID that is about to be deleted
	* @access private
	* @return bool	 True on success
	*/
	function delete_group($group_id)
	{
		$sql = "DELETE FROM acl WHERE group_id='$group_id'";
		return $this->query($sql);
	}
}

	/**
	* Checks if a user has permission for a ACL
	*
	* @param	int			$user_id	The user that needs authentication
	* @param	int			$acl_id	The ACL to check
	* @access private
	* @return bool	 True on success
	*/
	function has_permission($user_id, $acl_id) {
		 return false;
	}
