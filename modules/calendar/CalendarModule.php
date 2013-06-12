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
	
	public static function getDefaultCalendar($userId){
		$user = GO_Base_Model_User::model()->findByPk($userId);
		$calendar = GO_Calendar_Model_Calendar::model()->getDefault($user);		
		return $calendar;
	}
	
	public static function commentsRequired(){
		return isset(GO::config()->calendar_category_required)?GO::config()->calendar_category_required:false;
	} 
	
	public static function initListeners() {		
		GO_Base_Model_Reminder::model()->addListener('dismiss', "GO_Calendar_Model_Event", "reminderDismissed");
	}
	
	
	public static function submitSettings(&$settingsController, &$params, &$response, $user) {
		
		$settings = GO_Calendar_Model_Settings::model()->getDefault($user);
		if(!$settings){
			$settings = new GO_Calendar_Model_Settings();
			$settings->user_id=$params['id'];
		}
		
		$settings->background=$params['background'];
		$settings->reminder=$params['reminder_multiplier'] * $params['reminder_value'];
		$settings->calendar_id=$params['default_calendar_id'];
	

		$settings->save();
		
		return parent::submitSettings($settingsController, $params, $response, $user);
	}
	
	public static function loadSettings(&$settingsController, &$params, &$response, $user) {
		
		$settings = GO_Calendar_Model_Settings::model()->getDefault($user);
		$response['data']=array_merge($response['data'], $settings->getAttributes());
		
		$calendar = GO_Calendar_Model_Calendar::model()->findByPk($settings->calendar_id);
		
		if($calendar){
			$response['data']['default_calendar_id']=$calendar->id;
			$response['remoteComboTexts']['default_calendar_id']=$calendar->name;
		}
		
		$response = GO_Calendar_Controller_Event::reminderSecondsToForm($response);
		
		
		
		return parent::loadSettings($settingsController, $params, $response, $user);
	}
	
	public function install() {
		parent::install();
		
		$group = new GO_Calendar_Model_Group();
		$group->name=GO::t('calendars','calendar');
		$group->save();
	}
}