<?php
class GO_Ldapauth_LdapauthModule extends \GO\Base\Module{
	
	public static function initListeners() {		
		\GO::session()->addListener('beforelogin', 'GO_Ldapauth_LdapauthModule', 'beforeLogin');
	}
	
	
	public static function beforeLogin($username, $password){
		
		\GO::debug("LDAPAUTH: Active");
		
		$lh = new \GO_Ldapauth_Authenticator();
		return $lh->authenticate($username, $password);
	}
	
}