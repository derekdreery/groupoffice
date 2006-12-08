<?php
/*
   Copyright Intermesh 2003

   Author: Markus Schabel <markus.schabel@tgm.ac.at>
   Version: 1.0 Release date: 22 August 2003

   Author: Merijn Schering <mschering@intermesh.nl>
   Version: 1.1 Release date: 29 March 2004

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */

require_once( 'base.security.class.inc' );

class ldap_security extends base_security {

  function ldap_security() {
    global $GO_CONFIG;
    $this->base_security();
  }

  function logged_in( $user_id=null ) {
  	if ( isset( $user_id ) ) {
	    global $GO_LDAP;
	
	    // create temporary table
	    $sql = "DELETE FROM users_groups WHERE user_id=$user_id";
	    $this->query( $sql );
	
	    // Get the UserID Entries from LDAP for checking
	    $GO_LDAP->search( "(uidNumber=$user_id)", $GO_LDAP->BaseDN );
	    $GO_LDAP->next_entry();
	    $uid = $GO_LDAP->first_value( "uid" );
	
	    $GO_LDAP->search( "(&(gidNumber=*)(memberUid=$uid))",
	      $GO_LDAP->BaseDN, array("gidNumber") );
	    $groups = $GO_LDAP->get_entries();
	    for ( $i=0; $i<$groups["count"]; $i++ ) {
	      $sql = "INSERT INTO users_groups VALUES ( ".$groups[$i]["gidnumber"][0].", $user_id )";
	      $this->query( $sql );
	    }
  	}
    return parent::logged_in( $user_id );
  }

	/**
	 * Check if a user has permission for an acl.
	 * 
	 * This function checks if a given user has permissions for a given acl.
	 * For this it is first checked, if the user himself has access to the acl
	 * which is easy to find out. If the user has no direct permissions, we
	 * have to check, if the user is member of a group which has permission.
	 * 
	 * @access public
	 * 
	 * @param Integer $user_id is the ID of the user to check
	 * @param Integer $acl_id is the ID of the access list ID to check against.
	 * 
	 * @return Boolean true when the user has permissions, otherwise false.
	 */
	function has_permission( $user_id, $acl_id ) {
		global $GO_CONFIG, $auth_sources, $GO_LDAP;

		// Check if the given parameters are valid. Otherwise we log an error.
		// TODO move this parameter checking to some better place.
		if ( $user_id <= 0 ) {
			trigger_error( 'First parameter is to small', E_USER_NOTICE );
		}
		if ( $acl_id <= 0 ) {
			trigger_error( 'Second parameter is to small', E_USER_NOTICE );
		}

		// Check if the user has direct access to this ACL-ID.
		$sql = 'SELECT acl_id FROM acl WHERE ';
		$sql .= 'acl_id='.$acl_id.' AND user_id='.$user_id;
		$this->query( $sql );
		if ( $this->num_rows() > 0 ) {
			return true;
		}

		// The user has no direct access, so we have to check the groups.
		// For this we need the username, which can be checked against the
		// memberUid attribute in the directory.
		$GO_LDAP->search( '(uidNumber='.$user_id.')',
						$GO_LDAP->PeopleDN, array( 'uid' ) );
		// There can only be one user with the given ID!
		if ( $GO_LDAP->num_entries() != 1 ) {
			return false;
		}
		$entries = $GO_LDAP->get_entries();
		$uid = $entries[0]['uid'][0];

		// We have to find all groups that are associated with this ACL-ID
		// and check if the user is member of any of them.
		// TODO maybe it is better to fetch the groups where the user is
		// a member from LDAP, and compare them with the SQL database. This
		// should be performance-tested...
		$sql = 'SELECT acl.group_id FROM acl WHERE ';
		$sql .= 'acl.acl_id='.$acl_id.' AND acl.user_id=0 GROUP BY group_id';
		$this->query( $sql );
		
		while ( $this->next_record() ) {
			// Fetch the ID of the currently processed group.
			$group_id = $this->Record['group_id'];

			// Search if the given user is member of the given group.
			$GO_LDAP->search( '(&(gidNumber='.$group_id.')(memberUid='.$uid.'))',
							$GO_LDAP->GroupsDN, array() );
			if ( $GO_LDAP->num_entries() ) {
				return true;
			}
		}

		// There is nothing that can be checked, so the user has no permission.
		return false;
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
		$sql = 'SELECT group_id FROM acl WHERE acl_id='.$acl_id.' AND user_id=0';
		db::query( $sql );
		if ( db::num_rows() ) {
			$retArray = array();
			while (  db::next_record() ) {
				$retArray[] = db::f( 'group_id' );
			}
			return $retArray;
		}
		return false;
	}

  function get_groups_in_acl($acl_id)
  {
    global $GO_CONFIG, $auth_sources;
      $sql = "SELECT * FROM acl WHERE acl_id='$acl_id' AND user_id=0";
      $this->query($sql);
      return $this->num_rows();
/*
  This is old code (non-ldap-um). Still in there since the code above isn't
  finished yet.

      $sql = "SELECT groups.* FROM groups INNER JOIN acl ON".
	" acl.group_id=groups.id WHERE acl.acl_id='$acl_id'".
	" ORDER BY groups.name";
      $this->query( $sql );
      return $this->num_rows();
*/
  }
}
?>