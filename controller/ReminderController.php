<?php
class GO_Core_Controller_Reminder extends GO_Base_Controller_AbstractController {
	
	protected function actionSnooze($params){
		$reminderIds = json_decode($params['reminders'], true);
		
		foreach($reminderIds as $id){
			$r=GO_Base_Model_Reminder::model()->findByPk($id);
			$r->setForUser(GO::user()->id, time()+$params['snooze_time']);
		}
		$response['success']=true;
		
		return $response;
	}
	
	protected function actionDismiss($params){
		$reminderIds = json_decode($params['reminders'], true);
		
		foreach($reminderIds as $id){
			$r=GO_Base_Model_Reminder::model()->findByPk($id);
			$r->removeUser(GO::user()->id);
		}
		$response['success']=true;
		
		return $response;
	}
	
	protected function actionStore($params){
		$params = GO_Base_Db_FindParams::newInstance()
						->select('t.*')
						->join(GO_Base_Model_ReminderUser::model()->tableName(),
									GO_Base_Db_FindCriteria::newInstance()
											->addModel(GO_Base_Model_Reminder::model())
											->addCondition('id', 'ru.reminder_id','=','t',true, true),
										'ru')						
						->criteria(GO_Base_Db_FindCriteria::newInstance()
										->addModel(GO_Base_Model_ReminderUser::model(),'ru')
										->addCondition('user_id', GO::user()->id,'=','ru')
										->addCondition('time', time(),'<','ru')
										);
		
		$store = GO_Base_Data_Store::newInstance(GO_Base_Model_Reminder::model());
		$store->getColumnModel()->setFormatRecordFunction(array($this, 'formatReminderRecord'));
		
		$stmt = GO_Base_Model_Reminder::model()->find($params);
		
		$store->setStatement($stmt);		
		
		return $store->getData();
	}
	
	public function formatReminderRecord($record, $model, $store){
		
		if(!empty($record['model_type_id'])){
			$modelType = GO_Base_Model_ModelType::model()->findByPk($record['model_type_id']);
			$record['iconCls']='go-model-icon-'.$modelType->model_name;
			$record['type']=GO::getModel($modelType->model_name)->localizedName;
			$record['model_name']=$modelType->model_name;
		}  else {
			$record['iconCls']='go-icon-reminders';
			$record['type']=GO::t('other');
			$record['model_name']='';
		}
		
		$now = getdate(time());
		$today = mktime(0,0,0,$now['mon'],$now['mday'], $now['year']);

		$time = $model->vtime ? $model->vtime: $model->time;
		if($time == $today) {
			$record['local_time']=date(GO::user()->date_format,$time);
		}else {
			$record['local_time']=GO_Base_Util_Date::get_timestamp($time, true);
		}
		
		$record['text'] = htmlspecialchars_decode($record['text']);
		
		return $record;		
	}
}