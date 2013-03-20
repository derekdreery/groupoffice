<?php
class GO_Servermanager_Cron_SubCron extends GO_Base_Cron_AbstractCron {
	
	/**
	 * Return true or false to enable the selection for users and groups for 
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
		return "Cron for servermanager installations";
	}
	
	/**
	 * Get the unique name of the Cronjob
	 * 
	 * @return String
	 */
	public function getDescription(){
		return "Run Cron on all servermanager installations.";
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
	public function run(GO_Base_Cron_CronJob $cronJob,GO_Base_Model_User $user = null){
		$stmt = GO_Servermanager_Model_Installation::model()->find();
		while($installation = $stmt->fetch()){
			
			GO::debug($installation->name);
			
//			echo $installation->name."\n";
			
			if(!file_exists($installation->configPath)){
				trigger_error("Config file ".$installation->configPath." not found");
				continue;
			}		
			$cmd = GO::config()->root_path.'groupofficecli.php -q -r=cron/run -c="'.$installation->configPath.'" &';	
			system($cmd);			
		}
	}
	
}