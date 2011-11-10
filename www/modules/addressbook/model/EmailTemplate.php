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

class GO_Addressbook_Model_EmailTemplate extends GO_Base_Db_ActiveRecord {
	
	private $_message;
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Addressbook_Model_EmailTemplate
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	protected function getLocalizedName() {
		return GO::t('emailTemplate', 'addressbook');
	}
	
	public function aclField(){
		return 'acl_id';	
	}
	
	public function tableName(){
		return 'ab_email_templates';
	}
	
	/**
	 * @return GO_Email_Model_SavedMessage
	 */
	private function _getMessage(){
		if(!isset($this->_message)){
			
			//todo getFromMimeData
			$this->_message = GO_Email_Model_SavedMessage::model()->createFromMimeData($this->content);

		}
		return $this->_message;
	}
	protected function getBody(){
		return $this->_getMessage()->getHtmlBody();
	}
}