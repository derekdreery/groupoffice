<?php

/**
 * @var string $name 
 * @var boolean $noinferiors
 * @var boolean $marked
 * @var boolean $haschildren
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

		$this->_children = array();
	}

	public function __get($name) {
		return $this->_attributes[$name];
	}

	public function getParentName() {
		$pos = strrpos($this->name, $this->delimiter);

		if ($pos === false)
			return false;

		return substr($this->name, 0, $pos);
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
		$this->_children[] = $mailbox;
	}

	public function getChildren() {
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
}