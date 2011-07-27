<?php

class GO_Base_OutputStream_OutputStreamXML implements GO_Base_OutputStream_OutputStreamInterface {
	
	private $_headersSent=false;
	
	public function __construct(){
		$this->sendHeaders();
	}

	public function getHeaders() {
		
	}

	public function sendHeaders(){
		header('Content-Type: text/xml; charset=UTF-8');
	}
	
	public function write($data){
		require_once(GO::config()->root_path.'classes/xml/GOXML.class.inc.php');
		$dom = new DOMDocument('1.0');
		$GOXML = new GOXML($dom);
		$GOXML->addArray($data,$dom);
		echo $dom->saveXML($dom);
	}
	
}