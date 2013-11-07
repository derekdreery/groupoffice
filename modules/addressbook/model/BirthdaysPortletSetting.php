<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */
 
/**
 * This model is for saving  the setting to the birthdays portlet
 *
 * @package GO.modules.addressbook
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Michael de Hart mdhart@intermesh.nl
 *
 * @property int $user_id
 * @property int $addressbook_id
 */

class GO_Addressbook_Model_BirthdaysPortletSetting extends GO_Base_Db_ActiveRecord {

	public function tableName() { return 'ab_portlet_birthdays'; }
	public function primaryKey() { return array('addressbook_id','user_id');}
	
	public function relations() {
		return array(
			'addressbook' => array('type' => self::BELONGS_TO, 'model' => 'GO_Addressbook_Model_Addressbook', 'field' => 'addressbook_id', 'delete' => false),
			);
	}
	
}