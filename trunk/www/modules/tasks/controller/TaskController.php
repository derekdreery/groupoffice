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
		
		if(isset($params['rrule']) && !empty($params['rrule'])) {
			$rRule = new GO_Base_Util_Icalendar_Rrule();
			$rRule->readIcalendarRruleString($model->start_time, $model->rrule);
			$createdRule = $rRule->createJSONOutput();

			$response['data'] = array_merge($response['data'],$createdRule);
		}
		
		if(isset($response['data']['reminder']) && !empty($response['data']['reminder'])) {			
			$response['data']['remind']=1;
			$response['data']['remind_date']=date(GO::user()->completeDateFormat, strtotime($response['data']['reminder']));
			$response['data']['remind_time']=date(GO::user()->time_format, strtotime($response['data']['reminder']));
		}

		return parent::afterLoad($response, $model, $params);
	}
	
	protected function beforeSubmit(&$response, &$model, &$params) {
		
		if(isset($params['freq']) && !empty($params['freq']))
		{
			$rRule = new GO_Base_Util_Icalendar_Rrule();
			$rRule->readJsonArray($params);		
			$model->rrule = $rRule->createRrule();
		}
		
		if(isset($params['remind'])) // Check for a setted reminder
		{
			$model->reminder= GO_Base_Util_Date::to_unixtime($params['remind_date'].' '.$params['remind_time']);
			
			// Todo: Create the new reminder if it does not exist yet.
			//OUDE CODE UIT ACTION.PHP
			//			if(isset($_POST['remind'])) {
			//				$task['reminder']=Date::to_unixtime($_POST['remind_date'].' '.$_POST['remind_time']);
			//			}elseif(!isset($_POST['status'])) {
			//				//this task is added with the quick add option
			//				$settings=$tasks->get_settings($GLOBALS['GO_SECURITY']->user_id);
			//				if(!empty($settings['remind'])) {
			//					$reminder_day = $task['due_time'];
			//					if(!empty($settings['reminder_days']))
			//						$reminder_day = Date::date_add($reminder_day,-$settings['reminder_days']);
			//
			//					$task['reminder']=Date::to_unixtime(Date::get_timestamp($reminder_day, false).' '.$settings['reminder_time']);
			//				}
			//			}else {
			//				$task['reminder']=0;
			//			}		
		}
		else {
			$model->reminder = 0;
		}
		
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
	