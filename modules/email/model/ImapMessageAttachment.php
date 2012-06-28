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
 * IMAP message attachment model
 */

class GO_Email_Model_ImapMessageAttachment extends GO_Email_Model_MessageAttachment{

	/**
	 *
	 * @var GO_Email_Model_Account 
	 */
	public $account;
	public $mailbox;
	public $uid;
	
	/**
	 * Returns the static model of the specified AR class.
	 * Every child of this class must override it.
	 * 
	 * @return GO_Email_Model_ImapMessageAttachment the static model class
	 */
	public static function model($className=__CLASS__)
	{		
		return parent::model($className);
	}
	
	public function setImapParams(GO_Email_Model_Account $account, $mailbox, $uid){
		$this->account=$account;
		$this->mailbox=$mailbox;
		$this->uid=$uid;
	}
	
	public function getTempDir(){
		$this->_tmpDir=GO::config()->tmpdir.'imap_messages/'.$this->account->id.'-'.$this->mailbox.'-'.$this->uid.'/';
		if(!is_dir($this->_tmpDir))
			mkdir($this->_tmpDir, 0700, true);
		return $this->_tmpDir;
	}
	
	public function saveToFile(GO_Base_Fs_Folder $targetFolder){
		$imap = $this->account->openImapConnection($this->mailbox);
		return $imap->save_to_file($this->uid, $targetFolder->createChild($this->name)->path(),  $this->number, $this->encoding, true);
	}
	
	public function createTempFile() {
		
		if(!$this->hasTempFile()){
			$tmpFile = new GO_Base_Fs_File($this->getTempDir().$this->name);				
			if(!$tmpFile->exists()){
				$imap = $this->account->openImapConnection($this->mailbox);
				$imap->save_to_file($this->uid, $tmpFile->path(),  $this->number, $this->encoding, true);
			}
			$this->setTempFile($tmpFile);
		}
	}
	
	public function getUrl(){
		
		if($this->hasTempFile()){
			return parent::getUrl();
		}else
		{
			$params = array(
					"account_id"=>$this->account->id,
					"mailbox"=>$this->mailbox,
					"uid"=>$this->uid,
					"number"=>$this->number,				
					"encoding"=>$this->encoding,
					"filename"=>$this->name
			);
		}
		
		return GO::url('email/message/attachment', $params);
	}
}