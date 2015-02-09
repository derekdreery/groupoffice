<?php

namespace GO\Comments\Controller;


class CommentController extends \GO\Base\Controller\AbstractModelController{

	protected $model = 'GO\Comments\Model\Comment';


	protected function getStoreParams($params){

		return \GO\Base\Db\FindParams::newInstance()
						->ignoreAcl()	
						->select('t.*')
						->order('ctime','DESC')
						->criteria(
										\GO\Base\Db\FindCriteria::newInstance()
											->addCondition('model_id', $params['model_id'])
											->addCondition('model_type_id', \GO\Base\Model\ModelType::model()->findByModelName($params['model_name']))										
										);
	}
	
	protected function formatColumns(\GO\Base\Data\ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name','$model->user->name');
		return parent::formatColumns($columnModel);
	}
	
	protected function beforeStore(&$response, &$params, &$store) {
		
		$model = \GO::getModel($params['model_name'])->findByPk($params['model_id']);
		////\GO\Base\Model\SearchCacheRecord::model()->findByPk(array('model_id'=>$params['model_id'], 'model_type_id'=>\GO\Base\Model\ModelType::model()->findByModelName($params['model_name'])));

		$response['permisson_level']=$model->permissionLevel;
		$response['write_permission']=$model->checkPermissionLevel(\GO\Base\Model\Acl::WRITE_PERMISSION);
		if(!$response['permisson_level'])
		{
			throw new AccessDeniedException();
		}
		return $response;
	}
	
	protected function beforeSubmit(&$response, &$model, &$params) {
		
		$params['model_type_id']=\GO\Base\Model\ModelType::model()->findByModelName($params['model_name']);
		
		return parent::beforeSubmit($response, $model, $params);
	}
	
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		$modelTypeModel = \GO\Base\Model\ModelType::model()->findSingleByAttribute('id',$model->model_type_id);
		if ($modelTypeModel->model_name == 'GO\Addressbook\Model\Contact') {
			$modelWithComment = \GO::getModel($modelTypeModel->model_name)->findByPk($model->model_id);
			$modelWithComment->setAttribute('action_date',\GO\Base\Util\Date::to_unixtime($params['action_date']));
			$modelWithComment->save();
		}
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	
	protected function afterLoad(&$response, &$model, &$params) {
		$modelTypeModel = \GO\Base\Model\ModelType::model()->findSingleByAttribute('id',$model->model_type_id);
		if ($modelTypeModel->model_name == 'GO\Addressbook\Model\Contact') {
			$modelWithComment = \GO::getModel($modelTypeModel->model_name)->findByPk($model->model_id);
			$actionDate = $modelWithComment->getAttribute('action_date');
			$response['data']['action_date'] = \GO\Base\Util\Date::get_timestamp($actionDate,false);
		}
		return parent::afterLoad($response, $model, $params);
	}
	
	protected function actionCombinedStore($params) {
		$response = array(
			'success' => true,
			'total' => 0,
			'results' => array()
		);

		$cm = new \GO\Base\Data\ColumnModel();
		$cm->setColumnsFromModel(\GO::getModel('GO\Comments\Model\Comment'));
		
		$store = \GO\Base\Data\Store::newInstance($cm);
		
		$storeParams = $store->getDefaultParams($params)->mergeWith($this->getStoreParams($params));
		
		$findParams = \GO\Base\Db\FindParams::newInstance()
			->select('t.*,type.model_name')
			->joinModel(array(
				'model' => 'GO\Base\Model\ModelType',
				'localTableAlias' => 't',
				'localField' => 'model_type_id',
				'foreignField' => 'id',
				'tableAlias' => 'type'
			));

		$findParams->mergeWith($storeParams);
		
		$store->setStatement(\GO\Comments\Model\Comment::model()->find($findParams));
		return $store->getData();
//						
//		return $response;
	}
}