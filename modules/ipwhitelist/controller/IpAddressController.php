<?php
class GO_Ipwhitelist_Controller_IpAddress extends GO_Base_Controller_AbstractJsonController {
	
	protected function actionStore($params) {
		
		$groupId = $params['group_id'];
		
		$columnModel = new GO_Base_Data_ColumnModel(GO_Ipwhitelist_Model_IpAddress::model());
		
		$storeFindParams = 
			GO_Base_Db_FindParams::newInstance()
				->criteria(GO_Base_Db_FindCriteria::newInstance()
					->addCondition('group_id',$groupId)
				)
				->order('ip_address');
		
		$store = new GO_Base_Data_DbStore('GO_Ipwhitelist_Model_IpAddress', $columnModel, $params, $storeFindParams);
		echo $this->renderStore($store);
		
	}
	
	protected function actionLoad($params) {
		
		$model = GO_Ipwhitelist_Model_IpAddress::model()->createOrFindByParams($params);
		
//		$remoteComboFields = array(
//			'group_id' => '$model->group->name',
//			'user_id' => '$model->user->name'
//		);

		echo $this->renderForm($model);//, $remoteComboFields);
		
	}
	
	protected function actionSubmit($params) {
		
		$model = GO_Ipwhitelist_Model_IpAddress::model()->createOrFindByParams($params);

		$model->setAttributes($params);
		$model->save();

		echo $this->renderSubmit($model);
		
	}
	
}
?>
