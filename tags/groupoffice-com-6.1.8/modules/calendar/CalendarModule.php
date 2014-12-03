<?php


namespace GO\Calendar;


class CalendarModule extends \GO\Base\Module{
	
	
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
		$user = \GO\Base\Model\User::model()->findByPk($userId);
		$calendar = Model\Calendar::model()->getDefault($user);		
		return $calendar;
	}
	
	public static function commentsRequired(){
		return isset(\GO::config()->calendar_category_required)?\GO::config()->calendar_category_required:false;
	} 
	
	public static function initListeners() {		
		\GO\Base\Model\Reminder::model()->addListener('dismiss', "GO\Calendar\Model\Event", "reminderDismissed");
	}
	
	
	public static function submitSettings(&$settingsController, &$params, &$response, $user) {
		
		$settings = Model\Settings::model()->getDefault($user);
		if(!$settings){
			$settings = new Model\Settings();
			$settings->user_id=$params['id'];
		}
		
		$settings->background=$params['background'];
		$settings->reminder=$params['reminder_multiplier'] * $params['reminder_value'];
		$settings->calendar_id=$params['default_calendar_id'];
		$settings->show_statuses=$params['show_statuses'];
	

		$settings->save();
		
		return parent::submitSettings($settingsController, $params, $response, $user);
	}
	
	public static function loadSettings(&$settingsController, &$params, &$response, $user) {
		
		$settings = Model\Settings::model()->getDefault($user);
		$response['data']=array_merge($response['data'], $settings->getAttributes());
		
		$calendar = Model\Calendar::model()->findByPk($settings->calendar_id);
		
		if($calendar){
			$response['data']['default_calendar_id']=$calendar->id;
			$response['remoteComboTexts']['default_calendar_id']=$calendar->name;
		}
		
		$response = Controller\EventController::reminderSecondsToForm($response);
		
		
		
		return parent::loadSettings($settingsController, $params, $response, $user);
	}
	
	public function install() {
		parent::install();
		
		$group = new Model\Group();
		$group->name=\GO::t('calendars','calendar');
		$group->save();
		
		
		$cron = new \GO\Base\Cron\CronJob();
		
		$cron->name = 'Calendar publisher';
		$cron->active = true;
		$cron->runonce = false;
		$cron->minutes = '0';
		$cron->hours = '*';
		$cron->monthdays = '*';
		$cron->months = '*';
		$cron->weekdays = '*';
		$cron->job = 'GO\Calendar\Cron\CalendarPublisher';

		$cron->save();
		
	}
}