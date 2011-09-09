<?php
class GO_Tasks_Controller_Task extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Tasks_Model_Task';
	
	protected function afterDisplay(&$response, &$model,&$params) {
		$response['data']['user_name']=$model->user->name;
		$response['data']['tasklist_name']=$model->tasklist->name;
		$response['data']['status_text']=GO::t($model->status,'tasks');
		
		return parent::afterDisplay($response, $model, $params);
	}
	
	protected function afterLoad(&$response, &$model, &$params) {
		$rRule = new GO_Base_Util_Icalendar_Rrule();
		$rRule->readRruleString($model->start_time, $model->rrule);
		$createdRule = $rRule->createOutputArray();
		
		$response['data'] = array_merge($response['data'],$createdRule);

		return parent::afterLoad($response, $model, $params);
	}
	
	protected function beforeSubmit(&$response, &$model, &$params) {
		$rRule = new GO_Base_Util_Icalendar_Rrule();
		$rRule->readInputArray($model->start_time, $params);
		$model->rrule = $rRule->createRrule();

		return parent::beforeSubmit($response, $model, $params);
	}
	
//	protected function getGridParams($params) {
//		
//		switch($params['filter']){
//			case 'active':
//				$findParams = array(
//						'where'=>'start_time>:start_time',
//						'bindParams'=>array(':start_time'=>time())
//				);
//				break;
//		}
//		
//		return $findParams;
//	}

	protected function remoteComboFields(){
		return array(
				'category_id'=>'$model->category->name',
				'tasklist_id'=>'$model->tasklist->name'
				);
	}
}
	