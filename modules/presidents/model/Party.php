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
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * The Party model
 * 
 * @property int $id
 * @property string $name
 */
class GO_Presidents_Model_Party extends GO_Base_Db_ActiveRecord {
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Presidents_Model_Party 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function tableName() {
		return 'pm_parties';
	}


	public function relations() {
		return array(
			'presidents' => array('type' => self::HAS_MANY, 'model' => 'GO_Presidents_Model_President', 'field' => 'party_id', 'delete' => true)		
		);
	}

}