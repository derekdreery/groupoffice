<?php
class GO_Files_Filehandler_Inline implements GO_Files_Filehandler_Interface{

	public function supportedExtensions(){
		return array();
	}
	public function getName(){
		return GO::t('openInBrowser','files');
	}
	
	public function fileIsSupported(GO_Files_Model_File $file){
		return $file->isImage() || in_array(strtolower($file->extension),array('pdf','html','htm'));
	}
	
	public function getIconCls(){
		return 'fs-browser';
	}
	
	public function getHandler(GO_Files_Model_File $file){
		return 'window.open("'.$file->getDownloadUrl(false).'");';
	}
}
?>