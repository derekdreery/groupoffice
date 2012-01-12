<?php
class GO_Ldapauth_Controller_Test extends GO_Base_Controller_AbstractController{
	
	protected function actionTest(){
		
	
		$ldapConn = new GO_Base_Ldap_Connection(GO::config()->ldap_host, GO::config()->ldap_port);
		
//		$bound = $ldapConn->bind(GO::config()->ldap_bind_rdn, GO::config()->ldap_pass);
//		
//		if(!$bound)
//			exit("Could not bind");
		
		$result = $ldapConn->search(GO::config()->ldap_basedn, 'uid=merijn');
		
		$record = $result->fetch();
		
		echo $record->displayName;
		
		//var_dump($attr);
		
	}
}