<?php
/**
 * @copyright Copyright &copy; Intermesh 2003
 * @version $Revision: 1.29 $ $Date: 2006/04/23 21:28:47 $
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
 * We have to declare that $GO_CONFIG is a global variable, because it cannot
 * be guaranteed that this file is included from a global context. It should,
 * but the current implementation of phpUnit loads this file dynamically from
 * inside a function, so that it doesn't find this variable otherwise.
 */
global $GO_CONFIG;

/*
 * This file is overriding some of the functions that are defined in the
 * base_auth class. So we need to include this class.
 */
require_once( $GO_CONFIG->class_path.'base/base.auth.class.inc' );

/**
 * Implementation of LDAP Authentication.
 * 
 * This class provides the login-function for an LDAP based authentication,
 * it is possible to use it with SQL usermanagement and LDAP usermanagement.
 * 
 * @package Framework
 * @subpackage Usermanagement
 * @category Authentication
 * 
 * @access protected
 * 
 * @uses base_auth
 */
class ldap_auth extends base_auth
{
	/**
	 * Find the DN of the user in LDAP.
	 * 
	 * This function returns a string that represents the DN under which the
	 * given username is stored in LDAP.
	 * 
	 * @todo At the moment the search is hardcoded for the uid attribute. To
	 * enable support for other LDAP enabled directories, it is possible to
	 * implement other search filters here (and also in some other functions).
	 * 
	 * @access private
	 * 
	 * @param string $username is the username, we try to find the DN for.
	 * 
	 * @return string the DN we found or null if we didn't find anything.
	 */
	function getDNfromUsername( $username ) {
		// For accessing an LDAP directory, we need the LDAP functions, which
		// are defined inside the global $GO_LDAP object.
		global $GO_LDAP;

		// Search for the user inside the DN where the accounts are stored.
		$GO_LDAP->search( 'uid='.$username, $GO_LDAP->PeopleDN );

		// Check how many entries we got from this search. If we got more or
		// less than one entry, there's something wrong, so we cannot give a
		// valid and unique DN for this username.
		if ( $GO_LDAP->num_entries() != 1) {
			return null;
		}

		// Test if we found an entry, and if we did, fetch it. 
		if ( $GO_LDAP->next_entry() ) {
			// Return the DN of the entry we found.
			$dn = $GO_LDAP->dn();
			return $dn;
		}

		// We didn't find anything, so return null to indicate this.
		return null;
	}

	/**
	 * Authenticate the user against the LDAP directory.
	 * 
	 * This function authenticates a given user and password against the LDAP
	 * directory. First it searches if the user exists in the directory, and
	 * if the user could be found, we try to connect to LDAP using the user's
	 * DN and the given password. When this works, we return true to indicate
	 * our success. Otherwise we return false.
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
		// For authentication against an LDAP directory, we need the LDAP
		// functions, which are defined inside the $GO_LDAP object.
		global $GO_LDAP;
		
		// First we try to find the DN of the given username. If we don't get
		// a valid DN for this user, we can assume that the user doesn't exist
		// and return null.
		$dn = $this->getDNfromUsername( $username );
		if ( $dn == null ) {
			return false;
		}

		/*
		 * Authenticate the given user against the LDAP directory.
		 */
		if ( !@$GO_LDAP->bind( $dn, $password ) ) {
			return false;
		}

		return true;
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
		// When this function is called, this means that we are using LDAP as
		// authentication source, but NOT as user management database. So we
		// can fetch the user's profile from the directory using the LDAP user
		// class.
		$GO_LDAP_USERS = new ldap_users();

		// Fetch the user's profile.
		$user = $GO_LDAP_USERS->get_user_by_username( $username );

		// We have to create a new id for this user, so that we can prevent
		// different users (from different authenticateion backends) with the
		// same ids.
		unset( $user['id'] );

		// Add the user to the user management system.
		global $GO_USERS, $GO_GROUPS;
		$user_id = $GO_USERS->add_user( $user, 
				$GO_GROUPS->groupnames_to_ids($params['groups']), 
				$GO_GROUPS->groupnames_to_ids($params['visible_groups']), 
				$params['modules_read'], 
				$params['modules_write'] );

		return $user_id;
	}
}
?>
