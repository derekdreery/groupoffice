<?php

class GO_Base_OutputStream_OutputStreamJson implements GO_Base_OutputStream_OutputStreamInterface{
	
	//private $_headersSent=false;
	
	public function __construct(){
		$this->sendHeaders();
	}
	
	public function sendHeaders(){
		header('Content-Type: text/plain; charset=UTF-8');
	}
	
	public function write($data){
		
		echo json_encode($data);
	}
	
}