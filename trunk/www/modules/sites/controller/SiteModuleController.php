<?php

class GO_Sites_Controller_SiteModule extends GO_Sites_Controller_AbstractSiteModule {
		
	protected function getDefaultPages(){		
		
		return array(
			array('site_id'=>$this->site_id,'path'=>'login','controller'=>'GO_Sites_Controller_User','template'=>'login','action'=>'login','title'=>'Login','name'=>'Login'),
			array('site_id'=>$this->site_id,'path'=>'logout','controller'=>'GO_Sites_Controller_User','template'=>'logout','action'=>'logout','title'=>'Logout','name'=>'Logout'),
			array('site_id'=>$this->site_id,'path'=>'register','controller'=>'GO_Sites_Controller_User','template'=>'register','action'=>'register','title'=>'Register','name'=>'Register'),
			array('site_id'=>$this->site_id,'path'=>'resetpassword','controller'=>'GO_Sites_Controller_User','template'=>'resetpassword','action'=>'resetpassword','title'=>'Reset Password','name'=>'Reset Password'),
			array('site_id'=>$this->site_id,'path'=>'lostpassword','controller'=>'GO_Sites_Controller_User','template'=>'lostpassword','action'=>'recover','title'=>'Lost Password','name'=>'Lost Password')
		);
	}	
}