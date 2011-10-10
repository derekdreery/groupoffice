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
class GO_Email_Model_ImapMessage extends GO_Base_Model {

	private $_attributes = array(
			'to' => '',
			'cc' => '',
			'bcc' => '',
			'from' => '',
			'subject' => '',
			'uid' => '',
			'size' => '',
			'internal_date' => '',
			'date' => '',
			'udate' => '',
			'internal_udate' => '',
			'x_priority' => 3,
			'reply_to' => '',
			'message_id' => '',
			'content_type' => '',
			'content_type_attributes' => array(),
			'disposition_notification_to' => '',
			'content_transfer_encoding' => '',
			'charset' => '',
			'seen' => 0,
			'flagged' => 0,
			'answered' => 0,
			'forwarded' => 0,
			'account'
	);

	/**
	 * PHP getter magic method.
	 * This method is overridden so that AR attributes can be accessed like properties.
	 * @param string $name property name
	 * @return mixed property value
	 * @see getAttribute
	 */
	public function __get($name) {
		if (isset($this->_attributes[$name])) {
			return $this->_attributes[$name];
		}
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Every child of this class must override it.
	 * 
	 * @return GO_Email_Model_ImapMessage the static model class
	 */
	public static function model($className=__CLASS__) {
		if (isset(self::$_models[$className]))
			return self::$_models[$className];
		else {
			$model = self::$_models[$className] = new $className();
			return $model;
		}
	}

	public function setAttibutes($attributes) {

		$this->_attributes = array_merge($this->_attributes, $attributes);
		
		$this->_attributes['to']=new GO_Base_Mail_EmailRecipients($this->_attributes['to']);
		$this->_attributes['cc']=new GO_Base_Mail_EmailRecipients($this->_attributes['cc']);
		$this->_attributes['bcc']=new GO_Base_Mail_EmailRecipients($this->_attributes['bcc']);
		$this->_attributes['from']=new GO_Base_Mail_EmailRecipients($this->_attributes['from']);
		$this->_attributes['reply_to']=new GO_Base_Mail_EmailRecipients($this->_attributes['reply_to']);
		
		
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
		$imap = $this->account->openImapConnection();

		return $imap->save_to_file($this->uid, $path);
	}

	public function getBody() {
		
	}

}