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
 *
 * @property boolean $ignore_sent_folder
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
 * @property string $check_mailboxes
 * 
 * @property int $sieve_port
 * @property boolean $sieve_tls
 */
class GO_Email_Model_Account extends GO_Base_Db_ActiveRecord {
	
	/**
	 * Set to false if you don't want the IMAP connection on save.
	 * 
	 * @var boolean 
	 */
	public $checkImapConnectionOnSave=true;
	
	
	private $_imap;

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
				'filters' => array('type'=>self::HAS_MANY, 'model'=>'GO_Email_Model_Filter', 'field'=>'account_id','delete'=>true, 'findParams'=>  GO_Base_Db_FindParams::newInstance()->order("priority")),
				'portletFolders' => array('type'=>self::HAS_MANY, 'model'=>'GO_Email_Model_PortletFolder', 'field'=>'account_id','delete'=>true)
		);
	}


	protected function beforeSave() {
		if($this->isModified('password')){
			$encrypted = GO_Base_Util_Crypt::encrypt($this->password);		
			if($encrypted){
				$this->password = $encrypted;
				$this->password_encrypted=2;//deprecated. remove when email is mvc style.
			}
		}

		if($this->isModified('smtp_password')){
			$encrypted = GO_Base_Util_Crypt::encrypt($this->smtp_password);		
			if($encrypted)
				$this->smtp_password = $encrypted;
		}
		
		if(
				($this->isNew || $this->isModified("host") || $this->isModified("port") || $this->isModified("username")  || $this->isModified("password")) 
				&& $this->checkImapConnectionOnSave
			){

			$imap = $this->openImapConnection();
			$this->mbroot=$imap->check_mbroot($this->mbroot);

			$this->_createDefaultFolder('sent');
			$this->_createDefaultFolder('trash');
			//	$this->_createDefaultFolder('spam');
			$this->_createDefaultFolder('drafts');	
		}
		
		return parent::beforeSave();
	}

	private $_mailboxes;

	public function getMailboxes(){
		if(!isset($this->_mailboxes)){
			$this->_mailboxes= $this->openImapConnection()->get_folders($this->mbroot);
		}
		return $this->_mailboxes;
	}

	private $_subscribed;

	public function getSubscribed(){
		if(!isset($this->_subscribed)){
			$this->_subscribed= $this->openImapConnection()->get_folders($this->mbroot, true);
		}
		return $this->_subscribed;
	}


	private function _createDefaultFolder($name){

		if(empty($this->$name))
			return false;

		$mailboxes = $this->getMailboxes();
		
		//throw new Exception(var_export($mailboxes, true));
		
		if(!isset($mailboxes[$this->$name])){
//			throw new Exception($this->$name);
			if(!$this->openImapConnection()->create_folder($this->$name)){
//				if(isset($mailboxes[0])){
					$this->mbroot= $this->openImapConnection()->check_mbroot("INBOX");

					$this->$name = $this->mbroot.$this->$name;

					if(!in_array($this->$name, $mailboxes)){
						 $this->openImapConnection()->create_folder($this->$name);
					}
//				}
			}
		}
	}

	

	public function decryptPassword(){
		//return $this->password_encrypted==2 ? GO_Base_Util_Crypt::decrypt($this->password) : $this->password;
		$decrypted = GO_Base_Util_Crypt::decrypt($this->password);
		return $decrypted ? $decrypted : $this->password;
	}

	public function decryptSmtpPassword(){
		$decrypted = GO_Base_Util_Crypt::decrypt($this->smtp_password);
		return $decrypted ? $decrypted : $this->smtp_password;
	}
	
	/**
	 * Open a connection to the imap server.
	 *
	 * @param string $mailbox
	 * @return GO_Base_Mail_Imap
	 */
	public function openImapConnection($mailbox='INBOX'){
	
		if(empty($mailbox))
			$mailbox="INBOX";
		
		if(empty($this->_imap)){
			$this->_imap = new GO_Base_Mail_Imap();

			try{
				$this->_imap->connect($this->host, $this->port, $this->username, $this->decryptPassword(), $this->use_ssl);
			}catch(GO_Base_Mail_ImapAuthenticationFailedException $e){
				throw new Exception('Authententication failed for user '.$this->username.' on IMAP server '.$this->host);
			}
		}
		if(!$this->_imap->select_mailbox($mailbox))
			throw new Exception ("Could not open IMAP mailbox $mailbox\nIMAP error: ".$this->_imap->last_error());
	
		return $this->_imap;
	}
	
	/**
	 * Close the connection to imap
	 */
	public function closeImapConnection(){
		if(!empty($this->_imap)){
			$this->_imap->disconnect();
			$this->_imap=null;		
		}
	}
	
	public function __wakeup() {
		//reestablish imap connection after deserialization
		$this->_imap=null;
	}
	
	/**
	 * Get the imap connection if it's open.
	 * 
	 * @return GO_Base_Mail_Imap 
	 */
	public function getImapConnection(){
		if(isset($this->_imap)){
			return $this->_imap;
		}else
			return false;
	}
	
//	private function _getCacheKey(){
//		$user_id = GO::user() ? GO::user()->id : 0;
//		return $user_id.':'.$this->id.':uidnext';
//	}
	
//	protected function getHasNewMessages(){
//		
//		GO::debug("getHasNewMessages UIDNext ".(isset($this->_imap->selected_mailbox['uidnext']) ? $this->_imap->selected_mailbox['uidnext'] : ""));
//		
//		if(isset($this->_imap->selected_mailbox['name']) && $this->_imap->selected_mailbox['name']=='INBOX' && !empty($this->_imap->selected_mailbox['uidnext'])){
//			
//			$cacheKey = $this->_getCacheKey();
//			
//			$uidnext = $value = GO::cache()->get($cacheKey);
//			
//			GO::cache()->set($cacheKey, $this->_imap->selected_mailbox['uidnext']);					
//			
//			if($uidnext!==false && $uidnext!=$this->_imap->selected_mailbox['uidnext']){
//				return true;
//			}			
//		}
//			
//		return false;
//	}


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
	
	/**
	 * Get an array of mailboxes that should be checked periodically for new mail
	 * 
	 * @return array
	 */
	public function getAutoCheckMailboxes(){
		$checkMailboxArray = empty($this->check_mailboxes) ? array() : explode(',',$this->check_mailboxes);
//		if(!in_array("INBOX", $checkMailboxArray))
//			$checkMailboxArray[]="INBOX";
		return $checkMailboxArray;
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
	public function getRootMailboxes($withStatus=false, $subscribed=false){
		$imap = $this->openImapConnection();
		
		$rootMailboxes = array();
				
		$folders = $imap->list_folders($subscribed,$withStatus,"","{$this->mbroot}%", true);		
//		GO::debug($folders);
		foreach($folders as $folder){
			$mailbox = new GO_Email_Model_ImapMailbox($this,$folder);
			$rootMailboxes[]=$mailbox;
		}
		
		return $rootMailboxes;
	}

	
	/**
	 *
	 * @return \GO_Email_Model_ImapMailbox 
	 */
	public function getAllMailboxes($hierarchy=true, $withStatus=false){
		$imap = $this->openImapConnection();
		
		$folders = $imap->list_folders(true, $withStatus,'','*',true);
		
		//$node= array('name'=>'','children'=>array());
		
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
	
	public function defaultAttributes() {
		$attr = parent::defaultAttributes();
		
		$attr['check_mailboxes']="INBOX";
//		if (GO::modules()->isInstalled('sieve')) {
			$attr['sieve_port'] = !empty(GO::config()->sieve_port) ? GO::config()->sieve_port : '4190';
			if (isset(GO::config()->sieve_usetls))
				$attr['sieve_usetls'] = !empty(GO::config()->sieve_usetls);
			else
				$attr['sieve_usetls'] = true;
//		}	
		return $attr;
	}
	
}
