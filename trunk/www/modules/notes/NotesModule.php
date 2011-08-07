<?php
class GO_Notes_NotesModule extends GO_Base_Model_Module{
	
	public function initListeners(){
		GO_Core_Controller_Core::addListener('loadapplication', 'GO_Notes_NotesModule', 'loadApplication');
	}	
	
	/**
	 * 
	 * When a user is created, updated or logs in this function will be called.
	 * The function can check if the default calendar, addressbook, notebook etc.
	 * is created for this user.
	 * 
	 */
	public function loadApplication(){	
		
		$userId = GO::user()->id;
		
		$category = GO_Notes_Model_Category::model()->findSingleByAttribute('user_id', $userId);
		if (!$category){
			$category = GO_Notes_Model_Category::model();
			
			$user = GO_Base_Model_User::model()->findByPk($userId);
			
			$category->user_id=$user->id;
			$category->name=$user->name;
			$category->makeAttributeUnique('name');
			$category->save();
		}		
	}
}