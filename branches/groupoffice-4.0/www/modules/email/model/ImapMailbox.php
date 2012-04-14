<?php
/**
 * @var string $name 
 * @var boolean $noinferiors
 * @var boolean $marked
 * @var boolean $haschildren
 * @var int $unseen
 * @var int $messages
 * @var string $delimiter
 * @var string $name


 */
class GO_Email_Model_ImapMailbox extends GO_Base_Model {
	/**
	 *
	 * @var GO_Email_Model_Account 
	 */
	private $_account;
	
	/**
	 *
	 * @var string 
	 */
	private $_attributes;
	
	public function __construct(GO_Email_Model_Account $account, $attributes){		
		$this->_account = $account;
		
		$this->_attributes=$attributes;
	}
	
	public function __get($name){
		return $this->_attributes[$name];
	}
	
	public function getParentName(){
		$pos = strrpos($this->name, $this->delimiter);
		
		if($pos===false)
			return false;
		
		return substr($this->name,0, $pos);
	}
	
	
}