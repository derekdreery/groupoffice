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
 * @version $Id: ExportCSV.php 7607 2011-08-04 13:41:42Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits <wsmits@intermesh.nl>
 * @package GO.base.export
 */
class GO_Base_Export_ExportCSV extends GO_Base_Export_AbstractExport {
	
	public static $showInView = true;
	public static $name = "CSV";
	public static $useOrientation=false;
	
	private function _sendHeaders(){
		header('Content-Disposition: attachment; filename="'.$this->title.'.csv"');
		header('Content-Type: text/x-csv; charset=UTF-8');
	}

	private function _write($data){
		if(!isset($this->_fp)){
			$this->_fp=fopen('php://output','w+');		
		}		
		fputcsv($this->_fp, $data, GO::user()->list_separator, GO::user()->text_separator);
	}	
	
	public function output(){
		$this->_sendHeaders();
		
		if($this->header)
			$this->_write(array_keys ($this->getLabels()));
		
		while($record = $this->store->nextRecord()){
			$record = $this->prepareRecord($record);
			$this->_write($record);
		}
	}

}