<?php
class GO_Base_Util_CsvReader{
	
	private $_fp;
	
	public function __construct($file){
		$this->_fp = fopen($file, "r");
		if (!$this->_fp) {
			throw new Exception('Could not open CSV file');
		}
	}
	
	public function nextRecord(){
		return fgetcsv($this->_fp, 4096, GO::user()->list_separator, GO::user()->text_separator);
	}
}