<?php


namespace GO\Calendar\Cron;


class CalendarPublisher extends \GO\Base\Cron\AbstractCron {
	
	/**
	 * Return true or false to enable the selection fo users and groups for 
	 * this cronjob.
	 * 
	 * CAUTION: This will give the run() function a different behaviour. 
	 *					Please see the documentation of the run() function 
	 *					to see what is different.
	 */
	public function enableUserAndGroupSupport(){
		return false;
	}
	
	/**
	 * Get the unique name of the Cronjob
	 * 
	 * @return String
	 */
	public function getLabel(){
		return \GO::t('shareCalendarsCron','calendar');
	}
	
	/**
	 * Get the unique name of the Cronjob
	 * 
	 * @return String
	 */
	public function getDescription(){
		return \GO::t('shareCalendarsCronDescription','calendar');
	}
	
	/**
	 * The code that needs to be called when the cron is running
	 * 
	 * If $this->enableUserAndGroupSupport() returns TRUE then the run function 
	 * will be called for each $user. (The $user parameter will be given)
	 * 
	 * If $this->enableUserAndGroupSupport() returns FALSE then the 
	 * $user parameter is null and the run function will be called only once.
	 * 
	 * @param \GO\Base\Cron\CronJob $cronJob
	 * @param \GO\Base\Model\User $user [OPTIONAL]
	 */
	public function run(\GO\Base\Cron\CronJob $cronJob,\GO\Base\Model\User $user = null){
		
		\GO::session()->runAsRoot();
		
		\GO::debug("Start updating public calendars.");
	
		$calendars = \GO\Calendar\Model\Calendar::model()->findByAttribute('public', true);
		
		foreach($calendars as $calendar){

			$file = new \GO\Base\Fs\File($calendar->getPublicIcsPath());
	
			if(!$file->exists()){
				\GO::debug("Creating ".$file->path().".");
				$file->touch(true);
			}

			$file->putContents($calendar->toVObject());

			\GO::debug("Updating ".$calendar->name." to ".$file->path().".");
		}
		
		\GO::debug("Finished updating public calendars.");
	}
}