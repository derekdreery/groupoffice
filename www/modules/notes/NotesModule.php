<?php
class GO_Notes_NotesModule extends GO_Base_Model_Module{
	
	public function initListeners(){
		
	}	
	
	/**
	 * 
	 * When a user is created, updated or logs in this function will be called.
	 * The function can check if the default calendar, addressbook, notebook etc.
	 * is created for this user.
	 * 
	 */
	public function initUser($userId){		
		
		$category = GO_Notes_Model_Category::model()->findSingleByAttribute('user_id', $userId);
		if (!$category){
			$category = GO_Notes_Model_Category::model();
			
			$category->user_id=GO::user()->id;
			$category->name=GO::user()->name;
			$category->makeAttributeUnique('name');
			$category->save();
		}		
	}
}