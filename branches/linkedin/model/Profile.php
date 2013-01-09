<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The Linkedin Profile
 * 
 * @property int $contact_id
 * @property string $first_name
 * @property string $last_name
 * @property string $headline
 * @property string $area
 * @property string $country
 * @property string industry
 */
class GO_Linkedin_Model_AutoImport extends GO_Base_Db_ActiveRecord {
		
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function primaryKey() {
		return 'contact_id';
	}
	
	public function tableName(){
		return 'li_profiles';
	}
	
	public function relations(){
		return array(	
			'contact' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Addressbook_Model_Contacts', 'field'=>'contact_id') );
	}
		
}