<?php


namespace GO\Servermanager\Controller;


class SiteModule extends \GO\Sites\Controller\AbstractSiteModule {	
	
	protected function getDefaultPages(){	
		return array(
				'newtrial'=>array(
						'site_id'=>$this->site_id,
						'controller'=>'GO\Servermanager\Controller\Site',
						'template'=>'newtrial',
						'controller_action'=>'newTrial',
						'path'=>'newtrial',
						'title'=>'New trial',
						'login_required'=>false,
						'name'=>'New trial'),
				'trialcreated'=>array(
						'site_id'=>$this->site_id,
						'controller'=>'GO\Servermanager\Controller\Site',
						'template'=>'trialcreated',
						'controller_action'=>'trialcreated',
						'path'=>'trialcreated',
						'title'=>'Trial created',
						'login_required'=>false,
						'name'=>'Trial created'),
				);
	}
		
}