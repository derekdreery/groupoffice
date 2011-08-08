<?php
class GO_Comments_Controller_Comment extends GO_Base_Controller_AbstractModelController{

	protected $model = 'GO_Comments_Model_Comment';


	protected function getGridParams(){

		return array(
				'ignoreAcl'=>true,
				'joinCustomFields'=>false
				);
	}

	protected function formatModelForGrid($record, $model) {
		$record['user_name']=$model->user->name;
		return $record;
	}

	protected function beforeGridActions(&$params) {
		$params['by'] = array(array('link_id',$_REQUEST['link_id'],'='),array('link_type',$_REQUEST['link_type'],'='));

		// TODO : Change to 4.0
		require_once(GO::config()->class_path.'/base/search.class.inc.php');
		$search = new search();
		$record = $search->get_search_result($_REQUEST['link_id'], $_REQUEST['link_type']);
		$response['permisson_level']=$GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $record['acl_id']);
		$response['write_permission']=$response['permisson_level']>GO_SECURITY::WRITE_PERMISSION;
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