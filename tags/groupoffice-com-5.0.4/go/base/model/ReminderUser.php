<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Reminder-Office. You should have received a copy of the
 * Reminder-Office license along with Reminder-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * 
 * The Reminder model
 * 
 * @version $Id: Reminder.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.model
 * @property boolean $mail_sent
 * @property int $time
 * @property int $user_id
 * @property int $reminder_id
 */
class GO_Base_Model_ReminderUser extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Base_Model_ReminderUser 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
  
	public function tableName() {
		return 'go_reminders_users';
	}
	
	public function primaryKey() {
		return array('reminder_id','user_id');
	}
	  
}