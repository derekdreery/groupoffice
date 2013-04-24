<?php
class GO_Files_Filehandler_ImageViewer implements GO_Files_Filehandler_Interface{

	public function isDefault(\GO_Files_Model_File $file) {
		return $file->isImage();
	}
	
	public function getName(){
		return GO::t('imageViewer','files');
	}
	
	public function fileIsSupported(GO_Files_Model_File $file){
		return $file->isImage();
	}
	
	public function getIconCls(){
		return 'fs-imageviewer';
	}
	
	public function getHandler(GO_Files_Model_File $file){
		return 'GO.files.showImageViewer({id:'.$file->id.'});';
	}
}
?>