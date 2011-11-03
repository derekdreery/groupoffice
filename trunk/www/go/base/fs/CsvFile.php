<?php
class GO_Base_Fs_CsvFile extends GO_Base_Fs_File{
	public $delimiter=',';
	
	public $enclosure='"';
	
	private $_fp;
		
	public function getRecord(){
		if(!isset($this->_fp))
			$this->_fp = fopen($this->path(), 'r');				
		
		if(!is_resource($this->_fp))
			throw new Exception("Could not read CSV file");
		
		return fgetcsv($this->_fp, null, $this->delimiter, $this->enclosure);
	}
	
	public function __destruct() {
		if(is_resource($this->_fp))
			fclose($this->_fp);
	}
}