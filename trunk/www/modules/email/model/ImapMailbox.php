<?php

/**
 * @var string $name 
 * @var boolean $noinferiors
 * @var boolean $marked
 * @var boolean $haschildren
 * @var boolean $hasnochildren
 * @var boolean $noselect
 * @var int $unseen
 * @var int $messages
 * @var string $delimiter

 */
class GO_Email_Model_ImapMailbox extends GO_Base_Model {

	/**
	 *
	 * @var GO_Email_Model_Account 
	 */
	private $_account;
	private $_children;

	/**
	 *
	 * @var string 
	 */
	private $_attributes;

	public function __construct(GO_Email_Model_Account $account, $attributes) {
		$this->_account = $account;

		$this->_attributes = $attributes;
		
		//throw new Exception(var_export($attributes, true));

		//$this->_children = array();
	}
	
	public function __isset($name) {
		$var = $this->__get($name);
		return isset($var);
	}

	public function __get($name) {
		
		$getter = "get".$name;
		if(method_exists($this, $getter))
			return $this->$getter();			
		
		return $this->_attributes[$name];
	}
	
	public function getHasChildren(){
		
		//todo
	}
	
	public function getDelimiter(){
		if(!isset($this->_attributes["delimiter"]))
			$this->_attributes["delimiter"]=$this->getAccount()->openImapConnection ()->get_mailbox_delimiter ();
		
		return $this->_attributes["delimiter"];
	}

	public function getParentName() {
		$pos = strrpos($this->name, $this->delimiter);

		if ($pos === false)
			return false;

		return substr($this->name, 0, $pos);
	}
	
	public function getName($decode=false){
		return $decode ? GO_Base_Mail_Utils::utf7_decode($this->_attributes["name"]) : $this->_attributes["name"];
	}

	public function getBaseName($decode=false) {
		$name = $this->name;
		$pos = strrpos($name, $this->delimiter);

		if ($pos !== false)
			$name= substr($this->name, $pos + 1);
		
		
		return $decode ? GO_Base_Mail_Utils::utf7_decode($name) : $name;
	}

	public function getDisplayName() {
		switch ($this->name) {
			case 'INBOX':
				return GO::t('inbox', 'email');
				break;
			case $this->getAccount()->sent:
				return GO::t('sent', 'email');
				break;
			case $this->getAccount()->trash:
				return GO::t('trash', 'email');
				break;
			case $this->getAccount()->drafts:				
				return GO::t('drafts', 'email');
				break;
			case 'Spam':
				return GO::t('spam','email');
			default:
				return $this->getBaseName(true);
				break;
		}
	}

	public function addChild(GO_Email_Model_ImapMailbox $mailbox) {
		if(!isset($this->_children)){
			$this->_children = array();
		}
		$this->_children[] = $mailbox;
	}

	public function getChildren($subscribed=true, $withStatus=true) {
		if(!isset($this->_children)){

			$imap = $this->getAccount()->openImapConnection();

			$this->_children = array();

			$folders = $imap->list_folders($subscribed,$withStatus,$this->name.$this->delimiter,"%");
			foreach($folders as $folder){
				$mailbox = new GO_Email_Model_ImapMailbox($this->account,$folder);
				$this->_children[]=$mailbox;
			}

		}
		
		return $this->_children;
	}

	/**
	 *
	 * @return GO_Email_Model_Account 
	 */
	public function getAccount() {
		return $this->_account;
	}

//	public function isSent(){
//		return $this->name==$this->_account->sent;
//	}
	
	public function rename($name){
		$parentName = $this->getParentName();
		$newMailbox = empty($parentName) ? $name : $this->delimiter.$name;
		
		return $this->getAccount()->openImapConnection()->rename_folder($this->name, $newMailbox);
	}
	
	public function createChild($name, $subscribe=true){
		$newMailbox = empty($this->name) ? $name : $this->name.$this->delimiter.$name;
		
		//throw new Exception($newMailbox);
		
		return $this->getAccount()->openImapConnection()->create_folder($newMailbox, $subscribe);
	}
	
	public function move(GO_Email_Model_ImapMailbox $targetMailbox){
		
		$newMailbox =$targetMailbox->name.$this->delimiter.$this->getBaseName();
		
		$success = $this->getAccount()->openImapConnection()->rename_folder($this->name, $newMailbox);
		if(!$success)
			return false;
		
		$this->name = $newMailbox;
		
		return true;
	}
}