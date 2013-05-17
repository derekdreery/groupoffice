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
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 * @property int $template_id
 * @property int $user_id
 */

class GO_Addressbook_Model_DefaultTemplate extends GO_Base_Db_ActiveRecord {
	
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
	
	public function tableName() {
		return 'ab_default_email_templates';
	}
	
	public function primaryKey() {
		return 'user_id';
	}
	
	protected function defaultAttributes() {
		$attr = parent::defaultAttributes();
		
		$findParams = GO_Base_Db_FindParams::newInstance()->limit(1);
		$stmt = GO_Addressbook_Model_Template::model()->find($findParams);
		
		if($template=$stmt->fetch())
		{
			$attr['template_id']=$template->id;
		}
		
		return $attr;
	}
}