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
 * CSV Output stream.
 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.export
 */
class GO_Base_Export_ExportCSV implements GO_Base_Export_ExportInterface{	
	private $_fp;

	
	public function __construct($filename, $addKeysAsHeaders=true){
		header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
		
		$this->sendHeaders();
		
		$this->_addKeysAsHeaders=$addKeysAsHeaders;
	}
	
	public function showInView(){
		return true;
	}
	
	public function sendHeaders(){
		header('Content-Type: text/x-csv; charset=UTF-8');
		//header('Content-Type: text/plain; charset=UTF-8');
	}

	
	public function write($data){
		if(!isset($this->_fp)){
			$this->_fp=fopen('php://output','w+');
			
			if($this->_addKeysAsHeaders)
				fputcsv($this->_fp, array_keys($data), GO::user()->list_separator, GO::user()->text_separator);
		}
		
		fputcsv($this->_fp, $data, GO::user()->list_separator, GO::user()->text_separator);
	}	
	
	public function flush(){
		
	}
	
	public function endFlush(){
		
	}
	
	public function getName() {
		return 'CSV';
	}
		
	public function useOrientation(){
		return false;
	}
}