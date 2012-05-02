<?php

/**
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */class GO_Addressbook_Controller_Addresslist extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Addressbook_Model_Addresslist';
	
	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store, GO_Base_Db_FindParams $storeParams) {
		
		$multiSel = new GO_Base_Component_MultiSelectGrid(
						'addresslist_filter', 
						"GO_Addressbook_Model_Addresslist",$store, $params);		
		$multiSel->formatCheckedColumn();
		
		return parent::beforeStoreStatement($response, $params, $store, $storeParams);
	}


	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {

		$columnModel->formatColumn('user_name', '$model->user->name');

		return parent::formatColumns($columnModel);
	}

	protected function afterLoad(&$response, &$model, &$params) {
		$response['data']['user_name'] = $model->user->name;
		return $response;
	}

	protected function actionContacts($params) {

		$store = GO_Base_Data_Store::newInstance(GO_Addressbook_Model_Contact::model());

		$store->getColumnModel()->formatColumn('name', '$model->name', array(), array('first_name', 'last_name'));

		$store->processDeleteActions($params, "GO_Addressbook_Model_AddresslistContact", array('addresslist_id' => $params['addresslist_id']));

		$response = array();

		if (!empty($params['add_addressbook_id'])) {
			$addressbook = GO_Addressbook_Model_Addressbook::model()->findByPk($params['add_addressbook_id']);
			$model = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id']);
			$stmt = $addressbook->contacts();
			while ($contact = $stmt->fetch()) {
				$model->addManyMany('contacts', $contact->id);
			}
		} elseif (!empty($params['add_keys'])) {
			$add_keys = json_decode($params['add_keys'], true);
			$model = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id']);
			foreach ($add_keys as $add_key)
				$model->addManyMany('contacts', $add_key);
		}

		$stmt = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id'])->contacts($store->getDefaultParams($params));

		$store->setDefaultSortOrder('name', 'ASC');
		$store->setStatement($stmt);

		return array_merge($response, $store->getData());
	}

	protected function actionCompanies($params) {

		$store = GO_Base_Data_Store::newInstance(GO_Addressbook_Model_Company::model());

		$store->getColumnModel()->formatColumn('name', '$model->name', array(), array('first_name', 'last_name'));

		$store->processDeleteActions($params, "GO_Addressbook_Model_AddresslistCompany", array('addresslist_id' => $params['addresslist_id']));

		$response = array();

		if (!empty($params['add_addressbook_id'])) {
			$addressbook = GO_Addressbook_Model_Addressbook::model()->findByPk($params['add_addressbook_id']);
			$model = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id']);
			$stmt = $addressbook->companies();
			while ($company = $stmt->fetch()) {
				$model->addManyMany('companies', $company->id);
			}
		} elseif (!empty($params['add_keys'])) {
			$add_keys = json_decode($params['add_keys'], true);
			$model = !isset($model) ? GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id']) : $model;
			foreach ($add_keys as $add_key)
				$model->addManyMany('companies', $add_key);
		}

		$stmt = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id'])->companies($store->getDefaultParams($params));

		$store->setDefaultSortOrder('name', 'ASC');
		$store->setStatement($stmt);

		return array_merge($response, $store->getData());
	}
	
	protected function actionGetRecipientsAsString($params){
				
		if(empty($params['addresslists']))
			throw new Exception();
			
		$recipients = new GO_Base_Mail_EmailRecipients();
		
		$addresslistIds = json_decode($params['addresslists']);
				
		foreach($addresslistIds as $addresslistId){
		
			$addresslist = GO_Addressbook_Model_Addresslist::model()->findByPk($addresslistId);
			
			if($addresslist){
				$contacts = $addresslist->contacts(GO_Base_Db_FindParams::newInstance()->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('email', '','!=')));
				while($contact = $contacts->fetch())				
						$recipients->addRecipient($contact->email, $contact->name);

				$companies = $addresslist->companies(GO_Base_Db_FindParams::newInstance()->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('email', '','!=')));
				while($company = $companies->fetch())
						$recipients->addRecipient($company->email, $company->name);
			}
		}	
		
		return array(
				'success'=>true,
				'recipients'=>(string) $recipients
		);
	}
	
	

	// TODO: get cross-session "selected addresslist" identifiers for getting store
}

