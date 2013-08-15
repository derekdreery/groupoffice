<?php

class GO_Email_Controller_Alias extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Email_Model_Alias';

	protected function beforeStore(&$response, &$params, &$store) {

		$store->setDefaultSortOrder('name');

		return parent::beforeStore($response, $params, $store);
	}

	protected function getStoreParams($params) {
		
		if(empty($params['account_id'])){
			$findParams = GO_Base_Db_FindParams::newInstance()
							->select('t.*')
							->joinModel(array(
									'model' => 'GO_Email_Model_AccountSort',
									'foreignField' => 'account_id', //defaults to primary key of the remote model
									'localField' => 'account_id', //defaults to primary key of the model
									'type' => 'LEFT',
									'tableAlias'=>'sor',
									"criteria"=>  GO_Base_Db_FindCriteria::newInstance()->addCondition('user_id', GO::user()->id,"=",'sor')
							))
							->ignoreAdminGroup()
							->permissionLevel(GO_Base_Model_Acl::CREATE_PERMISSION)
							->order(array('order','default'), array('DESC','DESC'));
		}else
		{
			$findParams = GO_Base_Db_FindParams::newInstance();
			$findParams->getCriteria()->addCondition("account_id", $params['account_id'])->addCondition("default", 1,'!=');
		}

		return $findParams;
	}

	public function formatStoreRecord($record, $model, $store) {

		$r = new GO_Base_Mail_EmailRecipients();
		$r->addRecipient($model->email, $model->name);
		$record['from'] = (string) $r;
		$record['html_signature'] = GO_Base_Util_String::text_to_html($model->signature);
		$record['plain_signature'] = $model->signature;
		unset($record['signature']);

		return parent::formatStoreRecord($record, $model, $store);
	}

}
