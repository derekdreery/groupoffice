<?php
class GO_Core_Controller_Search extends GO_Base_Controller_AbstractModelController{
	protected $model = 'GO_Base_Model_SearchCacheRecord';
	
	protected function getGridParams($params) {
		$gridParams = array();
		if(isset($params['types'])){
			$types = json_decode($params['types'], true);
			if(count($types)){
				$gridParams['by']=array(
									array('model_type_id', $types,'IN')
							);
			}
		}
		return $gridParams;
	}
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('iconCls', '"go-model-".$model->model_name');		
		$columnModel->formatColumn('name_and_type', '"(".$model->type.") ".$model->name');
		$columnModel->formatColumn('model_name_and_id', '$model->model_name.":".$model->model_id');
		return parent::formatColumns($columnModel);
	}
	
	public function actionModelTypes($params){
		
		$grid = GO_Base_Data_Store::newInstance(GO_Base_Model_ModelType::model(), array(), $grid->getDefaultParams());			
		$grid->getColumnModel()->formatColumn('name', 'GO::getModel($model->model_name)->localizedName');

		return $grid->getData();		
	}
	
	
	
	public function actionLinks($params){
		
		$model = GO::getModel($params['model_name'])->findByPk($params['model_id']);
		
			$gridParams=array();
		if(isset($params['types'])){
			$types = json_decode($params['types'], true);
			if(count($types)){
				$gridParams['by']=array(
									array('model_type_id', $types,'IN')
							);
			}
		}
		
		
		$grid = GO_Base_Data_Store::newInstance(GO_Base_Model_ModelType::model());					
		$stmt = GO_Base_Model_SearchCacheRecord::model()->findLinks($model, $grid->getDefaultParams($gridParams));
		$grid->setStatement($stmt);
		
		$cm = $grid->getColumnModel();		
		$cm->formatColumn('iconCls', '"go-model-".$model->model_name');		
		$cm->formatColumn('name_and_type', '"(".$model->type.") ".$model->name');
		$cm->formatColumn('model_name_and_id', '$model->model_name.":".$model->model_id');
		$cm->formatColumn('link_count','GO::getModel($model->model_name)->countLinks($model->model_id)');

		return $grid->getData();
	}
	
	
}