<?php
class GO_Comments_Controller_Comment extends GO_Base_Controller_AbstractModelController{

	protected $model = 'GO_Comments_Model_Comment';


	protected function getGridParams($params){

		return array(
				'by' => array(
						array('model_id',$params['model_id'],'='),
						array('model_type_id',GO_Base_Model_ModelType::model()->findByModelName($params['model_name']),'=')
						),
				'ignoreAcl'=>true,
				'joinCustomFields'=>false
				);
	}
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name','$model->user->name');
		return parent::formatColumns($columnModel);
	}
	
	protected function beforeGrid(&$response, &$params, &$grid) {
		
		$model = GO_Base_Model_SearchCacheRecord::model()->findByPk(array('model_id'=>$params['model_id'], 'model_type_id'=>GO_Base_Model_ModelType::model()->findByModelName($params['model_name'])));

		$response['permisson_level']=$model->permissionLevel;
		$response['write_permission']=$model->permissionLevel>  GO_Base_Model_Acl::WRITE_PERMISSION;
		if(!$response['permisson_level'])
		{
			throw new AccessDeniedException();
		}
		return $response;
	}
	
	protected function beforeSubmit(&$response, &$model, &$params) {
		
		$params['model_type_id']=GO_Base_Model_ModelType::model()->findByModelName($params['model_name']);
		
		return parent::beforeSubmit($response, $model, $params);
	}
}