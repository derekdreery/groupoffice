<?php
/**
* @copyright Intermesh 2003
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.78 $ $Date: 2006/12/01 15:57:13 $3
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once($GO_CONFIG->class_path.'base/base.users.class.inc');

/**
 * This is the SQL class of the user management class family
 *
 * @package Framework
 * @author   Merijn Schering <mschering@intermesh.nl>
 * @since    Group-Office 2.05
 */

class sql_users extends base_users
{
	/**
   * Calls the constructor of the parent users class
   *
   * @access public 
   * @return void
   */
	function sql_users()
	{
		$this->base_users();
	}

	/**
   * This function returns an array of the fields that can be used as search
   * criterias for users.
   * 
   * @access public 
   * @param void 
   * @return array
   */
	function get_search_fields() {
		global $strFirstName, $strLastName, $strEmail,
		$strCompany, $strDepartment, $strFunction,
		$strAddress, $strCity, $strZip, $strCountry, $strState,
		$strWorkAddress, $strWorkZip, $strWorkCity, $strWorkState,
		$strWorkCountry, $strSearchAll;

		$searchfields[] = array( '',  $strSearchAll );
		$searchfields[] = array( 'first_name',  $strFirstName );
		$searchfields[] = array( 'last_name',   $strLastName );
		$searchfields[] = array( 'email',	    $strEmail );
		$searchfields[] = array( 'company',	    $strCompany );
		$searchfields[] = array( 'department',  $strDepartment );
		$searchfields[] = array( 'function',    $strFunction );
		$searchfields[] = array( 'address',	    $strAddress );
		$searchfields[] = array( 'city',	    $strCity );
		$searchfields[] = array( 'zip',	    $strZip );
		$searchfields[] = array( 'state',	    $strState );
		$searchfields[] = array( 'country',	    $strCountry );
		$searchfields[] = array( 'work_address',$strWorkAddress );
		$searchfields[] = array( 'work_cip',    $strWorkZip );
		$searchfields[] = array( 'work_city',   $strWorkCity );
		$searchfields[] = array( 'work_state',  $strWorkState );
		$searchfields[] = array( 'work_country',$strWorkCountry );
		return $searchfields;
	}

	/**
   * This function searches for users with the given search field.
   * 
   * @access public
   * 
   * @param string $query
   * @param string $field
   * @param int $user_id
   * @param int $start
   * @param int $offset
   * 
   * @return array
   */
	
	function search($query, $field, $user_id=0, $start=0, $offset=0, $sort="name", $sort_direction='ASC')
	{
		global $GO_MODULES;
		
		if($sort == 'name')
		{
			if(!isset($_SESSION['GO_SESSION']['sort_name']) || $_SESSION['GO_SESSION']['sort_name'] == 'first_name')
			{
				$sort = 'first_name '.$sort_direction.', last_name ';
			}else
			{
				$sort = 'last_name '.$sort_direction.', first_name ';
			}
		}

		if($user_id > 0)
		{
			$sql = "SELECT DISTINCT users.*";
			if(isset($GO_MODULES->modules['custom_fields']) && $GO_MODULES->modules['custom_fields']['read_permission'])
			{
				$sql .= ", cf_8.* ";
			}
			$sql .=" FROM users  INNER JOIN acl ON users.acl_id = acl.acl_id ".
			"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ";
			
			
			if(isset($GO_MODULES->modules['custom_fields']) && $GO_MODULES->modules['custom_fields']['read_permission'])
			{
				$sql .= "LEFT JOIN cf_8 ON cf_8.link_id=users.link_id ";
			}
			
			$sql .= "WHERE (acl.user_id=$user_id ".
			"OR users_groups.user_id=$user_id) AND ";
		}else
		{
			$sql = "SELECT * FROM users ";
			if(isset($GO_MODULES->modules['custom_fields']) && $GO_MODULES->modules['custom_fields']['read_permission'])
			{
				$sql .= "LEFT JOIN cf_8 ON cf_8.link_id=users.link_id ";
			}
			$sql .= "WHERE ";
			
		}
		
		if(!is_array($field))
		{
			$fields=array();
			if($field == '')
			{
				$fields_sql = "SHOW FIELDS FROM users";
				$this->query($fields_sql);
				while($this->next_record())
				{
					if(eregi('varchar', $this->f('Type')))
					{
						$fields[]='users.'.$this->f('Field');
					}
				}
				if(isset($GO_MODULES->modules['custom_fields']) && $GO_MODULES->modules['custom_fields']['read_permission'])
				{
					$fields_sql = "SHOW FIELDS FROM cf_8";
					$this->query($fields_sql);
					while ($this->next_record()) {
						$fields[]='cf_8.'.$this->f('Field');
					}
					
				}
			}else {
				$fields[]=$field;
			}
		}else {
			$fields=$field;
		}
		
		foreach($fields as $field)
		{
			if(count($fields)>1)
			{
				if(isset($first))
				{
					$sql .= ' OR ';
				}else
				{
					$first = true;
					$sql .= '(';
				}				
			}
			
			if($field=='name')
			{
				$sql .= "CONCAT(first_name,middle_name,last_name) LIKE '".str_replace(' ','%', $query)."' ";
			}else
			{
				$sql .= "$field LIKE '$query' ";
			}
		}
		if(count($fields)>1)
		{
			$sql .= ')';
		}			
		
		

		$sql .= " ORDER BY $sort $sort_direction";
		$this->query($sql);
		$count = $this->num_rows();

		if ($offset != 0)
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
		}

		return $count;
	}
	
	function get_linked_users($user_id, $link_id)
	{
		global $GO_LINKS;
		$links = $GO_LINKS->get_links($link_id, 8);
		
		if(count($links))
		{
			$sql = "SELECT users.* FROM users  INNER JOIN acl ON users.acl_id = acl.acl_id ".
				"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id WHERE (acl.user_id=$user_id ".
				"OR users_groups.user_id=$user_id) AND link_id IN (".implode(',',$links).") ORDER BY last_name ASC, first_name ASC";
			
			$this->query($sql);
			return $this->num_rows();
		}
		return 0;
	}

	function authorize($requesting_user_id, $authcode, $accepting_user_id)
	{
		global $GO_SECURITY;

		$this->query("SELECT acl_id, id FROM users WHERE authcode='".
		smart_addslashes($authcode)."' AND id='$requesting_user_id'");
		if ($this->next_record())
		{
			$acl_id = $this->f("acl_id");
			if (!$GO_SECURITY->user_in_acl($accepting_user_id, $acl_id))
			{
				if (!$GO_SECURITY->add_user_to_acl($accepting_user_id,$acl_id))
				{
					return false;
				}
			}
			$this->query("SELECT acl_id FROM users WHERE id='$accepting_user_id'");
			if ($this->next_record())
			{
				$acl_id = $this->f("acl_id");
				if (!$GO_SECURITY->user_in_acl($requesting_user_id, $acl_id))
				{
					if (!$GO_SECURITY->add_user_to_acl($requesting_user_id,$acl_id))
					{
						return false;
					}
				}
			}
			return true;
		}else
		{
			return false;
		}
	}

	function get_users($sort="name",$direction="ASC", $start=0, $offset=0)
	{
		if ($sort == 'name')
		{
			if(!isset($_SESSION['GO_SESSION']['sort_name']) ||  $_SESSION['GO_SESSION']['sort_name'] == 'first_name')
			{
				$sort = 'first_name '.$direction.', last_name';
			}else
			{
				$sort = 'last_name '.$direction.', first_name';
			}
			//      $sort = 'first_name '.$direction.', last_name';
		}
		$count=0;
		$this->query("SELECT id FROM users");
		if ($this->next_record())
		{
			$count = $this->num_rows();
		}

		if ($count > 0)
		{
			$sql = "SELECT * FROM users ORDER BY ".$sort." ".$direction;

			if ($offset != 0)
			{
				$sql .= " LIMIT $start,$offset";
			}
			$this->query($sql);
		}
		return $count;
	}

	function get_authorized_users($user_id, $sort="name",$direction="ASC")
	{
		if ($sort == 'users.name' || $sort=='name')
		{
			if($_SESSION['GO_SESSION']['sort_name'] == 'first_name')
			{
				$sort = 'users.first_name '.$direction.', users.last_name';
			}else
			{
				$sort = 'users.last_name '.$direction.', users.first_name';
			}
			//      $sort = 'users.first_name '.$direction.', users.last_name';
		}
		$sql = "SELECT DISTINCT users.* FROM users ".
		"INNER JOIN acl ON users.acl_id= acl.acl_id ".
		"LEFT JOIN users_groups ON (acl.group_id = users_groups.group_id) ".
		"WHERE users_groups.user_id=".$user_id." OR ".
		"acl.user_id = ".$user_id." ORDER BY ".$sort." ".$direction;

		$this->query($sql);
		return $this->num_rows();
	}

	function get_user_by_email($email)
	{
		$email = get_email_from_string($email);
		$sql = "SELECT * FROM users WHERE email='$email'";
		$this->query($sql);
		if ($this->next_record(MYSQL_ASSOC))
		{
			return $this->Record;
		}
		return false;
	}

	function check_password($password)
	{
		$this->query("SELECT id FROM users WHERE password='".md5($password).
		"' AND id='".$_SESSION['GO_SESSION']['user_id']."'");
		if ($this->num_rows() > 0)
		{
			return true;
		}
		return false;
	}

	function get_user($user_id)
	{
		$sql = "SELECT * FROM users WHERE id='$user_id'";
		$this->query( $sql );
		if ($this->next_record(MYSQL_ASSOC))
		{
			if($this->Record['date_seperator']=='')
			{
				$this->Record['date_seperator']=' ';
			}
			return $this->Record;
		}
		return false;
	}

	/**
	 * This function updates all userdata based on the given parameters.
	 * 
	 * @access public
	 *
	 * @return bool True on success
	 */

	function update_user(
	$user,
	$user_groups=null,
	$visible_user_groups=null,
	$modules_read=null,
	$modules_write=null)
	{
		global $GO_MODULES, $GO_SECURITY, $GO_GROUPS;

		if($this->update_profile($user))
		{
			$GO_MODULES->get_modules();
			while ($GO_MODULES->next_record())
			{
				if(isset($modules_read))
				{
					$could_read = $GO_SECURITY->has_permission($user['id'], $GO_MODULES->f('acl_read'));
					$can_read =  in_array($GO_MODULES->f('id'), $modules_read);

					if ($could_read && !$can_read)
					{
						$GO_SECURITY->delete_user_from_acl($user['id'], $GO_MODULES->f('acl_read'));
					}

					if ($can_read && !$could_read)
					{
						$GO_SECURITY->add_user_to_acl($user['id'], $GO_MODULES->f('acl_read'));
					}
				}

				if(isset($modules_write))
				{
					$could_write = $GO_SECURITY->has_permission($user['id'], $GO_MODULES->f('acl_write'));
					$can_write =  in_array($GO_MODULES->f('id'), $modules_write);

					if ($could_write && !$can_write)
					{
						$GO_SECURITY->delete_user_from_acl($user['id'], $GO_MODULES->f('acl_write'));
					}

					if ($can_write && !$could_write)
					{
						$GO_SECURITY->add_user_to_acl($user['id'], $GO_MODULES->f('acl_write'));
					}
				}
			}

			//make sure we have user['acl_id']
			$user = $this->get_user($user['id']);


			$GO_GROUPS->get_groups();
			$groups2 = new $GLOBALS['go_groups_class']();
			while($GO_GROUPS->next_record())
			{
				if(isset($user_groups))
				{
					$is_in_group = $groups2->is_in_group($user['id'], $GO_GROUPS->f('id'));
					$should_be_in_group = in_array($GO_GROUPS->f('id'), $user_groups);

					if ($is_in_group && !$should_be_in_group)
					{
						$groups2->delete_user_from_group($user['id'], $GO_GROUPS->f('id'));
					}

					if (!$is_in_group && $should_be_in_group)
					{
						$groups2->add_user_to_group($user['id'], $GO_GROUPS->f('id'));
					}
				}

				if(isset($visible_user_groups))
				{
					$group_is_visible = $GO_SECURITY->group_in_acl($GO_GROUPS->f('id'), $user['acl_id']);
					$group_should_be_visible = in_array($GO_GROUPS->f('id'), $visible_user_groups);

					if ($group_is_visible && !$group_should_be_visible)
					{
						$GO_SECURITY->delete_group_from_acl($GO_GROUPS->f('id'), $user['acl_id']);
					}

					if (!$group_is_visible  && $group_should_be_visible)
					{
						$GO_SECURITY->add_group_to_acl($GO_GROUPS->f('id'), $user['acl_id']);
					}
				}
			}
			return true;
		}
		return false;
	}

	function update_profile($user)
	{
		$user['mtime']=get_gmt_time();
		if($this->update_row('users', 'id', $user))
		{
			if($user['id'] == $_SESSION['GO_SESSION']['user_id'])
			{
				return $this->update_session($user['id']);
			}
			return true;
		}
		return false;
	}

	function update_password($user_id, $password)
	{
		$sql = "UPDATE users SET password='".md5($password)."' WHERE id='$user_id'";
		if ($this->query($sql))
		{
			return true;
		}
		return false;
	}

	function get_user_by_username($username)
	{
		$sql = "SELECT * FROM users WHERE username='$username'";
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}

	function email_exists($email)
	{
		$sql = "SELECT id FROM users WHERE email='$email'";
		$this->query($sql);
		if ($this->num_rows() > 0)
		{
			return true;
		}
		return false;
	}

	// the following fields are removed now: acl_id, authcode
	function add_user(
	$user,
	$user_groups=array(),
	$visible_user_groups=array(),
	$modules_read=array(),
	$modules_write=array(),
	$acl=array())
	{
		global $GO_CONFIG, $GO_LANGUAGE, $GO_SECURITY, $GO_GROUPS, $GO_MODULES;

		// We check if we are able to add a new user. If we already have too much
		// of them we do not want new ones ;)
		if ( $this->max_users_reached() ) {
			return false;
		}
		// We check if a user with this email address already exists. Since the
		// email address is used as key for the acl_id, no two users may have the
		// same address. It also should not be possible to have multiple users
		// with the same name...
		$this->query( "SELECT id FROM users WHERE	email='".$user['email']."' OR username='".$user['username']."'" );
		if ( $this->num_rows() > 0 ) {
			return false;
		}

		//if local_code is empty try HTTP_ACCEPT_LANGUAGE
		//if that failes use default from GO_CONFIG
		
		$user['language'] = isset($user['language']) ? $user['language'] : $GO_CONFIG->language;



		if(!empty($user['password']))
		{
			$user['password'] = md5($user['password']);
		}

		$user['currency'] = $GO_CONFIG->default_currency;
		$user['decimal_seperator'] = $GO_CONFIG->default_decimal_seperator;
		$user['thousands_seperator'] = $GO_CONFIG->default_thousands_seperator;
		$user['time_format'] = $GO_CONFIG->default_time_format;
		$user['date_format'] = $GO_CONFIG->default_date_format;
		$user['date_seperator'] = $GO_CONFIG->default_date_seperator;
		$user['first_weekday'] = $GO_CONFIG->default_first_weekday;
		$user['timezone'] = $GO_CONFIG->default_timezone;
		$user['DST'] = $GO_CONFIG->default_dst;

		if(empty($user['link_id']))
		{
			global $GO_LINKS;
			$user['link_id']= $GO_LINKS->get_link_id();
		}
		$user['theme'] = isset($user['theme']) ? $user['theme'] : $GO_CONFIG->theme;

		if (empty($user['id'])){
			$user['id'] = $this->nextid("users");
		}
		// When the acl_id is already given, we do not have to create a new one,
		// but it may be neccessary to change the owner of the acl - this is
		// needed when the authentication framework "accidentially" creates the
		// acl id for this user (which happens in the case, when the user is
		// authenticated against an LDAP directory, where the id is generated
		// when the LDAP entry is converted to the $user entry, which is given
		// as parameter to this function).
		if ( isset( $user['acl_id'] ) ) {
			$GO_SECURITY->set_acl_owner( $user['acl_id'], $user['id'] );
		} else {
			$user['acl_id'] = $GO_SECURITY->get_new_acl( $user['email'] );
		}
		$user['authcode'] = $this->random_password();

		$user['registration_time'] = $user['mtime']=get_gmt_time();

		if ($user['id'] > 0 && $this->insert_row('users', $user))
		{
			$GO_SECURITY->set_acl_owner( $user['acl_id'], $user['id'] );
			$GO_GROUPS->add_user_to_group( $user['id'], $GO_CONFIG->group_everyone);

			foreach($user_groups as $group_id)
			{
				if($group_id > 0 && $group_id != $GO_CONFIG->group_everyone && !$GO_GROUPS->is_in_group($user['id'], $group_id))
				{
					$GO_GROUPS->add_user_to_group($user['id'], $group_id);
				}
			}
			foreach($visible_user_groups as $group_id)
			{
				if($group_id > 0 && !$GO_SECURITY->group_in_acl($group_id, $user['acl_id']))
				{
					$GO_SECURITY->add_group_to_acl($group_id, $user['acl_id']);
				}
			}

			foreach($modules_read as $module_name)
			{
				$module = $GO_MODULES->get_module($module_name);
				if($module && !$GO_SECURITY->user_in_acl($user['id'], $module['acl_read']))
				{
					$GO_SECURITY->add_user_to_acl($user['id'], $module['acl_read']);
				}
			}

			foreach($modules_write as $module_name)
			{
				$module = $GO_MODULES->get_module($module_name);
				if($module && !$GO_SECURITY->user_in_acl($user['id'], $module['acl_write']))
				{
					$GO_SECURITY->add_user_to_acl($user['id'], $module['acl_write']);
				}
			}

			foreach($acl as $acl_id)
			{
				if(!$GO_SECURITY->user_in_acl($user['id'], $acl_id))
				{
					$GO_SECURITY->add_user_to_acl($user['id'], $acl_id);
				}
			}
			
			

			return $user['id'];
		} else {
			$GO_SECURITY->delete_acl( $user['id'] );
		}
		return false;
	}

	function max_users_reached()
	{
		global $GO_CONFIG;

		if($this->get_users() < $GO_CONFIG->max_users || $GO_CONFIG->max_users == 0)
		{
			return false;
		}else
		{
			return true;
		}
	}

	function delete_user($user_id)
	{
		global $GO_CONFIG,$GO_SECURITY, $GO_MODULES, $GO_GROUPS;

		if($user = $this->get_user($user_id))
		{
			$acl_id = $this->f("acl_id");
			$username = $this->f("username");
			$sql = "DELETE FROM users WHERE id='$user_id'";
			if ($this->query($sql))
			{
				$GO_SECURITY->delete_acl($acl_id);
				$GO_SECURITY->delete_user($user_id);

				foreach($GO_MODULES->modules as $module)
				{
					if(file_exists($module['class_path'].$module['id'].'.class.inc'))
					{
						require_once($module['class_path'].$module['id'].'.class.inc');
						$module_object = new $module['id'];
						if(method_exists($module_object, '__on_user_delete'))
						{
							$module_object->__on_user_delete($user_id);
						}
					}
					
					/*if($module['id']=='filesystem')
					{
						require_once($GO_CONFIG->class_path.'filesystem.class.inc');
						$fs = new filesystem();
						$fs->delete_user($user_id);
					}*/
				}

				$GO_GROUPS->__on_user_delete($user_id);

				if(file_exists($GO_CONFIG->root_path.'sync/classes/syncml.class.inc'))
				{
					require_once($GO_CONFIG->root_path.'sync/classes/syncml.class.inc');
					$sync_settings = new syncml_settings();
					$sync_settings->__on_user_delete($user_id);
				}
				
				$sql = "DELETE FROM acl WHERE user_id=$user_id;";
				$this->query($sql);

				/*$sql = "SELECT * FROM acl_items WHERE user_id='$user_id'";
				$this->query($sql);
				while($this->next_record())
				{
				$GO_SECURITY->delete_acl($this->f('id'));
				}*/
				system('rm -Rf '.$GO_CONFIG->file_storage_path.$username);
				return true;
			}
		}
		return false;
	}

	function increment_logins( $user_id ) {
		$sql =  "UPDATE users SET logins=logins+1, lastlogin='".get_gmt_time().
		"' WHERE id='$user_id'";
		$this->query( $sql );
	}
}
