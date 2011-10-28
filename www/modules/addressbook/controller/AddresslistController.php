<?php
class GO_Addressbook_Controller_Addresslist extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Addressbook_Model_Addresslist';	

	protected function afterLoad(&$response, &$model, &$params) {
		$response['data']['user_name'] = $model->user->name;
		return $response;
	}
	
	public function actionContacts($params){
		
		$store = GO_Base_Data_Store::newInstance(GO_Addressbook_Model_Contact::model());
						
		$store->getColumnModel()->formatColumn('name', '$model->name',array(),array('first_name','last_name'));
		
		$store->processDeleteActions($params, "GO_Addressbook_Model_AddresslistContact", array('addresslist_id'=>$params['addresslist_id']));
		
		$response = array();
		
		if (!empty($params['add_addressbook_id'])) {
			$addressbook = GO_Addressbook_Model_Addressbook::model()->findByPk($params['add_addressbook_id']);
			$model = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id']);
			$stmt = $addressbook->contacts();
			while ($contact = $stmt->fetch()) {
				$model->addManyMany('contacts',$contact->id);
			}
		} elseif (!empty($params['add_keys'])) {
			$add_keys = json_decode($params['add_keys'], true);
			$model = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id']);
			foreach ($add_keys as $add_key)
				$model->addManyMany('contacts',$add_key);
		}
			
		$stmt = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id'])->contacts($store->getDefaultParams());
		
		$store->setDefaultSortOrder('name', 'ASC');
		$store->setStatement ($stmt);

		return array_merge($response,$store->getData());
		
	}	
	
	public function actionCompanies($params){
		
		$store = GO_Base_Data_Store::newInstance(GO_Addressbook_Model_Company::model());
						
		$store->getColumnModel()->formatColumn('name', '$model->name',array(),array('first_name','last_name'));
		
		$store->processDeleteActions($params, "GO_Addressbook_Model_AddresslistCompany", array('addresslist_id'=>$params['addresslist_id']));
		
		$response = array();
		
		if (!empty($params['add_addressbook_id'])) {
			$addressbook = GO_Addressbook_Model_Addressbook::model()->findByPk($params['add_addressbook_id']);
			$model = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id']);
			$stmt = $addressbook->companies();
			while ($company = $stmt->fetch()) {
				$model->addManyMany('companies',$company->id);
			}
		} elseif (!empty($params['add_keys'])) {
			$add_keys = json_decode($params['add_keys'], true);
			$model = !isset($model) ? GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id']) : $model;
			foreach ($add_keys as $add_key)
				$model->addManyMany('companies',$add_key);
		} 
			
		$stmt = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id'])->companies($store->getDefaultParams());
		
		$store->setDefaultSortOrder('name', 'ASC');
		$store->setStatement ($stmt);

		return array_merge($response,$store->getData());
		
	}	
	
	// TODO: get cross-session "selected addresslist" identifiers for getting store
}

