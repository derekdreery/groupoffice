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
class GO_Dav_Auth_Backend extends Sabre\DAV\Auth\Backend\AbstractDigest {
	
	private $_user;
	
	/**
	 * Check user access for this module
	 * 
	 * @var string 
	 */
	public $checkModuleAccess='dav';
	
	public function getDigestHash($realm, $username) {
		$user = GO_Base_Model_User::model()->findSingleByAttribute("username", $username);
		
		if($user){
			//check dav module access		
			$davModule = GO_Base_Model_Module::model()->findByPk($this->checkModuleAccess, false, true);		
			if(!GO_Base_Model_Acl::getUserPermissionLevel($davModule->acl_id, $user->id))
			{
				$errorMsg = "No '".$this->checkModuleAccess."' module access for user '".$user->username."'";
				GO::debug($errorMsg);			
				throw new Sabre\DAV\Exception\Forbidden($errorMsg);			
			}else{		

				$this->_user=$user;
				return $user->digest;
			}		
		}else{
			return null;
		}
	}	
	
	public function authenticate(\Sabre\DAV\Server $server, $realm) {		
		
//		if(GO::user()){
//			$this->_user = GO::user();
//			return true;
//		}	
		if(parent::authenticate($server, $realm)){
			GO::session()->setCurrentUser($this->_user);
			return true;
		}

	}
	
//	For basic auth
//	protected function validateUserPass($username, $password) {
//		$user = GO::session()->login($username, $password, false);
//		if($user)
//			return true;
//		else 
//			return false;
//	}
}
