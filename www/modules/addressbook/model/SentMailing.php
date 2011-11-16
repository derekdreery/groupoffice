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
 */

 class GO_Addressbook_Model_SentMailing extends GO_Base_Db_ActiveRecord{
		 
	 /**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Addressbook_Model_Addressbook 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName(){
		return 'ab_sent_mailings';
	}
	
	public function hasFiles(){
		return true;
	}
	
	public function relations(){
		return array(
				'addresslist' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Addressbook_Model_Addresslist', 'field'=>'addresslist_id'),
		);
	}
}