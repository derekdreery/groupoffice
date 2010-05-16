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
 * Implementation of GroupOffice Authentication. This class provides the 
 * login-function for the Group-Office SQL database,
 * which is the default authentication mechanism.
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id$
 * @package go.basic
 * @since Group-Office 2.17
 * 
 */

class GO_AUTH extends db
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
	function authenticate($username, $password) {
		// Query the database for the given username with the associated
		// password. We only need to get the userid from the database, all
		// other columns are not interesting for the authentication.
		

		$sql = "SELECT * FROM go_users WHERE username='".$this->escape($username)."'";
		$this->query($sql);

		// Check if we got a valid result from the SQL database. Otherwise the
		// login has failed.

		$user = $this->next_record();

		if  (!$user) {
			return false;
		}

		//We used to use MD5 but we changed it to crypt 
		if($user['password_type']=='crypt'){
			if(crypt($password, $user['password']) != $user['password']){
				return false;
			}
		}else
		{
			//pwhash is not set yet. We're going to use the old md5 hashed password
			if(md5($password)!=$user['password']){
				return false;
			}else
			{
				//clear old md5 hash and set new pwhash for improved security.
				$u['id']=$user['id'];
				$u['password']=crypt($password);
				$u['password_type']='crypt';

				$this->update_row('go_users', 'id', $u);
			}
		}
		
		// There were not problems, so we can return the userid number.
		return $user;
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
	
	function addToUM( $username, $password, $params ) {
		// Query the database for the given username with the associated
		// password.
		$sql = 'SELECT id FROM go_users WHERE ' .
				"username='".$this->escape($username)."' AND password='".md5($password)."' " .
				"AND enabled='1'";
		$this->query( $sql );

		// Fetch the user array from the database
		$this->next_record();
		$user = $this->record;
		
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
	} */
	
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
	function updateAfterLogin($user, $count_login=true) {
		global $GO_SECURITY, $GO_MODULES, $GO_USERS,$GO_CONFIG;
		// Tell the security framework that a user has been logged in. The
		// security framework takes care on setting the userid as active.
		$GO_SECURITY->logged_in($user);

		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();

		// Increment the number of logins of the given user.
		if($count_login){
			$GO_USERS->increment_logins($user['id']);

			//clean temp dir only when counting the login
			//logins are not counted for example when a synchronization is done.
			//We also don't want to clear the temp dir in that case because that can
			//screw up an active session in the browser.			
			if(is_dir($GO_CONFIG->tmpdir.$user['id'].'/'))
			{
				$fs->delete($GO_CONFIG->tmpdir.$user['id'].'/');
			}
		}
		$fs->mkdir_recursive($GO_CONFIG->tmpdir.$user['id'].'/');
		//reinitialise available modules
		$GO_MODULES->load_modules();
	}

	function bad_login($username){
		///$sql = "UPDATE go_users SET failed_login_attempts=failed_login_attempts+1 WHERE username='".$this->escape($username)."'";
		//return $this->query($sql);
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
	function login($username, $password, $type='normal', $count_login=true) {
		// This variable is used to fetch the user's profile from the current
		// user management backend database.
		global $GO_USERS, $GO_EVENTS, $GO_SECURITY;

		$args = array(&$username, &$password);
		$GO_EVENTS->fire_event('before_login', $args);

		// This variable is used to set the id of the user that is currently
		// logged in. Since we try to login a (maybe new) user, we have to
		// clear the active user from the session.

		$GO_SECURITY->user_id = 0;

		// Authenticate the user.
		$user = $this->authenticate($username, $password, $type);
		// Check if the authentication was successful, otherwise exit.
		if (!$user) {
			$this->bad_login($username);
			go_debug('Wrong password entered for '.$username);
		}

		if($user['enabled']!=1){
			go_debug('Login attempt for disabled user '.$username);
			$user=false;
		}

		if(!$user)
		{
			//sleep for 3 seconds to slow down brute force attacks
			sleep(3);
			return false;
		}
		
		// Actualise session and other necessary things.
		$this->updateAfterLogin($user,$count_login);

		go_debug('LOGIN Username: '.$username.'; IP: '.$_SERVER['REMOTE_ADDR']);
		$args=array($username, $password, $user);
		$GO_EVENTS->fire_event('login', $args);

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
