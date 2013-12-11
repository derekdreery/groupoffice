<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
class GO_Dav_Auth_Backend extends \Sabre\DAV\Auth\Backend\AbstractDigest {
	
	private $_user;
	
	/**
	 * Check user access for this module
	 * 
	 * @var string 
	 */
	public $checkModuleAccess='dav';
	
	public function getDigestHash($realm, $username) {
		$user = \GO\Base\Model\User::model()->findSingleByAttribute("username", $username);
		
		//check dav module access		
		$davModule = \GO\Base\Model\Module::model()->findByPk($this->checkModuleAccess, false, true);		
		if(!\GO\Base\Model\Acl::getUserPermissionLevel($davModule->acl_id, $user->id))
		{
			$errorMsg = "No '".$this->checkModuleAccess."' module access for user '".$user->username."'";
			\GO::debug($errorMsg);			
			throw new \Sabre\DAV\Exception\Forbidden($errorMsg);			
		}
		
		if(!$user)
			return null;
		else{	
			$this->_user=$user;
			return $user->digest;
		}
	}	
	
	public function authenticate(\Sabre\DAV\Server $server, $realm) {		
		if(parent::authenticate($server, $realm)){
			\GO::session()->setCurrentUser($this->_user);
			return true;
		}
	}
	
//	For basic auth
//	protected function validateUserPass($username, $password) {
//		$user = \GO::session()->login($username, $password, false);
//		if($user)
//			return true;
//		else 
//			return false;
//	}
}
