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
 */

class GO_Addressbook_Controller_Addresslist extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Addressbook_Model_Addresslist';
	
	protected function beforeStore(&$response, &$params, &$store) {
		
		$store->setDefaultSortOrder('name','ASC');
		
		return parent::beforeStore($response, $params, $store);
	}
	
	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store, GO_Base_Db_FindParams $storeParams) {
		
		if (empty($params['forContextMenu'])) {
			$multiSel = new GO_Base_Component_MultiSelectGrid(
							'addresslist_filter', 
							"GO_Addressbook_Model_Addresslist",$store, $params, false);		
			$multiSel->formatCheckedColumn();
		}
		return parent::beforeStoreStatement($response, $params, $store, $storeParams);
	}

	public function formatStoreRecord($record, $model, $store) {
		if (!empty($_POST['forContextMenu'])) {
			$record['text'] = $record['name'];
			$record['addresslist_id']=$record['id'];
			unset($record['id']);
		}
		return $record;
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
		$store->getColumnModel()->formatColumn('company_name', '$model->company->name', array(), 'company_id');

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
		}elseif(!empty($params['add_search_result'])){
			$findParams = GO::session()->values["contact"]['findParams'];
			$findParams->limit(0)->select('t.id');
			
			$model = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id']);
			$stmt = GO_Addressbook_Model_Contact::model()->find($findParams);
			foreach ($stmt as $contact)
				$model->addManyMany('contacts', $contact->id);
			
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
		}elseif(!empty($params['add_search_result'])){
			$findParams = GO::session()->values["company"]['findParams'];
			$findParams->limit(0)->select('t.id');
			
			$model = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id']);
			
			$stmt = GO_Addressbook_Model_Company::model()->find($findParams);
			foreach ($stmt as $contact)
				$model->addManyMany('companies', $contact->id);
			
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
	
	/**
	 * Add contacts to an addresslist.
	 * @param type $params MUST contain addresslistId AND (EITHER senderNames and
	 * senderEmails OR contactIds)
	 * @return $response If there are email addresses that are not found in any
	 * addressbook, the corresponding senders are registered in 
	 * $response['unknownSenders'], and  $response['success'] becomes false, so
	 * that the user can decide what to do with the unknown senders.
	 */
	public function actionAddContactsToAddresslist($params) {
		$addresslistModel = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslistId']);
		$response = array(
			'success'=>true,
		);
		
		$nAddedContacts = 0;
		
		if (!empty($params['contactIds'])) {
			// Only contact ids are sent from the client
			$contactIds = json_decode($params['contactIds']);
			foreach ($contactIds as $contactId) {
				$addresslistModel->addManyMany('contacts',$contactId);
			}
		} else {
			// email addresses and names are sent from the client
			$senderEmails = json_decode($params['senderEmails']);
			$senderNames = json_decode($params['senderNames']);
			$senders = array(); // format: $senders[$senderEmail] = array('first_name'=>'Jack','middle_name'=>'','last_name'=>'Johnson');
			$unknownSenders = array(); // format: $unknownSenders[$senderEmail] = array('first_name'=>'Jack','middle_name'=>'','last_name'=>'Johnson');

			// Create array of senders
			foreach ($senderEmails as $key => $senderEmail) {
				if (empty($senders[$senderEmail]))
					$senders[$senderEmail] = $senderNames[$key];
			}

			foreach($senders as $senderEmail => $senderNameArr){
				$contactNameArr = GO_Base_Util_String::split_name($senderNameArr);
				$contactStmt = GO_Addressbook_Model_Contact::model()
					->find(
						GO_Base_Db_FindParams::newInstance()
							->criteria(
								GO_Base_Db_FindCriteria::newInstance()
									->addCondition('email', $senderEmail, '=', 't', false)
									->addCondition('email2', $senderEmail, '=', 't', false)
									->addCondition('email3', $senderEmail, '=', 't', false)
							)
					);//->findSingleByAttribute('email', $senderEmail);

				if (empty($contactStmt) && empty($unknownSenders[$senderEmail])) {
					// Keep track of contacts not found in database.
					$unknownSenders[] = array(
						'email'=>$senderEmail,
						'name'=>$senderNameArr,
						'first_name'=>$contactNameArr['first_name'],
						'middle_name'=>$contactNameArr['middle_name'],
						'last_name'=>$contactNameArr['last_name']
					);
				} else {
					// add contact to addresslist, but ensure only one email per addresslist
					$emailAlreadyInAddresslist = false;
					$linkableContactModel = false;
					while ($contactModel = $contactStmt->fetch()) {
						if ($addresslistModel->hasManyMany('contacts',$contactModel->id))
							$emailAlreadyInAddresslist = true;
						else
							$linkableContactModel = $contactModel;
					}
					if (!empty($linkableContactModel) && !$emailAlreadyInAddresslist) {
						$linkableContactModel->first_name = $contactNameArr['first_name'];
						$linkableContactModel->middle_name = $contactNameArr['middle_name'];
						$linkableContactModel->last_name = $contactNameArr['last_name'];
						$linkableContactModel->save();
						$addresslistModel->addManyMany('contacts', $linkableContactModel->id);
						$nAddedContacts++;
					}
				}
			}
			
			$response['addedSenders'] = $nAddedContacts;
			
			if (count($unknownSenders)) {
				$response['success'] = false;
				$response['unknownSenders'] = json_encode($unknownSenders);
				$response['addresslistId'] = $addresslistModel->id;
			}
		}
		
		return $response;
	}

	public function actionDeleteContactsFromAddresslist($params) {
		$addresslistModel = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslistId']);
		$response = array(
			'success'=>true,
			'nRemoved'=>0
		);
		
		// email addresses and names are sent from the client
		$senderEmails = json_decode($params['senderEmails']);

		foreach($senderEmails as $senderEmail){
			$contactStmt = GO_Addressbook_Model_Contact::model()
				->find(
						GO_Base_Db_FindParams::newInstance()
							->criteria(
								GO_Base_Db_FindCriteria::newInstance()
									->addCondition('email', $senderEmail, '=', 't', false)
									->addCondition('email2', $senderEmail, '=', 't', false)
									->addCondition('email3', $senderEmail, '=', 't', false)
							)
					);//->findSingleByAttribute('email',$senderEmail);
			while ($contactModel = $contactStmt->fetch()) {
				if ($addresslistModel->hasManyMany('contacts', $contactModel->id)) {
					$removed = $addresslistModel->removeManyMany('contacts', $contactModel->id);
					if ($removed)
						$response['nRemoved']++;
				}
			}
		}
		
		return $response;
	}
	
	public function actionAdd($params) {
		$response = array('success'=>true);
		
		$listId = $params['addresslistId'];
		$contactIds = json_decode($params['contacts'],true);
		$companyIds = json_decode($params['companies'],true);
		$removeOther = isset($params['move']) && $params['move']=='true';
		
		if (!empty($removeOther)) {
			foreach ($contactIds as $contactId) {
				$contactModel = GO_Addressbook_Model_Contact::model()->findByPk($contactId);
				$contactModel->removeAllManyMany('addresslists');
			}
			foreach ($companyIds as $companyId) {
				$companyModel = GO_Addressbook_Model_Company::model()->findByPk($companyId);
				$companyModel->removeAllManyMany('addresslists');
			}
		}

		$addresslistModel = GO_Addressbook_Model_Addresslist::model()->findByPk($listId);
		foreach ($contactIds as $contactId)
			$addresslistModel->addManyMany ('contacts', $contactId);
		foreach ($companyIds as $companyId)
			$addresslistModel->addManyMany ('companies', $companyId);
		
		return $response;
	}
	
	// TODO: get cross-session "selected addresslist" identifiers for getting store
}

