<?php


namespace GO\AdminManual;

use GO;
use GO\Base\Module;
use GO\Site\Model\Site;

class AdminmanualModule extends Module {

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
					'name'=>"Admin manual",
					'user_id'=>1,
					'domain'=>'*',
					'module'=>'adminmanual',
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
		}
		
		return parent::install();
	}
	
}