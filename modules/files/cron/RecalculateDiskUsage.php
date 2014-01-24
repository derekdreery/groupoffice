<?php

class GO_Files_Cron_RecalculateDiskUsage extends GO_Base_Cron_AbstractCron {

	public function enableUserAndGroupSupport() {
		return false;
	}

	public function getLabel() {
		return "Recalculate user quota";
	}

	public function getDescription() {
		return "";
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
	 * @param GO_Base_Cron_CronJob $cronJob
	 * @param GO_Base_Model_User $user [OPTIONAL]
	 */
	public function run(GO_Base_Cron_CronJob $cronJob, GO_Base_Model_User $user = null) {
		$controller = new GO_Files_Controller_File();
		$controller->run('RecalculateDiskUsage');
	}

}