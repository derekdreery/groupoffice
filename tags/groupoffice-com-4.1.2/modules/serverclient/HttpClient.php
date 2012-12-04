<?php
class GO_Serverclient_HttpClient extends GO_Base_Util_HttpClient{
	
	public function postfixLogin(){		
		return $this->groupofficeLogin(GO::config()->serverclient_server_url, GO::config()->serverclient_username, GO::config()->serverclient_password);		
	}
	
//	public function postfixRequest($params){
//		$this->postfixLogin();		
//		
//		$url = GO::config()->serverclient_server_url.'modules/postfixadmin/json.php';
//		
//		return $this->request($url, $params);
//	}	
	
	
}