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
 * JSON Output stream. Used by default.
 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.export
 */
class GO_Base_Export_ExportJson implements GO_Base_Export_ExportInterface{
	
	//private $_headersSent=false;
	
	private $_data;
	
	public function __construct(){
		$this->sendHeaders();
	}
	
	public function sendHeaders(){
		//text plain doesn't play nice with ajax upload solution with iframes in Extjs
		header('Content-Type: text/html; charset=UTF-8');
	}
	
	public function write($data){
		$this->_data=array_merge($this->_data, $data);
	}
	
	public function flush(){
		
	}
	
	public function endFlush(){
		if(isset($this->_data)){
			echo json_encode($this->_data);
			unset($this->_data);
		}
	}

	public function getName() {
		return 'JSON';
	}
	
}