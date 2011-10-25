<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: GO_Email_Model_LinkedEmail.php 7607 2011-09-01 15:38:01Z <<USERNAME>> $
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */

/**
 * The GO_Email_Model_LinkedEmail model

 */
class GO_Email_Model_Account extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Email_Model_Account
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * Enable this function if you want this model to check the acl's automatically.
	 */
	public function aclField() {
		return 'acl_id';
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'em_accounts';
	}

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	public function relations() {
		return array();
	}
	
	
	protected function beforeSave() {		
		if($this->isModified('password'))
			$this->password = GO_Base_Util_Crypt::encrypt($this->password);		
		
		if($this->isModified('smtp_password'))
			$this->smtp_password = GO_Base_Util_Crypt::encrypt($this->smtp_password);
		
		return parent::beforeSave();
	}
	
	private $_imap;
	
	/**
	 * Open a connection to the imap server.
	 * 
	 * @param string $mailbox
	 * @return GO_Base_Mail_Imap 
	 */
	public function openImapConnection($mailbox='INBOX'){
		if(!isset($this->_imap)){
			$this->_imap = new GO_Base_Mail_Imap();
			$password = GO_Base_Util_Crypt::decrypt($this->password);
			$this->_imap->connect($this->host, $this->port, $this->username, $password, $this->use_ssl);
		}
		$this->_imap->select_mailbox($mailbox);
		
		return $this->_imap;		
	}
}