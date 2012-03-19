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
	
	protected function allowGuests() {
		return array('photo');
	}
		
	protected function beforeSubmit(&$response, &$model, &$params) {
		
		if (!empty($model->id) && !empty($model->addressbook) && $model->addressbook->id != $params['addressbook_id']) {
			$this->run("changeAddressbook",array(
				'items'	=> '["'.$model->id.'"]',
				'book_id' => $params['addressbook_id']
			),false);
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
			
			$destinationFile = new GO_Base_Fs_File(GO::config()->tmpdir . $_FILES['image']['name'][0]);
			
			move_uploaded_file($_FILES['image']['tmp_name'][0], $destinationFile->path());
			
			$model->photo = $destinationFile->path();

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
	
	
	protected function actionPhoto($params){
		$file = new GO_Base_Fs_File(GO::config()->file_storage_path.'contacts/contact_photos/'.$params['id'].'.jpg');
		if(!$file->exists())
			exit("No photo set");		
		GO_Base_Util_Http::outputDownloadHeaders($file, true, true);
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
		$columnModel->formatColumn('name','$model->getName(GO::user()->sort_name)', array(),array('first_name','last_name'), GO::t('strName'));
		$columnModel->formatColumn('company_name','$model->company_name', array(),'', GO::t('company','addressbook'));
		$columnModel->formatColumn('ab_name','$model->ab_name', array(),'', GO::t('addressbook','addressbook'));
		
		$columnModel->formatColumn('cf', '$model->id.":".$model->name');//special field used by custom fields. They need an id an value in one.)
		return parent::formatColumns($columnModel);
	}
	


	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store, GO_Base_Db_FindParams $storeParams) {
		
		$abMultiSel = new GO_Base_Component_MultiSelectGrid(
						'books', 
						"GO_Addressbook_Model_Addressbook",$store, $params);		
		$abMultiSel->addSelectedToFindCriteria($storeParams->getCriteria(), 'addressbook_id');
//		$abMultiSel->setButtonParams($response);
//		$abMultiSel->setStoreTitle();
		
		$addresslistMultiSel = new GO_Base_Component_MultiSelectGrid(
						'addresslist_filter', 
						"GO_Addressbook_Model_Addresslist",$store, $params);				
		
		if(count($addresslistMultiSel->selectedIds))
		{
			$addresslistMultiSel->addSelectedToFindCriteria($storeParams->getCriteria(), 'addresslist_id','ac');
			
			//we need to join the addresslist link model if a filter for the addresslist is enabled.
			$storeParams->join(GO_Addressbook_Model_AddresslistContact::model()->tableName(),
							GO_Base_Db_FindCriteria::newInstance()->addCondition('id', 'ac.contact_id', '=', 't', true, true),
							'ac'
				);
		}
		
		return parent::beforeStoreStatement($response, $params, $store, $storeParams);
	}
	
	/*
	 * This function initiates the contact filters by:
	 * - search query (happens automatically in GO base class)
	 * - by clicked letter
	 * - checked addresslists
	 */
	protected function getStoreParams($params) {	
	
		$criteria = GO_Base_Db_FindCriteria::newInstance()
			->addModel(GO_Addressbook_Model_Contact::model(),'t');
				
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
			$queryCrit = GO_Base_Db_FindCriteria::newInstance()			
				->addRawCondition(GO::user()->sort_name, ':query', $query_type)
				->addBindParameter(':query', $query);
				
			$criteria->mergeWith($queryCrit);
		}
	
		$storeParams = GO_Base_Db_FindParams::newInstance()
			->export("contact")
			->criteria($criteria)		
			->joinModel(array(
				'model'=>'GO_Addressbook_Model_Company',					
	 			'foreignField'=>'id', //defaults to primary key of the remote model
	 			'localField'=>'company_id', //defaults to "id"
	 			'tableAlias'=>'c', //Optional table alias
	 			'type'=>'LEFT' //defaults to INNER,
	 			
			))
			->select('t.*,c.name AS company_name, addressbook.name AS ab_name, CONCAT_WS(\' \',`t`.`first_name`,`t`.`middle_name`,`t`.`last_name`) AS name');
	
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
	
	protected function actionChangeAddressbook($params) {
		$ids = json_decode($params['items']);
		
		$response['success'] = true;
		$response['failedToMove'] = array();
		
		foreach ($ids as $id) {
			$model = GO_Addressbook_Model_Contact::model()->findByPk($id);
			if (!empty($model->company)) {
				$companyContr = new GO_Addressbook_Controller_Company();
				$resp = $companyContr->run("changeAddressbook",array(
					'items' => '["'.$model->company->id.'"]',
					'book_id' => $params['book_id']
				),false);
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
		$attributes['companies.name']=array('name'=>'companies.name','label'=>GO::t('company','addressbook'));
		$attributes['contact_name']=array('name'=>'contact_name','label'=>GO::t('name'));
		
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
	protected function actionVCard($params) {
		$contact = GO_Addressbook_Model_Contact::model()->findByPk($params['id']);
		
		$filename = $contact->name.'.vcf';
		GO_Base_Util_Http::outputDownloadHeaders(new GO_Base_FS_File($filename));		
		
		$vobject = $contact->toVObject();
		
		if(!empty($params['vcard21']))
			GO_Base_VObject_Reader::convertVCard30toVCard21($vobject);
		
		echo $vobject->serialize();
	}
	
	
	protected function actionImportVCard($params){
		$contact = new GO_Addressbook_Model_Contact();
		
		$file = new GO_Base_Fs_File($params['file']);
		$data = $file->getContents();		
		$vobject = GO_Base_VObject_Reader::read($data);
		unset($params['file']);
		
		GO_Base_VObject_Reader::convertVCard21ToVCard30($vobject);
	
		$contact->importVObject($vobject, $params);
	}
	
}

