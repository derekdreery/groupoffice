<?php

/**
 * A message from the imap server
 * 
 * @package GO.modules.email
 * 
 * @property GO_Base_Mail_EmailRecipients $to
 * @property GO_Base_Mail_EmailRecipients $cc
 * @property GO_Base_Mail_EmailRecipients $bcc
 * @property GO_Base_Mail_EmailRecipients $from
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
abstract class GO_Email_Model_Message extends GO_Base_Model {

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
			'account',
			'smime_signed'=>false
	);
	
	protected $defaultCharset='UTF-8';

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
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Email_Model_ImapMessage
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function setAttributes($attributes) {

		$this->_attributes = array_merge($this->_attributes, $attributes);

		$this->_attributes['to'] = new GO_Base_Mail_EmailRecipients($this->_attributes['to']);
		$this->_attributes['cc'] = new GO_Base_Mail_EmailRecipients($this->_attributes['cc']);
		$this->_attributes['bcc'] = new GO_Base_Mail_EmailRecipients($this->_attributes['bcc']);
		$this->_attributes['from'] = new GO_Base_Mail_EmailRecipients($this->_attributes['from']);
		$this->_attributes['reply_to'] = new GO_Base_Mail_EmailRecipients($this->_attributes['reply_to']);
	}

	public function getHtmlBody() {
		return '';
	}

	public function getSource() {
		return '';
	}

	/**
	 *
	 * @return array
	 * 
	 * 
	 * indexed by $a['number']
	 * 
	 * $a['url']='';
	 *  $a['name']=$filename;
			$a['number']=$part_number_prefix.$part_number;
			$a['content_id']=$content_id;
			$a['mime']=$mime_type;
			$a['tmp_file']=false;
			$a['index']=count($this->_attachments);
			$a['size']=isset($part->body) ? strlen($part->body) : 0;
			$a['human_size']= GO_Base_Util_Number::formatSize($a['size']);
			$a['extension']=  $f->extension();
			$a['encoding'] = isset($part->headers['content-transfer-encoding']) ? $part->headers['content-transfer-encoding'] : '';
			$a['disposition'] = isset($part->disposition) ? $part->disposition : ''; 
	 */
	
	public function getAttachments() {
		return array();
	}
	
	public function getAttachment($number){
		$att = $this->getAttachments();
		
		return $att[$number];
	}

	/** 
	 * Return the URL to display the attachment
	 * 
	 * @param array $attachment See getAttachments
	 * @return string 
	 */
	protected function getAttachmentUrl($attachment) {
		return '';
	}

	public function toOutputArray() {

		$from = $this->from->getAddresses();
		
		

		$response['notification'] = $this->disposition_notification_to;
		$response['subject'] = $this->subject;
		$response['from'] = $this->from->getPersonal();
		$response['sender'] = $this->from->getEmail();
		$response['to'] = $this->to->getAddresses();
		$response['cc'] = $this->cc->getAddresses();
		$response['bcc'] = $this->bcc->getAddresses();
		$response['reply_to'] = (string) $this->reply_to;
		$response['message_id'] = $this->message_id;
		$response['date'] = $this->date;

		$response['to_string'] = (string) $this->to;


		if (empty($response['to'])) {
			$response['to'][] = array('email' => '', 'personal' => GO::t('no_recipients', 'email'));
		}

		$response['full_from'] = (string) $this->from;
		$response['priority'] = intval($this->x_priority);
		$response['udate'] = $this->udate;
		$response['date'] = GO_Base_Util_Date::get_timestamp($this->udate);
		$response['size'] = $this->size;

		$response['attachments'] = array();
		$response['body'] = $this->getHtmlBody();

		$response['smime_signed'] = false;

		$attachments = $this->getAttachments();

		foreach($attachments as $a){
			$replaceCount = 0;

			
			if (!empty($a['content_id']))
				$response['body'] = str_replace('cid:' . $a['content_id'], $a['url'], $response['body'], $replaceCount);

			if ($a['name'] == 'smime.p7s') {
				$response['smime_signed'] = true;
				continue;
			}

			if(!$replaceCount)
				$response['attachments'][] = $a;
		}
		
		$response['blocked_images']=0;

		//for compatibility with IMAP get_message_with_body
		//$response['url_replacements']=$response['inline_attachments'];

		return $response;
	}

}