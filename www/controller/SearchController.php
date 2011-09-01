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
	
	protected function prepareGrid($grid) {
		$grid->formatColumn('iconCls', '"go-model-".$model->mode_name');		
		$grid->formatColumn('name_and_type', '"(".$model->type.") ".$model->name');
		$grid->formatColumn('name_name_and_id', '$model->model_name.":".$model->model_id');
		//$grid->formatColumn('type', 'class_exists($model->model_name) ? call_user_func(array($model->model_name, "model"))->localizedName');
	}
	
	
	public function actionModelTypes(){
		$grid = new GO_Base_Provider_Grid();		    
		$stmt = GO_Base_Model_ModelType::model()->find($grid->getDefaultParams());
		$grid->setStatement($stmt);
		$grid->formatColumn('name', 'class_exists($model->model_name) ? call_user_func(array($model->model_name,"model"))->localizedName : $model->model_name');	
		
		return $grid->getData();		
	}
	
	
}