<?php

class GO_Modules_ModulesModule extends GO_Base_Module{
	
	public static function initListeners(){		

	}	
	
	/**
	 * 
	 * When a user is created, updated or logs in this function will be called.
	 * The function can check if the default calendar, addressbook, notebook etc.
	 * is created for this user.
	 * 
	 */
	public static function firstRun(){
		parent::firstRun();
		
	}
}