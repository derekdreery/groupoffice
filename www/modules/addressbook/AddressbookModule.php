<?php

class GO_Addressbook_AddressbookModule extends GO_Base_Module{
	
	
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
	
	public static function deleteUser($user){
		GO_Addressbook_Model_Addresslist::model()->deleteByAttribute('user_id', $user->id);
		GO_Addressbook_Model_Template::model()->deleteByAttribute('user_id', $user->id);		
	}

}