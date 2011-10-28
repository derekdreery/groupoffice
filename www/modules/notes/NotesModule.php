<?php

class GO_Notes_NotesModule extends GO_Base_Module{
	
	
	public static function initListeners(){		
//		GO_Base_Model_User::model()->addListener('save', 'GO_Notes_NotesModule', 'saveUser');
//		GO_Base_Model_User::model()->addListener('delete', 'GO_Notes_NotesModule', 'deleteUser');		
	}	
	
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
		$category = self::getDefaultNoteCategory(GO::user()->id);
		
		return array('exportVariables'=>array(
				'GO'=>array(
						"notes"=>array(
								"defaultCategory"=>array(
									'id'=>$category->id,
									'name'=>$category->name
									)
						)
				)
		));
	}

	
	public static function getDefaultNoteCategory($userId){
		$user = GO_Base_Model_User::model()->findByPk($userId);
		$category = GO_Notes_Model_Category::model()->getDefault($user);
		
		return $category;
	}
}