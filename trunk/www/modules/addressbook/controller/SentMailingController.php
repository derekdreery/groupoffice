<?php
class GO_Addressbook_Controller_SentMailing extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Addressbook_Model_SentMailing';	
	
	protected function beforeStore(&$response, &$params, &$store){
		$store->setDefaultSortOrder('ctime', 'DESC');
		return $response;
	}
	
	public function formatStoreRecord($record, $model, $store) {
		$record['addresslist'] = !empty($model->addresslist) ? $model->addresslist->name : '';
		$record['user_name'] = !empty($model->user) ? $model->user->name : '';
		return parent::formatStoreRecord($record, $model, $store);
	}
	
}