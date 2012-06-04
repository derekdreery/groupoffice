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
		
		//search query is required
		if(empty($params["query"])){
			return false;
		}
	
		
		return parent::beforeStore($response, $params, $store);
	}
	
	protected function getStoreParams($params) {
		$storeParams = GO_Base_Db_FindParams::newInstance()
						->select('t.*');
		
		if(isset($params['model_names'])){
			$model_names = json_decode($params['model_names'], true);
			$types = array();
			foreach($model_names as $model_name){
				$types[]=GO::getModel($model_name)->modelTypeId();
			}
			if(count($types))
			$storeParams->getCriteria()->addInCondition('model_type_id', $types);
		}
		
		if(!empty($params['type_filter'])) {
			if(isset($params['types'])) {
				$types= json_decode($params['types'], true);
				//only search for available types. eg. don't search for contacts if the user doesn't have access to the addressbook
				if(empty($types)){
					$stmt = GO_Base_Model_ModelType::model()->find();
					while($modelType = $stmt->fetch()){
						$model = GO::getModel($modelType->model_name);
						if(GO::modules()->{$model->module})
							$types[]=$modelType->id;
					}

				}
				if(!isset($params['no_filter_save']))
					GO::config()->save_setting ('link_type_filter', implode(',',$types), GO::user()->id);
			}else {
				$types = GO::config()->get_setting('link_type_filter', GO::user()->id);
				$types = empty($types) ? array() : explode(',', $types);	
			}
		}
		
		
		
		$storeParams->getCriteria()->addInCondition('model_type_id', $types);
		
		return $storeParams;
	}
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('iconCls', '"go-model-".$model->model_name');		
		$columnModel->formatColumn('name_and_type', '"(".$model->type.") ".$model->name');
		$columnModel->formatColumn('model_name_and_id', '$model->model_name.":".$model->model_id');
		return parent::formatColumns($columnModel);
	}
	
	protected function actionModelTypes($params){
		
		$stmt = GO_Base_Model_ModelType::model()->find();
		
		$typesString = GO::config()->get_setting('link_type_filter',GO::user()->id);
		$typesArr = explode(',',$typesString);
		
		$types=array();
		while($modelType = $stmt->fetch()){
			$model = GO::getModel($modelType->model_name);
			if(GO::modules()->{$model->module})
				$types[$model->localizedName]=array('id'=>$modelType->id, 'model_name'=>$modelType->model_name, 'name'=>$model->localizedName, 'checked'=>in_array($modelType->id,$typesArr));
		}
		
		ksort($types);
		
		$response['total']=count($types);
		$response['results']=array_values($types);
	
		
		return $response;		
	}
	
	
	
	protected function actionLinks($params){
		
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
		
		$storeParams = $store->getDefaultParams($params);
		
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