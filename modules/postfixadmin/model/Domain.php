<?php

class GO_Postfixadmin_Model_Domain extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Postfixadmin_Model_Domain 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function aclField() {
		return 'acl_id';
	}

	public function tableName() {
		return 'pa_domains';
	}
	

	public function relations() {
		return array(
				'mailboxes' => array('type' => self::HAS_MANY, 'model' => 'GO_Postfixadmin_Model_Mailbox', 'field' => 'domain_id', 'delete' => true)		);
	}
	
	protected function init() {
		$this->columns['domain']['unique']=true;
		return parent::init();
	}
}