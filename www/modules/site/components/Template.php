<?php

namespace GO\Site\Components;

use GO;

class Template{
	
	/**
	 * Get the path to the template folder
	 * 
	 * @return string
	 */
	public function getPath(){		
		if(empty(\Site::model()->module))
			return false;
		
		return \GO::config()->root_path . 'modules/' . \Site::model()->module . '/views/site/';	
	}
	
	/**
	 * Get URL to template folder. This is a static alias defined in the apache
	 * config
	 * 
	 * @return string
	 */
	public function getUrl(){
		$this->_checkLink();
		return \Site::assetManager()->getBaseUrl().'/template/';
	}
	
	private function _checkLink() {
		
		$folder = new \GO\Base\Fs\Folder(\Site::assetManager()->getBasePath());
				
//		if(!GO_Base_Util_Common::isWindows()){					
//			//Symlinks not supported on Windows
//			if(!is_link($folder->path().'/template')){
//				if(!symlink($this->getPath().'assets',$folder->path().'/template')){
//					throw new Exception("Could not publish template assets. Is the \$config['file_storage_path'] path writable?");
//				}
//			}
//		}else
//		{			
			$templateFolder = $folder->createChild('template', false);
			

			$mtime = GO::config()->get_setting('site_template_publish_date_'.\Site::model()->id);
			
			if(GO::config()->debug || $mtime != GO::config()->mtime || $templateFolder->exists()){
				$templateFolder->delete();
				
				$sourceTemplateFolder = new \GO\Base\Fs\Folder($this->getPath().'assets');
				
				if($sourceTemplateFolder->copy($folder, 'template')){
					GO::config()->save_setting('site_template_publish_date_'.\Site::model()->id, GO::config()->mtime);
				}
			}
//		}
	}
}