<?php
/**
* @copyright Intermesh 2003
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.16 $ $Date: 2006/04/20 13:34:47 $
*
*  This program is free software; you can redistribute it and/or modify it
*  under the terms of the GNU General Public License as published by the
*  Free Software Foundation; either version 2 of the License, or (at your
*  option) any later version.
*/

require_once($GO_CONFIG->class_path.'base/base.security.class.inc');

/**
* This is the SQL group management class.
*
* @package  Framework
* @author   Merijn Schering <mschering@intermesh.nl>
* @since    Group-Office 2.05
*/

class sql_security extends base_security {
	
	/**
	* Constructor. Initialises base class of the security class family
	*
	* @access public
	* @return void
	*/
	function sql_security() {
		$this->base_security();
	}

	//Checks if a user has permission for an acl
	/////////////////////////////////////////////////////////////////
	/* 
	 * Other version send by community, supposed to be faster.
	 * function has_permission($user_id, $acl_id)
	 {
	   global $GO_CONFIG;
	
	   if ($user_id > 0 && $acl_id > 0)
	   {
	     $sql = "SELECT acl.acl_id FROM acl, users_groups WHERE".
		" acl.acl_id='$acl_id' AND (acl.user_id='$user_id' OR".
		" (acl.group_id=users_groups.group_id AND".
		" users_groups.user_id='$user_id')) GROUP BY acl.acl_id";
	     $this->query($sql);
	
	     if ($this->num_rows() > 0)
	     {
					return true;
	     }
	   }
	   return false;
	 }*/

	/**
	* Checks if a user has permission for a ACL
	*
	* @param	int			$user_id	The user that needs authentication
	* @param	int			$acl_id	The ACL to check
	* @access private
	* @return bool	 True on success
	*/
	
	function has_permission($user_id, $acl_id) {
		global $GO_CONFIG;

		if ($user_id > 0 && $acl_id > 0) {
			$sql = "SELECT acl_id FROM acl	WHERE ".
				"acl_id='$acl_id' AND user_id='$user_id'";
			$this->query($sql);

			if ($this->num_rows() > 0) {
				return true;
			}

			$sql = "SELECT acl.acl_id FROM acl, users_groups	WHERE ".
				"acl.acl_id='$acl_id' AND	acl.group_id=users_groups.group_id AND ".
				"users_groups.user_id='$user_id'";
			$this->query($sql);

			if ($this->num_rows() > 0) {
				return true;
			}
		}
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
		$sql = 'SELECT groups.id AS group_id FROM groups INNER JOIN acl ON ';
		$sql .= 'acl.group_id=groups.id WHERE acl.acl_id='.$acl_id.' ';
		$sql .= 'ORDER BY groups.name';
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
}
?>
