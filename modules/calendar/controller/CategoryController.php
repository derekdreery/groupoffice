<?php

namespace GO\Calendar\Controller;


class CategoryController extends \GO\Base\Controller\AbstractModelController {

	protected $model = 'GO\Calendar\Model\Category';
	
	
	protected function beforeStoreStatement(array &$response, array &$params, \GO\Base\Data\AbstractStore &$store, \GO\Base\Db\FindParams $storeParams) {
		
		$storeCriteria = $storeParams->getCriteria();
		if(!empty($params['global_categories']) && !empty($params['calendar_id'])){
			$storeCriteria->addCondition('calendar_id', $params['calendar_id']);
			$storeCriteria->addCondition('calendar_id', 0,'=','t',false);
		}	elseif(!empty($params['calendar_id'])) {
			$storeCriteria->addCondition('calendar_id', $params['calendar_id']);
		} else {
			$storeCriteria->addCondition('calendar_id', 0);
		}
		$storeParams->criteria($storeCriteria);
		
		return parent::beforeStoreStatement($response, $params, $store, $storeParams);
	}
}