<?php
/**
 * @copyright Copyright &copy; Intermesh 2003
 * @version $Revision$ $Date$
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
 * @category Groups
 */

/*
 * This file is overriding some of the functions that are defined in the
 * base_users class. So we need to include this class.
 */
require_once ($GO_CONFIG->class_path.'base/base.groups.class.inc');

/**
 * Implementation of LDAP Group-Management.
 *
 * This class provides the full group-management functionality.
 * @todo add a better comment ;-)
 *
 * @package Framework
 * @subpackage Usermanagement
 * @category Groups
 *
 * @access protected
 *
 * @uses base_groups
 */
class ldap_groups extends base_groups
{
	var $ldap;
	var $grouplist;
	var $is_in_group;
	var $grouplist_index;

	function ldap_groups()
	{
		global $GO_CONFIG, $auth_sources;

		// TODO do we really need the database here?!?
		$this->db();
	}

	function get_group($group_id)
	{
		global $GO_LDAP;
		$GO_LDAP->search( "gidNumber=$group_id", $GO_LDAP->GroupsDN );
		if ( $GO_LDAP->num_entries() ) {
			$entry = $GO_LDAP->get_entries();
			$profile = new profiles();
			return $profile->convert_group_profile_ldap( $entry[0] );
		}
		return false;
	}

	/**
	 * Gets all groups, or all groups the user is member in.
	 * 
	 * This function returns all groups that exist, or - when a user is
	 * specified - all groups where the user is a member. The groups can be
	 * iterated using the next_record() method of this class.
	 * 
	 * @access public
	 * 
	 * @param Integer $user_id the user whose membership should be fetched
	 * 
	 * @return Integer the number of groups or false when an error occured.
	 */
	function get_groups( $user_id = 0 ) {
		global $GO_LDAP;

		// We specify a general search filter, which should return all existent
		// groups. This filter may be changed later, when we find out that we
		// only have to fetch some groups.
		$ldapfilter = '(gidNumber=*)';

		// First we have to find out if we have to fetch all groups, or only
		// the groups where the given user is a member.
		if ( $user_id > 0 ) {
			// We have to fetch the username of the given user, which can be
			// used to fetch the groups where the user is member.
			$GO_LDAP->search( '(uidNumber='.$user_id.')',
						$GO_LDAP->PeopleDN, array( 'uid' ) );
					// When the search returned more or less than one entry, something
					// wrong happend, so we do not proceed.
					if ( $GO_LDAP->num_entries() != 1 ) {
					return false;
					}
					$entries = $GO_LDAP->get_entries();
					$uid = $entries[0]['uid'][0];
					// We create a new search filter, which contains the user.
					$ldapfilter = '(&(memberUid='.$uid.')'.$ldapfilter.')';
							}

							// Now we can search for the groups that match the given filter.
							$GO_LDAP->search( $ldapfilter, $GO_LDAP->GroupsDN );
							// Check if we got some results
							if ( !$GO_LDAP->num_entries() ) {
							return false;
							}
							// We got some valid results, so we convert them, and return the
							// number of groups we got.
							$entries = $GO_LDAP->get_entries();
							$profile = new profiles();

							// TODO this block should be moved to it's own method, because it is
							// implemented multiple times.
							// Each group has to be converted to a SQL style record. There records
							// are stored in the grouplist attribute, and the index is used to
							// iterate over multiple groups using the next_record() method.
							$this->grouplist = array();
							for ( $i=0; $i<$entries['count']; $i++ ) {
								// Convert the current group.
								// TODO The profiles class should be removed and the functionality
								// should be included in this class.
								$this->grouplist[] = $profile->convert_group_profile_ldap( $entries[$i] );
							}
							$this->grouplist_index = 0;

							// Finally we can return the number of groups we have fetched.
							return count( $this->grouplist );
	}

	function get_group_by_name($name)
	{
		global $GO_LDAP;
		$name = htmlspecialchars($name);
		$GO_LDAP->search( "(|(cn=$name)(uid=$name))", $GO_LDAP->GroupsDN );
		if ( $GO_LDAP->num_entries() ) {
			$entry = $GO_LDAP->get_entries();
			$profile = new profiles();
			return $profile->convert_group_profile_ldap( $entry[0] );
		}
		return false;
	}

	/**
	 * Checks if a user is in a group.
	 * 
	 * This function checks if the given user is member of the given group. To
	 * be able to find this out, the function first has to resolve the given
	 * user-id to a username. It can then check if the given user is member in
	 * the group with the given ID.
	 * 
	 * @todo maybe this can be optimized when replacing the uid attribute with
	 * the DN of the user, which is stored in the uniqueMember attribute of
	 * the group.
	 * 
	 * @access public
	 * 
	 * @param Integer $user_id is the ID of the user who should be checked.
	 * @param Integer $group_id is the ID of the group.
	 * 
	 * @return true when the user is member of the group, otherwise false.
	 */
	function is_in_group( $user_id, $group_id ) {
		global $GO_LDAP;

		// Search for the user - we need the username and not the ID.
		$GO_LDAP->search( 'uidNumber='.$user_id,
				$GO_LDAP->PeopleDN, array( 'uid' ) );
		// We only should proceed when we were able to find exactly one user.
		if ( $GO_LDAP->num_entries() != 1 ) {
			return false;
		}
		// Fetch the username from the result entry.
		$entries = $GO_LDAP->get_entries();
		$uid = $entries[0]['uid'][0];

		// Now we search if we can find the given user in the given group. We
		// do not need to fetch any attributes, we just need to know if there
		// is a group that matches the given LDAP filter.
		$GO_LDAP->search( '(&(gidNumber='.$group_id.')(memberUid='.$uid.'))',
				$GO_LDAP->GroupsDN, array() );
		// When we found exactly one entry, we can assume that the user is
		// member of the group.
		if ( $GO_LDAP->num_entries() == 1 ) {
			return true;
		}

		// If we didn't find a entry, the user is not member of the group.
		return false;
	}

	function get_users_in_group($group_id, $sort="name", $direction="ASC")
	{
		global $GO_CONFIG, $GO_LDAP, $GO_USERS;

		$GO_LDAP->search( "(gidNumber=$group_id)", $GO_LDAP->BaseDN );

		$GO_LDAP->next_entry();
		$this->is_in_group = $GO_LDAP->get_values("memberUid");

		//TODO Its not nice, but necessary to get the users from the group ...
		// ... maybe its possible to join the two things?!
		for ( $i=0; $i<$this->is_in_group["count"]; $i++ ) {
			$this->grouplist[] = $GO_USERS->get_user_by_username( 
					$this->is_in_group[$i] );
		}

		$this->grouplist_index = 0;

		return $this->is_in_group["count"];
	}

	function next_record()
	{
		if ( count( $this->grouplist ) > $this->grouplist_index )
		{
			$this->Record = $this->grouplist[$this->grouplist_index++];
			return $this->Record;
		} else
			return false;
	}
}
