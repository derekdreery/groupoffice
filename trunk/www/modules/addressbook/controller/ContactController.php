<?php
/*
 * Copyright Intermesh BV
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 *
 */

class GO_Addressbook_Controller_Contact extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Addressbook_Model_Contact';	
		
	protected function beforeSubmit(&$response, &$model, &$params) {
		
		if (!empty($model->id) && !empty($model->addressbook) && $model->addressbook->id != $params['addressbook_id']) {
			$this->actionChangeAddressbook(array(
				'items'	=> '["'.$model->id.'"]',
				'book_id' => $params['addressbook_id']
			));
		}
		
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
				$company->save();
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

		if (GO_Base_Util_Http::isInternetExplorer()) {
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
		$response['data']['photo_url']=$model->photoURL;
		$response['data']['addressbook_name']=$model->addressbook->name;
		
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
		$columnModel->formatColumn('ab_name','$model->addressbook->name');
		
		$columnModel->formatColumn('cf', '$model->id.":".$model->name');//special field used by custom fields. They need an id an value in one.)
		return parent::formatColumns($columnModel);
	}
	
	/*
	 * This function initiates the contact filter by checked addressbooks.
	 */
	protected function getStoreMultiSelectProperties(){
		return array(
			'requestParam'=>'books',
			'permissionsModel'=>'GO_Addressbook_Model_Addressbook'
			//'titleAttribute'=>'name'
		);
	}	
	
	/**
	 *
	 * @param GO_Base_Db_FindCriteria $c
	 * @param type $advancedQueryData 
	 */
//	private function _handleAdvancedQuery($c, $advancedQueryData){
//		$advancedQueryData = json_decode($advancedQueryData, true);
//		
//		foreach($advancedQueryData as $record){
//			if(!empty($record['field'])){
//				//
//				
//				
//				$isCustomField = substr($record['field'],0,4)=='col_';
//				
//				if ($record['comparator']=='LIKE')
//					$record['value'] = '%'.$record['value'].'%';
//				
//				if($isCustomField){
//					$tableAlias = 'cf';
//				}else
//				{
//					$tableAlias = 't';
//					$record['value']=GO_Filesearch_Model_Filesearch::model()->formatInput($record['field'], $record['value']);
//				}
//				
//				if($record['close_group']){
//					//$oldC = clone $c;
//					$c->mergeWith(GO_Base_Db_FindCriteria::newInstance()->addCondition($record['field'], $record['value'], $record['comparator'],$tableAlias,$record['andor']=='AND'),$record['andor']=='AND');
//				}else
//				{								
//					$c->addCondition($record['field'], $record['value'], $record['comparator'],$tableAlias,$record['andor']=='AND');
//				}
//			}
//		}
//		
//		return $c;
//	}
	
	/*
	 * This function initiates the contact filters by:
	 * - search query (happens automatically in GO base class)
	 * - by clicked letter
	 * - checked addresslists
	 */
	protected function getStoreParams($params) {	
	
		$criteria = GO_Base_Db_FindCriteria::newInstance()
			->addModel(GO_Addressbook_Model_Contact::model(),'t')
			->addInCondition('addressbook_id', $this->multiselectIds);
				
		// Filter by clicked letter
		if (!empty($params['clicked_letter'])) {
			if ($params['clicked_letter'] == '[0-9]') {
				$query = '^[0-9].*$';
				$query_type = 'REGEXP';
			} else {
				$query = $params['clicked_letter'] . '%';
				$query_type = 'LIKE';
			}
			//$criteria->addRawCondition('CONCAT_WS(`t`.`first_name`,`t`.`middle_name`,`t`.`last_name`)', ':query', $query_type);
			$queryCrit = GO_Base_Db_FindCriteria::newInstance();
			$queryCrit->addRawCondition('first_name', ':query', $query_type)
				->addRawCondition('middle_name', ':query', $query_type, false)
				->addRawCondition('last_name', ':query', $query_type, false);
			$queryCrit->addBindParameter(':query', $query);
			$criteria->mergeWith($queryCrit);
		}
	
		$storeParams = GO_Base_Db_FindParams::newInstance()
			->export("contact")
			->criteria($criteria)						
			->select('t.*t, addressbook.name AS addressbook_name, CONCAT_WS(\' \',`t`.`first_name`,`t`.`middle_name`,`t`.`last_name`) AS name');
		
		//if(empty($params['enable_addresslist_filter'])){
		
		// Filter by addresslist
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
		$storeParams->debugSql();
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
		$this->formatColumns($store->getColumnModel());

		$response['success']=true;
		
		$storeParams = $store->getDefaultParams($params)->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('company_id',$params['company_id']))
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
			if (!empty($model->company)) {
				$companyContr = new GO_Addressbook_Controller_Company();
				$resp = $companyContr->actionChangeAddressbook(array(
					'items' => '["'.$model->company->id.'"]',
					'book_id' => $params['book_id']
				));
				array_merge($response['failedToMove'],$resp['failedToMove']);
			} else {
				$failed_id = !($model->setAttribute( 'addressbook_id' , $params['book_id'] ) && $model->save()) ? $id : null;
				if ($failed_id) {
					$response['failedToMove'][] = $failed_id;
					$response['success'] = false;
				}
			}
		}
		
		return $response;
	}

	protected function beforeHandleAdvancedQuery ($advQueryRecord, GO_Base_Db_FindCriteria &$criteriaGroup, GO_Base_Db_FindParams &$storeParams) {
		switch ($advQueryRecord['field']) {
			case 'companies.name':
				$storeParams->join(
					GO_Addressbook_Model_Company::model()->tableName(),
					GO_Base_Db_FindCriteria::newInstance()->addRawCondition('`t`.`company_id`','`companies'.$advQueryRecord['id'].'`.`id`'),
					'companies'.$advQueryRecord['id']
				);
				$criteriaGroup->addRawCondition(
					'companies'.$advQueryRecord['id'].'.name',
					':company_name'.$advQueryRecord['id'],
					$advQueryRecord['comparator'],
					$advQueryRecord['andor']=='AND'
				);
				$criteriaGroup->addBindParameter(':company_name'.$advQueryRecord['id'], $advQueryRecord['value']);
				return false;
				break;
			case 'contact_name':
				$criteriaGroup->addRawCondition(
					'CONCAT_WS(\' \',`t`.`first_name`,`t`.`middle_name`,`t`.`last_name`)',
					':contact_name'.$advQueryRecord['id'],
					$advQueryRecord['comparator'],
					$advQueryRecord['andor']=='AND'
				);
				$criteriaGroup->addBindParameter(':contact_name'.$advQueryRecord['id'], $advQueryRecord['value']);
				return false;
				break;
			default:
				//parent::integrateInSqlSearch($advQueryRecord, $findCriteria, $storeParams);
				return true;
				break;
		}
	}
	
	protected function afterAttributes(&$attributes, &$response, &$params, GO_Base_Db_ActiveRecord $model) {
		unset($attributes['t.company_id']);
		//$attributes['name']=GO::t('strName');
		$attributes['companies.name']=GO::t('company','addressbook');
		$attributes['contact_name']=GO::t('name');
		return parent::afterAttributes($attributes, $response, $params, $model);
	}
	
		
	protected function beforeImport(&$model, &$attributes, $record) {
		
		if(!empty($attributes['company'])){
				$company = GO_Addressbook_Model_Company::model()->findSingleByAttribute('name', $attributes['company']);
			
			if($company)
				$model->company_id = $company->id;
		}
		
		return parent::beforeImport($model, $attributes, $record);
	}
	
	
	/**
	 * Function exporting addressbook contents to VCFs. Must be called from export.php.
	 * @param type $params 
	 */
	public function actionVCard($params) {
		$contact = GO_Addressbook_Model_Contact::model()->findByPk($params['id']);
		
		$filename = $contact->name.'.vcf';
		GO_Base_Util_Http::outputDownloadHeaders(new GO_Base_FS_File($filename));		
		
		echo $contact->toVObject()->serialize();
	}
	
	
	public function actionImportVCard($params){
		$contact = new GO_Addressbook_Model_Contact();
		
		$file = new GO_Base_Fs_File($params['file']);
		$data = $file->getContents();
		$vobject = GO_Base_VObject_Reader::read($data);
		unset($params['file']);
		
		GO_Base_VObject_Reader::convertVCard21ToVCard30($vobject);
	
		$contact->importVObject($vobject, $params);
	}
	
}

