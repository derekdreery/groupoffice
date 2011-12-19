<?php

class GO_Email_Controller_Alias extends GO_Base_Controller_AbstractModelController {
	protected $model='GO_Email_Model_Alias';
	
	protected function beforeStore(&$response, &$params, &$store) {
		
		$store->setDefaultSortOrder('name');
		
		return parent::beforeStore($response, $params, $store);
	}
	
	public function formatStoreRecord($record, $model, $store) {
		
		$r = new GO_Base_Mail_EmailRecipients();
		$r->addRecipient($model->email, $model->name);
		$record['name']=(string) $r;
		$record['html_signature']=GO_Base_Util_String::text_to_html($model->signature);
		$record['plain_signature']=$model->signature;
		unset($record['signature']);
		
		return parent::formatStoreRecord($record, $model, $store);
	}

}
