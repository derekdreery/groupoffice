<?php

/**
 * This is an authentication backend that uses a file to manage passwords.
 *
 * The backend file must conform to Apache's htdigest format
 *
 * @package Sabre
 * @subpackage DAV
 * @copyright Copyright (C) 2007-2010 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class GO_DAV_Auth_Backend extends Sabre_DAV_Auth_Backend_Abstract {

	/**
     * Authenticates the user based on the current request.
     *
     * If authentication is succesful, true must be returned.
     * If authentication fails, an exception must be thrown.
     *
     * @return bool
     */
    public function authenticate(Sabre_DAV_Server $server,$realm){
		return true;
	}

    /**
     * Returns information about the currently logged in user.
     *
     * If nobody is currently logged in, this method should return null.
     *
     * @return array|null
     */
    public function getCurrentUser(){
		global $GO_USERS, $GO_SECURITY;
		$user = $GO_USERS->get_user(1/*$GO_SECURITY->user_id*/);
		$user['uri']=$user['username'];
		return $user;
	}

    /**
     * Returns the full list of users.
     *
     * This method must at least return a uri for each user.
     *
     * It is optional to implement this.
     *
     * @return array
     */
    public function getUsers() {

		global $GO_USERS, $GO_SECURITY;

		$users=array();
		
		$GO_USERS->get_users($GO_SECURITY->user_id);
		while($user=$GO_USERS->next_record()){
			$users[]=array('uri'=>$user['username']);
		}

        return $users;

    }

    
}
