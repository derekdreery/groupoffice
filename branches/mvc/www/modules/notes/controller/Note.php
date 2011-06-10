<?php
class GO_Notes_Controller_Note extends GO_Base_Controller_AbstractController{
	function init($output){
		parent::init($output);
		
		$this->addPermissionCheck(GO::modules()->modules['notes']['acl_id'], GO_SECURITY::WRITE_PERMISSION);
		
		$this->addPermissionCheck(GO::modules()->modules['notes']['acl_id'], GO_SECURITY::DELETE_PERMISSION,'delete');
		
	}
	
	public function actionSave($note_id=0){
		
		$note = new GO_Notes_Model_Note($note_id);
	
		$note->setAttributes($_POST);
		$response['success'] = $note->save();
		
		$this->output($response);		
	}
}

