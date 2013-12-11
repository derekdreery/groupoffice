<?php

class GO_Defaultsite_DefaultsiteModule extends \GO\Base\Module {

	public function author() {
		return 'Wesley Smits';
	}

	public function authorEmail() {
		return 'wsmits@intermesh.nl';
	}

	public function adminModule() {
		return false;
	}
	
	public function depends() {
		return array('site');
	}

	public function install() {
		
		if(\GO::modules()->isInstalled('site')){
			$alreadyExists = \GO_Site_Model_Site::model()->findSingleByAttribute('module','defaultsite');
			
			if(!$alreadyExists){
				
				$siteProperties = array(
					//'id'=>'', ID IS AUTO INCREMENT
					'name'=>\GO::t('name','defaultsite'),
					'user_id'=>1,
					//'mtime'=>'', AUTOMATIC
					//'ctime'=>'', AUTOMATIC
					'domain'=>'*',
					'module'=>'defaultsite',
					'ssl'=>'0',
					'mod_rewrite'=>'0',
					'mod_rewrite_base_path'=>'/',
					'base_path'=>'',
//					'acl_id'=>'0', AUTOMATIC
					'language'=>''
					//'files_folder_id'=>'' NOT NEEDED AUTOGENERATED
				);
				
				$defaultSite = new \GO_Site_Model_Site();
				$defaultSite->setAttributes($siteProperties);

				$defaultSite->save();
			}
		}
		
		return parent::install();
	}
	
}