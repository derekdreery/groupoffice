<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 * @property int $contact_id
 * @property int $addresslist_id
 */

class GO_Addressbook_Model_AddresslistContact extends GO_Base_Db_ActiveRecord {
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Addressbook_Model_Company 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName(){
		return 'ab_addresslist_contacts';
	}
	
	public function primaryKey() {
		return array('addresslist_id','contact_id');
	}
	
	public function relations() {
	 return array();
	}
	
}