<?php
class GO_Controller_Note extends GO_BaseController{
	function init(){
		$this->addPermissionCheck(GO::modules()->modules['notes']['acl_id'], GO_SECURITY::WRITE_PERMISSION);
		
		$this->addPermissionCheck(GO::modules()->modules['notes']['acl_id'], GO_SECURITY::DELETE_PERMISSION,'delete');
		
	}
	
	public function actionGet($note_id){
		
		$note = new GO_Notes_Model_Note($note_id);
		
		var_dump($note);
		
//		$note->setAttributes($_POST);
//		$note->date = $note->formatdate($_POST['date']);
//		$note->save();
//		
		
		
		
	}
}

