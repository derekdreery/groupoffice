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
 * @author WilmarVB <wilmar@intermesh.nl>
 */

class GO_Addressbook_Model_ContactVcardProperty extends GO_Base_Db_ActiveRecord {

	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName(){
		return 'ab_contacts_vcard_props';
	}
	
	public function relations(){
		return array(
			'contact' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Addressbook_Model_Contact', 'field'=>'contact_id')
		);
	}
	
}