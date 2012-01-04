<?php

class GO_Ldapauth_Authenticator {

	private $_mapping = false;

	private function _getMapping() {
		if ($this->_mapping) {
			return $this->_mapping;
		}

		$conf = str_replace('config.php', 'ldapauth.config.php', GO::config()->get_config_file());

		if (file_exists($conf)) {
			require($conf);
			$this->_mapping = $mapping;
		} else {
			$this->_mapping = array(
					'enabled' => new GO_Ldapauth_Mapping_Constant('1'),
					'username' => 'uid',
					'password' => 'userpassword',
					'first_name' => 'givenname',
					'middle_name' => 'middlename',
					'last_name' => 'sn',
					'initials' => 'initials',
					'title' => 'title',
					'sex' => 'gender',
					'birthday' => 'birthday',
					'email' => 'mail',
					'company' => 'o',
					'department' => 'ou',
					'function' => 'businessrole',
					'home_phone' => 'homephone',
					'work_phone' => 'telephonenumber',
					'fax' => 'homefacsimiletelephonenumber',
					'cellular' => 'mobile',
					'country' => 'homecountryname',
					'state' => 'homestate',
					'city' => 'homelocalityname',
					'zip' => 'homepostalcode',
					'address' => 'homepostaladdress',
//					'homepage' => 'homeurl',
//					'work_address' => 'postaladdress',
//					'work_zip' => 'postalcode',
//					'work_country' => 'c',
//					'work_state' => 'st',
//					'work_city' => 'l',
//					'work_fax' => 'facsimiletelephonenumber',
					'currency' => 'gocurrency',
					'max_rows_list' => 'gomaxrowslist',
					'timezone' => 'gotimezone',
					'start_module' => 'gostartmodule',
					'theme' => 'gotheme',
					'language' => 'golanguage',
			);
		}

		return $this->_mapping;
	}

	public function authenticate($username, $password) {

		$mapping = $this->_getMapping();

		$ldapConn = new GO_Base_Ldap_Connection(GO::config()->ldap_host, GO::config()->ldap_port);

		if (!empty(GO::config()->ldap_bind_rdn)) {
			$bound = $ldapConn->bind(GO::config()->ldap_bind_rdn, GO::config()->ldap_pass);
			if (!$bound)
				throw new Exception("Failed to bind to LDAP server with RDN: " . GO::config()->ldap_bind_rdn);
		}

		if (!empty(GO::config()->ldap_search_template))
			$query = str_replace('{username}', $username, GO::config()->ldap_search_template);
		else
			$query = $mapping['username'] . '=' . $username;

		$result = $ldapConn->search(GO::config()->ldap_basedn, $query);
		$record = $result->fetch();

		$authenticated = $ldapConn->bind($record->getDn(), $password);
		if (!$authenticated) {
			GO::debug("LDAPAUTH: LDAP authentication FAILED for " . $username);
			GO::session()->logout();
			return false;
		} else {
			GO::debug("LDAPAUTH: LDAP authentication SUCCESS for " . $username);

			$user = GO_Base_Model_User::model()->findSingleByAttribute('username', $username);
			if ($user) {
				GO::debug("LDAPAUTH: Group-Office user already exists.");
				if (!$user->checkPassword($password)) {
					GO::debug('LDAPAUTH: LDAP password has been changed. Updating Group-Office database');

					$user->password = $password;
					$user->save();
				}
			} else {
				GO::debug("LDAPAUTH: Group-Office user does not exist. Attempting to create it.");

				$attr = $this->_getUserAttributes($record);
				
				GO::debug($attr);

				$user = new GO_Base_Model_User();
				$user->setAttributes($attr);
				$user->password = $password;

				try {
					GO::$ignoreAclPermissions=true;
					$user->save();
					if (!empty(GO::config()->ldap_groups))
						$user->addToGroups(explode(',',GO::config()->ldap_groups));	
					
					$contact = $user->createContact();
					$contact->setAttributes($attr);
					$contact->save();
					
				} catch (Exception $e) {
					GO::debug('LDAPAUTH: Failed creating user ' .
									$attr['username'] . ' and e-mail ' . $attr['email'] .
									' Exception: ' .
									$e->getMessage(), E_USER_WARNING);
				}
			}
		}
	}

	private function _getUserAttributes(GO_Base_Ldap_Record $record) {

		$userAttributes = array();

		$mapping = $this->_getMapping();

		$ldapAttributes = $record->getAttributes();
		$lowercase = array();
		foreach ($ldapAttributes as $key => $value) {
			$lowercase[strtolower($key)] = $value;
		}
		
		//GO::debug($lowercase);

		foreach ($mapping as $userAttribute => $ldapMapping) {
			if (!empty($ldapMapping)) {
				if (!is_string($ldapMapping)) {
					$value = $ldapMapping->getValue($record);
				} else
				{
					$ldapMapping = strtolower($ldapMapping);
					if (!empty($lowercase[$ldapMapping])) {						
						$value = $lowercase[$ldapMapping][0];
					} else {
						continue;
					}
				}

				$userAttributes[$userAttribute] = $value;
			}
		}

		return $userAttributes;
	}

}