<?php

class GO_Sites_SitesModule extends GO_Base_Module{
		
	
	public static function initListeners(){		
//		GO_Base_Model_User::model()->addListener('save', 'GO_Notes_NotesModule', 'saveUser');
//		GO_Base_Model_User::model()->addListener('delete', 'GO_Notes_NotesModule', 'deleteUser');		
	}	
	
	public function autoInstall() {
		return true;
	}
	
	public function author() {
		return 'Wesley Smits';
	}
	
	public function authorEmail() {
		return 'wsmits@intermesh.nl';
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