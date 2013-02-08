<?php

class GO_Servermanager_Controller_SiteModule extends GO_Sites_Controller_AbstractSiteModule {	
	
	protected function getDefaultPages(){	
		return array(
				'newtrial'=>array(
						'site_id'=>$this->site_id,
						'controller'=>'GO_Servermanager_Controller_Site',
						'template'=>'newtrial',
						'controller_action'=>'newTrial',
						'path'=>'newtrial',
						'title'=>'New trial',
						'login_required'=>false,
						'name'=>'New trial'),
				'trialcreated'=>array(
						'site_id'=>$this->site_id,
						'controller'=>'GO_Servermanager_Controller_Site',
						'template'=>'trialcreated',
						'controller_action'=>'trialcreated',
						'path'=>'trialcreated',
						'title'=>'Trial created',
						'login_required'=>false,
						'name'=>'Trial created'),
				);
	}
		
}