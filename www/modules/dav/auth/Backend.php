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
class GO_Dav_Auth_Backend extends Sabre_DAV_Auth_Backend_AbstractDigest {
	
	private $_user_id;
	
	public function getDigestHash($realm, $username) {
		$user = GO_Base_Model_User::model()->findSingleByAttribute("username", $username);
		
		if(!$user)
			return null;
		else{
			$this->_user_id=$user->id;
			return $user->digest;
		}
	}
	
	
	public function authenticate(\Sabre_DAV_Server $server, $realm) {
		
		if(parent::authenticate($server, $realm)){
			GO::session()->setCurrentUser($this->_user_id);
		}
	}
	
//	protected function validateUserPass($username, $password) {
//		$user = GO::session()->login($username, $password, false);
//		if($user)
//			return true;
//		else 
//			return false;
//	}
}
