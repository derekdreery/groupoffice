<?php


namespace GO\Postfixadmin\Controller;


class AliasController extends \GO\Base\Controller\AbstractModelController {

	protected $model = 'GO\Postfixadmin\Model\Alias';
	
	
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

