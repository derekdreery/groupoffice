<?php
class GO_Notes_Controller_Note extends GO_Base_Controller_AbstractController{
	function init($output){
		parent::init($output);		
		$this->addPermissionCheck(GO::modules()->{$this->module}->acl_id, GO_SECURITY::READ_PERMISSION);		
		//$this->addPermissionCheck(GO::modules()->modules['notes']['acl_id'], GO_SECURITY::DELETE_PERMISSION,'delete');
	}
	
	public function actionSave(){
		
		$note = new GO_Notes_Model_Note($_POST['id']);	
		$note->setAttributes($_POST);
		
		$response['success'] = $note->save();
		
		$this->output($response);		
	}
	
	public function actionLoad(){
		$note = new GO_Notes_Model_Note($_POST['id']);
		
		go_debug($note);
		
		$response['data']=$note->getAttributes();
		$response['success']=true;
		
		$this->output($response);		
	}
	
	public function actionTest(){
		$note = new GO_Notes_Model_Note();
		$stmt = $note->find(array('by'=>array(array('category_id','1'))));
		
		while($o = $stmt->fetch()){
			var_dump($o->category);
		}
		
	}
	
	protected $remoteComboFields=array(
			//'user_id'=>'user->name',
			'category_id'=>'category->name'
	);
	
	private function loadComboTexts(){
		foreach($this->remoteComboFields as $property=>$map){
			
		}
		
	}
}

