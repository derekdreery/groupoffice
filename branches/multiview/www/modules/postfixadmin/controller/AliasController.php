<?php

class GO_Postfixadmin_Controller_Alias extends \GO\Base\Controller\AbstractModelController {

	protected $model = 'GO_Postfixadmin_Model_Alias';
	
	
	protected function beforeStoreStatement(array &$response, array &$params, \GO\Base\Data\AbstractStore &$store, \GO\Base\Db\FindParams $storeParams) {
		$storeParams
			->select('t.*')
			->criteria(
				\GO\Base\Db\FindCriteria::newInstance()
					->addCondition('domain_id',$params['domain_id'])
			);
		return parent::beforeStoreStatement($response, $params, $store, $storeParams);
	}
	
}

