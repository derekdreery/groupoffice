<?php

class GO_Calendar_CalendarModule extends GO_Base_Module{
	
	
	public function author() {
		return 'Merijn Schering';
	}
	
	public function authorEmail() {
		return 'mschering@intermesh.nl';
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
	
	public static function initListeners() {		
		GO_Base_Model_Reminder::model()->addListener('dismiss', "GO_Calendar_Model_Event", "reminderDismissed");
	}
}