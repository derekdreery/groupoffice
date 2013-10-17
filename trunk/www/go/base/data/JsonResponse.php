<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * The JsonResonse object can be used as any normal array but will output json when echoed
 * 
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Michael de Hart <mdhart@intermesh.nl>
 * @package GO.base.data
 */
class GO_Base_Data_JsonResponse implements ArrayAccess {
	
	private $_data;
	
	public function __construct($data=array()) {
		$this->_data = $data;
	}
	
	public function __toString() {
		
		$this->setHeaders();
		
		// make values that start with startjs: functions
//		$this->_data = array_walk_recursive($this->_data, function($item, $key) {
//			if(strpos($item,'startjs:')!==false)
//				$item = stripslashes(str_replace(array('\t','\n', 'startjs:', ':endjs'),'',$item));
//		});
		
		$string = json_encode($this->_data);

		if(strpos($string,'startjs:')!==false){
			preg_match_all('/"startjs:(.*?):endjs"/usi', $string, $matches, PREG_SET_ORDER);

			for($i=0;$i<count($matches);$i++){
				$string = str_replace($matches[$i][0], stripslashes(str_replace(array('\t','\n'),'',$matches[$i][1])), $string);
			}
		}

		return $string;
	}
	
	/**
	 * Render the headers of the generated response
	 * If headers are not set already. Set them to application/json
	 */
	protected function setHeaders() {
		if (headers_sent())
			return;

		header('Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0'); //prevent caching
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); //resolves problem with IE GET requests
		header('Content-type: application/json; charset=UTF-8'); //tell the browser we are returning json
	}

	public function offsetGet($offset) {
		return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
	}

	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
            $this->_data[] = $value;
        } else {
            $this->_data[$offset] = $value;
        }
	}

	public function offsetExists($offset) {
		return isset($this->_data[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->_data[$offset]);
	}
	
	public function getData(){
		return $this->_data;
	}
	
	public function mergeWith(GO_Base_Data_JsonResponse $response){
		$this->_data = array_merge($this->_data, $response->getData());
	}
	
}