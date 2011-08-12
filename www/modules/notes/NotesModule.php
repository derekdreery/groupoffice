<?php

class GO_Notes_NotesModule extends GO_Base_Model_Module{
	
	public static function initListeners(){		
		GO_Base_Model_User::addListener('save', 'GO_Notes_NotesModule', 'saveUser');
		GO_Base_Model_User::addListener('delete', 'GO_Notes_NotesModule', 'deleteUser');		
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
		self::createDefaultNoteCategory(GO::user()->id);	
	}
	
	
	public static function saveUser($user){
		self::createDefaultNoteCategory($user->id);	
	}
	
	public static function deleteUser($user){
		$stmt = GO_Notes_Model_Category::model()->find(array(
				'by'=>array(array('user_id', $user->id)),
				'ignoreAcl'=>true
				));
		
		$stmt->callOnEach('delete');
	}
	
	
	public static function createDefaultNoteCategory($userId){
		$category = GO_Notes_Model_Category::model()->findSingleByAttribute('user_id', $userId);
		if (!$category){
			$category = new GO_Notes_Model_Category();
			
			$user = GO_Base_Model_User::model()->findByPk($userId);
			
			$category->user_id=$user->id;
			$category->name=$user->name;
			$category->makeAttributeUnique('name');
			$category->save();
		}		
	}
}