<?php

class GO_Site_Controller_Front extends GO_Site_Controller_Abstract {
	protected function allowGuests() {
		return array('content','thumb');
	}
	
	protected function actionContent($params){
		$content = GO_Site_Model_Content::model()->findBySlug($params['slug']);
		$this->render($content->template,array('content'=>$content));
	}
	
	
	protected function actionThumb($params){
			
		$rootFolder = new GO_Base_Fs_Folder(GO::config()->file_storage_path.'site/'.Site::model()->id);
		$file = new GO_Base_Fs_File(GO::config()->file_storage_path.'site/'.Site::model()->id.'/'.$params['src']);
		$folder = $file->parent();
		
		$ok = $folder->isSubFolderOf($rootFolder);
		
		if(!$ok)
			Throw new GO_Base_Exception_AccessDenied();
		
		
		$c = new GO_Core_Controller_Core();
		return $c->run('thumb', $params, true, false);
	}
	
	
}