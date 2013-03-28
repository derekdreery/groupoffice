<?php
class GO_Files_Filehandler_Inline implements GO_Files_Filehandler_Interface{

	private $defaultExtensions = array('pdf','html','htm','txt','xml','log');
	
	public function isDefault(\GO_Files_Model_File $file) {
		return in_array(strtolower($file->extension), $this->defaultExtensions);
	}
	
	public function getName(){
		return GO::t('openInBrowser','files');
	}
	
	public function fileIsSupported(GO_Files_Model_File $file){
		return $file->isImage() || in_array(strtolower($file->extension),$this->defaultExtensions);
	}
	
	public function getIconCls(){
		return 'fs-browser';
	}
	
	public function getHandler(GO_Files_Model_File $file){
		return 'window.open("'.$file->getDownloadUrl(false, true).'");';
	}
}
?>