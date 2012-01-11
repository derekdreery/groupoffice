<?php

class GO_Calendar_CalendarModule extends GO_Base_Module{
	
	
	public function author() {
		return 'Merijn Schering';
	}
	
	public function authorEmail() {
		return 'mschering@intermesh.nl';
	}
	
	public function autoInstall() {
		return true;
	}
	
	/**
	 * 
	 * When a user is created, updated or logs in this function will be called.
	 * The function can check if the default calendar, addressbook, notebook etc.
	 * is created for this user.
	 * 
	 */
	public static function firstRun(){
		parent::firstRun();

	}
	
	public static function saveUser($user, $wasNew){
		if($wasNew)
			self::getDefaultCalendar($user->id);	
	}
	
	public static function deleteUser($user){
		GO_Calendar_Model_Category::model()->deleteByAttribute('user_id', $user->id);		
	}
	
	
	public static function getDefaultCalendar($userId){
		$user = GO_Base_Model_User::model()->findByPk($userId);
		$calendar = GO_Calendar_Model_Calendar::model()->getDefault($user);		
		return $calendar;
	}
	
	public static function initListeners() {		
		GO_Base_Model_Reminder::model()->addListener('dismiss', "GO_Calendar_Model_Event", "reminderDismissed");
	}
	
	
	public static function submitSettings(&$settingsController, &$params, &$response) {
		
		$settings = GO_Calendar_Model_Settings::model()->findByPk($params['id']);
		if(!$settings){
			$settings = new GO_Calendar_Model_Settings();
			$settings->user_id=$params['id'];
		}
		
		$settings->background=$params['background'];
		$settings->reminder=$params['reminder_multiplier'] * $params['reminder_value'];
		$settings->calendar_id=$params['default_calendar_id'];
	

		$settings->save();
		
		return parent::submitSettings($settingsController, $params, $response);
	}
	
	public static function loadSettings(&$settingsController, &$params, &$response) {
		
		$settings = GO_Calendar_Model_Settings::model()->findByPk($params['id']);
		$response['data']=array_merge($response['data'], $settings->getAttributes());
		
		$calendar = GO_Calendar_Model_Calendar::model()->findByPk($settings->calendar_id);
		
		if($calendar){
			$response['data']['default_calendar_id']=$calendar->id;
			$response['remoteComboTexts']['default_calendar_id']=$calendar->name;
		}
		
		$response = GO_Calendar_Controller_Event::reminderSecondsToForm($response);
		
		
		
		return parent::loadSettings($settingsController, $params, $response);
	}
	
	public function install() {
		parent::install();
		
		$group = GO_Calendar_Model_Group();
		$group->name=GO::t('calendars','calendar');
		$group->save();
	}
}