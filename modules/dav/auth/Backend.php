<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Auth_Backend.class.inc.php 7752 2011-07-26 13:48:43Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
class GO_Dav_Auth_Backend implements Sabre_DAV_Auth_IBackend {

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
	protected $users;

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
	public function authenticate(Sabre_DAV_Server $server, $realm) {
		
		if (GO::user())
			return true;

		$this->realm = $realm;

		$cred = $this->getUserPass();
		if ($cred) {
			$user = GO::session()->login($cred[0], $cred[1], false);
			if($user)
				return true;
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

		//GO::debug($_SERVER['PHP_AUTH_USER']);
		// Apache and mod_php
		if (($user = $this->httpRequest->getRawServerValue('PHP_AUTH_USER')) && ($pass = $this->httpRequest->getRawServerValue('PHP_AUTH_PW'))) {
			
			return array($user, $pass);
		}

		// Most other webservers
		$auth = $this->httpRequest->getHeader('Authorization');

		if (!$auth)
			return false;

		if (strpos(strtolower($auth), 'basic') !== 0)
			return false;

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

		$this->httpResponse->setHeader('WWW-Authenticate', 'Basic realm="' . $this->realm . '"');
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
	public function getCurrentUser() {
		global $GO_SECURITY, $GO_CONFIG;

		require_once(GO::config()->class_path . 'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		$user = $GO_USERS->get_user($GLOBALS['GO_SECURITY']->user_id);
		$user['uri'] = 'principals/' . $user['username'];
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
//    public function getUsers() {
//
//		global $GO_SECURITY, $GO_CONFIG;
//
//		require_once(GO::config()->class_path.'base/users.class.inc.php');
//		$GO_USERS = new GO_USERS();
//
//		GO::debug('GO_DAV_Auth_Backend::getUsers()');
//
//		if(!isset($this->users)){
//
//			$this->users=array($this->recordToDAVUser($GO_USERS->get_user($GLOBALS['GO_SECURITY']->user_id)));
//			GO::debug('Fetching users from database');
//			/*$GO_USERS->get_authorized_users($GLOBALS['GO_SECURITY']->user_id, 'username');
//			//$GO_USERS->get_users('username', 'asc',0,10);
//			while($user=$GO_USERS->next_record()){
//				
//				$this->users[]=$this->recordToDAVUser($user);
//			}*/
//		}
//
//        return $this->users;
//
//    }

	private function recordToDAVUser($record) {

		return array(
				'uri' => 'principals/' . $record['username'],
				'{http://sabredav.org/ns}email-address' => $record['email'],
				'{urn:ietf:params:xml:ns:caldav}schedule-inbox-URL' => new Sabre_DAV_Property_Href('principals/' . $record['username'] . '/inbox'),
				'{urn:ietf:params:xml:ns:caldav}schedule-outbox-URL' => new Sabre_DAV_Property_Href('principals/' . $record['username'] . '/outbox')
		);
	}

	public function getUser($name) {

		global $GO_CONFIG;

		require_once(GO::config()->class_path . 'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		$user = $GO_USERS->get_user_by_username($name);
		if (!$user)
			return false;
		else
			return $this->recordToDAVUser($user);
	}

}
