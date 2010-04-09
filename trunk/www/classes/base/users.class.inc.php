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
 * @package go.basic
 */


/**
 * This class is used to manage users in Group-Office.
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 
 * @package go.basic
 * @since Group-Office 2.05
 * 
 * @uses db
 */
class GO_USERS extends db
{
	/**
	 * The constructor initializes the SQL database connection.
	 * 
	 * Some functionality is global and therefore implemented in this class,
	 * and not in the child classes. For this functions a database connection
	 * is needed, so we initialize it in the constructor. There is no need to
	 * do anything else here.
	 * 
	 * @access public
	 */
	function __construct()
	{
		global $GO_CONFIG;
		
		if(!isset($_SESSION['GO_SESSION']['decimal_separator']))
		{		
			$_SESSION['GO_SESSION']['decimal_separator'] = $GO_CONFIG->default_decimal_separator;
		}		
		if(!isset($_SESSION['GO_SESSION']['thousands_separator']))
		{		
			$_SESSION['GO_SESSION']['thousands_separator'] = $GO_CONFIG->default_thousands_separator;
		}
		if(!isset($_SESSION['GO_SESSION']['date_separator']))
		{		
			$_SESSION['GO_SESSION']['date_separator'] = $GO_CONFIG->default_date_separator;
		}		
		if(!isset($_SESSION['GO_SESSION']['date_format']))
		{		
			$_SESSION['GO_SESSION']['date_format'] = Date::get_dateformat( $GO_CONFIG->default_date_format, $_SESSION['GO_SESSION']['date_separator']);
		}
		if(!isset($_SESSION['GO_SESSION']['time_format']))
		{		
			$_SESSION['GO_SESSION']['time_format'] = $GO_CONFIG->default_time_format;
		}
		if(!isset($_SESSION['GO_SESSION']['currency']))
		{		
			$_SESSION['GO_SESSION']['currency'] = $GO_CONFIG->default_currency;
		}
		if(!isset($_SESSION['GO_SESSION']['timezone']))
		{		
			$_SESSION['GO_SESSION']['timezone'] = $GO_CONFIG->default_timezone;
		}
		if(!isset($_SESSION['GO_SESSION']['country']))
		{	
			$_SESSION['GO_SESSION']['country'] = $GO_CONFIG->default_country;
		}
		if(!isset($_SESSION['GO_SESSION']['sort_name']))
		{		
			$_SESSION['GO_SESSION']['sort_name'] = 'last_name';
		}
		
		
		parent::__construct();
	}
	
/**
	 * Updates the session data corresponding to the user_id.
	 * 
	 * @access public
	 * 
	 * @param int $user_id
	 * 
	 * @return bool
	 */
	function update_session( $user_id , $update_language=false) {
		global $GO_LANGUAGE, $GO_CONFIG, $GO_THEME;
		if ($userdata = $this->get_user($user_id)) {
			$middle_name = $userdata['middle_name'] == '' ? '' : $userdata['middle_name'].' ';
				
			if($update_language && $GO_LANGUAGE->language != $userdata['language'])
			{
				$userdata['language'] = $up_user['language'] = $GO_LANGUAGE->language;
				$up_user['id']=$user_id;				
				
				$this->update_row('go_users', 'id', $up_user);
			}else
			{			
				$GO_LANGUAGE->set_language($userdata['language']);
			}

			$_SESSION['GO_SESSION']['user_id'] = $user_id;
			
			$_SESSION['GO_SESSION']['username'] = $userdata['username'];
			$_SESSION['GO_SESSION']['name'] = trim($userdata['first_name'].' '.$middle_name.$userdata['last_name']);
			$_SESSION['GO_SESSION']['company'] = $userdata['company'];
			$_SESSION['GO_SESSION']['function'] = $userdata['function'];
			$_SESSION['GO_SESSION']['department'] = $userdata['department'];
			
			$_SESSION['GO_SESSION']['first_name'] = $userdata['first_name'];
			$_SESSION['GO_SESSION']['middle_name'] = $userdata['middle_name'];
			$_SESSION['GO_SESSION']['last_name'] = $userdata['last_name'];
			$_SESSION['GO_SESSION']['country'] = $userdata['country'];
			$_SESSION['GO_SESSION']['email'] = $userdata['email'];
			$_SESSION['GO_SESSION']['work_phone'] = $userdata['work_phone'];
			$_SESSION['GO_SESSION']['home_phone'] = $userdata['home_phone'];

			$_SESSION['GO_SESSION']['thousands_separator'] = $userdata['thousands_separator'];
			$_SESSION['GO_SESSION']['decimal_separator'] = $userdata['decimal_separator'];
			$_SESSION['GO_SESSION']['date_format'] = Date::get_dateformat($userdata['date_format'], $userdata['date_separator']);
			$_SESSION['GO_SESSION']['date_separator'] = $userdata['date_separator'];
			$_SESSION['GO_SESSION']['time_format'] = $userdata['time_format'];
			$_SESSION['GO_SESSION']['currency'] = $userdata['currency'];
			$_SESSION['GO_SESSION']['lastlogin'] = isset ($userdata['lastlogin']) ? $userdata['lastlogin'] : time();
			$_SESSION['GO_SESSION']['max_rows_list'] = $userdata['max_rows_list'];
			$_SESSION['GO_SESSION']['timezone'] = $userdata['timezone'];
			$_SESSION['GO_SESSION']['start_module'] = isset ($userdata['start_module']) ? $userdata['start_module'] : 'summary';

			//$_SESSION['GO_SESSION']['language'] = $userdata['language'];
			$_SESSION['GO_SESSION']['theme'] = $userdata['theme'];
			$_SESSION['GO_SESSION']['mute_sound'] = $userdata['mute_sound'];
			$_SESSION['GO_SESSION']['first_weekday'] = $userdata['first_weekday'];
			$_SESSION['GO_SESSION']['sort_name'] = !empty($userdata['sort_name']) ? $userdata['sort_name'] : 'last_name';
			
			$_SESSION['GO_SESSION']['list_separator'] = $userdata['list_separator'];
			$_SESSION['GO_SESSION']['text_separator'] = $userdata['text_separator'];

			if (isset($GO_THEME)) $GO_THEME->set_theme();
			return true;
		}
		return false;
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
		
		global $lang;

		$searchfields[] = array( '',  $lang['common']['SearchAll'] );
		$searchfields[] = array( 'first_name',  $lang['common']['firstName'] );
		$searchfields[] = array( 'last_name',   $lang['common']['lastName'] );
		$searchfields[] = array( 'email',	    $lang['common']['email'] );
		$searchfields[] = array( 'company',	    $lang['common']['company'] );
		$searchfields[] = array( 'department',  $lang['common']['department'] );
		$searchfields[] = array( 'function',    $lang['common']['function'] );
		$searchfields[] = array( 'address',	    $lang['common']['address'] );
		$searchfields[] = array( 'city',	    $lang['common']['city'] );
		$searchfields[] = array( 'zip',	    $lang['common']['zip'] );
		$searchfields[] = array( 'state',	    $lang['common']['state'] );
		$searchfields[] = array( 'country',	    $lang['common']['country'] );
		$searchfields[] = array( 'work_address',$lang['common']['workAddress'] );
		$searchfields[] = array( 'work_cip',    $lang['common']['workZip'] );
		$searchfields[] = array( 'work_city',   $lang['common']['workCity'] );
		$searchfields[] = array( 'work_state',  $lang['common']['workState'] );
		$searchfields[] = array( 'work_country',$lang['common']['workCountry'] );
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
	
	function search($query, $field, $user_id=0, $start=0, $offset=0, $sort="name", $sort_direction='ASC', $search_operator='LIKE')
	{
		global $GO_MODULES;
		
		if($sort == 'name')
		{
			if(!isset($_SESSION['GO_SESSION']['sort_name']) || $_SESSION['GO_SESSION']['sort_name'] == 'last_name')
			{
				$sort = 'last_name '.$sort_direction.', first_name ';
			}else
			{
				$sort = 'first_name '.$sort_direction.', last_name ';
			}
		}

		if($user_id > 0)
		{
			$where=true;
			$sql = "SELECT DISTINCT u.*";
			if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
			{
				$sql .= ", cf_8.* ";
			}
			$sql .=" FROM go_users u INNER JOIN go_acl a ON u.acl_id = a.acl_id ".
			"LEFT JOIN go_users_groups ug ON a.group_id = ug.group_id ";
			
			
			if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
			{
				$sql .= "LEFT JOIN cf_8 ON cf_8.link_id=u.id ";
			}
			
			$sql .= "WHERE (a.user_id=".$this->escape($user_id)." ".
			"OR ug.user_id=".$this->escape($user_id).")";
		}else
		{
			$where=false;
			$sql = "SELECT u.* ";
			if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
			{
				$sql .= ", cf_8.* ";
			}
			$sql .= "FROM go_users u";
			if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
			{
				$sql .= " LEFT JOIN cf_8 ON cf_8.link_id=u.id ";
			}			
		}
		
		if($query!='')
		{
			$sql .= $where ? " AND " : " WHERE ";
			
			if(!is_array($field))
			{
				$fields=array();
				if($field == '')
				{
					$fields_sql = "SHOW FIELDS FROM go_users";
					$this->query($fields_sql);
					while($this->next_record())
					{
						if(stripos($this->f('Type'),'varchar')!==false)
						{
							$fields[]='u.'.$this->f('Field');
						}
					}
					if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
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
					$sql .= "CONCAT(first_name,middle_name,last_name) $search_operator '".$this->escape(str_replace(' ','%', $query))."' ";
				}else
				{
					$sql .= "$field $search_operator '".$this->escape($query)."' ";
				}
			}
			if(count($fields)>1)
			{
				$sql .= ')';
			}
		}	

	 	$sql .= " ORDER BY $sort $sort_direction";
		$this->query($sql);
		$count = $this->num_rows();

		if ($offset != 0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
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
			$sql = "SELECT go_users.* FROM go_users  INNER JOIN go_acl ON go_users.acl_id = go_acl.acl_id ".
				"LEFT JOIN go_users_groups ON go_acl.group_id = go_users_groups.group_id WHERE (go_acl.user_id=".$this->escape($user_id)." ".
				"OR go_users_groups.user_id=".$this->escape($user_id).") AND link_id IN (".implode(',',$links).") ORDER BY last_name ASC, first_name ASC";
			
			$this->query($sql);
			return $this->num_rows();
		}
		return 0;
	}

	/**
	 * Fetch all users from the user management backend.
	 * 
	 * This function retrieves all users from the database and returns their
	 * number. After that you are able to process each user via next_record.
	 * 
	 * @access public
	 * 
	 * @param string $sort The field to sort on
	 * @param string $direction The sort direction
	 * @param int $start Return results starting from this row
	 * @param int $offset Return this number of rows
	 * 
	 * @return int The number of users
	 */

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
		$this->query("SELECT id FROM go_users");
		if ($this->next_record())
		{
			$count = $this->num_rows();
		}

		if ($count > 0)
		{
			$sql = "SELECT * FROM go_users ORDER BY ".$sort." ".$direction;

			if ($offset != 0)
			{
				$sql .= " LIMIT ".$this->escape($start.",".$offset);
			}
			$this->query($sql);
		}
		return $count;
	}

	/**
	 * This function retrieves all users that are visible to a user.
	 * 
	 * This function fetches all users that should be visible to the given
	 * user. next_record() can be used to iterate over the result set.
	 * 
	 * @access public
	 * 
	 * @param string $sort The field to sort on
	 * @param string $direction The sort direction
	 * @param int $start Return results starting from this row
	 * @param int $offset Return this number of rows
	 * 
	 * @return int The number of users
	 */
	function get_authorized_users($user_id, $sort="name",$direction="ASC")
	{
		if ($sort == 'users.name' || $sort=='name')
		{
			if($_SESSION['GO_SESSION']['sort_name'] == 'first_name')
			{
				$sort = 'users.first_name '.$direction.', go_users.last_name';
			}else
			{
				$sort = 'users.last_name '.$direction.', go_users.first_name';
			}
			//      $sort = 'users.first_name '.$direction.', go_users.last_name';
		}
		$sql = "SELECT DISTINCT go_users.* FROM go_users ".
		"INNER JOIN go_acl ON go_users.acl_id= go_acl.acl_id ".
		"LEFT JOIN go_users_groups ON (go_acl.group_id = go_users_groups.group_id) ".
		"WHERE go_users_groups.user_id=".$this->escape($user_id)." OR ".
		"go_acl.user_id = ".$this->escape($user_id)." ORDER BY ".$sort." ".$direction;

		$this->query($sql);
		return $this->num_rows();
	}

	/**
	 * This function searches for a user by his email address.
	 * 
	 * This function retrieves all userdata based on the users email address.
	 * 
	 * @access public
	 * 
	 * @param string $email The e-mail address of a user
	 * 
	 * @return array
	 */
	function get_user_by_email($email)
	{
		$email = String::get_email_from_string($email);
		$sql = "SELECT * FROM go_users WHERE email='".$this->escape($email)."'";
		$this->query($sql);
		
		//return false if there is more then one result
		if($this->num_rows()!=1)
		{
			return false;
		}elseif ($this->next_record(DB_ASSOC))
		{
			return $this->record;
		}
		
	}
	/**
	 * This function returns all userdata based on the user's name.
	 * 
	 * @access public
	 * 
	 * @param int $user_id The user to check access for
	 * @param string $username
	 * 
	 * @return array The user profile
	 */
	function get_authorized_user_by_email($user_id, $email)
	{
		$sql = "SELECT DISTINCT go_users.* FROM go_users ".
		"INNER JOIN go_acl ON go_users.acl_id= go_acl.acl_id ".
		"LEFT JOIN go_users_groups ON (go_acl.group_id = go_users_groups.group_id) ".
		"WHERE (go_users_groups.user_id=".$this->escape($user_id)." OR ".
		"go_acl.user_id = ".$this->escape($user_id).") AND email='".$this->escape($email)."'";
		$this->query($sql);
		if ($this->next_record(DB_ASSOC))
		{
			return $this->record;
		}
		return false;
	}

	/**
	 * This function checks if the password the user supplied is valid.
	 * 
	 * @access public
	 * 
	 * @param string $password
	 * 
	 * @return bool
	 */
	function check_password($password)
	{
		$this->query("SELECT id FROM go_users WHERE password='".md5($password).
		"' AND id='".$_SESSION['GO_SESSION']['user_id']."'");
		if ($this->num_rows() > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * This function searches for a user by his ID andreturns all userdata based on the users ID.
	 * 
	 * @access public	
	 * @param int $user_id 
	 * @return array
	 */
	function get_user($user_id)
	{
		$sql = "SELECT * FROM go_users WHERE id='".$this->escape($user_id)."'";
		$this->query( $sql );
		if ($this->next_record(DB_ASSOC))
		{
			if($this->record['date_separator']=='')
			{
				$this->record['date_separator']=' ';
			}			
			return $this->record;
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
			//make sure we have user['acl_id']
			$user = $this->get_user($user['id']);
			
			if(isset($modules_read) && isset($modules_write)){
				$GO_MODULES->get_modules();
				while ($mod = $GO_MODULES->next_record())
				{
					$level = 0;
					if(in_array($mod['id'], $modules_write)){
						$level = GO_SECURITY::WRITE_PERMISSION;
					}elseif(in_array($mod['id'], $modules_read)){
						$level = GO_SECURITY::READ_PERMISSION;
					}

					if ($level)
					{
						if(!$GO_SECURITY->has_permission($user['id'], $mod['acl_id']))
						{
							$GO_SECURITY->add_user_to_acl($user['id'], $mod['acl_id'], $level);
						}
					} else {
						if($GO_SECURITY->user_in_acl($user['id'], $mod['acl_id']))
						{
							$GO_SECURITY->delete_user_from_acl($user['id'], $mod['acl_id']);
						}
					}
				}
			}

			


			$GO_GROUPS->get_groups();
			$groups2 = new GO_GROUPS();
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

	/**
	 * This function updates a the profile of a user.
	 * 
	 * Using an SQL update record, this function actualizes the profile of the
	 * given user.
	 * 
	 * 
	 * @access protected
	 * 
	 * @param Array $user is an array of all data that should be updated.
	 * 
	 * @return Boolean to indicate the success of the operation.
	 */
	function update_profile($user, $complete_profile=false)
	{
		global $GO_EVENTS;
		
		$user['mtime']=time();

		$params = array($user);
		
		$ret = false;
		if(!empty($user['password']))
		{			
			$user['password']=md5($user['password']);
		}
		
		if($this->update_row('go_users', 'id', $user))
		{
			if(isset($_SESSION['GO_SESSION']['user_id']) && $user['id'] == $_SESSION['GO_SESSION']['user_id'])
			{
				$ret = $this->update_session($user['id']);
			}
			$ret = true;
		}
		
		$this->cache_user($user['id']);
		
		if($complete_profile)
		{
			$user=$this->get_user($user['id']);
			if(isset($params[0]['password']))
			{
				$user['password']=$params[0]['password'];
			}
			$params = array($user);
			$GO_EVENTS->fire_event('add_user', $params);						
		}else
		{
			$GO_EVENTS->fire_event('update_user', $params);
		}
		
		return $ret;
	}
	/**
	 * This function updates the user's password.
	 * 
	 * @access public
	 * 
	 * @param int $user_id
	 * @param string $password
	 * 
	 * @return bool True on success
	 */
	
	function update_password($user_id, $password)
	{
		global $GO_EVENTS;
		
		$sql = "UPDATE go_users SET password='".md5($password)."' WHERE id='$user_id'";
		if ($this->query($sql))
		{
			$GO_EVENTS->fire_event('change_user_password', array($user_id, $password));
			
			return true;
		}
		return false;
	} 
	/**
	 * This function returns all userdata based on the user's name.
	 * 
	 * @access public
	 * 
	 * @param string $username
	 * 
	 * @return array The user profile
	 */
	function get_user_by_username($username)
	{
		$sql = "SELECT * FROM go_users WHERE username='".$this->escape($username)."'";
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->record;
		}
		return false;
	}
	/**
	 * This function checks, if there is already a user with the given email
	 * address.
	 * 
	 * @access public
	 * 
	 * @param string $email
	 * 
	 * @return bool True if exists
	 */
	function email_exists($email)
	{
		$sql = "SELECT id FROM go_users WHERE email='".$this->escape($email)."'";
		$this->query($sql);
		if ($this->num_rows() > 0)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Check if a string is valid to use for a username
	 *
	 * @param string $username
	 * @return bool true if valid
	 */
	function check_username($username)
	{
		return preg_match('/^[A-Za-z0-9_\-\.\@]*$/', $username);
	}

	/**
	 * This function adds a new user to the database.
	 * 
	 * @access public
	 * 
	 * @param string $user Array of all columns of table 'go_users'
	 * @param string $user_groups The user group id's the user will be member of
	 * @param string $visible_user_groups The user group id's where the user will be visible to
	 * @param string $modules_read The modules the user will have read permissions for
	 * @param string $modules_write The modules the user will have write permissions for
	 * @param string $acl	Some custom ACL id's the user will have access to (Be careful)

	 * 
	 * @return bool True on success
	 */

	function add_user(
	&$user,
	$user_groups=array(),
	$visible_user_groups=array(),
	$modules_read=array(),
	$modules_write=array(),
	$acl=array(),
	$send_invitation=false
	)
	{
		global $GO_CONFIG, $GO_LANGUAGE, $GO_SECURITY, $GO_GROUPS, $GO_MODULES, $GO_EVENTS, $lang;

		require_once($GO_LANGUAGE->get_language_file('users'));

		if(empty($user['username']) || empty($user['email']))
		{
			throw new Exception($lang['common']['missingField']);
		}

		// We check if we are able to add a new user. If we already have too much
		// of them we do not want new ones ;)
		if ( $this->max_users_reached() ) {
			throw new Exception($lang['users']['max_users_reached']);
		}

		if (!String::validate_email($user['email'])) {
			throw new Exception($lang['users']['error_email']);
		}


		if (!$this->check_username($user['username'])) {
			throw new Exception($lang['users']['error_username']);
		}

		// We check if a user with this email address already exists. Since the
		// email address is used as key for the acl_id, no two users may have the
		// same address. It also should not be possible to have multiple users
		// with the same name...
		if(!$GO_CONFIG->allow_duplicate_email)
		{
			$this->query( "SELECT email,username,id FROM go_users WHERE email='".$this->escape($user['email'])."' OR username='".$this->escape($user['username'])."'");
			if ($existing = $this->next_record()) {
				if($existing['email']==$user['email'])
					throw new Exception($lang['users']['error_email_exists']);
				else
					throw new Exception($lang['users']['error_username_exists']);
			}
		}		
		
		if(!isset($user['start_module']))
			$user['start_module']='summary';
		
		if(!isset($user['language']))
	 		$user['language'] = $GO_LANGUAGE->language;

	 		
		if(!isset($user['currency']))
	 		$user['currency'] = $GO_CONFIG->default_currency;
	 		
	 	if(!isset($user['decimal_separator']))
			$user['decimal_separator'] = $GO_CONFIG->default_decimal_separator;
			
		if(!isset($user['thousands_separator']))
			$user['thousands_separator'] = $GO_CONFIG->default_thousands_separator;
			
		if(!isset($user['time_format']))
			$user['time_format'] = $GO_CONFIG->default_time_format;
			
		if(!isset($user['date_format']))
			$user['date_format'] = $GO_CONFIG->default_date_format;
			
		if(!isset($user['date_separator']))
			$user['date_separator'] = $GO_CONFIG->default_date_separator;
		
		if(!isset($user['first_weekday']))
			$user['first_weekday'] = $GO_CONFIG->default_first_weekday;
			
		if(!isset($user['timezone']))
			$user['timezone'] = $GO_CONFIG->default_timezone;
		
		if(!isset($user['theme']))
			$user['theme'] = $GO_CONFIG->theme;
			
		if(!isset($user['max_rows_list']))
			$user['max_rows_list'] = 30;
			
		if(!isset($user['sex']))			
			$user['sex'] = 'M';

		if(!isset($user['sort_name']))
			$user['sort_name'] = 'last_name';



		if (empty($user['id'])){
			$user['id'] = $this->nextid("go_users");
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
		
		$user['auth_md5_pass']='';

		$user['registration_time'] = $user['mtime']=time();


		if(empty($user['password'])){
			$user['password']=$this->random_password();
		}
		
	
		
		$GO_EVENTS->fire_event('before_add_user', array($user));
		
		$unencrypted_password = $user['password'];
		if(!empty($user['password']))
		{
			$unencrypted_password = $user['password'];
			$user['password'] = md5($user['password']);
		}
	
		if(isset($GO_MODULES->modules['files']))
		{
			require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
			$files = new files();
			
			$usersdir = $files->resolve_path('users',true, 1);
			$admindir = $files->resolve_path('adminusers',true, 1);
		
			$files->mkdir($usersdir, $user['username'], $user['id'], $user['id'],true,'1','1');
			$folder = $files->mkdir($admindir, $user['username'], 1, 1,true,'1');
			if($folder)
				$user['files_folder_id']=$folder['id'];			
		}
		

		if ($user['id'] > 0 && $this->insert_row('go_users', $user))
		{
			
			$this->cache_user($user['id']);
			
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
				if($module)
				{
					$GO_SECURITY->add_user_to_acl($user['id'], $module['acl_id'], GO_SECURITY::READ_PERMISSION);
				}
			}

			foreach($modules_write as $module_name)
			{
				$module = $GO_MODULES->get_module($module_name);
				if($module)
				{
					$GO_SECURITY->add_user_to_acl($user['id'], $module['acl_id'], GO_SECURITY::WRITE_PERMISSION);
				}
			}

			foreach($acl as $acl_id)
			{
				if(!$GO_SECURITY->user_in_acl($user['id'], $acl_id))
				{
					$GO_SECURITY->add_user_to_acl($user['id'], $acl_id);
				}
			}
			
			$user['password']=$unencrypted_password;

			//delay add user event because name must be supplied first.
			if(!empty($user['first_name']))
			{
				$GO_EVENTS->fire_event('add_user', array($user));
			}

			if($send_invitation){
				require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
				require_once($GO_MODULES->modules['users']['class_path'].'users.class.inc.php');
				$users = new users();

				$email = $users->get_register_email();

				$swift = new GoSwift($user['email'], $email['register_email_subject']);
				foreach($user as $key=>$value){
					$email['register_email_body'] = str_replace('{'.$key.'}', $value, $email['register_email_body']);
				}

				$email['register_email_body']= str_replace('{url}', $GO_CONFIG->full_url, $email['register_email_body']);
				$email['register_email_body']= str_replace('{title}', $GO_CONFIG->title, $email['register_email_body']);
				$swift->set_body($email['register_email_body'],'plain');
				$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
				$swift->sendmail();
			}

			return $user['id'];
		} else {
			$GO_SECURITY->delete_acl( $user['id'] );
		}
	
		return false;
	}
	/**
	 * This function tells us if we exceeded the maximum number of users if set in
	 * config.php
	 * 
	 * @access public
	 * 
	 * @param void
	 * 
	 * @return bool
	 */
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
	/**
	 * This function deletes a user from the database.
	 * 
	 * @access public
	 * 
	 * @param int $user_id
	 * 
	 * @return bool
	 */
	function delete_user($user_id)
	{
		global $GO_CONFIG,$GO_SECURITY, $GO_EVENTS, $GO_GROUPS;

		if($user = $this->get_user($user_id))
		{
			$acl_id = $this->f("acl_id");
			$username = $this->f("username");
			$sql = "DELETE FROM go_users WHERE id='".$this->escape($user_id)."'";
			if ($this->query($sql))
			{
				$GO_GROUPS->delete_user($user_id);
				$GO_SECURITY->delete_acl($acl_id);
				$GO_SECURITY->delete_user($user_id);
				
				require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
				$search = new search();
				
				$search->delete_search_result($user_id, 8);		

				$args=array($user);
				
				$GO_EVENTS->fire_event('user_delete', $args);

				$sql = "DELETE FROM go_acl WHERE user_id=".$this->escape($user_id).";";
				$this->query($sql);

				return true;
			}
		}
		throw new Exception('An error has occured while deleting the user');
	}

	function increment_logins( $user_id ) {
		$sql =  "UPDATE go_users SET logins=logins+1, lastlogin='".time().
		"' WHERE id='$user_id'";
		$this->query( $sql );
	}
	
	/**
	 * This function generates a randomized password.
	 * 
	 * @access public
	 * 
	 * @param string $characters_allow
	 * @param string $characters_disallow
	 * @param int $password_length
	 * @param int $repeat
	 * 
	 * @return string
	 */
	function random_password( $characters_allow = 'a-z,1-9', $characters_disallow = 'i,o', $password_length = 0, $repeat = 0 ) {

		if($password_length==0)
		{
			global $GO_CONFIG;
			$password_length=$GO_CONFIG->default_password_length;
		}
		
		// Generate array of allowable characters.
		$characters_allow = explode(',', $characters_allow);
	
		for ($i = 0; $i < count($characters_allow); $i ++) {
			if (substr_count($characters_allow[$i], '-') > 0) {
				$character_range = explode('-', $characters_allow[$i]);
	
				for ($j = ord($character_range[0]); $j <= ord($character_range[1]); $j ++) {
					$array_allow[] = chr($j);
				}
			} else {
				$array_allow[] = $characters_allow[$i];
			}
		}
	
		// Generate array of disallowed characters.
		$characters_disallow = explode(',', $characters_disallow);
	
		for ($i = 0; $i < count($characters_disallow); $i ++) {
			if (substr_count($characters_disallow[$i], '-') > 0) {
				$character_range = explode('-', $characters_disallow[$i]);
	
				for ($j = ord($character_range[0]); $j <= ord($character_range[1]); $j ++) {
					$array_disallow[] = chr($j);
				}
			} else {
				$array_disallow[] = $characters_disallow[$i];
			}
		}
	
		mt_srand(( double ) microtime() * 1000000);
	
		// Generate array of allowed characters by removing disallowed
		// characters from array.
		$array_allow = array_diff($array_allow, $array_disallow);

		// Resets the keys since they won't be consecutive after
		// removing the disallowed characters.
		reset($array_allow);
		$new_key = 0;
		while (list ($key, $val) = each($array_allow)) {
			$array_allow_tmp[$new_key] = $val;
			$new_key ++;
		}

		$array_allow = $array_allow_tmp;
		$password = '';
		while (strlen($password) < $password_length) {
			$character = mt_rand(0, count($array_allow) - 1);

			// If characters are not allowed to repeat,
			// only add character if not found in partial password string.
			if ($repeat == 0) {
				if (substr_count($password, $array_allow[$character]) == 0) {
					$password .= $array_allow[$character];
				}
			} else {
				$password .= $array_allow[$character];
			}
		}
		return $password;
	}
	
/**
	 * When a global search action is performed this function will be called for each module
	 *
	 * @param int $last_sync_time The time this function was called last
	 */

	

	function cache_user($user_id)
	{
		global $GO_MODULES, $GO_CONFIG, $GO_LANGUAGE;
		
		require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
		$search = new search();

		require($GO_LANGUAGE->get_language_file('users'));

		$sql = "SELECT DISTINCT *  FROM go_users WHERE id=?";
		$this->query($sql, 'i', $user_id);
		$record = $this->next_record();
		if($record)
		{	
			$cache['id']=$this->f('id');
			$cache['user_id']=1;
			$cache['name'] = htmlspecialchars(String::format_name($this->f('last_name'),$this->f('first_name'),$this->f('middle_name')), ENT_QUOTES, 'utf-8');;
			$cache['link_type']=8;
			$cache['description']='';
			$cache['type']=$us_user;
			$cache['keywords']=$search->record_to_keywords($this->record).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			$cache['module']='users';
			$cache['acl_id']=$GO_MODULES->modules['users']['acl_id'];
			
			$search->cache_search_result($cache);
		}
	}
}
