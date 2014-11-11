<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *

 */

/**
 * @property int $contact_id
 * @property int $user_id
 * @property int $last_mail_time
 */

class GO_Email_Model_ContactMailTime extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Base_Model_UserGroup 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName() {
		return 'em_contacts_last_mail_times';
	}
  
	public function relations() {
		return array(
				'contact' => array('type' => self::BELONGS_TO, 'model' => 'GO_Addressbook_Model_Contact', 'field' => 'contact_id'),
		);
	}
  public function primaryKey() {
    return array('contact_id','user_id');
  }
}