<?php
require(\GO::config()->root_path.'go/vendor/odtphp/library/odf.php');

class GO_Base_Util_Odf extends Odf{
	
	public function __construct($filename, $config = array()) {
		
		$this->_filename=\GO\Base\Fs\File::utf8Basename($filename);
		
		return parent::__construct($filename, $config);
	}
	
	public function getFilename(){
		return $this->_filename;
	}

}