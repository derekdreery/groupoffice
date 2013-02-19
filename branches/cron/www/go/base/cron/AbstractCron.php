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
	 * An unique name for the cronjob.
	 * This Property needs to be overridden by the parent class.
	 *
	 * @var string
	 */
	public $uniqueName = 'AbstractCron';
	
	/**
	 * Get the unique name of the Cronjob
	 * 
	 * @return String
	 */
	public function getName(){
		return $this->uniqueName;
	}
	
	/**
	 * The code that needs to be called when the cron is running
	 */
	public static function run(){
		echo "RUN THIS CRON";
	}

}
