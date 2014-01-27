<?php

namespace GO\Ldapauth;


class LdapauthModule extends \GO\Base\Module{
	
	public static function initListeners() {		
		\GO::session()->addListener('beforelogin', 'GO\Ldapauth\LdapauthModule', 'beforeLogin');
	}
	
	
	public static function beforeLogin($username, $password){
		
		\GO::debug("LDAPAUTH: Active");
		
		$lh = new Authenticator();
		return $lh->authenticate($username, $password);
	}
	
}