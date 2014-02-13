<?php

namespace GO\Ldapauth;


class LdapauthModule extends \GO\Base\Module{
	
	public static function initListeners() {		
		\GO::session()->addListener('beforelogin', 'GO\Ldapauth\LdapauthModule', 'beforeLogin');
	}
	
	
	public static function beforeLogin($username, $password){
		

		if(!\GO::config()->ldap_peopledn)
			return true;
		GO::debug("LDAPAUTH: Active");

		try{
			$lh = new Authenticator();
			return $lh->authenticate($username, $password);
		} catch(Exception $e) { //When LDAP binding fail continue with GroupOffice Login
			return isset(GO::config()->ldap_login_on_exception) ? GO::config()->ldap_login_on_exception : true; 
		}

	}
	
	/**
	 * Save the Person attributes from LDAP with the given username
	 */
	public static function submitSettings(&$settingsController, &$params, &$response, $user) {
		//save what is loaded
		try{
			$person = \GO\Ldapauth\Model\Person::findByUsername($user->username);
			$person->setAttributes($params);

			$response['success'] = $response['success'] && $person->save();	
			if(!empty($_POST["current_password"]) || !empty($_POST["password"]) )
				$response['success'] = $response['success'] && $person->changePassword($_POST["current_password"],$_POST["password"]);
			$response['feedback'] = 'Save failed: LDAP '. $person->getError();
		} catch(Exception $e) {
			$response['success'] = false;
			$response['feedback'] = 'Exception duration LDAP save';
		}
	}

	/**
	 * Load the Person attributes from LDAP with the given username
	 */
	public static function loadSettings(&$settingsController, &$params, &$response, $user){	
		try{
			$person = \GO\Ldapauth\Model\Person::findByUsername($user->username);
			if($person) {
				$response['data']=array_merge($response['data'], $person->getAttributes());
				$response['data']['ldap_fields']=$person->getExtraVars();
			}
		} catch (Exception $e) {
			//LDAP record not available
		}
	}
	
}