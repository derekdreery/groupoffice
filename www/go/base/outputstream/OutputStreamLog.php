<?php
class GO_Base_OutputStream_OutputStreamLog implements GO_Base_OutputStream_OutputStreamInterface{	
	public function __construct(){		
		$this->sendHeaders();
	}
	
	public function sendHeaders(){
		header('Content-Type: text/plain; charset=UTF-8');
	}

	
	public function write($data){
		echo $data."\n";
		flush();
	}	
}