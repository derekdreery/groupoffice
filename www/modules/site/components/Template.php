<?php
class GO_Site_Components_Template{
	
	/**
	 * Get the path to the template folder
	 * 
	 * @return string
	 */
	public function getPath(){		
		if(empty(Site::model()->module))
			return false;
		
		return GO::config()->root_path . 'modules/' . Site::model()->module . '/views/site/';	
	}
	
	/**
	 * Get URL to template folder. This is a static alias defined in the apache
	 * config
	 * 
	 * @return string
	 */
	public function getUrl(){
		//when using rewrite we must publish the assets with a symlink
		if(Site::model()->mod_rewrite){
			$this->_checkLink();
			return '/public/assets/template/';
		}else
		{
			return GO::config()->host . 'modules/' . Site::model()->module . '/views/site/assets/';
		}
	}
	
	private function _checkLink() {
		
		$folder = Site::model()->getFileStorageFolder();
		
		$public = $folder->createChild('public/assets',false);
		$public->create();
		
		if(!is_link($public->path().'/template')){
			
			if(!symlink($this->getPath().'assets',$public->path().'/template')){
				throw new Exception("Could not publish template assets. Is the \$config['file_storage_path'] path writable?");
			}
		}
	}
}