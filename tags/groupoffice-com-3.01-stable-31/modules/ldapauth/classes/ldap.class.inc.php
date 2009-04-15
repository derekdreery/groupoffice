<?php
/**
 * @copyright Copyright &copy; Intermesh 2004
 * @version $Revision: 1.19 $ $Date: 2006/01/20 19:31:47 $
 *
 * @author Markus Schabel <markus.schabel@tgm.ac.at>
 * @author Michael Borko <michael.borko@tgm.ac.at>

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
 * LDAP directory server abstraction class.
 *
 * This class defines a lot of functions which can be used to access an LDAP
 * based directory server. It uses the PHP LDAP functions and only provides a
 * simple abstraction layer to these functions.
 *
 * @package Database abstraction
 * @subpackage LDAP
 * @category LDAP
 *
 * @access public
 */
class ldap {

	/* public: connection parameters */
	var $Host     = "";
	var $User	= "";
	var $Password = "";
	var $BaseDN	= "";
	var $PeopleDN	= "";
	var $GroupsDN	= "";

	/* public: result array and current row number */
	var $Entry   = array();
	var $NumEntry = 0;
	var $Values = array();
	var $NumValues = 0;

	/* public: configuration options */
	var $Debug	= 0;     ## Set to 1 for debugging messages.
	var $Halt_On_Error = "no";

	/* public: current error number and error text */
	var $Errno    = 0;
	var $Error    = "";

	/* private: link and query handles */
	var $Link_ID  = 0;
	var $Bind_ID  = 0;
	var $Search_ID = 0;
	var $Modify_ID = 0;
	var $Create_ID = 0;
	var $Delete_ID = 0;

	var $Auto_Free = 0;

	/* public: constructor */
	function ldap($host, $user, $password, $basedn, $peopledn, $groupsdn) {
		$this->Host = $host;
		$this->User = $user;
		$this->Password = $password;
		$this->BaseDN = $basedn;
		$this->PeopleDN = $peopledn;
		$this->GroupsDN = $groupsdn;
	}

	/* public: some trivial reporting */
	function link_id() {
		return $this->Link_ID;
	}

	function search_id() {
		return $this->Search_ID;
	}

	/* public: connection management */
	function connect($Host = "") {
		/* Handle defaults */
		if ("" == $Host)
		$Host     = $this->Host;

		/* establish connection, select database */
		if ( 0 == $this->Link_ID ) {
			$this->Link_ID=ldap_connect($Host);
			if (!$this->Link_ID) {
				$this->halt("pconnect($Host) failed.");
				return 0;
			}
			@ldap_set_option($this->Link_ID,LDAP_OPT_PROTOCOL_VERSION,3);
		}

		return $this->Link_ID;
	}

	function disconnect() {
		if ( $this->Link_ID ) {
			@ldap_close( $this->Link_ID );
		}
	}

	function bind($User = "", $Password = "" ) {
		/* Handle defaults */
		if ("" == $User)
		$User     = $this->User;
		if ("" == $Password)
		$Password = $this->Password;

		if ( $this->Link_ID ) {
			if ( $User != "" ) {
				$this->Bind_ID = @ldap_bind( $this->Link_ID, $User, $Password );
				if (!$this->Bind_ID) {
					return 0;
				}
			}
		}

		return $this->Bind_ID;
	}

	function unbind() {
		if ( $this->Bind_ID ) {
			ldap_unbind( $this->Link_ID );
			$this->Bind_ID = 0;
		}
	}

	/* public: discard the query result */
	function free() {
		@ldap_free_result($this->Search_ID);
		$this->Search_ID = 0;
		$this->NumEntry = 0;
		$this->NumValues = 0;
	}

	/* public: perform a query */
	function search($Search_String,$Base_DN="",$SearchThis="") {
		if ( $Base_DN == "" )
		$Base_DN = $this->BaseDN;

		/* No empty queries, please, since PHP4 chokes on them. */
		if ($Search_String == "")
		return 0;

		if (!$this->connect()) {
			return 0;
		};

		# New query, discard previous result.
		if ($this->Search_ID) {
			$this->free();
		}

		if ($this->Debug)
		printf("Debug: search = %s<br>\n", $Search_String);

		if ( is_array( $SearchThis ) ) {
			$this->Search_ID = @ldap_search( $this->Link_ID, $Base_DN, $Search_String,
	  $SearchThis);
		} else {
			$this->Search_ID = @ldap_search( $this->Link_ID, $Base_DN, $Search_String );
		}
		$this->NumEntry = 0;
		$this->Errno = ldap_errno( $this->Link_ID );
		$this->Error = ldap_error( $this->Link_ID );
		if (!$this->Search_ID) {
			$this->halt("Invalid LDAP Filter: ".$Search_String);
		}

		# Will return nada if it fails. That's fine.
		return $this->Search_ID;
	}

	/* public: modifying entries */
	function modify( $ToModify, $DN, $Host="", $User="", $Password="") {

		if ( $Host == "" ) {
			$Host = $this->Host;
			$newconnect = false;
		}
		elseif ( $Host != $this->Host ) {
			$this->disconnect();
			$this->connect($Host);
			$newconnect = true;
		}
		if ( $User == "" || $Password == "" ) {
			$User = $this->User;
			$Password = $this->Password;
			$this->bind();
			$newbind = false;
		}
		elseif ( $User != $this->User ) {
			$this->bind($User, $Password);
			$newbind = true;
		}

		if ( $ToModify == "" )
		return false;

		if ( $DN == "" )
		return false;

		if ( $this->Link_ID ) {
			$this->Modify_ID = ldap_modify( $this->connect($Host), $DN, $ToModify );
			$not_modified = false;
			if ( !$this->Modify_ID )
			$not_modified = true;
		}
		if ( $newconnect ) {
			$this->unbind();
			$this->disconnect();
			$newbind = false;
		}
		if ( $newbind ) {
			$this->unbind();
		}
		if ( $not_modified )
		return false;
		return true;
	}

	/* public: create entries */
	function create( $ToCreate, $DN, $Host="", $User="", $Password="") {

		if ( $Host == "" ) {
			$Host = $this->Host;
			$newconnect = false;
		}
		elseif ( $Host != $this->Host ) {
			$this->disconnect();
			$this->connect($Host);
			$newconnect = true;
		}
		if ( $User == "" || $Password == "" ) {
			$User = $this->User;
			$Password = $this->Password;
			$this->bind();
			$newbind = false;
		}
		elseif ( $User != $this->User ) {
			$this->bind($User, $Password);
			$newbind = true;
		}

		if ( $ToCreate == "" )
		return false;

		if ( $DN == "" )
		return false;

		if ( $this->Link_ID ) {
			$this->Create_ID = ldap_add( $this->connect($Host), $DN, $ToCreate );
			$not_created = false;
			if ( !$this->Create_ID )
			$not_created = true;
		}
		if ( $newconnect ) {
			$this->unbind();
			$this->disconnect();
			$newbind = false;
		}
		if ( $newbind ) {
			$this->unbind();
		}
		if ( $not_created )
		return false;
		return true;
	}

	/* public: delete entries */
	function del( $DN, $Host="", $User="", $Password="") {

		if ( $Host == "" ) {
			$Host = $this->Host;
			$newconnect = false;
		}
		elseif ( $Host != $this->Host ) {
			$this->disconnect();
			$this->connect($Host);
			$newconnect = true;
		}
		if ( $User == "" || $Password == "" ) {
			$User = $this->User;
			$Password = $this->Password;
			$this->bind();
			$newbind = false;
		}
		elseif ( $User != $this->User ) {
			$this->bind($User, $Password);
			$newbind = true;
		}

		if ( $DN == "" )
		return false;

		if ( $this->Link_ID ) {
			$this->Delete_ID = ldap_delete( $this->connect($Host), $DN );
			$not_deleted = false;
			if ( !$this->Delete_ID )
			$not_deleted = true;
		}
		if ( $newconnect ) {
			$this->unbind();
			$this->disconnect();
			$newbind = false;
		}
		if ( $newbind ) {
			$this->unbind();
		}
		if ( $not_deleted )
		return false;
		return true;
	}

	function sort($Filter) {
		if ( $this->Search_ID ) {
			ldap_sort( $this->Link_ID, $this->Search_ID, $Filter );
		}
	}

	/* public: walk result set */
	function next_entry() {
		if ( $this->NumEntry == 0 ) {
			$this->Entry = @ldap_first_entry( $this->Link_ID, $this->Search_ID );
		} else {
			$this->Entry = @ldap_next_entry( $this->Link_ID, $this->Entry );
		}
		$this->NumEntry++;

		$this->Errno  = ldap_errno( $this->Link_ID );
		$this->Error  = ldap_error( $this->Link_ID );

		return $this->Entry;
	}

	/**
	 * Fetch an entry (identified by it's DN) from LDAP.
	 *
	 * This function uses the ldap_read functions to fetch an entry from the
	 * directory server. If the DN of an entry is known, this method is much
	 * faster than searching for the entry. Note that it is possible to give
	 * an array of needed attributes as parameter, so that the function only
	 * queries LDAP for these attributes, which may increase performance.
	 *
	 * @access public
	 *
	 * @param string $dn is the DN of the entry that should be fetched from
	 * the directory.
	 * @param array $attributes is an array of attribute names (string) that
	 * should be returned from the directory server.
	 *
	 * @return a reference to the found LDAP entry object.
	 */
	function fetch_entry( $dn, $attributes = null ) {
		if ( $attributes != null ) {
			$this->Search_ID = @ldap_read(
			$this->Link_ID, $dn, 'objectclass=*' );
		} else {
			$this->Search_ID = @ldap_read(
			$this->Link_ID, $dn, 'objectclass=*', $attributes );
		}
		$this->Entry = $this->next_entry();
		return $this->Entry;
	}

	function get_entries() {
		return @ldap_get_entries( $this->Link_ID, $this->Search_ID );
	}

	function num_entries() {
		return @ldap_count_entries( $this->Link_ID, $this->Search_ID );
	}

	function dn() {
		if ( $this->Entry )
		{
			return @ldap_get_dn( $this->Link_ID, $this->Entry );
		}
	}

	function f($Name) {
		return @ldap_get_values( $this->Link_ID, $this->Entry, $Name );
	}

	function get_values( $attr )
	{
		if ( $this->Entry )
		{
			return @ldap_get_values( $this->Link_ID, $this->Entry, $attr );
		}
	}

	function first_value( $attr )
	{
		if ( $this->Entry )
		{
			$this->NumValues = 0;
			$this->Values = @ldap_get_values( $this->Link_ID, $this->Entry, $attr );
			return $Values[$this->NumValues];
		} else {
			return false;
		}
	}

	function next_value()
	{
		$this->NumValues++;
		if ($this->NumValues < count($this->Values))
		{
			return $this->Values[$this->NumValues];
		}else
		{
			return false;
		}
	}

	function in_values( $attr, $val )
	{
		$found = false;
		$value = $this->first_value( $attr );
		do {
			if ($value == $val)
			{
				$found = true;
			}
		} while ( (!$found) && ($value = $this->next_value()) );
		return $found;
	}

	/* private: error handling */
	function halt($msg) {
		$this->Error = @ldap_error($this->Link_ID);
		$this->Errno = @ldap_errno($this->Link_ID);
		if ($this->Halt_On_Error == "no")
		return;

		$this->haltmsg($msg);

		if ($this->Halt_On_Error != "report")
		die("Session halted.");
	}

	function haltmsg($msg) {
		printf("</td></tr></table><b>Database error:</b> %s<br>\n", $msg);
		printf("<b>LDAP Error</b>: %s (%s)<br>\n",
		$this->Errno,
		$this->Error);
	}
}
