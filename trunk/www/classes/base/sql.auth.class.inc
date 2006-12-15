<?php
/**
 * @copyright Copyright &copy; Intermesh 2003
 * @version $Revision: 1.19 $ $Date: 2006/04/11 12:35:15 $
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
 * @category Authentication
 */

/*
 * This file is overriding some of the functions that are defined in the
 * base_auth class. So we need to include this class.
 */
require_once( $GO_CONFIG->class_path.'base/base.auth.class.inc' );

/**
 * Implementation of GroupOffice Authentication.
 * 
 * This class provides the login-function for the Group-Office SQL database,
 * which is the default authentication mechanism.
 * 
 * @package Framework
 * @subpackage Usermanagement
 * @category Authentication
 * 
 * @access protected
 * 
 * @uses base_auth
 */
class sql_auth extends base_auth
{
	/**
	 * Authenticate the user against the Group-Office SQL database.
	 * 
	 * This function authenticates a given user and password against the SQL
	 * database. First it checks if the username and the given password are
	 * available inside the database. The it fetches the userid number of the
	 * found user. When an error (or authentication failure) occours, the
	 * function returns null.
	 * 
	 * @access private
	 * 
	 * @param string $username is the username we should authenticate.
	 * @param string $password is the user's password, we should use.
	 * 
	 * @return int the userid number of the given user if the authentication
	 * was successfull and we were able to fetch the ID, true if we were able
	 * to authenticate the user, but got no ID, and null if the authentication
	 * has failed.
	 */
	function authenticate( $username, $password ) {
		// Query the database for the given username with the associated
		// password. We only need to get the userid from the database, all
		// other columns are not interesting for the authentication.
		$sql = 'SELECT id FROM users WHERE ' .
				"username='$username' AND password='".md5($password)."' " .
				"AND enabled='1'";
		$this->query( $sql );

		// Check how many results we got from the search above. If we got more
		// than one result, something is wrong, and we should not authenticate
		// the given user.
		if ( $this->num_rows() != 1 ) {
			return null;
		}

		// Check if we got a valid result from the SQL database. Otherwise the
		// login has failed.
		if  ( !$this->next_record() ) {
			return null;
		}
		// Fetch the userid number from the database
		$user_id = $this->f('id');

		// Check if we were able to fetch an user_id. If we were not able, this
		// means that the authentication was successful, but the database has
		// no user_id number stored for the given user, so we return true.
		if ( $user_id == null ) {
			return true;
		}

		// Check if the userid number is valid. If it is not, the login should
		// fail.
		if ( $user_id < 1 ) {
			return null;
		}
		
		// There were not problems, so we can return the userid number.
		return $user_id;
	}

	/**
	 * This function adds a user to the user management system.
	 * 
	 * When the given user does not exist in the user management system he has
	 * to be added. This function adds a user to the UM-database, using all
	 * available user information that can be obtained from the user's LDAP
	 * account. When finished, this function returns the new uidnumber of this
	 * user.
	 * 
	 * @access private
	 * 
	 * @param string $username is the name of the user to add.
	 * @param string $password is the password needed to connect to the directory.
	 * @param array $params The authentication source specified in auth_sources.inc
	 * 
	 * @return int the userid number or null if the function has failed.
	 */
	function addToUM( $username, $password, $params ) {
		// Query the database for the given username with the associated
		// password.
		$sql = 'SELECT id FROM users WHERE ' .
				"username='$username' AND password='".md5($password)."' " .
				"AND enabled='1'";
		$this->query( $sql );

		// Fetch the user array from the database
		$this->next_record();
		$user = $this->Record;
		
		// We have to create a new id for this user, so that we can prevent
		// different users (from different authenticateion backends) with the
		// same ids.
		unset( $user['id'] );

		// Add the user to the user management system.
		$user_id = $GO_USERS->add_user( $user, 
				$GO_GROUPS->groupnames_to_ids($params['groups']), 
				$GO_GROUPS->groupnames_to_ids($params['visible_groups']), 
				$params['modules_read'], 
				$params['modules_write'] );

		return $user_id;
	}
}
?>