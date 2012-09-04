<?php

class GO_Imapauth_Authenticator {

	public $config;
	public $goUsername;
	public $imapUsername;
	public $imapPassword;
	public $email;
	public $user;

	public function setCredentials($username, $password) {
		GO::debug('IMAPAUTH: module active');
		$arr = explode('@', $username);

		$this->email = trim($username);
		$mailbox = trim($arr[0]);
		$domain = isset($arr[1]) ? trim($arr[1]) : '';

		$config = $this->getDomainConfig($domain);

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

	/**
	 * Authenticate to imap and return 
	 * @return GO_Base_Model_User 
	 */
	
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
					if (!$this->checkEmailAccounts($this->user, $this->config['host'], $this->imapUsername, $this->imapPassword)) {
						$this->createEmailAccount($this->user, $this->config, $this->imapUsername, $this->imapPassword);
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
	
	public function checkEmailAccounts($user, $host, $imapUsername, $password){
		$stmt = GO_Email_Model_Account::model()->findByAttributes(array(
					'host' => $host,
					'username' => $imapUsername
							));
		$foundAccount = false;
		while ($account = $stmt->fetch()) {

			if($account->user_id==$user->id)
				$foundAccount=true;

			$account->password = $password;
			if ($this->config['smtp_use_login_credentials']) {
				$account->smtp_password = $password;
			}
			$account->save();
		}
		
		return $foundAccount;
	}
	
	public function createEmailAccount($user, $config, $username, $password) {
		
		
		if (GO::modules()->isInstalled('email')) {
			
			GO::debug('IMAPAUTH: Creating IMAP account for user');
			$account['user_id'] = $user->id;
			$account['type'] = 'imap'; //$config['proto'];
			$account['host'] = $config['host'];
			$account['smtp_host'] = $config['smtp_host'];
			$account['smtp_port'] = $config['smtp_port'];
			$account['smtp_encryption'] = $config['smtp_encryption'];

			if (!empty($config['smtp_use_login_credentials'])) {
				$account['smtp_username'] = $username;
				$account['smtp_password'] = $password;
			} elseif (isset($config['smtp_username'])) {
				$account['smtp_username'] = $config['smtp_username'];
				$account['smtp_password'] = $config['smtp_password'];
			}

			$account['port'] = $config['port'];
			$account['use_ssl'] = empty($config['ssl']) ? 0 : 1;
			$account['mbroot'] = $config['mbroot'];
			$account['username'] = $username;
			$account['password'] = $password;

			$model = new GO_Email_Model_Account();
			$model->setAttributes($account);
			$model->save();
			$model->addAlias($user->email, $user->name);
			
		}else
		{
			GO::debug('IMAPAUTH: E-mail module not installed. Skipping e-mail account creation.');
		}
	}

	public function getDomainConfig($domain) {
		
		GO::debug("IMAPAUTH: Finding config for domain: ".$domain);
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