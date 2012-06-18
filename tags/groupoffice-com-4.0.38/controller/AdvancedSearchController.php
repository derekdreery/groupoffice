<?php
class GO_Core_Controller_AdvancedSearch extends GO_Base_Controller_AbstractModelController {
	
	protected $model = 'GO_Base_Model_AdvancedSearch';

	protected function getStoreParams($params) {	
		
		$storeParams = GO_Base_Db_FindParams::newInstance();
		$storeParams->getCriteria()->addCondition('model_name', $params['model_name']);
		$storeParams->select('t.*');
		
		return $storeParams;
	}
}
?>