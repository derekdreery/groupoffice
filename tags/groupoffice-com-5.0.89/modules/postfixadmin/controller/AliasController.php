<?php

class GO_Postfixadmin_Controller_Alias extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Postfixadmin_Model_Alias';
	
	
	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store, GO_Base_Db_FindParams $storeParams) {
		$storeParams
			->select('t.*')
			->criteria(
				GO_Base_Db_FindCriteria::newInstance()
					->addCondition('domain_id',$params['domain_id'])
			);
		return parent::beforeStoreStatement($response, $params, $store, $storeParams);
	}
	
}

