<?php

namespace GO\Base;

class Request {

	private $_params;

	public function getContentType() {
		if (PHP_SAPI == 'cli') {
			return 'cli';
		} else {
			return isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : '';
		}
	}
	
	public function isJson(){
		return isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], 'application/json')!==false;
	}

	public function getParams() {

		if (!isset($this->_params)) {
			if(PHP_SAPI == 'cli'){				
					$this->_params = Util\Cli::parseArgs();
			}elseif($this->isJson()){

				$this->_params = array_merge($_REQUEST, json_decode(file_get_contents('php://input'), true));
			}else{
				$this->_params = $_REQUEST;
			}
		}

		return $this->_params;
	}
	
	/**
	 * Check if this request SSL secured
	 * 
	 * @return boolean
	 */
	public static function isHttps(){
		return !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'],'off');
	}

}
