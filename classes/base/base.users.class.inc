<?php
/**
 * @copyright Copyright &copy; Intermesh 2003
 * @version $Revision: 1.44 $ $Date: 2006/11/14 12:56:56 $
 * 
 * @author Markus Schabel <markus.schabel@tgm.ac.at>
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 * @package Framework
 * @subpackage Usermanagement
 * @category Accounts
 */

/**
 * This is the base class of the user management files.
 * 
 * This class is abstract and should not used directly. It just defines all
 * methods the users classes should provide, but they are fully implemented in
 * the corresponding child classes, for examle sql.users.class.inc.
 *
 * @package Framework
 * @subpackage Usermanagement
 * @category Accounts
 * 
 * @access protected
 * 
 * @uses db
 */
class base_users extends db
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
	function base_users() {
		global $GO_CONFIG;
		
		if(!isset($_SESSION['GO_SESSION']['decimal_seperator']))
		{		
			$_SESSION['GO_SESSION']['decimal_seperator'] = $GO_CONFIG->default_decimal_seperator;
		}		
		if(!isset($_SESSION['GO_SESSION']['thousands_seperator']))
		{		
			$_SESSION['GO_SESSION']['thousands_seperator'] = $GO_CONFIG->default_thousands_seperator;
		}
		if(!isset($_SESSION['GO_SESSION']['date_seperator']))
		{		
			$_SESSION['GO_SESSION']['date_seperator'] = $GO_CONFIG->default_date_seperator;
		}		
		if(!isset($_SESSION['GO_SESSION']['date_format']))
		{		
			$_SESSION['GO_SESSION']['date_format'] = get_dateformat( $GO_CONFIG->default_date_format, $_SESSION['GO_SESSION']['date_seperator']);
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
		if(!isset($_SESSION['GO_SESSION']['DST']))
		{		
			$_SESSION['GO_SESSION']['DST'] = $GO_CONFIG->default_dst;
		}
		
		if(!isset($_SESSION['GO_SESSION']['mail_client']))
		{		
			$_SESSION['GO_SESSION']['mail_client'] = '0';
		}
		
		
		
		$this->db();
	}

	/**
	 * Authorize a user to view another user.
	 * 
	 * This function authorizes a user to view another user when his authorization
	 * request is accepted
	 * 
	 * @access public
	 * 
	 * @param int $requesting_user_id
	 * @param string $authcode
	 * @param int $accepting_user_id
	 * 
	 * @return bool
	 */
	function authorize( $requesting_user_id, $authcode, $accepting_user_id ) {
		return false;
	}

	/**
	 * This function fetches a list of countries from the database.
	 * 
	 * All available countries are stored in the database in the countries
	 * table. This method fetches a list of all countries from this table.
	 * 
	 * @access public
	 * 
	 * @return Integer the number of countries that are available.
	 */
	function get_countries() {
		$sql = 'SELECT * FROM countries ORDER BY name ASC';
		$this->query( $sql );
		return $this->num_rows();
	}

	/**
	 * This function fetches a given country from the database.
	 * 
	 * The given country is searched in the database, and if it is found,
	 * the according record is returned.
	 * 
	 * @access public
	 * 
	 * @param String $country_id is the identifier of the country.
	 * 
	 * @return array the SQL record for this country or false if it could not
	 * be found in the database.
	 */
	function get_country( $country_id ) {
		$sql = 'SELECT * FROM countries WHERE id="'.$country_id.'"';
		$this->query( $sql );
		if ( $this->next_record() ) {
			return $this->Record;
		}
		return false;
	}

	/**
	 * Get the supported search fields of the user management system.
	 * 
	 * For the different user management system implementations, there may be
	 * different available search fields - some systems may support more (or
	 * less) fields than other systems. This function should return a list of
	 * all supported fields.
	 * 
	 * @access public 
	 * 
	 * @return array that contains the names of the fields.
	 */
	function get_search_fields() {
		trigger_error(
			'get_search_fields() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

	/**
	 * This function searches for users with the given search field.
	 * 
	 * This function searches for a user using the given search field, and if
	 * the user exists, it returns the according record.
	 * 
	 * @access public
	 * 
	 * @param string $query The search query
	 * @param string $field The field to search on. Leave empty for all fields
	 * @param int $user_id The user_id used for permissions
	 * @param int $start Return results starting from this row
	 * @param int $offset Return this number of rows
	 * 
	 * @return array
	 */
	function search( $query, $field, $user_id, $start = 0, $offset = 0 ) {
		trigger_error(
			'search() is an abstract method.',
			E_USER_ERROR );
		return 0;
	}

	/**
	 * This function searches for a user by his ID.
	 * 
	 * This function returns all userdata based on the users ID.
	 * 
	 * @access public
	 * 
	 * @param int $user_id
	 * 
	 * @return array
	 */
	function get_user( $user_id ) {
		return false;
	}

	/**
	 * Fetch all users from the user management backend.
	 * 
	 * This function retrieves all users from the database and returns their
	 * number. After that you are able to process each user via next_record.
	 * 
	 * @todo Maybe another method than next_record should be used to iterate
	 * over the different user accounts. next_user() should be considered.
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
	function get_users( $sort = "name", $direction = "ASC", $start = 0, $offset = 0 ) {
		trigger_error(
			'get_users() is an abstract method.',
			E_USER_ERROR );
		return 0;
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
	function get_authorized_users( $user_id, $sort = "name", $direction = "ASC" ) {
		return 0;
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
	function get_user_by_email( $email ) {
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
	function get_user_by_username( $username ) {
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
	function check_password( $password ) {
		return false;
	}
	
	/**
	 * This function updates all userdata based on the given parameters.
	 *
	 * @todo re-unite update_user and update_profile
	 *  
	 * @access public
	 *
	 * @param string $user Array of all columns of table 'users'
	 * @param string $user_groups The user groups the user will be member of
	 * @param string $visible_user_groups The user groups where the user will be visible to
	 * @param string $modules_read The modules the user will have read permissions for
	 * @param string $modules_write The modules the user will have write permissions for
	 *
	 * @return bool True on success
	 */
	function update_user( $user, 
		$user_groups=array(), 
		$visible_user_groups=array(), 
		$modules_read=array(), 
		$modules_write=array())
	{
		return false;
	}

	/**
	 * This function updates a the profile of a user.
	 * 
	 * Using an SQL update record, this function actualizes the profile of the
	 * given user.
	 * 
	 * @todo this function is deprecated, it should not be used. But as long as
	 * there are child classes which implement it, it is defined here.
	 * 
	 * @access protected
	 * 
	 * @param Array $user is an array of all data that should be updated.
	 * 
	 * @return Boolean to indicate the success of the operation.
	 */
	function update_profile( $user ) {
		trigger_error(
			'update_profile() is deprecated.',
			E_USER_WARNING );
		return false;
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
	function update_password( $user_id, $password ) {
		return false;
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
	function update_session( $user_id ) {
		global $GO_LANGUAGE, $GO_CONFIG;
		if ($userdata = $this->get_user($user_id)) {
			$middle_name = $userdata['middle_name'] == '' ? '' : $userdata['middle_name'].' ';
			$GO_LANGUAGE->set_language($userdata['language']);

			$_SESSION['GO_SESSION']['user_id'] = $user_id;
			
			//It session could be stolen from another host!
			$_SESSION['DIR_CHECK'] = $GO_CONFIG->root_path;
			
			$_SESSION['GO_SESSION']['username'] = $userdata['username'];
			$_SESSION['GO_SESSION']['authcode'] = $userdata['authcode'];
			$_SESSION['GO_SESSION']['name'] = $userdata['first_name'].' '.$middle_name.$userdata['last_name'];
			$_SESSION['GO_SESSION']['first_name'] = $userdata['first_name'];
			$_SESSION['GO_SESSION']['middle_name'] = $userdata['middle_name'];
			$_SESSION['GO_SESSION']['last_name'] = $userdata['last_name'];
			$_SESSION['GO_SESSION']['country_id'] = $userdata['country_id'];
			$_SESSION['GO_SESSION']['email'] = $userdata['email'];

			$_SESSION['GO_SESSION']['thousands_seperator'] = $userdata['thousands_seperator'];
			$_SESSION['GO_SESSION']['decimal_seperator'] = $userdata['decimal_seperator'];
			$_SESSION['GO_SESSION']['date_format'] = get_dateformat($userdata['date_format'], $userdata['date_seperator']);
			$_SESSION['GO_SESSION']['date_seperator'] = $userdata['date_seperator'];
			$_SESSION['GO_SESSION']['time_format'] = $userdata['time_format'];
			$_SESSION['GO_SESSION']['currency'] = $userdata['currency'];
			$_SESSION['GO_SESSION']['mail_client'] = $userdata['mail_client'];
			$_SESSION['GO_SESSION']['lastlogin'] = isset ($userdata['lastlogin']) ? $userdata['lastlogin'] : get_gmt_time();
			$_SESSION['GO_SESSION']['max_rows_list'] = $userdata['max_rows_list'];
			$_SESSION['GO_SESSION']['timezone'] = $userdata['timezone'];
			$_SESSION['GO_SESSION']['start_module'] = isset ($userdata['start_module']) ? $userdata['start_module'] : 'summary';
			$_SESSION['GO_SESSION']['DST'] = $userdata['DST'];

			$_SESSION['GO_SESSION']['theme'] = $userdata['theme'];
			$_SESSION['GO_SESSION']['first_weekday'] = $userdata['first_weekday'];
			$_SESSION['GO_SESSION']['sort_name'] = !empty($userdata['sort_name']) ? $userdata['sort_name'] : 'first_name';
			$_SESSION['GO_SESSION']['use_checkbox_select'] = $userdata['use_checkbox_select'];
			return true;
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
	function email_exists( $email ) {
		return true;
	}

	/**
	 * This function adds a new user to the database.
	 * 
	 * @access public
	 * 
	 * @param string $user Array of all columns of table 'users'
	 * @param string $user_groups The user group id's the user will be member of
	 * @param string $visible_user_groups The user group id's where the user will be visible to
	 * @param string $modules_read The modules the user will have read permissions for
	 * @param string $modules_write The modules the user will have write permissions for
	 * @param string $acl	Some custom ACL id's the user will have access to (Be carefull)

	 * 
	 * @return bool True on success
	 */
	function add_user(
		$user, 
		$user_groups=array(), 
		$visible_user_groups=array(), 
		$modules_read=array(), 
		$modules_write=array(),
		$acl=array()) {
		return false;
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
	function delete_user( $user_id ) {
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
	function max_users_reached() {
		return true;
	}

	/**
	 * This function set's the preferences of the user.
	 * 
	 * @access public
	 * 
	 * @param int $user_id
	 * @param string $date_format
	 * @param string $time_format
	 * @param char $thousands_seperator
	 * @param char $decimal_seperator
	 * @param char $currency
	 * @param bool $mail_client
	 * @param int $max_rows_list
	 * @param string $timezone_offset
	 * @param string $DST
	 * @param string $start_module
	 * @param string $language
	 * @param string $theme
	 * @param string $first_weekday
	 * 
	 * @return bool True on success
	 */
	function set_preferences( $user_id, $date_format, $time_format, $thousands_seperator, $decimal_seperator, $currency, $mail_client, $max_rows_list, $timezone_offset, $DST, $start_module, $language, $theme, $first_weekday ) {
		return false;
	}

	/**
	* This function stores the user's language
	* 
	* @access public
	* 
	* @param int $user_id
	* @param string $language
	* 
	* @return bool True on success
	*/
	function set_language( $user_id, $language ) {
		return false;
	}

	/**
	 * This function stores the user's start module
	 * 
	 * @access public
	 * 
	 * @param int $user_id
	 * @param string $module_id
	 * 
	 * @return bool
	 */
	function set_start_module( $user_id, $module_id ) {
		return false;
	}

	/**
	 * This function stores the user's notation settings.
	 * 
	 * @access public
	 * 
	 * @param int $user_id
	 * @param string $date_format
	 * @param string $time_format
	 * @param char $thousands_seperator
	 * @param char $decimal_seperator
	 * @param char $currency
	 * @param string $timezone_offset
	 * @param string $DST
	 * @param string $first_weekday
	 * @param string $sort_name
	 * 
	 * @return bool
	 */
	function set_notations( $user_id, $date_format, $time_format, $thousands_seperator, $decimal_seperator, $currency, $timezone_offset, $DST, $first_weekday, $sort_name ) {
		return false;
	}

	/**
	 * This function stores the user's look&feel settings.
	 * 
	 * @access public
	 * 
	 * @param int $user_id
	 * @param int $max_rows_list
	 * @param string $start_module
	 * @param string $language
	 * @param string $theme
	 * 
	 * @return bool
	 */
	function set_look_and_feel( $user_id, $max_rows_list, $start_module, $language, $theme ) {
		return false;
	}

	/**
	 * This function updates the number of logins of the user in the database.
	 * 
	 * @access public
	 * 
	 * @param int $user_id
	 * 
	 * @return bool
	 */
	function increment_logins( $user_id ) {
		return false;
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
	function random_password( $characters_allow = 'a-z,1-9', $characters_disallow = 'i,o', $password_length = 8, $repeat = 0 ) {
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
}
