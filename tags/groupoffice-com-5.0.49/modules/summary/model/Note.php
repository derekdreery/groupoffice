<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @property int $user_id
 * @property string $text
 */

class GO_Summary_Model_Note extends GO_Base_Db_ActiveRecord {
	
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function getLocalizedName(){
		return GO::t('note','summary');
	}
	
	public function tableName(){
		return 'su_notes';
	}
	
	public function primaryKey() {
    return 'user_id';
  }
	
}