<?php
/**
 * @copyright Copyright &copy; Intermesh 2003
 * @version $Revision: 1.13 $ $Date: 2006/10/24 20:56:10 $
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

/**
 * Basic functions for authentication mechanisms.
 * 
 * This class provides generale functions that are used for the Group-Office
 * authentication system. The functions defined in this class are either used
 * by the different authentication backends, or overridden by them.
 * 
 * @package Framework
 * @subpackage Usermanagement
 * @category Authentication
 * 
 * @access protected
 * 
 * @uses db
 */
class base_auth extends db
{
	/**
	 * Authenticate the user. To be overridden by authentication classes.
	 * 
	 * This function authenticates a given user and password against the used
	 * authentication system. This function is abstract and has to be written
	 * for each different authentication system (back-end).
	 * When the given username and password are valid, the function should
	 * return the user's userid number, otherwise null.
	 * 
	 * @access private
	 * 
	 * @param string $username is the username we should authenticate.
	 * @param string $password is the user's password, we should use.
	 * 
	 * @return boolean true if the authentication was successful, and false if
	 * the authentication has failed.
	 */
	function authenticate( $username, $password ) {
		return false;
	}

	/**
	 * This function adds a user to the user management system.
	 * 
	 * When the given user does not exist in the user management system he has
	 * to be added. This function adds a user to the UM-database, using all
	 * available user information that can be fetched from the authentication
	 * source.
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
		return null;
	}

	/**
	 * Actualise session, increment logins and check WebDAV status.
	 * 
	 * This function is executed when the authentication was successful, and
	 * is used to set the necessary session variables, inform the security
	 * framework that the user has been logged in, checks the permissions for
	 * WebDAV and increments the login count of the user.
	 * 
	 * @access private
	 * 
	 * @param int $user_id is the userid number of the user that has been
	 * authenticated successfully.
	 */
	function updateAfterLogin( $user_id ) {
		global $GO_SECURITY, $GO_MODULES;
		// Tell the security framework that a user has been logged in. The
		// security framework takes care on setting the userid as active.
		$GO_SECURITY->logged_in( $user_id );

		global $GO_USERS;
		// Update the current session with the user's profile information.
		$GO_USERS->update_session( $user_id );
		// Increment the number of logins of the given user.
		$GO_USERS->increment_logins( $user_id );

		global $GO_CONFIG;
		// Check if WebDAV support is enabled, and if it is, check the state
		// of the given user. check_login() need's the username (not the id),
		// so we fetch the username from the (already registered) session.
		if ( $GO_CONFIG->dav_switch ) {
			global $GO_DAV;
			$username = $_SESSION['GO_SESSION']['username'];
			$GO_DAV->check_login( $username );
		}
		
		//reinitialise available modules
		$GO_MODULES->load_modules();
	}

	/**
	 * This function logs a user in
	 * 
	 * This function tries to authenticate a given username against the used
	 * authentication backend (using the authenticate() function of the active
	 * backend - that means from the used child class from this class).
	 * The authentication may have two results: successful or failed:
	 * * failed: when the authentication was not possible (the reason doesn't
	 *   matter), this method returns false to indicate the failure.
	 * * successful: when the authentication was successful, the method checks
	 *   if the authenticated user exists in the currently used user management
	 *   database. If the user doesn't exist there, it is added.
	 * 
	 * When the user exists in the user management database from the beginning,
	 * the method checks if the account is enabled.
	 * 
	 * Only when the account is in the user management database and is enabled,
	 * then the user is registered in the session (using the updateAfterLogin()
	 * method) and the function will return true to indicate that the login was
	 * successful.
	 *
	 * @access public
	 * 
	 * @param string $username
	 * @param string $password
	 * @param array $params The authentication source specified in auth_sources.inc
	 * 
	 * @return bool true if the login was possible, false otherwise.
	 */
	function login( $username, $password, $params=array() ) {
		// This variable is used to fetch the user's profile from the current
		// user management backend database.
		global $GO_USERS;

		// This variable is used to set the id of the user that is currently
		// logged in. Since we try to login a (maybe new) user, we have to
		// clear the active user from the session.
		global $GO_SECURITY;
		// TODO do not access the user_id of the security class directly. For
		// better encapsulation there should be a wrapper function in the
		// security class.
		$GO_SECURITY->user_id = 0;

		// Authenticate the user.
		$user_id = $this->authenticate( $username, $password );
		// Check if the authentication was successful, otherwise exit.
		if ( $user_id == null ) {
			go_log(LOG_DEBUG, 'Wrong password entered for '.$username);
			return false;
		}

		// Check if the given user can be found in the user management system.
		$user = $GO_USERS->get_user_by_username( $username );
		if ( $user == null ) {
			// If the user was not found, we have to add it.
			$user_id = $this->addToUM( $username, $password, $params );
			// Check if we were able to add the user to the database. If we
			// were not able, this function should fail here.
			if ( $user_id == null ) {				
				go_log(LOG_DEBUG, 'BAD LOGIN Username: '.$username.'; IP: '.$_SERVER['REMOTE_ADDR'].' User-Agent: '.$_SERVER['HTTP_USER_AGENT']);
				return false;
			}
		} else {
			// The user exists in the user management system. So we have to
			// check, if his account is enabled. If it isn't, the login should
			// fail.
			if ( $user['enabled'] != 1 ) {
				return false;
			}
			// The user was found and is enabled, so we can get the user_id
			// from the user's profile.
			
			go_log(LOG_DEBUG, 'LOGIN Username: '.$username.'; IP: '.$_SERVER['REMOTE_ADDR']);
			
			$user_id = $user['id'];
		}

		// Actualise session and other necessary things.
		$this->updateAfterLogin( $user_id );

		return true;
	}

	/**
	 * Check if a given user is enabled.
	 * 
	 * This function checks, if a given user is enabled (allowed to login) and
	 * return a regarding boolean value.
	 * 
	 * @access public
	 * 
	 * @param int $user_id is the userid number the function should check.
	 * 
	 * @return bool true if the user is enabled, false otherwise.
	 */
	function is_enabled( $user_id ) {
		global $GO_USERS;
		// The status of the user is stored inside the user management system,
		// so we need to fetch the user's profile from the user manager.
		$user = $GO_USERS->get_user( $user_id );
		
		// Check if the user's enabled attribute is set.
		if ( $user['enabled'] == '1' ) {
			return true;
		}

		return false;
	}
}
