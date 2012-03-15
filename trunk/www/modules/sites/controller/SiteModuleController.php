<?php

class GO_Sites_Controller_SiteModule extends GO_Sites_Controller_AbstractSiteModule {
		
	protected function getDefaultPages($site_id=0){		
		
		return array(
			array('site_id'=>$site_id,'page'=>'login','controller'=>'GO_Sites_Controller_User','template'=>'login','action'=>'login','title'=>'Login','name'=>'Login'),
			array('site_id'=>$site_id,'page'=>'logout','controller'=>'GO_Sites_Controller_User','template'=>'logout','action'=>'logout','title'=>'Logout','name'=>'Logout'),
			array('site_id'=>$site_id,'page'=>'register','controller'=>'GO_Sites_Controller_User','template'=>'register','action'=>'register','title'=>'Register','name'=>'Register'),
			array('site_id'=>$site_id,'page'=>'resetpassword','controller'=>'GO_Sites_Controller_User','template'=>'resetpassword','action'=>'resetpassword','title'=>'Reset Password','name'=>'Reset Password'),
			array('site_id'=>$site_id,'page'=>'lostpassword','controller'=>'GO_Sites_Controller_User','template'=>'lostpassword','action'=>'recover','title'=>'Lost Password','name'=>'Lost Password')
		);
	}	
}