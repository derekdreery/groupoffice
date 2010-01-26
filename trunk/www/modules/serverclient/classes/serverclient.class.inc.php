<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: class.tpl 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

class serverclient
{
	var $server_url;
	var $server_username;
	var $server_password;
	var $domains=array();
	var $ch = false;
	
	public function __on_load_listeners($events){
		$events->add_listener('before_add_user', __FILE__, 'serverclient', 'before_add_user');
		$events->add_listener('update_user', __FILE__, 'serverclient', 'update_user');
		$events->add_listener('add_user', __FILE__, 'serverclient', 'add_user');
	}

	public function __construct()
	{
		global $GO_CONFIG;

		if(isset($GO_CONFIG->serverclient_server_url))
		{
			$this->server_url = $GO_CONFIG->serverclient_server_url;
			$this->server_username = $GO_CONFIG->serverclient_username;
			$this->server_password = $GO_CONFIG->serverclient_password;
			$this->ch = curl_init();
		}
		$this->domains = empty($GO_CONFIG->serverclient_domains) ? array() : explode(',',$GO_CONFIG->serverclient_domains);		
	}

	public static function before_add_user($user)
	{
		global $GO_CONFIG, $GO_USERS;
		
		$sc = new serverclient();

		if(isset($_POST['serverclient_domains']))
		{
			$sc->login();
				
			foreach($_POST['serverclient_domains'] as $domain)
			{
				$aliases='';
				if(strpos($user['email'],'@'.$domain) && $user['email']!=$user['username'].'@'.$domain){
					$aliases=$user['email'];
				}
				$params=array(
					'task'=>'serverclient_create_mailbox',
					'domain'=>($domain),
					'go_installation_id'=>$GO_CONFIG->id,
					'username'=>$user['username'],
					'password1'=>$user['password'],
					'password2'=>$user['password'],
					'aliases'=>$aliases,
					'name'=>String::format_name($user),
					'quota'=>0,
					'active'=>'1',					
					'vacation_subject'=>'',
					'vacation_body'=>''					
					);

				$response = $sc->send_request($sc->server_url.'modules/postfixadmin/action.php', $params);
				$response = json_decode($response, true);
					
				//go_debug($response, true);

				if(!is_array($response) || !$response['success'])
				{
					throw new Exception($response['feedback']);
				}
			}
		}
	}

	public static function update_user($user)
	{
		global $GO_USERS, $GO_CONFIG, $GO_MODULES;

		$sc = new serverclient();

		if(!empty($user['password']) && !empty($GO_CONFIG->serverclient_domains))
		{
			$new_password = $user['password'];
			$domains = explode(',', $GO_CONFIG->serverclient_domains);
				
			$user = $GO_USERS->get_user($user['id']);

			if(isset($GO_MODULES->modules['servermanager']) && $user['username']==$sc->server_username){
				$sc->server_password=$new_password;
			}

			$sc->login();
				
			foreach($domains as $domain)
			{
				$params=array(
					'task'=>'serverclient_set_password',
					'domain'=>$domain,
					'username'=>$user['username'].'@'.$domain,
					'password'=>$new_password);
					
				$response = $sc->send_request($sc->server_url.'modules/postfixadmin/action.php', $params);
				$response = json_decode($response, true);

				if(is_array($response) && $response['success'] && isset($GO_MODULES->modules['email']))
				{
					require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc.php');
					$email = new email();
						
					$email->update_password($GO_CONFIG->serverclient_host,$user['username'].'@'.$domain,$new_password);
				}
			}
		}
	}


	/*function __on_user_delete($user)
	{
		global $GO_CONFIG;

		if(!empty($GO_CONFIG->serverclient_domains))
		 {
		 	
			if(!$this->login())
			{
			throw new Exception('Could not connect to server manager! Authentication failed');
			}
				
			$domains = explode(',', $GO_CONFIG->serverclient_domains);
				
			foreach($domains as $domain)
			{
			$params=array(
			'task'=>'delete_mailbox',
			'domain'=>$domain,
			'username'=>$user['username'].'@'.$domain);

			$response = $this->send_request($this->server_url.'modules/postfixadmin/action.php', $params);
			}

			//go_debug(var_export($response, true));
			}
	}*/

	public static function add_user($user)
	{
		global $GO_MODULES, $GO_CONFIG;
		

		if(isset($_POST['serverclient_domains']) && isset($GO_MODULES->modules['email']))
		{
			require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc.php');

			$email = new email();
				
			foreach($_POST['serverclient_domains'] as $domain)
			{

				$account['user_id']=$user['id'];
				$account['mbroot'] = $GO_CONFIG->serverclient_mbroot;
				$account['use_ssl'] = $GO_CONFIG->serverclient_use_ssl;
				$account['novalidate_cert'] = $GO_CONFIG->serverclient_novalidate_cert;
				$account['type']=$GO_CONFIG->serverclient_type;
				$account['host']=$GO_CONFIG->serverclient_host;
				$account['port']=$GO_CONFIG->serverclient_port;
				$account['username']=$user['username'].'@'.$domain;
				$account['password']=$user['password'];
				$account['name']=String::format_name($user);
				$account['email']=$user['email'];
				$account['smtp_host']=$GO_CONFIG->serverclient_smtp_host;
				$account['smtp_port']=$GO_CONFIG->serverclient_smtp_port;
				$account['smtp_encryption']=$GO_CONFIG->serverclient_smtp_encryption;
				$account['smtp_username']=$GO_CONFIG->serverclient_smtp_username;
				$account['smtp_password']=$GO_CONFIG->serverclient_smtp_password;

				//go_debug(var_export($account, true));

				$account['id'] = $email->add_account($account);

				if($account['id']>0)
				{
					//get the account because we need special folder info
					$account = $email->get_account($account['id']);
					$email->synchronize_folders($account);
				}
			}
		}
	}



	function login()
	{
		global $GO_LANGUAGE;

		$postfields =array(
			'task'=>'login',
			'username'=>$this->server_username,
			'password'=>$this->server_password
		);


		$response =  $this->send_request($this->server_url.'action.php', $postfields);

		$response = json_decode($response, true);

		if(!isset($response['success']) || !$response['success'])
		{
			go_debug($response);
			require($GO_LANGUAGE->get_language_file('serverclient'));
			$feedback = isset($response['feedback']) ? $response['feedback'] : sprintf($lang['serverclient']['connect_error'], $this->server_url);
			throw new Exception($feedback);
		}
	}

	function send_request($url, $params)
	{
		global $GO_CONFIG, $GO_SECURITY;

		curl_setopt($this->ch, CURLOPT_URL,$url);
		curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_COOKIEJAR, $GO_CONFIG->tmpdir.'cookie_'.$GO_SECURITY->user_id.'.txt');
		curl_setopt($this->ch, CURLOPT_COOKIEFILE, $GO_CONFIG->tmpdir.'cookie_'.$GO_SECURITY->user_id.'.txt');
		
		//for self-signed certificates
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);


		$response = curl_exec ($this->ch);


		return $response;
	}

	function __destruct(){

		global $GO_CONFIG, $GO_SECURITY;

		if($this->ch)
		{
			curl_close ($this->ch);
		}

		if(file_exists($GO_CONFIG->tmpdir.'cookie_'.$GO_SECURITY->user_id.'.txt'))
		{
			unlink($GO_CONFIG->tmpdir.'cookie_'.$GO_SECURITY->user_id.'.txt');
		}
	}
}

?>
