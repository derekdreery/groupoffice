<?php

class GO_Sites_Controller_SiteModule extends GO_Sites_Controller_AbstractSiteModule {
		
	protected function getDefaultPages(){		
		return array(
			array('page'=>'login','controller'=>'GO_Sites_Controller_User','template'=>'login','action'=>'login','title'=>'Login','name'=>'Login'),
			array('page'=>'logout','controller'=>'GO_Sites_Controller_User','template'=>'logout','action'=>'logout','title'=>'Logout','name'=>'Logout'),
			array('page'=>'register','controller'=>'GO_Sites_Controller_User','template'=>'register','action'=>'register','title'=>'Register','name'=>'Register'),
			array('page'=>'resetpassword','controller'=>'GO_Sites_Controller_User','template'=>'resetpassword','action'=>'resetpassword','title'=>'Reset Password','name'=>'Reset Password'),
			array('page'=>'lostpassword','controller'=>'GO_Sites_Controller_User','template'=>'lostpassword','action'=>'recover','title'=>'Lost Password','name'=>'Lost Password')
		);
	}	
}