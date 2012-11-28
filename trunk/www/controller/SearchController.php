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
		}else
		{
			//we'll do a full text search in getStoreParams			
			$params['match']=$params["query"];
			unset($params["query"]);
		}
	
		
		return parent::beforeStore($response, $params, $store);
	}
	
	protected function getStoreParams($params) {
		$storeParams = GO_Base_Db_FindParams::newInstance()
						->select('t.*')
						->debugSql();
		
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
			}else {
				$types = GO::config()->get_setting('link_type_filter', GO::user()->id);
				$types = empty($types) ? array() : explode(',', $types);	
			}
			
			//only search for available types. eg. don't search for contacts if the user doesn't have access to the addressbook
			if(empty($types))
					$types=$this->_getAllModelTypes();
			
			if(!isset($params['no_filter_save']) && isset($params['types']))
				GO::config()->save_setting ('link_type_filter', implode(',',$types), GO::user()->id);
		}else
		{
			$types=$this->_getAllModelTypes();
		}		
		
		$storeParams->getCriteria()->addInCondition('model_type_id', $types);
		
		
		//if (!empty($matchQuery))
		
		
		$str='+'.preg_replace('/[\s]+/',' +', $params['match']);
		//$str=str_replace('-','*',$str);
		//throw new Exception($str);
		
		$subCriteria = GO_Base_Db_FindCriteria::newInstance()
						//->addMatchCondition(array('name','keywords'), $params['match'],'t',true,'NATURAL');
						->addMatchCondition(array('name','keywords'), $str)
						->addCondition('name', preg_replace('/[\s*]+/','%',$params['match']).'%', 'LIKE','t',false);
		
		$storeParams->getCriteria()->mergeWith($subCriteria);
		
		return $storeParams;
	}
	
	private function _getAllModelTypes(){
		$types=array();
		$stmt = GO_Base_Model_ModelType::model()->find();
		while($modelType = $stmt->fetch()){
			$model = GO::getModel($modelType->model_name);
			$module = $modelType->model_name == "GO_Base_Model_User" ? "users" : $model->module;
			if(GO::modules()->{$module})
				$types[]=$modelType->id;
		}
		return $types;

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
			
			$module = $modelType->model_name == "GO_Base_Model_User" ? "users" : $model->module;
			
			if(GO::modules()->{$module})
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
		
		$storeParams = $store->getDefaultParams($params)->select("t.*,l.description AS link_description");
		
		//if(!empty($params['folder_id']))
		$storeParams->getCriteria ()->addCondition ('folder_id', $params['folder_id'],'=','l');
		
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
	
	
	
	protected function actionEmail($params){
		
		$response['success']=true;
		$response['results']=array();
	
		if(empty($params['query']))
			return $response;
		
		$query = '%'.preg_replace ('/[\s*]+/','%', $params['query']).'%'; 
		
		if(GO::modules()->addressbook){
			$findParams = GO_Base_Db_FindParams::newInstance()
							->searchQuery($query,array("CONCAT(t.first_name,' ',t.middle_name,' ',t.last_name)",'t.email','t.email2','t.email3'))
							->select('t.*, addressbook.name AS ab_name')
							->limit(10);
			

			$criteria = GO_Base_Db_FindCriteria::newInstance()
							->addCondition("email", "","!=")
							->addCondition("email2", "","!=",'t',false)
							->addCondition("email3", "","!=",'t',false);

			$findParams->getCriteria()->mergeWith($criteria);
							
			$stmt = GO_Addressbook_Model_Contact::model()->find($findParams);
			

			while($contact = $stmt->fetch()){
				$record['name']=$contact->name;

				if($contact->email!=""){
					$l = new GO_Base_Mail_EmailRecipients();
					$l->addRecipient($contact->email, $record['name']);

					$record['info']=htmlspecialchars((string) $l.' ('.sprintf(GO::t('contactFromAddressbook','addressbook'), $contact->ab_name).')', ENT_COMPAT, 'UTF-8');
					$record['full_email']=htmlspecialchars((string) $l , ENT_COMPAT, 'UTF-8');										

					$response['results'][]=$record;
				}

				if($contact->email2!=""){
					$l = new GO_Base_Mail_EmailRecipients();
					$l->addRecipient($contact->email2, $record['name']);

					$record['info']=htmlspecialchars((string) $l.' ('.sprintf(GO::t('contactFromAddressbook','addressbook'), $contact->ab_name).')', ENT_COMPAT, 'UTF-8');
					$record['full_email']=htmlspecialchars((string) $l , ENT_COMPAT, 'UTF-8');										

					$response['results'][]=$record;
				}

				if($contact->email3!=""){
					$l = new GO_Base_Mail_EmailRecipients();
					$l->addRecipient($contact->email3, $record['name']);

					$record['info']=htmlspecialchars((string) $l.' ('.sprintf(GO::t('contactFromAddressbook','addressbook'), $contact->ab_name).')', ENT_COMPAT, 'UTF-8');
					$record['full_email']=htmlspecialchars((string) $l , ENT_COMPAT, 'UTF-8');										

					$response['results'][]=$record;
				}
			}

			if(count($response['results'])<10) {

				$findParams = GO_Base_Db_FindParams::newInstance()
								->limit(10-count($response['results']))
								->select('t.*, addressbook.name AS ab_name')
								->searchQuery($query,array('t.name','t.email'));

				$findParams->getCriteria()						
								->addCondition("email", "","!=");

				$stmt = GO_Addressbook_Model_Company::model()->find($findParams);

				while($company = $stmt->fetch()){
					$record['name']=$company->name;

					$l = new GO_Base_Mail_EmailRecipients();
					$l->addRecipient($company->email, $record['name']);

					$record['info']=htmlspecialchars((string) $l.' ('.sprintf(GO::t('companyFromAddressbook','addressbook'), $company->ab_name).')', ENT_COMPAT, 'UTF-8');
					$record['full_email']=htmlspecialchars((string) $l , ENT_COMPAT, 'UTF-8');										

					$response['results'][]=$record;

				}
			}
			
		}
		
		if(count($response['results'])<10)
		{
			//no addressbook module for this user. Fall back to user search.
			$findParams = GO_Base_Db_FindParams::newInstance()
						->searchQuery($query)
						->select('t.*')
						->limit(10-count($response['results']));

			
			$stmt = GO_Base_Model_User::model()->find($findParams);

			while($user = $stmt->fetch()){
				$record['name']=$user->name;

				$l = new GO_Base_Mail_EmailRecipients();
				$l->addRecipient($user->email, $record['name']);

				$record['info']=htmlspecialchars((string) $l.' ('.GO::t('strUser').')', ENT_COMPAT, 'UTF-8');
				$record['full_email']=htmlspecialchars((string) $l , ENT_COMPAT, 'UTF-8');										

				$response['results'][]=$record;

			}
		}
		
		return $response;
	}
	
}