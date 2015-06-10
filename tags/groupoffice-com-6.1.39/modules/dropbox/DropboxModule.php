<?php
namespace GO\Dropbox;

class DropboxModule extends \GO\Base\Module{
	
	public function package() {
		return self::PACKAGE_UNSUPPORTED;
	}

	const DBX_ROOT = '/'; //todo this can't be changed because it will break dbxToGoFolder and vice versa
	const GO_ROOT = '/Dropbox';

	private static $client;
	
	public static function initListeners() {
		\GO\Base\Model\User::model()->addListener('delete', "GO\Dropbox\DropboxModule", "userDelete");
	}
	
	public function depends() {
		return array('files');
	}
	
	public function install() {
		parent::install();
		
		$cron = new \GO\Base\Cron\CronJob();
		
		$cron->name = 'Dropbox sync';
		$cron->active = true;
		$cron->runonce = false;
		$cron->minutes = '*';
		$cron->hours = '*';
		$cron->monthdays = '*';
		$cron->months = '*';
		$cron->weekdays = '*';
		$cron->job = 'GO\\Dropbox\\Cron\\Sync';

		$cron->save();
	}
	
	public function uninstall() {
		parent::uninstall();
		
		$cron = \GO\Base\Cron\CronJob::model()->findSingleByAttribute('job','GO\\Dropbox\\Cron\\Sync');
		
		if(!$cron) // No cronjob found, so nothing needs to be deleted.
			return true;
		
		if(!$cron->delete()) // Try to delete the cronjob, if it's not possible then throw an exception.
			Throw new \Exception('The Dropbox systemtask could not be deleted automatically. Please try to delete it manually in the "System tasks module."');
		else
			return true;
	}
	
	public static function userDelete(\GO\Base\Model\User $user){
		$dbxUser = Model\User::model()->findByPk($user->id);
		
		if($dbxUser){
			$dbxUser->delete();
		}
	}

}