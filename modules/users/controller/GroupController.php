<?php
class GO_Users_Controller_Group extends GO_Base_Controller_AbstractJsonController{
	
	protected function actionStore($params) {
		
		$columnModel = new GO_Base_Data_ColumnModel('GO_Base_Model_Group');
		
		$store = new GO_Base_Data_DbStore('GO_Base_Model_Group', $columnModel, $params);
		$store->defaultSort = array('name');
		$store->multiSelectable('users-groups-panel');
		
		echo $this->renderStore($store);
		
	}
	
}
?>
