<?php
class GO_Core_Controller_Search extends GO_Base_Controller_AbstractModelController{
	protected $model = 'GO_Base_Model_SearchCacheRecord';
	
	protected function getStoreParams($params) {
		$storeParams = array();
		if(isset($params['types'])){
			$types = json_decode($params['types'], true);
			if(count($types)){
				$storeParams['by']=array(
									array('model_type_id', $types,'IN')
							);
			}
		}
		return $storeParams;
	}
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('iconCls', '"go-model-".$model->model_name');		
		$columnModel->formatColumn('name_and_type', '"(".$model->type.") ".$model->name');
		$columnModel->formatColumn('model_name_and_id', '$model->model_name.":".$model->model_id');
		return parent::formatColumns($columnModel);
	}
	
	public function actionModelTypes($params){
		
		$store = GO_Base_Data_Store::newInstance(GO_Base_Model_ModelType::model());			
		$store->getColumnModel()->formatColumn('name', 'GO::getModel($model->model_name)->localizedName');

		$store->setStatement (GO_Base_Model_ModelType::model()->find($store->getDefaultParams()));
		
		return $store->getData();		
	}
	
	
	
	public function actionLinks($params){
		
		$model = GO::getModel($params['model_name'])->findByPk($params['model_id']);
		
			$storeParams=array();
		if(isset($params['types'])){
			$types = json_decode($params['types'], true);
			if(count($types)){
				$storeParams['by']=array(
									array('model_type_id', $types,'IN')
							);
			}
		}
		
		
		$store = GO_Base_Data_Store::newInstance(GO_Base_Model_SearchCacheRecord::model());					
		$stmt = GO_Base_Model_SearchCacheRecord::model()->findLinks($model, $store->getDefaultParams($storeParams));
		$store->setStatement($stmt);
		
		$cm = $store->getColumnModel();		
		$cm->formatColumn('iconCls', '"go-model-".$model->model_name');		
		$cm->formatColumn('name_and_type', '"(".$model->type.") ".$model->name');
		$cm->formatColumn('model_name_and_id', '$model->model_name.":".$model->model_id');
		$cm->formatColumn('link_count','GO::getModel($model->model_name)->countLinks($model->model_id)');

		return $store->getData();
	}
	
	
}