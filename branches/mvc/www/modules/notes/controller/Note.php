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
		$note = new GO_Notes_Model_Note($_REQUEST['id']);
		
		go_debug($note);
		
		$response['data']=$note->getAttributes();		
		$response['success']=true;
		
		$response=$this->_loadComboTexts($response, $note);
		
		$this->output($response);		
	}
	
	/**
	 * List all fields that require a remote text to load for a remote combobox.
	 * eg. with a note you want to provide the category name so that that the
	 * category combo store does not need to be loaded to show it.
	 * 
	 * You would list that like this:
	 * 
	 * 'category_id'=>array('category','name')
	 * 
	 * The category name would be looked up in the note model ->category->name.
	 * A relation for this must be defined. See ActiveRecord->relations.
	 * 
	 * 
	 * @var array remote combo mappings 
	 */
	
	protected $remoteComboFields=array(
			'category_id'=>array('category','name')
	);	
	
	
	private function _loadComboTexts($response, $model){
		
		$response['remoteComboTexts']=array();
		
		foreach($this->remoteComboFields as $property=>$map){
			$response['remoteComboTexts'][$property]=$model->{$map[0]}->{$map[1]};
		}
		
		return $response;
		
	}
}

