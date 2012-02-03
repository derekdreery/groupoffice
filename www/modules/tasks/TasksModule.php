<?php
class GO_Tasks_TasksModule extends GO_Base_Module{
	public static function initListeners() {
		//GO_Core_Controller_Settings::model()->addListener('actionLoad', 'GO_Tasks_TasksModule', 'onLoadSettings');		
		//todo create default tasklist on user create and delete. See notes.
	}

	public function autoInstall() {
		return true;
	}
	
	public static function submitSettings(&$settingsController, &$params, &$response, $user) {
		
		$settings = GO_Tasks_Model_Settings::model()->getDefault($user);		
		if($settings->remind = isset($params['remind'])) {
			$settings->reminder_days = $params['reminder_days'];
			$settings->reminder_time = $params['reminder_time'];
		}
		
		$settings->default_tasklist_id=$params['default_tasklist_id'];

		$settings->save();
		
		return parent::submitSettings($settingsController, $params, $response);
	}
	
	public static function loadSettings(&$settingsController, &$params, &$response, $user) {
		
		$settings = GO_Tasks_Model_Settings::model()->getDefault($user);
		$response['data']=array_merge($response['data'], $settings->getAttributes());
		
		$tasklist = $settings->tasklist;
		
		if($tasklist) {
			$response['data']['default_tasklist_id']=$tasklist->id;
			$response['remoteComboTexts']['default_tasklist_id']=$tasklist->name;
		}
				
		//$response = GO_Tasks_Controller_Task::reminderSecondsToForm($response);
		
		return parent::loadSettings($settingsController, $params, $response);
	}
	
	public static function deleteUser($user) {
		
		GO_Tasks_Model_PortletTasklist::model()->deleteByAttribute('user_id', $user->id);
		
		return parent::deleteUser($user);
	}
	
}