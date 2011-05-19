<?php

/**
 * PDO principal backend
 *
 * This is a simple principal backend that maps exactly to the users table, as 
 * used by Sabre_DAV_Auth_Backend_PDO.
 *
 * It assumes all principals are in a single collection. The default collection 
 * is 'principals/', but this can be overriden.
 *
 * @package Sabre
 * @subpackage DAVACL
 * @copyright Copyright (C) 2007-2011 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/) 
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class GO_DAV_PrincipalBackend implements Sabre_DAVACL_IPrincipalBackend {

	private function recordToDAVUser($record){

		return array(
			'uri'=>'principals/'.$record['username'],
			'{DAV:}displayname' => $record['username'],
			'{http://sabredav.org/ns}email-address'=>$record['email'],
			'{urn:ietf:params:xml:ns:caldav}schedule-inbox-URL'=>new Sabre_DAV_Property_Href('principals/'.$record['username'].'/inbox'),
			'{urn:ietf:params:xml:ns:caldav}schedule-outbox-URL'=>new Sabre_DAV_Property_Href('principals/'.$record['username'].'/outbox')
		);

	}
    /**
     * Returns a list of principals based on a prefix.
     *
     * This prefix will often contain something like 'principals'. You are only 
     * expected to return principals that are in this base path.
     *
     * You are expected to return at least a 'uri' for every user, you can 
     * return any additional properties if you wish so. Common properties are:
     *   {DAV:}displayname 
     *   {http://sabredav.org/ns}email-address - This is a custom SabreDAV 
     *     field that's actualy injected in a number of other properties. If
     *     you have an email address, use this property.
     * 
     * @param string $prefixPath 
     * @return array 
     */
    public function getPrincipalsByPrefix($prefixPath) {

		global $GO_SECURITY, $GO_CONFIG;

		require_once($GO_CONFIG->class_path . 'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		go_debug('GO_DAV_Auth_Backend::getUsers()');

		if (!isset($this->users)) {

			$this->users = array($this->recordToDAVUser($GO_USERS->get_user($GO_SECURITY->user_id)));
			go_debug('Fetching users from database');
			/* $GO_USERS->get_authorized_users($GO_SECURITY->user_id, 'username');
			  //$GO_USERS->get_users('username', 'asc',0,10);
			  while($user=$GO_USERS->next_record()){

			  $this->users[]=$this->recordToDAVUser($user);
			  } */
		}

		return $this->users;
	}

    /**
     * Returns a specific principal, specified by it's path.
     * The returned structure should be the exact same as from 
     * getPrincipalsByPrefix. 
     * 
     * @param string $path 
     * @return array 
     */
		public function getPrincipalByPath($path) {

			$username = basename($path);

			global $GO_CONFIG;

			require_once($GO_CONFIG->class_path . 'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$user = $GO_USERS->get_user_by_username($username);
			if (!$user)
				return false;
			else
				return $this->recordToDAVUser($user);
		}

    /**
     * Returns the list of members for a group-principal 
     * 
     * @param string $principal 
     * @return array 
     */
    public function getGroupMemberSet($principal) {

//        $principal = $this->getPrincipalByPath($principal);
//        if (!$principal) throw new Sabre_DAV_Exception('Principal not found');
//
//        $stmt = $this->pdo->prepare('SELECT principals.uri as uri FROM groupmembers LEFT JOIN principals ON groupmembers.member_id = principals.id WHERE groupmembers.principal_id = ?');
//        $stmt->execute(array($principal['id']));
//
//        $result = array();
//        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//            $result[] = $row['uri'];
//        }
//        return $result;
    
    }

    /**
     * Returns the list of groups a principal is a member of 
     * 
     * @param string $principal 
     * @return array 
     */
    public function getGroupMembership($principal) {

//        $principal = $this->getPrincipalByPath($principal);
//        if (!$principal) throw new Sabre_DAV_Exception('Principal not found');
//
//        $stmt = $this->pdo->prepare('SELECT principals.uri as uri FROM groupmembers LEFT JOIN principals ON groupmembers.principal_id = principals.id WHERE groupmembers.member_id = ?');
//        $stmt->execute(array($principal['id']));
//
//        $result = array();
//        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//            $result[] = $row['uri'];
//        }
//        return $result;

    }

    /**
     * Updates the list of group members for a group principal.
     *
     * The principals should be passed as a list of uri's. 
     * 
     * @param string $principal 
     * @param array $members 
     * @return void
     */
    public function setGroupMemberSet($principal, array $members) {

        // Grabbing the list of principal id's.
//        $stmt = $this->pdo->prepare('SELECT id, uri FROM principals WHERE uri IN (? ' . str_repeat(', ? ', count($members)) . ');');
//        $stmt->execute(array_merge(array($principal), $members));
//
//        $memberIds = array();
//        $principalId = null;
//
//        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//            if ($row['uri'] == $principal) {
//                $principalId = $row['id'];
//            } else {
//                $memberIds[] = $row['id'];
//            }
//        }
//        if (!$principalId) throw new Sabre_DAV_Exception('Principal not found');
//
//        // Wiping out old members
//        $stmt = $this->pdo->prepare('DELETE FROM groupmembers WHERE principal_id = ?;');
//        $stmt->execute(array($principalId));
//
//        foreach($memberIds as $memberId) {
//
//            $stmt = $this->pdo->prepare('INSERT INTO groupmembers (principal_id, member_id) VALUES (?, ?);');
//            $stmt->execute(array($principalId, $memberId));
//
//        }

    }

}
