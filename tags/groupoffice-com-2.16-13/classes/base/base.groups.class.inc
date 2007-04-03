<?php
/**
 * @copyright Copyright &copy; Intermesh 2003
 * @version $Revision: 1.9 $ $Date: 2006/04/13 16:55:32 $
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

/**
 * This is the base class of the group management files.
 * 
 * This class is abstract and should not used directly. It just defines all
 * methods the groups classes should provide, but they are fully implemented
 * in the corresponding child classes, for examle sql.groups.class.inc.
 *
 * @package Framework
 * @subpackage Usermanagement
 * @category Groups
 * 
 * @access protected
 * 
 * @uses db
 */
class base_groups extends db {
	/**
	 * The constructor initializes the SQL database connection.
	 * 
	 * Some functionality may be global and is therefore implemented in this
	 * class, and not in the child classes. For this functions a database
	 * connection is needed, so we initialize it in the constructor. But the
	 * database can also be used in the child classes, and is initialized
	 * there automatically by calling the constructor of the base class.
	 * There is no need to do anything else here.
	 * 
	 * @access public
	 */
	function base_groups() {
		$this->db();    
	}

/*
 * ****************************************************************************
 * First some methods that are used for fetching groups                       *
 * ****************************************************************************
 */

	/**
	 * Get a group and return it as associative array.
	 * 
	 * This function returns all information that can be found about a specific
	 * group, whose ID is given as parameter.
	 * 
	 * @access public
	 * 
	 * @param Integer $group_id the ID of the group to fetch.
	 * 
	 * @return Array that contains the group's attributes.
	 */
	function get_group( $group_id ) {
		trigger_error(
			'get_group() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

	/**
	 * Get a group by name and return it as associative array.
	 * 
	 * This function returns all information that can be found about a specific
	 * group, whose name is given as parameter.
	 * 
	 * @access public
	 * 
	 * @param String $name is the name of the group to fetch.
	 * 
	 * @return Array that contains the group's attributes.
	 */
	function get_group_by_name( $name ) {
		trigger_error(
			'get_group_by_name() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

	/**
	 * Gets all groups, or all groups the user is member in.
	 * 
	 * This function returns all groups that exist, or - when a user is
	 * specified - all groups where the user is a member. The groups can be
	 * iterated using the next_record() method of the db base class.
	 * 
	 * @access public
	 * 
	 * @param Integer $user_id the user whose membership should be fetched
	 * 
	 * @return Integer the number of groups or false when an error occured.
	 */
	function get_groups( $user_id = 0 ) {
		trigger_error(
			'get_groups() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

	/**
	 * Search for a visible user for another user.
	 * 
	 * This function searches for all users that are visible to the given
	 * user and returns the number of users that are visible to him. The
	 * users can be iterated using the next_record() method.
	 * @todo this method should not be defined inside the groups class,
	 * because it returns a list of users, so it should be moved to the
	 * users class.
	 * 
	 * @access public
	 * 
	 * @param String $query is the keyword that should be searched for.
	 * @param String $field is the field where the keyword should be searched.
	 * @param Integer $user_id is the ID of the user whose permissions should
	 * be used for the search.
	 * @param Integer $start is the first users that should be returned.
	 * @param Integer $offset is the number of users that should be returned.
	 * 
	 * @return Integer is the number of users we found.
	 */
	function search( $query, $field, $user_id, $start=0, $offset=0 ) {
		trigger_error(
			'search() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

	/**
	 * This function fetches all users that are members of the group.
	 * 
	 * For a given group ID this function fetches all members of this group,
	 * and returns the number of users that are member. The members can be
	 * iterated by the next_record() method.
	 * @todo this function should return an Array of the member IDs, because
	 * the next_record() method should be used to iterate over the groups that
	 * were returned by other methods.
	 * 
	 * @access public
	 * 
	 * @param Integer $group_id is the ID of the group where the users should
	 * be returned.
	 * @param String $sort is the name of the attribute that should be used as
	 * sorting value.
	 * @param String $direction to define the direction in which the users
	 * should be sorted.
	 * 
	 * @return Integer number of users that are member of the given group.
	 */
	function get_users_in_group( $group_id, $sort='name', $direction='ASC' ) {
		trigger_error(
			'get_users_in_group() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

	/**
	 * This function checks if a user is member in a group.
	 * 
	 * For a given user and a given group it is checked, if the user is member
	 * of the group.
	 * 
	 * @access public
	 * 
	 * @param Integer $user_id is the ID of the user to check
	 * @param Integer $group_id is the ID of the group to check
	 * 
	 * @return Boolean to indicate if the user is member in the group.
	 */
	function is_in_group( $user_id, $group_id ) {
		trigger_error(
			'is_in_group() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

	/**
	 * Fetch the group IDs for the given names.
	 * 
	 * This function is passed an array of names of groups, and it resolves
	 * these names to the corresponding IDs.
	 * 
	 * @access public
	 * 
	 * @param Array $groupnames is a list of names that should be resolved.
	 * 
	 * @return Array of IDs.
	 */
	function groupnames_to_ids( $groupnames ) {
		trigger_error(
			'groupnames_to_ids() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

/*
 * ****************************************************************************
 * Now some methods that are used to add, delete and modify groups            *
 * ****************************************************************************
 */

	/**
	 * Creates a new group.
	 * 
	 * This method creates a new group for the given owner, and sets the name
	 * of this group to the given value.
	 * 
	 * @access public
	 * 
	 * @param Integer $user_id is the owner of the group
	 * @param String $name is the name of the group
	 * 
	 * @result Integer is the ID of the created group or false if it was not
	 * possible to create the group.
	 */
	function add_group( $user_id, $name ) {
		trigger_error(
			'add_group() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

	/**
	 * Deletes a group.
	 * 
	 * This method removes a group from the user management backend.
	 * 
	 * @access public
	 * 
	 * @param Integer $group_id is the ID of the group that should be deleted.
	 * 
	 * @return Boolean to indicate if it was possible to remove the group.
	 */
	function delete_group( $group_id ) {
		trigger_error(
			'delete_group() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

	/**
	 * Actualizes the name of a given group.
	 * 
	 * This method is used to set the name of a group.
	 * @todo maybe this method should be renamed, so that the name reflects
	 * the functionality.
	 * 
	 * @access public
	 * 
	 * @param Integer $group_id is the ID of the group that should be modified
	 * @param String $name is the new name for this group
	 * 
	 * @return Boolean to indicate if it was possible to change the name
	 */
	function update_group( $group_id, $name ) {
		trigger_error(
			'update_group() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

	/**
	 * Remove all users from a group.
	 * 
	 * This method removes all users from a group.
	 * 
	 * @access public
	 * 
	 * @param Integer $group_id the ID of the group that should be cleared.
	 * 
	 * @return Boolean to indicate if the clearing was successful.
	 */
	function clear_group( $group_id ) {
		trigger_error(
			'clear_group() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

	/**
	 * Add a user to a group.
	 * 
	 * This function adds a user to a group.
	 * 
	 * @access public
	 * 
	 * @param Integer $user_id is the ID of the user to add to the group.
	 * @param Integer $group_id is the ID of the group where the user is added.
	 * 
	 * @return Boolean to indicate if it was successful to add the user.
	 */
	function add_user_to_group( $user_id, $group_id ) {
		trigger_error(
			'add_user_to_group() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

	/**
	 * Delete a user from a group.
	 * 
	 * This function removes a user from a group.
	 * 
	 * @access public
	 * 
	 * @param Integer $user_id is the ID of the user that should be removed.
	 * @param Integer $group_id is the ID of the group where the user is removed
	 * 
	 * @return Boolean to indicate if it was successful to remove the user.
	 */
	function delete_user_from_group( $user_id, $group_id ) {
		trigger_error(
			'remove_user_from_group() is an abstract method.',
			E_USER_ERROR );
		return false;
	}

	/**
	 * Remove all groups that are owned by a given user.
	 * 
	 * This function is called when a user is deleted. It has to delete all
	 * memberships of this user, and all groups that are owned by the user.
	 * 
	 * @access public
	 * 
	 * @param Integer $user_id is the ID of the user that will be deleted.
	 * 
	 * @return Boolean to indicate if the removal of groups and memberships
	 * has been successful.
	 */
	function delete_user( $user_id ) {
		trigger_error(
			'delete_user() is an abstract method.',
			E_USER_ERROR );
		return false;
	}
}
