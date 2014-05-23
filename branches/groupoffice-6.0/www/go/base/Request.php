<?php

namespace GO\Base;

class Request {
	
	public $post;
	
	public $get;
	
	public function __construct() {
		if($this->isJson()){
			$this->post = json_decode(file_get_contents('php://input'), true);
		}else
		{
			$this->post=$_POST;
		}
		
		$this->get=$_GET;		
	}
	
	public function getContentType() {
		if (PHP_SAPI == 'cli') {
			return 'cli';
		} else {
			return isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : '';
		}
	}

	public function isJson() {
		return isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], 'application/json') !== false;
	}

	/**
	 * Check if this request SSL secured
	 * 
	 * @return boolean
	 */
	public function isHttps() {
		return !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'off');
	}
	
	/**
	 * Return true if this is a HTTP post
	 * 
	 * @return boolean
	 */
	public function isPost(){
		return $_SERVER['REQUEST_METHOD']==='POST';
	}
}
