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
 * @package GO.base.outputstream
 */
class GO_Base_OutputStream_OutputStreamJson implements GO_Base_OutputStream_OutputStreamInterface{
	
	//private $_headersSent=false;
	
	public function __construct(){
		$this->sendHeaders();
	}
	
	public function sendHeaders(){
		//text plain doesn't play nice with ajax upload solution with iframes in Extjs
		header('Content-Type: text/html; charset=UTF-8');
	}
	
	public function write($data){
		
		echo json_encode($data);
	}
	
}