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
//			$params['match']=$params["query"];
//			unset($params["query"]);
		}
	
		
		return parent::beforeStore($response, $params, $store);
	}
	
	protected function getStoreParams($params) {
		$storeParams = GO_Base_Db_FindParams::newInstance();
		
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
		
//		$subCriteria = GO_Base_Db_FindCriteria::newInstance();
//		
//		if(strlen($params['match'])<4){
//			$subCriteria->addCondition('keywords', '%'.trim($params['match'],' *%').'%', 'LIKE','t',false);
//		}else
//		{
//			$str='+'.preg_replace('/[\s]+/',' +', $params['match']);	
//			$subCriteria->addMatchCondition(array('keywords'), $str);
//		}
//		
//		$storeParams->getCriteria()->mergeWith($subCriteria);
		
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
		
		//we'll do a full text search in getStoreParams			
		$params['match']=isset($params["query"]) ? $params["query"] : '';
		unset($params["query"]);
		
		$storeParams = $store->getDefaultParams($params)->select("t.*,l.description AS link_description");
		
		$storeParams->mergeWith($this->getStoreParams($params));
		
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
	
	
	
	protected function actionEmail($params) {

		$response['success'] = true;
		$response['results'] = array();

		if (empty($params['query']))
			return $response;

		$query = '%' . preg_replace('/[\s*]+/', '%', $params['query']) . '%';



		if (GO::modules()->addressbook) {
			$response = array('total' => 0, 'results' => array());

			$userContactIds = array();
			$findParams = GO_Base_Db_FindParams::newInstance()
							->searchQuery($query, array("CONCAT(t.first_name,' ',t.middle_name,' ',t.last_name)", 't.email', 't.email2', 't.email3'))
							->select('t.*, "' . addslashes(GO::t('strUser')) . '" AS ab_name,c.name AS company_name')
							->limit(10)
							->joinModel(array(
					'model' => 'GO_Addressbook_Model_Company',
					'foreignField' => 'id', //defaults to primary key of the remote model
					'localField' => 'company_id', //defaults to "id"
					'tableAlias' => 'c', //Optional table alias
					'type' => 'LEFT' //defaults to INNER,
							));

			if (!empty($params['requireEmail'])) {
				$criteria = GO_Base_Db_FindCriteria::newInstance()
								->addCondition("email", "", "!=")
								->addCondition("email2", "", "!=", 't', false)
								->addCondition("email3", "", "!=", 't', false);

				$findParams->getCriteria()->mergeWith($criteria);
			}

			$stmt = GO_Addressbook_Model_Contact::model()->findUsers(GO::user()->id, $findParams);

			$userContactIds = array();

			foreach ($stmt as $contact) {

				$this->_formatContact($response,$contact);

				$userContactIds[] = $contact->id;
			}
			




			if (count($response['results']) < 10) {


				$findParams = GO_Base_Db_FindParams::newInstance()
								->ignoreAcl()
								->select('t.*,c.name AS company_name, a.name AS ab_name')
								->searchQuery($query, array(
										"CONCAT(t.first_name,' ',t.middle_name,' ',t.last_name, ' ',a.name)",
										't.email',
										't.email2',
										't.email3'
								))
								->joinModel(array(
										'model' => 'GO_Addressbook_Model_Addressbook',
										'foreignField' => 'id', //defaults to primary key of the remote model
										'localField' => 'addressbook_id', //defaults to "id"
										'tableAlias' => 'a', //Optional table alias
										'type' => 'INNER' //defaults to INNER,
								))
								->limit(10-count($response['results']));


				//		if(!empty($params['joinCompany'])){
				$findParams->joinModel(array(
						'model' => 'GO_Addressbook_Model_Company',
						'foreignField' => 'id', //defaults to primary key of the remote model
						'localField' => 'company_id', //defaults to "id"
						'tableAlias' => 'c', //Optional table alias
						'type' => 'LEFT' //defaults to INNER,
				));
				//		}

				$findParams->getCriteria()->addInCondition('id', $userContactIds, 't', true, true);


				if (!empty($params['addressbook_id'])) {
					$abs = array($params['addressbook_id']);
				} else {
					$abs = GO_Addressbook_Model_Addressbook::model()->getAllReadableAddressbookIds();
				}

				if (!empty($abs)) {

					$findParams->getCriteria()->addInCondition('addressbook_id', $abs);

					if (!empty($params['requireEmail'])) {
						$criteria = GO_Base_Db_FindCriteria::newInstance()
										->addCondition("email", "", "!=")
										->addCondition("email2", "", "!=", 't', false)
										->addCondition("email3", "", "!=", 't', false);

						$findParams->getCriteria()->mergeWith($criteria);
					}

					$stmt = GO_Addressbook_Model_Contact::model()->find($findParams);

					$user_ids = array();
					foreach ($stmt as $contact) {
						$this->_formatContact($response,$contact);

						if ($contact->go_user_id)
							$user_ids[] = $contact->go_user_id;
					}
				}
			}
		}else {

			//no addressbook module for this user. Fall back to user search.
			$findParams = GO_Base_Db_FindParams::newInstance()
							->searchQuery($query)
							->select('t.*')
							->limit(10 - count($response['results']));


			$stmt = GO_Base_Model_User::model()->find($findParams);

			while ($user = $stmt->fetch()) {
				$record['name'] = $user->name;
				$record['user_id'] = $user->id;

				$l = new GO_Base_Mail_EmailRecipients();
				$l->addRecipient($user->email, $record['name']);

				$record['info'] = htmlspecialchars((string) $l . ' (' . GO::t('strUser') . ')', ENT_COMPAT, 'UTF-8');
				$record['full_email'] = htmlspecialchars((string) $l, ENT_COMPAT, 'UTF-8');

				$response['results'][] = $record;
			}
		}

		
		return $response;
	}

	private function _formatContact(&$response, $contact) {
		$record['name'] = $contact->name;
		$record['contact_id'] = $contact->id;
		$record['user_id'] = $contact->go_user_id;
		if ($contact->email != "") {
			$l = new GO_Base_Mail_EmailRecipients();
			$l->addRecipient($contact->email, $record['name']);

			$record['info'] = htmlspecialchars((string) $l . ' (' . sprintf(GO::t('contactFromAddressbook', 'addressbook'), $contact->addressbook->name) . ')', ENT_COMPAT, 'UTF-8');
			if (!empty($contact->department))
				$record['info'].=' (' . htmlspecialchars($contact->department, ENT_COMPAT, 'UTF-8') . ')';
			$record['full_email'] = htmlspecialchars((string) $l, ENT_COMPAT, 'UTF-8');

			$response['results'][] = $record;
		}

		if ($contact->email2 != "") {
			$l = new GO_Base_Mail_EmailRecipients();
			$l->addRecipient($contact->email2, $record['name']);

			$record['info'] = htmlspecialchars((string) $l . ' (' . sprintf(GO::t('contactFromAddressbook', 'addressbook'), $contact->addressbook->name) . ')', ENT_COMPAT, 'UTF-8');
			if (!empty($contact->department))
				$record['info'].=' (' . htmlspecialchars($contact->department, ENT_COMPAT, 'UTF-8') . ')';
			$record['full_email'] = htmlspecialchars((string) $l, ENT_COMPAT, 'UTF-8');

			$response['results'][] = $record;
		}

		if ($contact->email3 != "") {
			$l = new GO_Base_Mail_EmailRecipients();
			$l->addRecipient($contact->email3, $record['name']);

			$record['info'] = htmlspecialchars((string) $l . ' (' . sprintf(GO::t('contactFromAddressbook', 'addressbook'), $contact->addressbook->name) . ')', ENT_COMPAT, 'UTF-8');
			if (!empty($contact->department))
				$record['info'].=' (' . htmlspecialchars($contact->department, ENT_COMPAT, 'UTF-8') . ')';
			$record['full_email'] = htmlspecialchars((string) $l, ENT_COMPAT, 'UTF-8');

			$response['results'][] = $record;
		
		}

	}

}