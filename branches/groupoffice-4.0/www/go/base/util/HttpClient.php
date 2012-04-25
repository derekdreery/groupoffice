<?php
class GO_Base_Util_HttpClient{
	
	private $_curl;
	private $_cookieFile;
	
	public function __construct(){
		$this->_curl = curl_init();
		
		$cookieFile = GO::user() ? 'cookie_'.GO::user()->id.'.txt' : 'cookie_0.txt';
		$this->_cookieFile = GO::config()->tmpdir.$cookieFile;
		
		
		curl_setopt($this->_curl, CURLOPT_COOKIEJAR, $this->_cookieFile);
		curl_setopt($this->_curl, CURLOPT_COOKIEFILE, $this->_cookieFile);

		//for self-signed certificates
		curl_setopt($this->_curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->_curl, CURLOPT_SSL_VERIFYHOST, false);
		@curl_setopt($this->_curl, CURLOPT_FOLLOWLOCATION, TRUE);
	}
	
	public function setCurlOption($option, $value){
		curl_setopt($this->_curl, $option,$value);
	}
	
	public function request($url, $params=array()){
		
		curl_setopt($this->_curl, CURLOPT_URL,$url);
		curl_setopt($this->_curl, CURLOPT_POST, !empty($params));
		if(!empty($params))
			curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $params);
		curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($this->_curl);
		
		$error = curl_error($this->_curl);
		if(!empty($error))
			throw new Exception("curl error: ".$error);
		
		return $response;		
	}	
	
	public function __destruct(){
		if($this->_curl)
			curl_close($this->_curl);
		
		if(file_exists($this->_cookieFile))
			unlink($this->_cookieFile);
	}
}