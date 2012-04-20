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

 * @property int $password_encrypted
 * @property string $smtp_password
 * @property string $smtp_username
 * @property string $smtp_encryption
 * @property int $smtp_port
 * @property string $smtp_host
 * @property string $spam
 * @property string $trash
 * @property string $drafts
 * @property string $sent
 * @property string $mbroot
 * @property string $password
 * @property string $username
 * @property boolean $novalidate_cert
 * @property boolean $use_ssl
 * @property int $port
 * @property string $host
 * @property string $type
 * @property int $acl_id
 * @property int $user_id
 * @property int $id
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
		return array(
				'aliases' => array('type'=>self::HAS_MANY, 'model'=>'GO_Email_Model_Alias', 'field'=>'account_id','delete'=>true),
		);
	}
	
	
	protected function beforeSave() {		
		if($this->isModified('password')){
			$encrypted = GO_Base_Util_Crypt::encrypt($this->password);
			if($encrypted){
				$this->password_encrypted=2;
				$this->password = $encrypted;					
			}
		}
		
		//todo
//		if($this->isModified('smtp_password'))
//			$this->smtp_password = GO_Base_Util_Crypt::encrypt($this->smtp_password);
		
		
		$imap = $this->openImapConnection();
		$this->mbroot=$imap->check_mbroot($this->mbroot);
		
	
		$this->_createDefaultFolder('sent');
		$this->_createDefaultFolder('trash');
		$this->_createDefaultFolder('spam');
		$this->_createDefaultFolder('drafts');	
		
		return parent::beforeSave();
	}
	
	private $_mailboxes;
	
	public function getMailboxes(){
		if(!isset($_mailboxes)){
			$this->_mailboxes= $this->openImapConnection()->get_folders($this->mbroot);
		}
		return $this->_mailboxes;
	}
	
	private $_subscribed;
	
	public function getSubscribed(){
		if(!isset($_subscribed)){
			$this->_subscribed= $this->openImapConnection()->get_folders($this->mbroot, true);
		}
		return $this->_subscribed;
	}
	
	
	private function _createDefaultFolder($name){
		
		if(empty($this->$name))
			return false;
		
		$mailboxes = $this->getMailboxes();
		if(!in_array($this->$name, $mailboxes)){
			if(! $this->openImapConnection()->create_folder($this->$name)){
				if(isset($mailboxes[0])){
					$this->mbroot= $this->openImapConnection()->check_mbroot($mailboxes[0]);

					$this->$name = $this->mboot.$this->$name;

					if(!in_array($this->$name, $mailboxes)){
						 $this->openImapConnection()->create_folder($this->$name);
					}
				}
			}
		}
	}
	
	private $_imap;
	
	public function decryptPassword(){
		return $this->password_encrypted==2 ? GO_Base_Util_Crypt::decrypt($this->password) : $this->password;
	}
	
	/**
	 * Open a connection to the imap server.
	 * 
	 * @param string $mailbox
	 * @return GO_Base_Mail_Imap 
	 */
	public function openImapConnection($mailbox='INBOX'){
		if(!isset($this->_imap)){
			$this->_imap = new GO_Base_Mail_Imap();			
			
			try{
				$this->_imap->connect($this->host, $this->port, $this->username, $this->decryptPassword(), $this->use_ssl);
			}catch(GO_Base_Mail_ImapAuthenticationFailedException $e){
				throw new Exception('Authententication failed for user '.$this->username.' on IMAP server ".$this->host.');
			}		
		}
		if(!$this->_imap->select_mailbox($mailbox))
			throw new Exception ("Could not open IMAP mailbox $mailbox");
		
		return $this->_imap;		
	}

	
	/**
	 * Find an account by e-mail address.
	 * 
	 * @param string $email
	 * @return GO_Email_Model_Account 
	 */
	public function findByEmail($email){
		
		$joinCriteria = GO_Base_Db_FindCriteria::newInstance()
						->addRawCondition('t.id', 'a.account_id');
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->single()
						->join(GO_Email_Model_Alias::model()->tableName(), $joinCriteria,'a')
						->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('email', $email,'=','a'));
		
		return $this->find($findParams);
	}
	
	/**
	 * Get the default alias for this account.
	 * 
	 * @return GO_Email_Model_Alias 
	 */
	public function getDefaultAlias(){
		return GO_Email_Model_Alias::model()->findSingleByAttributes(array(
				'default'=>1,
				'account_id'=>$this->id
		));
	}
	
	
	public function addAlias($email, $name, $signature='', $default=1){
		$a = new GO_Email_Model_Alias();
		$a->account_id=$this->id;
		$a->email=$email;
		$a->name=$name;
		$a->signature=$signature;
		$a->default=$default;
		$a->save();
		
		return $a;
	}
	
	
	/**
	 *
	 * @return \GO_Email_Model_ImapMailbox 
	 */
	public function getAllMailboxes($hierarchy=true, $withStatus=false){
		$imap = $this->openImapConnection();
		
		$folders = $imap->list_folders(true,true, $withStatus);
		
		$node= array('name'=>'','children'=>array());
		
		$rootMailboxes = array();
		
		$mailboxModels =array();
		
		foreach($folders as $folder){
			$mailbox = new GO_Email_Model_ImapMailbox($this,$folder);
			if($hierarchy){
				$mailboxModels[$folder['name']]=$mailbox;
				$parentName = $mailbox->getParentName();
				if($parentName===false){
					$rootMailboxes[]=$mailbox;
				}else{
					$mailboxModels[$parentName]->addChild($mailbox);
				}
			}else
			{
				$rootMailboxes[]=$mailbox;
			}
			
		}
		
		return $rootMailboxes;
	}
	
	

}