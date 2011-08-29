<?php
class GO_Base_OutputStream_OutputStreamCSV implements GO_Base_OutputStream_OutputStreamInterface{	
	private $_fp;
	
	public function __construct($filename, $addKeysAsHeaders=true){
		header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
		
		$this->sendHeaders();
		
		$this->_addKeysAsHeaders=$addKeysAsHeaders;
	}
	
	public function sendHeaders(){
		header('Content-Type: text/x-csv; charset=UTF-8');
	}

	
	public function write($data){
		if(!isset($this->_fp)){
			$this->_fp=fopen('php://output','w+');
			
			if($this->_addKeysAsHeaders)
				fputcsv($this->_fp, array_keys($data), GO::user()->list_separator, GO::user()->text_separator);
		}
		
		fputcsv($this->_fp, $data, GO::user()->list_separator, GO::user()->text_separator);
	}
	
}