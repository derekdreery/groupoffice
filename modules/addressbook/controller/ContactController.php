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
		//if user typed in a new company name manually we set this attribute so a new company will be autocreated.
		if(!is_numeric($params['company_id'])){
			$model->company_name = $params['company_id'];
		}
		
		
		return parent::beforeSubmit($response, $model, $params);
	}
	
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		
		$stmt = GO_Addressbook_Model_Addresslist::model()->find(GO_Base_Db_FindParams::newInstance()->permissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION));
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
		
		
		if(!empty($params['delete_photo'])){
			$model->removePhoto();
			$model->save();
		}
		if (isset($_FILES['image']['tmp_name'][0]) && is_uploaded_file($_FILES['image']['tmp_name'][0])) {
		
			
			$destinationFile = new GO_Base_Fs_File(GO::config()->getTempFolder()->path().'/'.$_FILES['image']['name'][0]);
			
			move_uploaded_file($_FILES['image']['tmp_name'][0], $destinationFile->path());
			
			$model->setPhoto($destinationFile);
			$model->save();
			$response['photo_url'] = $model->photoThumbURL;
			$response['original_photo_url'] = $model->photoURL;
		}elseif(!empty($params['download_photo_url'])){
			
			$file = GO_Base_Fs_File::tempFile();	
			$c = new GO_Base_Util_HttpClient();
			
			if(!$c->downloadFile($params['download_photo_url'], $file))
				throw new Exception("Could not download photo from: '".$params['download_photo_url']."'");
						
			$model->setPhoto($file);
			$model->save();					
			$response['photo_url'] = $model->photoThumbURL;
			$response['original_photo_url'] = $model->photoURL;
		}
		
		
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		if (GO::modules()->customfields)
			$response['customfields'] = GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Addressbook_Model_Contact", $model->addressbook_id);
		
		$response['data']['photo_url']=$model->photoThumbURL;		
		$response['data']['original_photo_url']=$model->photoURL;
		
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
		//fetching contact will check read permission
		$contact = GO_Addressbook_Model_Contact::model()->findByPk($params['id']);
		
		GO_Base_Util_Http::outputDownloadHeaders($contact->getPhotoFile(), true, false);
		$contact->getPhotoFile()->output();
	}
	

	protected function afterDisplay(&$response, &$model, &$params) {
			
		$response['data']['name']=$model->name;
		$response['data']['photo_url']=$model->photoThumbURL;
		$response['data']['original_photo_url']=$model->photoURL;
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
		
		$response['data']['formatted_address']=nl2br($model->getFormattedAddress());
		
		
		
		if(GO::modules()->customfields && isset($response['data']['customfields']) && GO_Customfields_Model_DisableCategories::isEnabled("GO_Addressbook_Model_Contact", $model->addressbook_id)){

			$ids = GO_Customfields_Model_EnabledCategory::model()->getEnabledIds("GO_Addressbook_Model_Contact", $model->addressbook_id);
			
			$enabled = array();
			foreach($response['data']['customfields'] as $cat){
				if(in_array($cat['id'], $ids)){
					$enabled[]=$cat;
				}
			}
			$response['data']['customfields']=$enabled;
		}
		
		
		if (GO::modules()->isInstalled('customfields')) {
			
			$response['data']['items_under_blocks'] = array();
			
			$enabledBlocksStmt = GO_Customfields_Model_EnabledBlock::getEnabledBlocks($model->addressbook_id, 'GO_Addressbook_Model_Addressbook', $model->className());
			foreach ($enabledBlocksStmt as $i => $enabledBlockModel) {
				
				$items = $enabledBlockModel->block->getItemNames($model->id,$model->name);
				
				if (!empty($items)) {
					$blockedItemsEl = array(
						'id' => $i,
						'block_name' => $enabledBlockModel->block->name,
						'items' => $items
					);

					$blockedItemsEl['model_name'] = !empty($items[0]) ? $items[0]['model_name'] : '';
					$modelNameArr = explode('_', $blockedItemsEl['model_name']);
					$blockedItemsEl['type'] = !empty($modelNameArr[3]) ? $modelNameArr[3] : '';

					$response['data']['items_under_blocks'][] = $blockedItemsEl;
				}
			}
			
		}
		
		
		return parent::afterDisplay($response, $model, $params);
	}
	
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		
		$sortAlias = GO::user()->sort_name=="first_name" ? array('first_name','last_name') : array('last_name','first_name');
		
		$columnModel->formatColumn('name','$model->getName(GO::user()->sort_name)', array(),$sortAlias, GO::t('strName'));
		$columnModel->formatColumn('company_name','$model->company_name', array(),'', GO::t('company','addressbook'));
		$columnModel->formatColumn('ab_name','$model->ab_name', array(),'', GO::t('addressbook','addressbook'));
		$columnModel->formatColumn('age', '$model->age', array(), 'birthday');
		
		$columnModel->formatColumn('cf', '$model->id.":".$model->name');//special field used by custom fields. They need an id an value in one.)
		return parent::formatColumns($columnModel);
	}
	


	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store, GO_Base_Db_FindParams $storeParams) {
		if(!empty($params['filters'])){
			$abMultiSel = new GO_Base_Component_MultiSelectGrid(
							'books', 
							"GO_Addressbook_Model_Addressbook",$store, $params, true);		
			
			$abMultiSel->addSelectedToFindCriteria($storeParams, 'addressbook_id');
	//		$abMultiSel->setButtonParams($response);
	//		$abMultiSel->setStoreTitle();

			$addresslistMultiSel = new GO_Base_Component_MultiSelectGrid(
							'addresslist_filter', 
							"GO_Addressbook_Model_Addresslist",$store, $params, false);				

			if(!empty($params['addresslist_filters']))
			{
				$addresslistMultiSel->addSelectedToFindCriteria($storeParams, 'addresslist_id','ac');

				if(count($addresslistMultiSel->selectedIds)){
					//we need to join the addresslist link model if a filter for the addresslist is enabled.
					$storeParams->join(GO_Addressbook_Model_AddresslistContact::model()->tableName(),
									GO_Base_Db_FindCriteria::newInstance()->addCondition('id', 'ac.contact_id', '=', 't', true, true),
									'ac'
						);
				}
			}
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
		
		$searchFields = GO_Addressbook_Model_Contact::model()->getFindSearchQueryParamFields();
		$searchFields[]="c.name";
		
		$storeParams = GO_Base_Db_FindParams::newInstance()
			->export("contact")
			->joinAclFieldTable()
			->criteria($criteria)		
			->searchFields($searchFields)
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

	public function actionMergeEmailWithContact($params) {
		$email = (isset($params['email']) && $params['email']) ? $params['email'] : '';
		$replaceEmail = (isset($params['replace_email']) && $params['replace_email']) ? $params['replace_email'] : '';
		$contactId = (isset($params['contact_id']) && $params['contact_id']) ? $params['contact_id'] : 0;

		$response['success'] = false;
		if($email && $contactId)
		{
			$contactModel = GO_Addressbook_Model_Contact::model()->findByPk($contactId);
			$emailAddresses = array($contactModel->email, $contactModel->email2, $contactModel->email3);

			if(!$replaceEmail)
			{		    		    		    
				if(!in_array($email, $emailAddresses))
				{
					$index = array_search('', $emailAddresses);
					if($index === false) {
						$response['addresses'] = array(array('name' => $contactModel->email), array('name' => $contactModel->email2), array('name' => $contactModel->email3));
						$response['contact_name'] = $contactModel->name;
					} else{
						$field = ($index == 0) ? 'email' : 'email'.($index+1);
						$contactModel->$field = $email;
						$contactModel->save();
					}	
					$response['success'] = true;
				} else {
					$response['feedback'] = GO::t('emailAlreadyExists','addressbook');
				}
			} else {
				$index = array_search($replaceEmail, $emailAddresses);
				if($index === false)
				{
					$response['feedback'] = GO::t('emailDoesntExists','addressbook');
				}else
				{
					$field = ($index == 0) ? 'email' : 'email'.($index+1);
					$contactModel->$field = $email;
					$contactModel->save();
					$response['success']=true;
				}		        
			}	
	  }
		return $response;
	}
	
	function actionEmployees($params) {
		$result['success'] = false;
		$company = GO_Addressbook_Model_Company::model()->findByPk($params['company_id']);
		
		if(!$company->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION))
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
			try{
				
				if ($model->company) {
					//the company will move it's contact along too.
					$model->company->addressbook_id=$params['book_id'];
					$model->company->save();
				} else {

					$model->addressbook_id=$params['book_id'];
					$model->save();				
				}
			}catch(GO_Base_Exception_AccessDenied $e){
				$response['failedToMove'][]=$model->id;
			}
		}
		$response['success']=empty($response['failedToMove']);
		
		if(!$response['success']){
			$count = count($response['failedToMove']);
			$response['feedback'] = sprintf(GO::t('cannotMoveError'),$count);
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
		
		return parent::afterAttributes($attributes, $response, $params, $model);
	}
	
    /**
     * Before importing a contact in the database first check if the company name of this contact
     * Is a company that excists in the database. If not create a company. After this set the id
     * of the create company to the contact we insert.
     * 
     * If the email addres set to a contact does not validate. Remove it so import wont fail
     */
	protected function beforeImport($params, &$model, &$attributes, $record) {	
		
		$impBasParams = json_decode($params['importBaseParams'],true);
		$addressbookId = $impBasParams['addressbook_id'];
		
		if(!empty($attributes['Company']))
			$companyName = $attributes['Company'];
		else if(!empty($attributes['company']))
			$companyName = $attributes['company'];
		else if(!empty($attributes['company_name']))
			$companyName = $attributes['company_name'];
		else if(!empty($attributes['companyName']))
			$companyName = $attributes['companyName'];	
		else if(!empty($attributes['name']))
			$companyName = $attributes['name'];	
		
		if(!empty($companyName)) {
			$companyModel = GO_Addressbook_Model_Company::model()->find(
				GO_Base_Db_FindParams::newInstance()
					->single()
					->criteria(
						GO_Base_Db_FindCriteria::newInstance()
							->addCondition('name',$companyName)
							->addCondition('addressbook_id',$addressbookId)
					)
			);
			if (empty($companyModel)) {
				$companyModel = new GO_Addressbook_Model_Company();
				$companyModel->setAttributes(array(
					'name' => $companyName,
					'addressbook_id' => $addressbookId
				));
				$companyModel->save();
			}
			$model->company_id = $companyModel->id;
		}
		
        if(isset($attributes['email']) && !GO_Base_Util_String::validate_email($attributes['email']))
          unset($attributes['email']);
        if(isset($attributes['email2']) && !GO_Base_Util_String::validate_email($attributes['email2']))
          unset($attributes['email2']);
        if(isset($attributes['email3']) && !GO_Base_Util_String::validate_email($attributes['email3']))
          unset($attributes['email3']);
        
		return parent::beforeImport($params, $model, $attributes, $record);
	}
	
	protected function actionHandleAttachedVCard($params) {
		$outString = '';
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		$imap = $account->openImapConnection($params['mailbox']);
		$imap->get_message_part_start($params['uid'], $params['number']);
		while ($line = $imap->get_message_part_line()) {
			switch (strtolower($params['encoding'])) {
				case 'base64':
					$outString .= base64_decode($line);
					break;
				case 'quoted-printable':
					$outString .= quoted_printable_decode($line);
					break;
				default:
					$outString .= $line;
					break;
			}
		}		
		$tmpFile = new GO_Base_Fs_File(GO::config()->tmpdir.$params['filename']);
		$tmpFile->tempFile(GO::config()->tmpdir.$params['filename'],'vcf');
		$tmpFile->putContents($outString);
		$abController = new GO_Addressbook_Controller_Contact();
		$response = $abController->run('importVCard', array('file'=>$tmpFile->path(),'readOnly'=>true), false, true);
		echo json_encode($response);
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
		
		$summaryLog = new GO_Base_Component_SummaryLog();
		
		$readOnly = !empty($params['readOnly']);
		
		if(isset($_FILES['files']['tmp_name'][0]))
			$params['file'] = $_FILES['files']['tmp_name'][0];
		
		if (!empty($params['importBaseParams'])) {
			$importBaseParams = json_decode($params['importBaseParams'],true);
			$params['addressbook_id'] = $importBaseParams['addressbook_id'];
		}
		
		$file = new GO_Base_Fs_File($params['file']);
		$file->convertToUtf8();

		$data = "BEGIN:ADDRESSBOOK\n".$file->getContents()."\nEND:ADDRESSBOOK";
		GO::debug($data);
		
		$vaddressbook = GO_Base_VObject_Reader::read($data);
		
//		GO::debug($vObjectsArray);
		unset($params['file']);
		$nr=0;
		if ($readOnly)
			$contactsAttr = array();
		foreach($vaddressbook->vcard as $vObject) {
			$nr++;
			GO_Base_VObject_Reader::convertVCard21ToVCard30($vObject);
			GO::debug($vObject->serialize());
			
			$contact = new GO_Addressbook_Model_Contact();
			try {
				if ($contact->importVObject($vObject, $params, !$readOnly))
					$summaryLog->addSuccessful();
				if ($readOnly)
					$contactsAttr[] = $contact->getAttributes('formatted');
			} catch (Exception $e) {
				var_dump($e->getMessage());
				$summaryLog->addError($nr, $e->getMessage());
			}
		}
		
		$response = $summaryLog->getErrorsJson();
		if ($readOnly) {
			$response['contacts'] = $contactsAttr;
		}
		$response['successCount'] = $summaryLog->getTotalSuccessful();
		$response['totalCount'] = $summaryLog->getTotal();
		$response['success']=true;
		
		return $response;
	}
	
	/**
	 * The actual call to the import CSV function
	 * 
	 * @param array $params
	 * @return array $response 
	 */
	protected function actionImportCsv($params){		
		$params['file'] = $_FILES['files']['tmp_name'][0];
		$summarylog = parent::actionImport($params);
		$response = $summarylog->getErrorsJson();
		$response['successCount'] = $summarylog->getTotalSuccessful();
		$response['totalCount'] = $summarylog->getTotal();
		$response['success'] = true;
		return $response;
	}
	
	
	protected function actionSelectContact($params){
		
		$response = array('total'=>0, 'results'=>array());
		
		$query = '%'.preg_replace ('/[\s*]+/','%', $params['query']).'%'; 
		
		
		
		$userContactIds=array();
		if(empty($params['addressbook_id']) && empty($params['no_user_contacts'])) {
			$findParams = GO_Base_Db_FindParams::newInstance()
					->searchQuery($query,
									array("CONCAT(t.first_name,' ',t.middle_name,' ',t.last_name)",'t.email','t.email2','t.email3'))
					->select('t.*, "'.addslashes(GO::t('strUser')).'" AS ab_name,c.name AS company_name')
					->limit(10)
					->joinModel(array(
						'model'=>'GO_Addressbook_Model_Company',					
						'foreignField'=>'id', //defaults to primary key of the remote model
						'localField'=>'company_id', //defaults to "id"
						'tableAlias'=>'c', //Optional table alias
						'type'=>'LEFT' //defaults to INNER,

					));
			
			if(!empty($params['requireEmail'])){
				$criteria = GO_Base_Db_FindCriteria::newInstance()
								->addCondition("email", "","!=")
								->addCondition("email2", "","!=",'t',false)
								->addCondition("email3", "","!=",'t',false);

				$findParams->getCriteria()->mergeWith($criteria);
			}
			
			$stmt = GO_Addressbook_Model_Contact::model()->findUsers(GO::user()->id, $findParams);
			
			$userContactIds=array();
		
			foreach($stmt as $contact){
				$record =$contact->getAttributes();
				$record['name']=$contact->name;
				$record['cf']=$contact->id.":".$contact->name;

				$response['results'][]=$record;
				$response['total']++;	
				
				$userContactIds[]=$contact->id;
			}
					
		}
		
		
		
		
		
		
		
		
		
		if(count($response['results'])<10){
		
		
			$findParams = GO_Base_Db_FindParams::newInstance()
				->ignoreAcl()
				->select('t.*,c.name AS company_name, a.name AS ab_name')
				->searchQuery($query,
								array(
										"CONCAT(t.first_name,' ',t.middle_name,' ',t.last_name, ' ',a.name)",
										't.email',
										't.email2',
										't.email3'
										))					
				->joinModel(array(
					'model'=>'GO_Addressbook_Model_Addressbook',					
					'foreignField'=>'id', //defaults to primary key of the remote model
					'localField'=>'addressbook_id', //defaults to "id"
					'tableAlias'=>'a', //Optional table alias
					'type'=>'INNER' //defaults to INNER,

				))			
				->limit(10-count($response['results']));


	//		if(!empty($params['joinCompany'])){
				$findParams->joinModel(array(
					'model'=>'GO_Addressbook_Model_Company',					
					'foreignField'=>'id', //defaults to primary key of the remote model
					'localField'=>'company_id', //defaults to "id"
					'tableAlias'=>'c', //Optional table alias
					'type'=>'LEFT' //defaults to INNER,

				));
	//		}
				
				$findParams->getCriteria()->addInCondition('id', $userContactIds,'t',true,true);
		

			if(!empty($params['addressbook_id'])){		
				$abs= array($params['addressbook_id']);
			}else
			{
				$abs = GO_Addressbook_Model_Addressbook::model()->getAllReadableAddressbookIds();			
			}

			if(!empty($abs)){

				$findParams->getCriteria ()->addInCondition('addressbook_id', $abs);

				if(!empty($params['requireEmail'])){
					$criteria = GO_Base_Db_FindCriteria::newInstance()
									->addCondition("email", "","!=")
									->addCondition("email2", "","!=",'t',false)
									->addCondition("email3", "","!=",'t',false);

					$findParams->getCriteria()->mergeWith($criteria);
				}

				$stmt = GO_Addressbook_Model_Contact::model()->find($findParams);

				$user_ids=array();
				foreach($stmt as $contact){
					$record =$contact->getAttributes();
					//$record['name']=$contact->name;
					$record['cf']=$contact->id.":".$contact->name;

					$response['results'][]=$record;
					$response['total']++;			

					if($contact->go_user_id)
						$user_ids[]=$contact->go_user_id;
				}
			}
		}
		
		
		return $response;
		
	}
	
	protected function actionSearchEmail($params) {
		
		$response['success']=true;
		$response['results']=array();
		
		if(empty($params['query']))
			return $response;
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->searchQuery('%'.preg_replace ('/[\s*]+/','%', $params['query']).'%')
						->select('t.*, addressbook.name AS ab_name, c.name AS company_name')
						->limit(20)
						->joinModel(array(
							'model'=>'GO_Addressbook_Model_Company',					
							'foreignField'=>'id', //defaults to primary key of the remote model
							'localField'=>'company_id', //defaults to "id"
							'tableAlias'=>'c', //Optional table alias
							'type'=>'LEFT' //defaults to INNER,
						));

		$criteria = GO_Base_Db_FindCriteria::newInstance()
							->addCondition("email", "","!=")
							->addCondition("email2", "","!=",'t',false)
							->addCondition("email3", "","!=",'t',false);

		$findParams->getCriteria()->mergeWith($criteria);

		$stmt = GO_Addressbook_Model_Contact::model()->find($findParams);


		while ($contact = $stmt->fetch()) {
			
			$record = $contact->getAttributes();
			
			if ($contact->email != "")				
				$response['results'][] = $record;

			if ($contact->email2 != "") {
				$record['email']=$contact->email2;
				$response['results'][] = $record;
			}

			if ($contact->email3 != "") {
				$record['email']=$contact->email3;				
				$response['results'][] = $record;
			}
		}
		
		return $response;
	}
}