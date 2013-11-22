<?php

class GO_Tasks_Controller_ScheduleCall extends GO_Base_Controller_AbstractJsonController {

	protected function actionLoad($params){
		$scheduleCall = new GO_Tasks_Model_Task();

		$remoteComboFields = array(
				'category_id'=>'$model->category->name',
				'tasklist_id'=>'$model->tasklist->name'				
		);
		
		if(GO::modules()->projects)
			$remoteComboFields['project_id']='$model->project->path';
		
		echo $this->renderForm($scheduleCall,$remoteComboFields);
	}
	
	protected function actionSave($params){		
		
		if(empty($params['number']) || empty($params['remind_date']) || empty($params['remind_time']))
			throw new Exception('Not all parameters are given');

		$scheduleCall = new GO_Tasks_Model_Task();
				
		$scheduleCall->setAttributes($params);
				
		// Check if the contact_id is really an ID or if it is a name. (The is_contact is true when it is an ID) 
		if(!empty($params['contact_id'])){
			$contact = GO_Addressbook_Model_Contact::model()->findByPk($params['contact_id']);
			
			if(!empty($params['number']) && !empty($params['save_as'])){
				$contact->{$params['save_as']} = $params['number'];
				$contact->save();
			}
			
			$name = $contact->name;
		}else{ 
			$name = $params['contact_name'];
		}
		
		$scheduleCall->name = str_replace(array('{name}','{number}'),array($name, $params['number']),GO::t('scheduleCallTaskName','tasks'));
		$scheduleCall->reminder= GO_Base_Util_Date::to_unixtime($params['remind_date'].' '.$params['remind_time']);
		
		$scheduleCall->save();
		
		if(isset($contact))
			$scheduleCall->link($contact);
		
		echo $this->renderSubmit($scheduleCall);
	}
}

?>
