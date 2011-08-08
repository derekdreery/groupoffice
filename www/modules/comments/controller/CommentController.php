<?php
class GO_Comments_Controller_Comment extends GO_Base_Controller_AbstractModelController{

	protected $model = 'GO_Comments_Model_Comment';


	protected function getGridParams(){

		return array(
				'by' => array(array('link_id',$_REQUEST['link_id'],'='),array('link_type',$_REQUEST['link_type'],'=')),
				'ignoreAcl'=>true,
				'joinCustomFields'=>false
				);
	}

	protected function formatModelForGrid($record, $model) {
		$record['user_name']=$model->user->name;
		return $record;
	}

	protected function beforeGridActions(&$params) {
		
		$model = GO_Base_Model_SearchCacheRecord::model()->findByPk(array('id'=>$_REQUEST['link_id'], 'link_type'=>$_REQUEST['link_type']));

		$response['permisson_level']=$model->permissionLevel;
		$response['write_permission']=$model->permissionLevel>GO_SECURITY::WRITE_PERMISSION;
		if(!$response['permisson_level'])
		{
			throw new AccessDeniedException();
		}
		return $response;
	}

	protected $remoteComboFields=array(
			'user_id'=>array('user','name')
	);
}