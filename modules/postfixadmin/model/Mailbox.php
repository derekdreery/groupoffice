<?php

class GO_Postfixadmin_Model_Mailbox extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Postfixadmin_Model_Mailbox 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function tableName() {
		return 'pa_mailboxes';
	}
	

	public function relations() {
		return array(
				'aliases' => array('type' => self::HAS_MANY, 'model' => 'GO_Postfixadmin_Model_Alias', 'field' => 'mailbox_id', 'delete' => true)		);
	}
	
	protected function init() {
		$this->columns['username']['unique']=true;
		$this->columns['username']['required']=true;
		
		return parent::init();
	}
}