<?php
class GO_Comments_Controller_Comment extends GO_Base_Controller_AbstractModelController{

	protected $model = 'GO_Comments_Model_Comment';


	protected function getStoreParams($params){

		return GO_Base_Db_FindParams::newInstance()
						->ignoreAcl()	
						->select('t.*')
						->order('id','DESC')
						->criteria(
										GO_Base_Db_FindCriteria::newInstance()
											->addCondition('model_id', $params['model_id'])
											->addCondition('model_type_id', GO_Base_Model_ModelType::model()->findByModelName($params['model_name']))										
										);
	}
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name','$model->user->name');
		return parent::formatColumns($columnModel);
	}
	
	protected function beforeStore(&$response, &$params, &$store) {
		
		$model = GO::getModel($params['model_name'])->findByPk($params['model_id']);
		////GO_Base_Model_SearchCacheRecord::model()->findByPk(array('model_id'=>$params['model_id'], 'model_type_id'=>GO_Base_Model_ModelType::model()->findByModelName($params['model_name'])));

		$response['permisson_level']=$model->permissionLevel;
		$response['write_permission']=$model->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION);
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
	
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		$modelTypeModel = GO_Base_Model_ModelType::model()->findSingleByAttribute('id',$model->model_type_id);
		if ($modelTypeModel->model_name == 'GO_Addressbook_Model_Contact') {
			$modelWithComment = GO::getModel($modelTypeModel->model_name)->findByPk($model->model_id);
			$modelWithComment->setAttribute('action_date',GO_Base_Util_Date::to_unixtime($params['action_date']));
			$modelWithComment->save();
		}
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	
	protected function afterLoad(&$response, &$model, &$params) {
		$modelTypeModel = GO_Base_Model_ModelType::model()->findSingleByAttribute('id',$model->model_type_id);
		if ($modelTypeModel->model_name == 'GO_Addressbook_Model_Contact') {
			$modelWithComment = GO::getModel($modelTypeModel->model_name)->findByPk($model->model_id);
			$actionDate = $modelWithComment->getAttribute('action_date');
			$response['data']['action_date'] = GO_Base_Util_Date::get_timestamp($actionDate,false);
		}
		return parent::afterLoad($response, $model, $params);
	}
	
	protected function actionCombinedStore($params) {
		$response = array(
			'success' => true,
			'total' => 0,
			'results' => array()
		);

		$cm = new GO_Base_Data_ColumnModel();
		$cm->setColumnsFromModel(GO::getModel('GO_Comments_Model_Comment'));
		
		$store = GO_Base_Data_Store::newInstance($cm);
		
		$storeParams = $store->getDefaultParams($params)->mergeWith($this->getStoreParams($params));
		
		$findParams = GO_Base_Db_FindParams::newInstance()
			->select('t.*,type.model_name')
			->joinModel(array(
				'model' => 'GO_Base_Model_ModelType',
				'localTableAlias' => 't',
				'localField' => 'model_type_id',
				'foreignField' => 'id',
				'tableAlias' => 'type'
			));

		$findParams->mergeWith($storeParams);
		
		$store->setStatement(GO_Comments_Model_Comment::model()->find($findParams));
		return $store->getData();
//						
//		return $response;
	}
}