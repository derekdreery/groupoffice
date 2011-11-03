<?php

/**
 * A message from the imap server
 * 
 * @package GO.modules.email
 * 
 * @property GO_Base_Mail_EmailRecipients $to
 * @property GO_Base_Mail_EmailRecipients $cc
 * @property GO_Base_Mail_EmailRecipients $bcc
 * @property GO_Base_Mail_EmailRecipientstring $from
 * @property GO_Base_Mail_EmailRecipients $reply_to
 * @property string $subject
 * @property int $uid
 * @property int $size
 * @property string $internal_date
 * @property string $date
 * @property int $udate
 * @property int $internal_udate
 * @property string $x_priority 
 * @property string $message_id
 * @property string $content_type
 * @property array $content_type_attributes
 * @property string $disposition_notification_to
 * @property string $content_transfer_encoding
 * @property string $charset
 * @property bool $seen
 * @property bool $flagged
 * @property bool $answered
 * @property bool $forwarded
 * @property GO_Email_Model_Account $account
 * @property String $mailbox
 */
class GO_Email_Model_ImapMessage extends GO_Email_Model_Message {
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Email_Model_ImapMessage
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	/**
	 *
	 * @param GO_Email_Model_Account $account
	 * @param int $uid 
	 */
	public function findByUid($account, $mailbox, $uid) {

		$imapMessage = new GO_Email_Model_ImapMessage();
		$imap = $account->openImapConnection($mailbox);

		$attributes = $imap->get_message_header($uid, true);
		if (!$attributes)
			return false;

		$attributes['account'] = $account;
		$attributes['mailbox'] = $mailbox;

		$imapMessage->setAttributes($attributes);

		return $imapMessage;
	}

	public function saveToFile($path) {
		$imap = $this->account->openImapConnection($this->mailbox);

		return $imap->save_to_file($this->uid, $path);
	}
	
	/**
	 *
	 * @return GO_Base_Mail_Imap 
	 */
	private function _getImapConnection(){
		return $this->account->openImapConnection($this->mailbox);
	}

	public function getBody() {
		
		$headers = $this->_getImapConnection()->get_message_header($uid, true);
		
	}
	
	public function getSource(){
		
	}

}