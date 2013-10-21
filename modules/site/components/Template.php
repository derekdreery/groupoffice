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
		$this->_checkLink();
		return Site::assetManager()->getBaseUrl().'/template/';
	}
	
	private function _checkLink() {
		
		$folder = new GO_Base_Fs_Folder(Site::assetManager()->getBasePath());
		if(!is_link($folder->path().'/template')){
			
			if(!symlink($this->getPath().'assets',$folder->path().'/template')){
				throw new Exception("Could not publish template assets. Is the \$config['file_storage_path'] path writable?");
			}
		}
	}
}