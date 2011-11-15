<?php
class GO_Addressbook_Controller_Contact extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Addressbook_Model_Contact';	
	
	protected function beforeSubmit(&$response, &$model, &$params) {
				
		if(!empty($params['company_id']) && $params['company_id']==$params['company']){			
			$company = GO_Addressbook_Model_Company::model()->findSingleByAttributes(array(
				'addressbook_id'=>$params['addressbook_id'],
				'name'=>$params['company_id']
			));
			
			if(!$company)
			{
				$company = new GO_Addressbook_Model_Company();
				$company->name=$params['company_id'];
				$company->addressbook_id=$params['addressbook_id'];
				if(!$company->save())
					throw new GO_Base_Exception_Save("Company ".$company->name);
			}
			$model->company_id=$company->id;
			unset($params['company_id']);
			
		}
		
		return parent::beforeSubmit($response, $model, $params);
	}
	
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		
		if(!empty($params['delete_photo'])){
			$model->photo='';
		}
		
		if (isset($_FILES['image']['tmp_name'][0]) && is_uploaded_file($_FILES['image']['tmp_name'][0])) {
			$f = new GO_Base_Fs_Folder(GO::config()->tmpdir);
			$f->create();
			
			move_uploaded_file($_FILES['image']['tmp_name'][0], GO::config()->tmpdir . $_FILES['image']['name'][0]);
			
			$model->photo = GO::config()->tmpdir . $_FILES['image']['name'][0];

			$response['photo_url'] = $model->photoURL;
		}
		
		
		$stmt = GO_Addressbook_Model_Addresslist::model()->find();
		while($addresslist = $stmt->fetch()){
			$linkModel = $addresslist->hasManyMany('contacts', $model->id);
			$mustHaveLinkModel = isset($params['addresslist_' . $addresslist->id]);
			if ($linkModel && !$mustHaveLinkModel) {
				$linkModel->delete();
			}
			if (!$linkModel && $mustHaveLinkModel) {
				$addresslist->addManyMany('contacts',$model->id);
			}
		}		
		
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		if (GO::modules()->customfields)
			$response['customfields'] = GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Addressbook_Model_Contact", $model->addressbook_id);
		
		$response['data']['photo_url']=$model->photoURL;		
		
		$stmt = $model->addresslists();
		while($addresslist = $stmt->fetch()){
			$response['data']['addresslist_'.$addresslist->id]=1;
		}
		
		return parent::afterLoad($response, $model, $params);
	}	
	
	protected function remoteComboFields() {
		return array(
				'addressbook_id'=>'$model->addressbook->name',
				'company_id'=>'$model->company->name'
				);
	}
	
	
	public function actionPhoto($params){

		$contact = GO::getModel($this->model)->findByPk($params['id']);
		
		if(empty($contact->photo))
			exit("No photo set");
		
		$file = new GO_Base_Fs_File($contact->photo);	

		header('Content-Length: '.$file->size());
		header('Content-Transfer-Encoding: binary');

		header("Last-Modified: ".gmdate("D, d M Y H:i:s", $file->mtime())." GMT");
		header("ETag: ".md5_file($file->path()));


		header("Expires: " . date("D, j M Y G:i:s ", time()+86400) . 'GMT');//expires in 1 day
		header('Cache-Control: cache');
		header('Pragma: cache');

		if (GO_Base_Util_Common::isInternetExplorer()) {
			header('Content-Type: application/download');
			header('Content-Disposition: inline; filename="'.$file->name().'"');
		}else {
			header('Content-Type: '.$file->mimeType());
			header('Content-Disposition: inline; filename="'.$file->name().'"');
		}

		$file->output();
	}
	

	protected function afterDisplay(&$response, &$model, &$params) {
			
		$response['data']['name']=$model->name;
		$response['data']['photo_url']=$model->photo_url;
		
		$company = $model->company();
		if($company){					
			$response['data']['company_name'] = $company->name;
			$response['data']['company_name2'] = $company->name2;
		} else {
			$response['data']['company_name'] = '';
			$response['data']['company_name2'] = '';
		}
		
		$response['data']['google_maps_link']=GO_Base_Util_Common::googleMapsLink(
						$model->address, $model->address_no,$model->city, $model->country);
		
		$response['data']['formatted_address']=nl2br(GO_Base_Util_Common::formatAddress(
						$model->country, $model->address, $model->address_no,$model->zip, $model->city, $model->state
						));
		
		
		
		return parent::afterDisplay($response, $model, $params);
	}
	
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('name','$model->name', array(),array('first_name','last_name'));
		$columnModel->formatColumn('company_name','$model->company->name');
		$columnModel->formatColumn('cf', '$model->id.":".$model->name');//special field used by custom fields. They need an id an value in one.)
		return parent::formatColumns($columnModel);
	}
	
	protected function getStoreMultiSelectProperties(){
		return array(
			'requestParam'=>'books',
			'permissionsModel'=>'GO_Addressbook_Model_Addressbook'
			//'titleAttribute'=>'name'
		);
	}	
	
	protected function getStoreParams($params) {	
		$query = !empty($params['query']) ? ($params['query']) : null;
		$field = isset($params['field']) ? ($params['field']) : 'name';
		$clicked_letter = isset($params['clicked_letter']) ? ($params['clicked_letter']) : false;
		
		$query_type = 'LIKE';
		if(!empty($clicked_letter))
		{
			if($clicked_letter=='[0-9]')
			{
				$query = '^[0-9].*$';
				$query_type = 'REGEXP';
			}else
			{
				$query= $clicked_letter.'%';
			}
		} else {
			$query = !empty($query) ? '%'.$query.'%' : '';
		}

		$criteria = GO_Base_Db_FindCriteria::newInstance()
			->addModel(GO_Addressbook_Model_Contact::model(),'t')
			->addInCondition('addressbook_id', $this->multiselectIds);
		
		if (!empty($query)) {
			if ($field=='name') {
				$criteria->addRawCondition('CONCAT_WS(`t`.`first_name`,`t`.`middle_name`,`t`.`last_name`)','\''.$query.'\'',$query_type);
			} else {
				$criteria->addCondition($field,$query,$query_type);
			}
		}

		$storeParams = GO_Base_Db_FindParams::newInstance()
			->criteria($criteria)
			->select('t.*t, ab.name AS addressbook_name')
			->joinModel(array(
				'model'=>'GO_Addressbook_Model_Addressbook',					
				'localField'=>'addressbook_id',
				'tableAlias'=>'ab' //Optional table alias
			));
		
		//if(empty($params['enable_addresslist_filter'])){
			if(isset($params['addresslist_filter']))
			{
				$addresslist_filter = json_decode($params['addresslist_filter'],true);
				if (!empty($addresslist_filter)) {
					$storeParams->join(GO_Addressbook_Model_AddresslistContact::model()->tableName(),
							GO_Base_Db_FindCriteria::newInstance()->addCondition('id', 'ac.contact_id', '=', 't', true, true),
							'ac'
						)->getCriteria()->addInCondition('addresslist_id', $addresslist_filter,'ac');
				}
				GO::config()->save_setting('ms_addresslist_filter', implode(',',$addresslist_filter), GO::user()->id);
			}
			//we should only add it if it's passed.
//			elseif ($addresslist_filter = GO::config()->get_setting('ms_addresslist_filter', GO::user()->id))
//			{	
//				$addresslist_filter = empty($addresslist_filter) ? array() : explode(',', $addresslist_filter);
//				$storeParams->join(GO_Addressbook_Model_AddresslistContact::model()->tableName(),
//						GO_Base_Db_FindCriteria::newInstance()->addCondition('id', 'ac.contact_id', '=', 't', true, true),
//						'ac'
//					)->getCriteria()->addInCondition('addresslist_id', $addresslist_filter,'ac');
//			}
		//}
		return $storeParams;
		
	}
	
	
	function actionEmployees($params) {
		$result['success'] = false;
		$company = GO_Addressbook_Model_Company::model()->findByPk($params['company_id']);
		if(GO_Base_Model_Acl::getUserPermissionLevel($company->getAcl()->id,GO::user()->id)<GO_Base_Model_Acl::WRITE_PERMISSION)
			throw new GO_Base_Exception_AccessDenied();
		if(isset($params['delete_keys']))
		{
			$response['deleteSuccess'] = true;
			try{
				$delete_contacts = json_decode(($params['delete_keys']));

				foreach($delete_contacts as $id)
				{
					$contact = GO_Addressbook_Model_Contact::model()->findByPk($id);
					$contact->setAttributes(array('id'=>$id,'company_id'=>0));
					$contact->save();
				}
			}
			catch (Exception $e)
			{
				$response['deleteFeedback'] = $strDeleteError;
				$response['deleteSuccess'] = false;
			}
		}

		if(isset($params['add_contacts']))
		{
			$add_contacts = json_decode(($params['add_contacts']));

			foreach($add_contacts as $id)
			{
				$contact = GO_Addressbook_Model_Contact::model()->findByPk($id);
				$contact->setAttributes(array('id'=>$id,'company_id'=>$params['company_id']));
				$contact->save();
			}			
		}

		$params['field'] = isset($params['field']) ? ($params['field']) : 'addressbook_name';

		$store = new GO_Base_Data_Store($this->getStoreColumnModel());	

		$this->prepareStore($store);
		
		$response['success']=true;
		
		$storeParams = $store->getDefaultParams()->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('company_id',$params['company_id']))
						->mergeWith($this->getStoreParams($params));
		$store->setStatement(call_user_func(array('GO_Addressbook_Model_Contact','model'))->find($storeParams));
		return array_merge($response, $store->getData());
	}
	
	public function actionChangeAddressbook($params) {
		$ids = json_decode($params['items']);
		
		$response['success'] = true;
		$response['failedToMove'] = array();
		
		foreach ($ids as $id) {
			$model = GO_Addressbook_Model_Contact::model()->findByPk($id);
			$failed_id = !$model->setAttribute('addressbook_id',$params['book_id']) ? $id : null;
			$failed_id = !$model->save() ? $id : null;
			if ($failed_id) {
				$response['failedToMove'][] = $failed_id;
				$response['success'] = false;
			}
		}
		
		return $response;
	}
}

