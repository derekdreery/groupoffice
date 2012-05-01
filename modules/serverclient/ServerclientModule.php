<?php
class GO_Serverclient_ServerclientModule extends GO_Base_Module{
	
	public static function initListeners() {
		
		GO_Base_Model_User::model()->addListener("beforesave", "GO_Serverclient_ServerclientModule", "onBeforeSave");
		
		return parent::initListeners();
	}
	
	public static function onBeforeSave($user){
		
	}
	
	public static function saveUser($user, $wasNew){
		if($wasNew){
			if(!empty($user->serverclient_domains)){
				
				$httpClient = new GO_Serverclient_HttpClient();
				if(!$httpClient->postfixLogin())
					throw new Exception("Could not login to postfixadmin module");
				
				foreach ($user->serverclient_domains as $domain) {
					//domain is, for example "intermesh.dev".
					$url = GO::config()->serverclient_server_url."?r=postfixadmin/mailbox/submit";
					$response = $httpClient->request($url, array(
						"r"=>"postfixadmin/mailbox/submit",
						"name"=>$user->name,
						"username"=>$user->username,
						"password"=>$user->getUnencryptedPassword(),
						"password2"=>$user->getUnencryptedPassword(),
						"domain"=>$domain
					));
										
					$result=json_decode($response);
					
					if(!$result->success)
						throw new Exception("Could not create mailbox on postfixadmin module. ".$result->feedback);
					
					self::_addAccount($user,$domain);
				}
			}
		}
	}
	
	private static function _addAccount($user,$domainName) {
		$accountModel = new GO_Email_Model_Account();
		$accountModel->user_id=$user->id;
		$accountModel->mbroot = GO::config()->serverclient_mbroot;
		$accountModel->use_ssl = GO::config()->serverclient_use_ssl;
		$accountModel->novalidate_cert = GO::config()->serverclient_novalidate_cert;
		$accountModel->type=GO::config()->serverclient_type;
		$accountModel->host=GO::config()->serverclient_host;
		$accountModel->port=GO::config()->serverclient_port;

		$accountModel->username=$user->username;
		if(empty(GO::config()->serverclient_dont_add_domain_to_imap_username)){
			$accountModel->username.='@'.$domainName;
		}
		$accountModel->password=$user->getUnencryptedPassword();
		
		$accountModel->smtp_host=GO::config()->serverclient_smtp_host;
		$accountModel->smtp_port=GO::config()->serverclient_smtp_port;
		$accountModel->smtp_encryption=GO::config()->serverclient_smtp_encryption;
		$accountModel->smtp_username=GO::config()->serverclient_smtp_username;
		$accountModel->smtp_password=GO::config()->serverclient_smtp_password;
		$accountModel->save();
		$accountModel->addAlias($user->email, $user->name);
	}
}