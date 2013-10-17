<?php
class GO_Calendar_Controller_Category extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Calendar_Model_Category';
	
	
	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store, GO_Base_Db_FindParams $storeParams) {
		
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