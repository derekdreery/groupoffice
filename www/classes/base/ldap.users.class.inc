<?php
/**
 * @copyright Copyright &copy; Intermesh 2003
 * @version $Revision: 1.41 $ $Date: 2006/05/02 15:31:26 $
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

/*
 * This file is currently using the profiles class. However, we are working on
 * it, so that these methods are merged into this class, so that we can drop the
 * profiles  class.
 */
require_once($GO_CONFIG->class_path.'profiles.class.inc');

/*
 * This file is overriding some of the functions that are defined in the
 * base_users class. So we need to include this class.
 */
require_once($GO_CONFIG->class_path.'base/base.users.class.inc');

/**
 * Implementation of LDAP User-Management.
 * 
 * This class provides the full user-management functionality.
 * @todo add a better comment ;-)
 * 
 * @package Framework
 * @subpackage Usermanagement
 * @category Accounts
 * 
 * @access protected
 * 
 * @uses base_users
 */
class ldap_users extends base_users {
	var $user_id;
	var $profile;
	var $userlist;
	var $userlist_index;
	
	/**
	 * This variable stores the SQL column to LDAP attribute mappings.
	 */
	var $mapping = null;

	/**
	 * Initialize database connection, bind to directory, load mappings.
	 * 
	 * This function initializes the SQL database connection, binds to the
	 * LDAP directory and loads the SQL column to LDAP attribute mappings
	 * from the 'users.ldap.mapping.inc' file.
	 * 
	 * @access public
	 */
	function ldap_users() {
		global $GO_CONFIG;
		$this->base_users();
		
		/*
		 * Include the SQL column / LDAP attribute mapping file and initialize
		 * the local mapping variable with the variable that is defined in this
		 * mapping file ($users_ldap_mapping) - to prevent IDEs from displaying
		 * an error about an uninitialized variable, we define it first.
		 */
		$users_ldap_mapping = array();
		include_once( $GO_CONFIG->root_path.$GO_CONFIG->slash.'lib'.
			$GO_CONFIG->slash.'ldap'.$GO_CONFIG->slash.
			'users.ldap.mapping.inc' );
		$this->mapping = $users_ldap_mapping;

		/*
		 * TODO: Probably we could connect with our own ldap-uid and not as
		 * admin (or whatever is configured in GO as rootdn), so that we
		 * definitely can only see what we are allowed by LDAP access rights.
		 * So we cannot change attributes of other users.
		 * Probably administrator should bind with rootdn.
		 */
		//$this->ldap->bind($_SESSION['GO_SESSION']['user'], $_SESSION['GO_SESSION']['password']);
	}

	/**
	 * Get the supported search fields of the ldap user management system.
	 * 
	 * The LDAP user management system currently only supports searching by the
	 * name of a user. Additional search fields need an updated search() method
	 * - each search field has to be implemented in the search() function.
	 * 
	 * @access public 
	 * 
	 * @return array that contains the names of the fields.
	 */
	function get_search_fields() {
		$searchfields[] = array( 'name', "Name" );
		return $searchfields;
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
	function search( $query, $field, $user_id, $start=0, $offset=0 ) {
		global $GO_LDAP;

		$query = substr( $query, 1, strlen( $query ) - 2 );

		switch( $field ) {
			default:
			case 'name':
				$filter = '(&(cn=*'.utf8_encode( $query ).'*)(mail=*))';
				break;
		}

		$GO_LDAP->search( $filter, $GO_LDAP->PeopleDN );
		$GO_LDAP->sort( 'sn' );

		$entries = $GO_LDAP->get_entries();
		for ( $i=0; $i<$entries['count']; $i++ ) {
			$this->userlist[] = $this->convertEntryToRecord( $entries[$i] );
		}

		$this->userlist_index = 0;
		return count( $this->userlist );
	}

	/**
	 * Fetch all users from the user management backend.
	 * 
	 * This function retrieves all users from the directory and returns their
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
	function get_users( $sort='name', $direction='ASC', $start=0, $offset=0 ) {
		$user_id = $GO_SECURITY->user_id;
		return $this->search( '', 'name', $user_id, $start, $offset );
	}

	/**
	 * This method iterates over the results from a search.
	 * 
	 * This method is used to fetch the next result from the result array that
	 * was generated by the last search. When the last user has been returned
	 * and not additional accounts are available, this method returns null to
	 * indicate the end of the list. Otherwise it returns the current entry as
	 * an assoziative array.
	 * 
	 * @access public
	 * 
	 * @return Array is the current entry of the result set, or null if there
	 * are no unreturned entries.
	 */
	function next_record() {
		if ( count( $this->userlist ) > $this->userlist_index ) {
			$this->Record = $this->userlist[$this->userlist_index++];
			return $this->Record;
		}
		return false;
	}

	/**
	 * Check if the given password is valid for the current user.
	 * 
	 * This function checks if the given password is a valid password for the
	 * currently active user. To check this, the method rebinds to the LDAP
	 * server.
	 * 
	 * @access public
	 * 
	 * @param String $password is the password that should be validated.
	 * 
	 * @return Boolean if the password is valid or not.
	 */
	function check_password( $password ) {
		global $GO_LDAP;
		// If it is not possible to rebind, the password is false. This is the
		// default value.
		$validPassword = false;
		// The dn we have to use to bind to the directory is constructed of the
		// user id of the currently active user, and the DN where the accounts
		// are stored.
		$dn = 'uid='.$_SESSION['GO_SESSION']['user_id'].','.$GO_LDAP->PeopleDN;
		// Try to bind with the DN of the active user and the given password.
		if ( $GO_LDAP->bind( $dn, $password ) ) {
			// When the bind was successful, the password is valid.
			$validPassword = true;
		}
		// If the bind was not successful we have to rebind. If the bind was
		// successful this should not be necessary, but it doesn't harm.
		$GO_LDAP->bind();
		// Return weather the bind was successful or not.
		return $validPassword;
	}

	/**
	 * This function returns all userdata based on the email address.
	 * 
	 * @access public
	 * 
	 * @param string $mail
	 * 
	 * @return array The user profile
	 */
	function get_user_by_email( $email ) {
		return $this->get_user_by_search( 'mail='.$email );
	}

	/**
	 * This function returns all userdata based on the uidNumber.
	 * 
	 * @access public
	 * 
	 * @param string $uidNumber
	 * 
	 * @return array The user profile
	 */
	function get_user( $uidNumber ) {
		return $this->get_user_by_search( 'uidNumber='.$uidNumber );
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
		return $this->get_user_by_search( 'uid='.$username );
	}

	/**
	 * This function returns all userdata based on a valid LDAP search filter.
	 * 
	 * Since there are some functions, that fetch a user from the directory, but
	 * use different searches, we've moved this functionality to a new function.
	 * This function needs a valid LDAP search filter, and retrieves the user
	 * that matches this filter - if, and only if there is only one user that
	 * matches.
	 * 
	 * @access private
	 * 
	 * @param string $filter is the search filter used to fetch the entry.
	 * 
	 * @return array The user profile
	 */
	function get_user_by_search( $search ) {
		// For accessing an LDAP directory, we need the LDAP functions, which
		// are defined inside the global $GO_LDAP object.
		global $GO_LDAP;

		// Search for the user inside the DN where the accounts are stored.
		$GO_LDAP->search( $search, $GO_LDAP->PeopleDN );

		// Check how many entries we got from this search. If we got more or
		// less than one entry, there's something wrong, because we cannot
		// identify the user.
		if ( $GO_LDAP->num_entries() != 1) {
			$this->Record = null;
			return null;
		}

		// Fetch the entry from the directory.
		$entry = $GO_LDAP->get_entries();

		// Take the entry and convert it to a SQL-Style row.
		$this->Record = $this->convertEntryToRecord( $entry[0] );

		// Return the converted entry.
		return $this->Record;
	}

	/**
	 * Check if a given mail address already exists.
	 * 
	 * This method checks if a given mail address exists in the directory. It is
	 * necessary to check the mail attributes and the alternateMailAddress
	 * attributes of the accounts and the mail attributes of the groups. Only
	 * when the mail address does not exist everywhere it is save to identify it
	 * as free.
	 * 
	 * @access public
	 * 
	 * @param String $email is the address that should be checked.
	 * 
	 * @return Boolean whather this address is already in use or not.
	 */
	function email_exists( $email ) {
		global $GO_LDAP;
		// First we check the accounts
		$filter = '(|(mail='.$email.')(alternateMailAddress='.$email.'))';
		$GO_LDAP->search( $filter, $GO_LDAP->PeopleDN, array() );
		if ( $GO_LDAP->num_entries() > 0 ) {
			return true;
		}
		$filter = '(mail='.$email.')';
		$GO_LDAP->search( $filter, $GO_LDAP->GroupsDN, array() );
		if ( $GO_LDAP->num_entries() > 0 ) {
			return true;
		}
		return false;
	}

	/**
	 * Convert an LDAP entry to an SQL record.
	 * 
	 * This function takes an LDAP entry, as you get from ldap_fetch_entries()
	 * and converts this entry to an SQL result record. It is used to convert
	 * the account data that is stored in the directory server to an SQL style
	 * result as is expected from the framework.
	 * The mapping of table-columns to ldap-attributes is included from the
	 * users.ldap.mapping file (which is located in the lib/ldap directory),
	 * which is loaded from the constructor in this class. The name of this
	 * file can be overridden in the configuration.
	 * 
	 * @access private
	 * 
	 * @param $entry is the LDAP entry that should be converted.
	 * 
	 * @return Array is the converted entry.
	 */
	function convertEntryToRecord( $entry ) {
		global $GO_SECURITY, $GO_CONFIG, $GO_LDAP, $GO_GROUPS;

		/*
		 * If the user is not member of the everyone group, he should be added
		 * if ( !$GO_GROUPS->is_in_group( $entry["uidnumber"][0], $GO_CONFIG->group_everyone ) ) {
		 * 		$GO_GROUPS->add_user_to_group( $entry["uidnumber"][0], $GO_CONFIG->group_everyone );
		 * }
		 */

		$row = array();
		/*
		 * Process each SQL/LDAP key pair of the mapping array, so that we can
		 * fetch all values that are needed for each SQL key.
		 */
		foreach ( $this->mapping as $key => $ldapkey ) {
			/*
			 * If the ldapkey is undefined, we don't know any attributes that
			 * match the specifiy SQL column, so we can leave it empty.
			 */
			if ( $ldapkey == '' ) {
				$row[$key] = '';
				continue;
			}

			/*
			 * Check if this is already a new mapping - if the data type is not
			 * a string, we can savely assume that it is a ldap_user_mapping
			 * object, so we can directly execute the generic method.
			 */
			if ( !is_string( $ldapkey ) ) {
				if ( $ldapkey->isFunction() ) {
					$myMethod = $ldapkey->value;
					$row[$key] = $myMethod( $entry );
					continue;
				}
				if ( $ldapkey->isConstant() ) {
					$row[$key] = $ldapkey->value;
					continue;
				}
				if ( $ldapkey->isDirect() ) {
					$row[$key] = utf8_decode($entry[$ldapkey->value][0]);
					continue;
				}
			}

			/*
			 * All other ldapkeys have no special meaning, so we can directly
			 * fetch them from the entry, and use them as values. If the
			 * attribute is emtpy, we return an empty column.
			 */
			if ( isset( $entry[$ldapkey] ) ) {
				$value = utf8_decode( $entry[$ldapkey][0] );
			} else {
				$value = '';
			}
			if ( !$value ) {
				$value = '';
			}
			$row[$key] = $value;
		}

		/*
		 * We have processed all mapping fields and created our SQL result
		 * array. So we can return it.
		 */
		return $row;
	}

	/**
	 * Get the acl_id that corresponds to the given email address.
	 * 
	 * This function fetches the acl_id that belongs to the user with the given
	 * email address. When there is no matching acl_id available, a new one is
	 * created and the user is set as the owner of this new acl_id.
	 * This function is for internal use only and should only be called by the
	 * convertEntryToRecord() method.
	 * 
	 * @access private
	 * 
	 * @param String $mail is the email address of the user. It is the unique
	 * identifier for an users access list.
	 * @param Integer $uidnumber is the ID of the user. This parameter is only
	 * used, when the acl_id does not exist and has to be created.
	 * 
	 * @return Integer the access list that was found or created. null if an
	 * error occured.
	 */
	function getOrCreateAclId( $mail, $uidnumber ) {
		global $GO_SECURITY;

		// Check if we got an email address. There is no way to fetch an acl_id
		// without the according mail address.
		if ( !$mail ) {
			return null;
		}

		// Check if there exists an access list for the given account.
		$sql = 'SELECT * FROM acl_items WHERE description="'.$mail.'"';
		db::query( $sql );

		// If there is an access list, we return it's ID.
		if ( db::next_record() ) {
			return db::f( 'id' );
		}

		// It seems that there is no access list, so we have to create a
		// new one using the email address of the account.
		$acl_id = $GO_SECURITY->get_new_acl( $mail );

		// When we know the ID of the user, we can set him as owner of the new
		// access list ID. When we are not using LDAP user management, this is
		// overridden by the addToUM() method of the authentication class.
		if ( $uidnumber ) {
			$GO_SECURITY->set_acl_owner( $acl_id, $uidnumber );
		}

		// We can return the newly created access list ID.
		return $acl_id;
	}
}
