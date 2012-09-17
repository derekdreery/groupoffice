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
					//'password' => 'userpassword',
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

		$oldIgnoreAcl = GO::setIgnoreAclPermissions(true);

		$mapping = $this->_getMapping();

		if (empty(GO::config()->ldap_host) || empty(GO::config()->ldap_basedn)) {
			GO::debug("LDAPAUTH: Aborting because one or more of the following " .
							"required values is not set: \$config['ldap_host'], \$config['ldap_port'] and \$config['ldap_basedn'].");
			return false;
		}

//		$ldapConn = new GO_Base_Ldap_Connection(GO::config()->ldap_host, GO::config()->ldap_port, !empty(GO::config()->ldap_tls));
//
//		//support old deprecated config.
//		if(!empty(GO::config()->ldap_user))
//			GO::config()->ldap_bind_rdn=GO::config()->ldap_user;
//		
//		if (!empty(GO::config()->ldap_bind_rdn)) {
//			$bound = $ldapConn->bind(GO::config()->ldap_bind_rdn, GO::config()->ldap_pass);
//			if (!$bound)
//				throw new Exception("Failed to bind to LDAP server with RDN: " . GO::config()->ldap_bind_rdn);
//		}
		$ldapConn = GO_Base_Ldap_Connection::getDefault();

		if (!empty(GO::config()->ldap_search_template))
			$query = str_replace('{username}', $username, GO::config()->ldap_search_template);
		else
			$query = $mapping['username'] . '=' . $username;

		$result = $ldapConn->search(GO::config()->ldap_basedn, $query);
		$record = $result->fetch();

		if (!$record) {
			GO::debug("LDAPAUTH: No LDAP entry found for " . $username);
			return false;
		}

		$authenticated = $ldapConn->bind($record->getDn(), $password);
		if (!$authenticated) {
			GO::debug("LDAPAUTH: LDAP authentication FAILED for " . $username);
			GO::session()->logout();
			return false;
		} else {
			GO::debug("LDAPAUTH: LDAP authentication SUCCESS for " . $username);

			
			$user = $this->syncUserWithLdapRecord($record, $password);


			$this->_checkEmailAccounts($user, $password);

			GO::setIgnoreAclPermissions($oldIgnoreAcl);
		}
	}

	/**
	 * 
	 * @param GO_Base_Ldap_Record $user
	 * @param type $password
	 * @return \GO_Base_Model_User
	 */
	public function syncUserWithLdapRecord(GO_Base_Ldap_Record $record, $password = null) {

		$attr = $this->getUserAttributes($record);
		
		$user = GO_Base_Model_User::model()->findSingleByAttribute('username', $attr['username']);
		if ($user) {
			GO::debug("LDAPAUTH: Group-Office user already exists.");
			if (isset($password) && !$user->checkPassword($password)) {
				GO::debug('LDAPAUTH: LDAP password has been changed. Updating Group-Office database');

				$user->password = $password;
			}

			if (empty(GO::config()->ldap_auth_dont_update_profiles)) {
				//never update the e-mail address because the user
				//can't change it to something invalid.

				

				if ($this->validateUserEmail($record, $user->email))
					unset($attr['email']);

				$user->setAttributes($attr);

				GO::debug('LDAPAUTH: updating user profile');
				GO::debug($attr);

				$this->_updateContact($user, $attr);
			}else {
				GO::debug('LDAPAUTH: Profile updating from LDAP is disabled');
			}

			$user->save();
		} else {
			GO::debug("LDAPAUTH: Group-Office user does not exist. Attempting to create it.");

			$attr = $this->getUserAttributes($record);

			GO::debug($attr);

			$user = new GO_Base_Model_User();
			$user->setAttributes($attr);
			$user->password = $password;

			try {
				$user->save();
				if (!empty(GO::config()->ldap_groups))
					$user->addToGroups(explode(',', GO::config()->ldap_groups));

				$this->_updateContact($user, $attr);

				$user->checkDefaultModels();
			} catch (Exception $e) {
				GO::debug('LDAPAUTH: Failed creating user ' .
								$attr['username'] .
								' Exception: ' .
								$e->getMessage(), E_USER_WARNING);
			}
		}
		return $user;
	}

	private function _updateContact($user, $attributes) {
		$contact = $user->createContact();
		if ($contact) {
			GO::debug('LDAPAUTH: updating user contact');
			$contact->setAttributes($attributes);

			if (!empty($attributes['company'])) {
				$company = GO_Addressbook_Model_Company::model()->findSingleByAttributes(array(
						'addressbook_id' => $contact->addressbook_id,
						'name' => $attributes['company']
								));

				if (!$company) {
					GO::debug('LDAPAUTH: creating company for contact');
					$company = new GO_Addressbook_Model_Company();
					$company->name = $attributes['company'];
					$company->addressbook_id = $contact->addressbook_id;
					$company->save();
				} else {
					GO::debug('LDAPAUTH: found existing company for contact');
				}
				$contact->company_id = $company->id;
			}

			$contact->save();
		}
	}

	private function _checkEmailAccounts(GO_Base_Model_User $user, $password) {
		if (GO::modules()->isInstalled('email')) {

			$arr = explode('@', $user->email);
			$mailbox = trim($arr[0]);
			$domain = isset($arr[1]) ? trim($arr[1]) : '';

			$imapauth = new GO_Imapauth_Authenticator();
			$config = $imapauth->getDomainConfig($domain);

			if (!$config) {
				GO::debug('LDAPAUTH: No E-mail configuration found for domain: ' . $domain);
				return false;
			}

			GO::debug('LDAPAUTH: E-mail configuration found. Creating e-mail account');
			$imapUsername = empty($config['ldap_use_email_as_imap_username']) ? $user->username : $user->email;

			if (!$imapauth->checkEmailAccounts($user, $config['host'], $imapUsername, $password)) {
				$imapauth->createEmailAccount($user, $config, $imapUsername, $password);
			}
		}
	}

	public function getUserAttributes(GO_Base_Ldap_Record $record) {

		$userAttributes = array();

		$mapping = $this->_getMapping();

		$lowercase = $record->getAttributes();

		foreach ($mapping as $userAttribute => $ldapMapping) {
			if (!empty($ldapMapping)) {
				if (!is_string($ldapMapping)) {
					$value = $ldapMapping->getValue($record);
				} else {
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

		if (!empty(GO::config()->ldap_use_uid_with_email_domain))
			$userAttributes['email'] = $userAttributes['username'] . '@' . GO::config()->ldap_use_uid_with_email_domain;

		return $userAttributes;
	}

	/**
	 * Checks if an e-mail address is present in the LDAP directory
	 * 	 
	 * @param GO_Base_Ldap_Record $record
	 * @param string $email
	 * @param array $validAddresses
	 * @return type 
	 */
	public function validateUserEmail(GO_Base_Ldap_Record $record, $email, &$validAddresses = array()) {

		$mapping = $this->_getMapping();

		$lowercase = $record->getAttributes();

		if (isset($lowercase[$mapping['email']]))
			$val = $lowercase[$mapping['email']];
		else
			return false;

		if (is_string($val)) {
			$val = array('count' => 1, '0' => $val);
		}
		if (is_array($val)) {
			$validAddresses = array();
			for ($i = 0; $i < $val['count']; $i++) {
				$validAddresses[] = strtolower($val[$i]);
			}

			if (!empty(GO::config()->ldap_use_uid_with_email_domain)) {

				$default = strtolower($lowercase[$mapping['username']][0]) . '@' . GO::config()->ldap_use_uid_with_email_domain;

				if (!in_array($default, $validAddresses)) {
					$validAddresses[] = $default;
				}
			}

			if (!in_array(strtolower($email), $validAddresses)) {
				return false;
			}
		}

		return true;
	}

}