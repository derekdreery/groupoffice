<?php
class GO_Serverclient_HttpClient extends GO_Base_Util_HttpClient{
	
	public function postfixLogin(){
		$postfields =array(
			'task'=>'login',
			'username'=>GO::config()->serverclient_username,
			'password'=>GO::config()->serverclient_password
		);

		$response =  $this->request(GO::config()->serverclient_server_url.'?r=auth/login', $postfields);
		$response = json_decode($response, true);

		if(!isset($response['success']) || !$response['success'])
		{
			GO::debug($response);
			require($GLOBALS['GO_LANGUAGE']->get_language_file('serverclient'));
			$feedback = sprintf(GO::t('connect_error','serverclient'), GO::config()->serverclient_server_url);
			if(isset($response['feedback']))
				$feedback .= "\n\n".$response['feedback'];
			throw new Exception($feedback);
		}
		
		return true;
	}
	
	public function postfixRequest($params){
		$this->postfixLogin();		
		
		$url = GO::config()->serverclient_server_url.'modules/postfixadmin/json.php';
		
		return $this->request($url, $params);
	}	
	
	
}