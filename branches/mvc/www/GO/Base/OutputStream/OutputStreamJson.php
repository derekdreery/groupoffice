<?php

class GO_Base_OutputStream_OutputStreamJson implements GO_Base_OutputStream_OutputStreamInterface{
	
	public function __construct(){
		$this->getHeaders();
	}
	
	public function getHeaders(){
		header('Content-Type','text/plain');
	}
	
	public function write($data){
		echo json_encode($data);
	}
	
}