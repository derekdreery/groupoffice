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
 * Log Output stream. Useful for maintenance scripts
 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.outputstream
 */
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