<?php
class GO_Core_Controller_Search extends GO_Base_Controller_AbstractModelController{
	protected $model = 'GO_Base_Model_SearchCacheRecord';
	
	protected function beforeStore(&$response, &$params, &$store) {
		//handle deletes for searching differently
		
		if(!empty($params['delete_keys'])){
			
			try{
				$keys = json_decode($params['delete_keys'], true);
				unset($params['delete_keys']);
				foreach($keys as $key){
					$key = explode(':',$key);

					$linkedModel = GO::getModel($key[0])->findByPk($key[1]);				
					$linkedModel->delete();				
				}
				unset($params['delete_keys']);
				$response['deleteSuccess']=true;
			}
			catch(Exception $e){
				$response['deleteSuccess']=false;
				$response['deleteFeedback']=$e->getMessage();
			}
		}
	
		
		return parent::beforeStore($response, $params, $store);
	}
	
	protected function getStoreParams($params) {
		$storeParams = GO_Base_Db_FindParams::newInstance();
		if(isset($params['types'])){
			$types = json_decode($params['types'], true);
			if(count($types))
				$storeParams->getCriteria()->addInCondition('model_type_id', $types);
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
	
		
		$store = GO_Base_Data_Store::newInstance(GO_Base_Model_SearchCacheRecord::model());
		
		//$model->unlink($model);
		
		if(!empty($params['unlinks'])){
			$keys = json_decode($params['unlinks'], true);
			
			foreach($keys as $key){
				$key = explode(':',$key);
				
				$linkedModel = GO::getModel($key[0])->findByPk($key[1]);				
				$model->unlink($linkedModel);				
			}
		}
		
//		if(!empty($params['delete_keys'])){
//			
//			$keys = json_decode($params['delete_keys'], true);
//			
//			foreach($keys as $key){
//				$key = explode(':',$key);
//				
//				$linkedModel = GO::getModel($key[0])->findByPk($key[1]);				
//				$linkedModel->delete();				
//			}
//		}
		
		$storeParams = $store->getDefaultParams();
		
		if(isset($params['types'])){
			$types = json_decode($params['types'], true);
			if(count($types))
				$storeParams->getCriteria ()->addInCondition ('model_type_id', $types);
		}
		
		
		$stmt = GO_Base_Model_SearchCacheRecord::model()->findLinks($model, $storeParams);
		$store->setStatement($stmt);
		
		$cm = $store->getColumnModel();		
		$cm->formatColumn('iconCls', '"go-model-".$model->model_name');		
		$cm->formatColumn('name_and_type', '"(".$model->type.") ".$model->name');
		$cm->formatColumn('model_name_and_id', '$model->model_name.":".$model->model_id');
		$cm->formatColumn('link_count','GO::getModel($model->model_name)->countLinks($model->model_id)');

		$data = $store->getData();
		
		$data['permissionLevel']=$model->getPermissionLevel();
		return $data;
	}
	
	
}