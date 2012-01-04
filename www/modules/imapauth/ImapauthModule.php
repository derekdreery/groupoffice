<?php

class GO_Imapauth_ImapauthModule extends GO_Base_Module {

	public static function initListeners() {
		//GO::session()->addListener('beforelogin', 'GO_Imapauth_ImapauthModule', 'beforeLogin');

		$controller = new GO_Core_Controller_Auth();
		$controller->addListener('beforelogin', 'GO_Imapauth_ImapauthModule', 'beforeLogin');
	}

	public static function beforeControllerLogin($params, &$response) {
		if (!isset($params['first_name'])) {
			try {
				$imap = new GO_Base_Mail_Imap();
				$imap->connect(
								$config['host'], $config['port'], $mail_username, $password, $config['ssl']);

				GO::debug('IMAPAUTH: IMAP login succesful');
				$imap->disconnect();

				$user = GO_Base_Model_User::model()->findSingleByAttribute('username', $go_username);
				if (!$user) {
					$response['needCompleteProfile'] = true;
				}
			} catch (Exception $e) {
				GO::debug('IMAPAUTH: Authentication to IMAP server failed with Exception: ' . $e->getMessage() . ' IMAP error:' . $imap->last_error());
				$imap->clear_errors();

				GO::session()->logout(); //for clearing remembered password cookies

				return false;
			}
		}
	}

	protected $config;
	public $goUsername;
	protected $imapUsername;
	public $imapPassword;
	public $email;
	protected $user;

	public function setCredentials($username, $password) {
		GO::debug('IMAPAUTH: module active');
		$arr = explode('@', $username);

		$this->email = trim($username);
		$mailbox = trim($arr[0]);
		$domain = isset($arr[1]) ? trim($arr[1]) : '';

		$config = $this->_getDomainConfig($domain);

		if (!$config) {
			GO::debug('IMAPAUTH: No config for domain found');
			return false;
		} else {
			GO::debug($config);
			$this->config = $config;

			$this->goUsername = $this->imapUsername = $this->email;
			if ($config['remove_domain_from_username']) {
				$this->imapUsername = $mailbox;
			}

			$this->imapPassword = $password;

			GO::debug('IMAPAUTH: Attempt IMAP login');

			return true;
		}
	}

	public function imapAuthenticate() {
		$imap = new GO_Base_Mail_Imap();
		try {
			$imap->connect(
							$this->config['host'], $this->config['port'], $this->imapUsername, $this->imapPassword, $this->config['ssl']);

			GO::debug('IMAPAUTH: IMAP login succesful');
			$imap->disconnect();


			$user = GO_Base_Model_User::model()->findSingleByAttribute('username', $this->goUsername);
			if ($user) {
				GO::debug("IMAPAUTH: Group-Office user already exists.");
				if (!$user->checkPassword($this->imapPassword)) {
					GO::debug('IMAPAUTH: IMAP password has been changed. Updating Group-Office database');

					$user->password = $this->imapPassword;
					$user->save();
				}
				$this->user = $user;

				if (GO::modules()->isInstalled('email')) {
					$stmt = GO_Email_Model_Account::model()->findByAttributes(array(
							'host' => $this->config['host'],
							'username' => $this->imapUsername
									));
					$foundAccount = false;
					while ($account = $stmt->fetch()) {
						
						if($account->user_id==$this->user->id)
							$foundAccount=true;
						
						$account->password = $this->imapPassword;
						if ($this->config['smtp_use_login_credentials']) {
							$account->smtp_password = $this->imapPassword;
						}
						$account->save();
					}
					
					if(!$foundAccount){
						$this->createEmailAccount();
					}
				}
			}

			return true;
		} catch (Exception $e) {
			GO::debug('IMAPAUTH: Authentication to IMAP server failed with Exception: ' . $e->getMessage() . ' IMAP error:' . $imap->last_error());
			$imap->clear_errors();

			GO::session()->logout(); //for clearing remembered password cookies

			return false;
		}
	}

	public static function beforeLogin($params, &$response) {

		GO::$ignoreAclPermissions = true;

		$ia = new GO_Imapauth_ImapauthModule();

		if ($ia->setCredentials($params['username'], $params['password'])) {
			if ($ia->imapAuthenticate()) {
				if (!$ia->user) {
					GO::debug("IMAPAUTH: Group-Office user doesn't exist.");
					if (!isset($params['first_name'])) {
						$response['needCompleteProfile'] = true;
						$response['success'] = false;

						$response['feedback'] = "Please fill in some additional information to complete your user account.";
						return false;
					} else {
						//user doesn't exist. create it now
						$user = new GO_Base_Model_User();
						$user->email = $ia->email;
						$user->username = $ia->goUsername;
						$user->password = $ia->imapPassword;
						$user->first_name = $params['first_name'];
						$user->middle_name = $params['middle_name'];
						$user->last_name = $params['last_name'];

						try {

							$user->save();
							if (!empty($config['groups']))
								$user->addToGroups($config['groups']);
							
							$ia->user = $user;

							$ia->createEmailAccount();
						} catch (Exception $e) {
							GO::debug('IMAPAUTH: Failed creating user ' .
											$ia->goUsername . ' and e-mail ' . $ia->email .
											'Exception: ' .
											$e->getMessage(), E_USER_WARNING);
						}
					}

					//$ia->create_email_account($config, $user_id, $mail_username, $password, $email);
				}
			}
		}

		GO::$ignoreAclPermissions = false;
	}

	public function createEmailAccount() {
		
		
		if (GO::modules()->isInstalled('email')) {
			
			GO::debug('IMAPAUTH: Creating IMAP account for user');
			$account['user_id'] = $this->user->id;
			$account['type'] = 'imap'; //$this->config['proto'];
			$account['host'] = $this->config['host'];
			$account['smtp_host'] = $this->config['smtp_host'];
			$account['smtp_port'] = $this->config['smtp_port'];
			$account['smtp_encryption'] = $this->config['smtp_encryption'];

			if (!empty($this->config['smtp_use_login_credentials'])) {
				$account['smtp_username'] = $this->imapUsername;
				$account['smtp_password'] = $this->imapPassword;
			} elseif (isset($this->config['smtp_username'])) {
				$account['smtp_username'] = $this->config['smtp_username'];
				$account['smtp_password'] = $this->config['smtp_password'];
			}

			$account['port'] = $this->config['port'];
			$account['use_ssl'] = $this->config['ssl'];
			$account['mbroot'] = $this->config['mbroot'];
			$account['username'] = $this->imapUsername;
			$account['password'] = $this->imapPassword;

			$model = new GO_Email_Model_Account();
			$model->setAttributes($account);
			$model->save();
			$model->addAlias($this->email, $this->user->name);
			
		}else
		{
			GO::debug('IMAPAUTH: E-mail module not installed. Skipping e-mail account creation.');
		}
	}

	private function _getDomainConfig($domain) {
		global $GO_CONFIG;

		if (!empty($domain)) {
			$conf = str_replace('config.php', 'imapauth.config.php', GO::config()->get_config_file());

			if (file_exists($conf)) {
				require($conf);
				$configs = $config;
			} else {
				$configs = array();
			}
			foreach ($configs as $config) {
				if ($config['domains'] == '*') {
					return $config;
				}
				$domains = explode(',', $config['domains']);
				$domains = array_map('trim', $domains);

				if (in_array($domain, $domains)) {
					return $config;
				}
			}
		}
		return false;
	}

}