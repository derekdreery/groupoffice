<?php
class GO_Controller_Note extends GO_BaseController{
	function init(){
		$this->addPermissionCheck(GO::modules()->modules['notes']['acl_id'], GO_SECURITY::WRITE_PERMISSION);
	}
	
	public function actionGet($note_id){
		echo $note_id;
	}
}

