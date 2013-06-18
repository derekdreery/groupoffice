<?php
class GO_Files_Filehandler_Download implements GO_Files_Filehandler_Interface{

	public function isDefault(\GO_Files_Model_File $file) {
		return false;
	}
	
	public function getName(){
		return GO::t('download');
	}
	
	public function fileIsSupported(GO_Files_Model_File $file){
		return true;
	}
	
	public function getIconCls(){
		return 'btn-download';
	}
	
	public function getHandler(GO_Files_Model_File $file){
		return 'window.location.href="'.$file->getDownloadUrl(true, true).'";';
	}
}
?>