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
		GO_Addressbook_Model_Addressbook::model()->deleteByAttribute('user_id', $user->id);
		GO_Addressbook_Model_Addresslist::model()->deleteByAttribute('user_id', $user->id);
		GO_Addressbook_Model_Template::model()->deleteByAttribute('user_id', $user->id);
		
	}
	
	public static function saveUser($user, $wasNew) {
		
		if($wasNew)
			GO_Addressbook_AddressbookModule::getDefaultAddressbook($user->id);
		
		return parent::saveUser($user, $wasNew);
	}
	
	public static function getDefaultAddressbook($userId){
		$addressbook = GO_Addressbook_Model_Addressbook::model()->findSingleByAttribute('user_id', $userId);
		if (!$addressbook){
			$addressbook = new GO_Addressbook_Model_Addressbook();
			
			$user = GO_Base_Model_User::model()->findByPk($userId);
			
			$addressbook->user_id=$user->id;
			$addressbook->name=$user->name;
			$addressbook->makeAttributeUnique('name');
			$addressbook->save();
		}
		
		return $addressbook;
	}
}