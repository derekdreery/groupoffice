<?php
/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id: AbstractCron.php 7962 2011-08-24 14:48:45Z wsmits $
 * @author Wesley Smits <wsmits@intermesh.nl>
 * @package GO.base.cron
 */

/**
 * 
 * @package GO.base.cron
 */
abstract class GO_Base_Cron_AbstractCron extends GO_Base_Model{
	
	/**
	 * Return true or false to enable the selection fo users and groups for 
	 * this cronjob.
	 * 
	 * CAUTION: This will give the run() function a different behaviour. 
	 *					Please see the documentation of the run() function 
	 *					to see what is different.
	 */
	public abstract function enableUserAndGroupSupport();
	
	/**
	 * Get the label of this Cronjob
	 * 
	 * @return String
	 */
	public abstract function getLabel();
	
	/**
	 * Get the description of this Cronjob
	 * 
	 * @return String
	 */
	public abstract function getDescription();
	
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
	public abstract function run(GO_Base_Cron_CronJob $cronJob,GO_Base_Model_User $user = null);
		
}
