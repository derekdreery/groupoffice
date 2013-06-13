<?php

/**
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 *
 */
class GO_Addressbook_Controller_Company extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Addressbook_Model_Company';

	protected function afterDisplay(&$response, &$model, &$params) {

		$response['data']['addressbook_name'] = $model->addressbook->name;

		$response['data']['google_maps_link'] = GO_Base_Util_Common::googleMapsLink(
										$model->address, $model->address_no, $model->city, $model->country);

		$response['data']['formatted_address'] = nl2br($model->getFormattedAddress());

		$response['data']['post_google_maps_link'] = GO_Base_Util_Common::googleMapsLink(
										$model->post_address, $model->post_address_no, $model->post_city, $model->post_country);

		$response['data']['post_formatted_address'] = nl2br($model->getFormattedPostAddress());

		$response['data']['employees'] = array();
		$sortAlias = GO::user()->sort_name=="first_name" ? array('first_name','last_name') : array('last_name','first_name');
		$stmt = $model->contacts(GO_Base_Db_FindParams::newInstance()->order($sortAlias));
		while ($contact = $stmt->fetch()) {
			$response['data']['employees'][] = array(
					'id' => $contact->id,
					'name' => $contact->getName(GO::user()->sort_name),
					'function' => $contact->function,
					'email' => $contact->email
			);
		}
		
		
		if(GO::modules()->customfields && isset($response['data']['customfields']) && GO_Customfields_Model_DisableCategories::isEnabled("GO_Addressbook_Model_Company", $model->addressbook_id)){
			$ids = GO_Customfields_Model_EnabledCategory::model()->getEnabledIds("GO_Addressbook_Model_Company", $model->addressbook_id);
			
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

	public function formatStoreRecord($record, $model, $store) {

		$record['name_and_name2'] = $model->name;

		if (!empty($model->name2))
			$record['name_and_name2'] .= ' - ' . $model->name2;

		$record['ab_name'] = $model->addressbook->name;
		
		$record['cf'] = $model->id.":".$model->name;//special field used by custom fields. They need an id an value in one.)

		return parent::formatStoreRecord($record, $model, $store);
	}

	protected function remoteComboFields() {
		return array(
				'addressbook_id' => '$model->addressbook->name'
		);
	}

	protected function afterLoad(&$response, &$model, &$params) {

		if (GO::modules()->customfields)
			$response['customfields'] = GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Addressbook_Model_Company", $model->addressbook_id);

		$stmt = $model->addresslists();
		while ($addresslist = $stmt->fetch()) {
			$response['data']['addresslist_' . $addresslist->id] = 1;
		}
		
		$response['data']['name_and_name2'] = $model->name;
		if (!empty($model->name2))
			$response['data']['name_and_name2'] .= ' - ' . $model->name2;


		return parent::afterLoad($response, $model, $params);
	}

	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		$stmt = GO_Addressbook_Model_Addresslist::model()->find();
		while ($addresslist = $stmt->fetch()) {
			$linkModel = $addresslist->hasManyMany('companies', $model->id);
			$mustHaveLinkModel = isset($params['addresslist_' . $addresslist->id]);
			if ($linkModel && !$mustHaveLinkModel) {
				$linkModel->delete();
			}
			if (!$linkModel && $mustHaveLinkModel) {
				$addresslist->addManyMany('companies', $model->id);
			}
		}

		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	
	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store, GO_Base_Db_FindParams $storeParams) {
		
		if(!empty($params['filters'])){
			$abMultiSel = new GO_Base_Component_MultiSelectGrid(
							'books', 
							"GO_Addressbook_Model_Addressbook",$store, $params, true);		
			
			$abMultiSel->addSelectedToFindCriteria($storeParams, 'addressbook_id');
			
			//$abMultiSel->setButtonParams($response);
			//$abMultiSel->setStoreTitle();

			$addresslistMultiSel = new GO_Base_Component_MultiSelectGrid(
							'addresslist_filter', 
							"GO_Addressbook_Model_Addresslist",$store, $params, false);				

			if(!empty($params['addresslist_filters']))
			{
				$addresslistMultiSel->addSelectedToFindCriteria($storeParams, 'addresslist_id','ac');

				if(count($addresslistMultiSel->selectedIds)){
					//we need to join the addresslist link model if a filter for the addresslist is enabled.
					$storeParams->join(
									GO_Addressbook_Model_AddresslistCompany::model()->tableName(), 
									GO_Base_Db_FindCriteria::newInstance()->addCondition('id', 'ac.company_id', '=', 't', true, true), 
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
						->addModel(GO_Addressbook_Model_Company::model(), 't');

		// Filter by clicked letter
		if (!empty($params['clicked_letter'])) {
			if ($params['clicked_letter'] == '[0-9]') {
				$query = '^[0-9].*$';
				$query_type = 'REGEXP';
			} else {
				$query = $params['clicked_letter'] . '%';
				$query_type = 'LIKE';
			}
			$criteria->addCondition('name', $query, $query_type);
		}

		$storeParams = GO_Base_Db_FindParams::newInstance()
						->export("company")
						->criteria($criteria)
						->joinAclFieldTable()
						->select('t.*, addressbook.name AS addressbook_name');
//						->joinModel(array(
//				'model' => 'GO_Addressbook_Model_Addressbook',
//				'localField' => 'addressbook_id',
//				'tableAlias' => 'ab', //Optional table alias
//						));
										

		if (!empty($params['addressbook_id'])) {
			$storeParams->getCriteria()->addCondition('addressbook_id', $params['addressbook_id']);
		}
		
		if(!empty($params['require_email']))
			$storeParams->getCriteria()->addCondition('email', "","!=");
		
		return $storeParams;
	}

	protected function actionChangeAddressbook($params) {
		$ids = json_decode($params['items']);

		$response['success'] = true;
		$response['failedToMove'] = array();

		foreach ($ids as $id) {
			$model = GO_Addressbook_Model_Company::model()->findByPk($id);
			try {
				$model->addressbook_id=$params['book_id'];
				$model->save();
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

	protected function actionMoveEmployees($params) {
		$to_company = GO_Addressbook_Model_Company::model()->findByPk($params['to_company_id']);

		$contacts = GO_Addressbook_Model_Contacts::model()->find(
						GO_Base_Db_FindCriteria::newInstance()
										->addCondition('company_id', $params['from_company_id'])
		);

		foreach ($contacts as $contact) {
			$attributes = array(
					'addressbook_id' => $to_company->addressbook_id,
					'company_id' => $to_company->id
			);
			$contact->setAttributes($attributes);
			$contact->save();
		}

		$response['success'] = true;
		return $response;
	}

	protected function beforeHandleAdvancedQuery($advQueryRecord, GO_Base_Db_FindCriteria &$criteriaGroup, GO_Base_Db_FindParams &$storeParams) {
		$storeParams->debugSql();
		switch ($advQueryRecord['field']) {
			case 'employees.name':
				$storeParams->join(
								GO_Addressbook_Model_Contact::model()->tableName(), GO_Base_Db_FindCriteria::newInstance()->addRawCondition('`t`.`id`', '`employees' . $advQueryRecord['id'] . '`.`company_id`'), 'employees' . $advQueryRecord['id']
				);
				$criteriaGroup->addRawCondition(
								'CONCAT_WS(\' \',`employees' . $advQueryRecord['id'] . '`.`first_name`,`employees' . $advQueryRecord['id'] . '`.`middle_name`,`employees' . $advQueryRecord['id'] . '`.`last_name`)', ':employee' . $advQueryRecord['id'], $advQueryRecord['comparator'], $advQueryRecord['andor'] == 'AND'
				);
				$criteriaGroup->addBindParameter(':employee' . $advQueryRecord['id'], $advQueryRecord['value']);
				return false;
				break;
			default:
				return true;
				break;
		}
	}

// This breaks the company combobox selections (replaces t.name for employees.name (both have the name as ext))
// 
//	protected function afterAttributes(&$attributes, &$response, &$params, GO_Base_Db_ActiveRecord $model) {
//		//unset($attributes['t.company_id']);
//		$attributes['employees.name'] = array('name'=>'employees.name','label'=>GO::t('cmdPanelEmployee', 'addressbook'));
//		return parent::afterAttributes($attributes, $response, $params, $model);
//	}
	
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
	
	/**
	 * Remove the invalid emails from records to be imported
	 */
	protected function beforeImport($params, &$model, &$attributes, $record) {	
	  if(isset($attributes['email']) && !GO_Base_Util_String::validate_email($attributes['email']))
          unset($attributes['email']);
        
	  return parent::beforeImport($params, $model, $attributes, $record);
	}
	
}