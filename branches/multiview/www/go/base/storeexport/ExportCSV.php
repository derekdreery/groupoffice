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
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits <wsmits@intermesh.nl>
 * @package GO.base.export
 */
namespace GO\Base\Storeexport;

class ExportCSV extends \GO\Base\Storeexport\AbstractExport {
	
	public static $showInView = true;
	public static $name = "CSV";
	public static $useOrientation=false;
	
	/**
	 * Here you can add extra data(lines) that will be parsed after the store data
	 * 
	 * @var array 
	 */
	private $_lines = false;
	
	private function _sendHeaders(){		
		$file = new \GO\Base\Fs\File($this->title.'.csv');
		\GO\Base\Util\Http::outputDownloadHeaders($file);
	}

	private function _write($data){
		if(!isset($this->_fp)){
			$this->_fp=fopen('php://output','w+');		
		}		
		fputcsv($this->_fp, $data, \GO::user()->list_separator, \GO::user()->text_separator);
	}	
	
	public function output(){
		$this->_sendHeaders();
		
		
		
		if($this->header){
			if($this->humanHeaders){
				
				//workaround Libreoffice bug: https://bugs.freedesktop.org/show_bug.cgi?id=48347
				$headers = array_values($this->getLabels());
				
				for($i=0;$i<count($headers);$i++){
					if($headers[$i] == 'ID')
						$headers[$i] = 'Id';
				}
				
				$this->_write($headers);
				// End of workaround
				
				//$this->_write(array_values($this->getLabels()));
			}else
				$this->_write(array_keys($this->getLabels()));
		}
		
		while($record = $this->store->nextRecord()){
			$record = $this->prepareRecord($record);
			$this->_write($record);
		}
		
		// If extra lines given, then add them to the .csv file
		if($this->_lines !== false){
			foreach($this->_lines as $record){
				$record = $this->prepareRecord($record);
				$this->_write($record);
			}
		}
	}
	
	/**
	 * Add extra lines to the end of the document
	 * 
	 * @param array $lines key value array
	 */
	public function addLines($lines){
		$this->_lines = $lines;
	}
}