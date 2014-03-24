<?php


namespace GO\Manualsite;

use GO;
use GO\Base\Module;
use GO\Site\Model\Site;

class ManualsiteModule extends Module {

	public function adminModule() {
		return false;
	}
	
	public function depends() {
		return array('site');
	}

	public function install() {
		
		if(GO::modules()->isInstalled('site')){
			$alreadyExists = Site::model()->findSingleByAttribute('module','adminmanual');
			
			if(!$alreadyExists){
				
				$siteProperties = array(
					'name'=>"Manual",
					'user_id'=>1,
					'domain'=>'*',
					'module'=>'manualsite',
					'ssl'=>'0',
					'mod_rewrite'=>'0',
					'mod_rewrite_base_path'=>'/',
					'base_path'=>'',
					'language'=>'en'
				);
				
				$defaultSite = new Site();
				$defaultSite->setAttributes($siteProperties);
				$defaultSite->save();
				
				
			}
			
			$category = \GO\Customfields\Model\Category::model()->createIfNotExists("GO\Site\Model\Site", "Extra");
			\GO\Customfields\Model\Field::model()->createIfNotExists($category->id, "Google tracking code");
		}
		
		return parent::install();
	}
	
}