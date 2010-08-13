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
     * HTTP response helper
     *
     * @var Sabre_HTTP_Response
     */
    protected $httpResponse;


    /**
     * HTTP request helper
     *
     * @var Sabre_HTTP_Request
     */
    protected $httpRequest;

	protected $realm;

    /**
     * __construct
     *
     */
    public function __construct() {

        $this->httpResponse = new Sabre_HTTP_Response();
        $this->httpRequest = new Sabre_HTTP_Request();

    }

	/**
     * Authenticates the user based on the current request.
     *
     * If authentication is succesful, true must be returned.
     * If authentication fails, an exception must be thrown.
     *
     * @return bool
     */
    public function authenticate(Sabre_DAV_Server $server,$realm){

		$this->realm=$realm;

		$cred = $this->getUserPass();		
		if($cred){
			global $GO_AUTH;

			if ($GO_AUTH->login($cred[0], $cred[1], 'normal', false)) {
				return true;
			}
		}

		$this->requireLogin();

	}

	/**
     * Returns the supplied username and password.
     *
     * The returned array has two values:
     *   * 0 - username
     *   * 1 - password
     *
     * If nothing was supplied, 'false' will be returned
     *
     * @return mixed
     */
    public function getUserPass() {

        // Apache and mod_php
        if (($user = $this->httpRequest->getRawServerValue('PHP_AUTH_USER')) && ($pass = $this->httpRequest->getRawServerValue('PHP_AUTH_PW'))) {

            return array($user,$pass);

        }

        // Most other webservers
        $auth = $this->httpRequest->getHeader('Authorization');

        if (!$auth) return false;

        if (strpos(strtolower($auth),'basic')!==0) return false;

        return explode(':', base64_decode(substr($auth, 6)));

    }

    /**
     * Returns an HTTP 401 header, forcing login
     *
     * This should be called when username and password are incorrect, or not supplied at all
     *
     * @return void
     */
    public function requireLogin() {

        $this->httpResponse->setHeader('WWW-Authenticate','Basic realm="' . $this->realm . '"');
        $this->httpResponse->sendStatus(401);

		echo "Authentication required\n";
		die();

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
		$user = $GO_USERS->get_user($GO_SECURITY->user_id);
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
