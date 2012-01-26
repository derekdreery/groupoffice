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
 * @property array $content_typeattributes
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

	protected $attributes = array(
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
			'content_typeattributes' => array(),
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
	
	protected $attachments=array();
	
	protected $defaultCharset='UTF-8';
	
	public function __construct() {
		$this->attributes['to'] = new GO_Base_Mail_EmailRecipients($this->attributes['to']);
		$this->attributes['cc'] = new GO_Base_Mail_EmailRecipients($this->attributes['cc']);
		$this->attributes['bcc'] = new GO_Base_Mail_EmailRecipients($this->attributes['bcc']);
		$this->attributes['from'] = new GO_Base_Mail_EmailRecipients($this->attributes['from']);
		$this->attributes['reply_to'] = new GO_Base_Mail_EmailRecipients($this->attributes['reply_to']);
	}

	/**
	 * PHP getter magic method.
	 * This method is overridden so that AR attributes can be accessed like properties.
	 * @param string $name property name
	 * @return mixed property value
	 * @see getAttribute
	 */
	public function __get($name) {
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
	}
	
	public function __set($name, $value){
		$this->attributes[$name]=$value;
	}
	
	public function __isset($name) {
		return isset($this->attributes['name']);
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

		$this->attributes = array_merge($this->attributes, $attributes);

		$this->attributes['to'] = new GO_Base_Mail_EmailRecipients($this->attributes['to']);
		$this->attributes['cc'] = new GO_Base_Mail_EmailRecipients($this->attributes['cc']);
		$this->attributes['bcc'] = new GO_Base_Mail_EmailRecipients($this->attributes['bcc']);
		$this->attributes['from'] = new GO_Base_Mail_EmailRecipients($this->attributes['from']);
		$this->attributes['reply_to'] = new GO_Base_Mail_EmailRecipients($this->attributes['reply_to']);
	}

	/**
	 * Get the body in HTML format. If no HTML body was found the text version will
	 * be converted to HTML.
	 * 
	 * @return string 
	 */
	abstract public function getHtmlBody();
	
	/**
	 * Get the body in plain text format. If no plain text body was found the HTML version will
	 * be converted to plain text.
	 * 
	 * @return string 
	 */
	abstract public function getPlainBody();
		
	/**
	 * Return the raw MIME source as string
	 * 
	 * @return string
	 */
	abstract public function getSource();

	/**
	 * Get an array of attachments in this message.
	 * 
	 * @return array
	 * 
	 * The array is formatted like this:
	 * 
	 * indexed by $a['number']
	 * 
	 * $a['url']='';
	 *  $a['name']=$filename;
			$a['number']="2";
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
		return $this->attachments;
	}
	
	/**
	 * Get an attachment by MIME partnumber.
	 * eg. 1.1 or 2
	 * 
	 * @param string $number
	 * @return array See getAttachments 
	 */
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
	
	private function _convertRecipientArray($r){
		$new = array();
		foreach($r as $email=>$personal)
			$new[]=array('email'=>$email, 'personal'=>$personal);
		
		return $new;
	}

	/**
	 * Returns MIME fields contained in this class's instance as an associative
	 * array.
	 * 
	 * @param boolean $html Whether or not to return the HTML body. The alternative is
	 * plain text. Defaults to true.
	 * 
	 * @return Array
	 */
	public function toOutputArray($html=true, $recipientsAsString=false) {

		$from = $this->from->getAddresses();		

		$response['notification'] = $this->disposition_notification_to;
		$response['subject'] = $this->subject;
				
		$from = $this->from->getAddress();
		$response['from'] = $from['personal'];
		$response['sender'] = $from['email'];
		$response['to'] = $recipientsAsString ? (string) $this->to : $this->_convertRecipientArray($this->to->getAddresses());
		$response['cc'] = $recipientsAsString ? (string) $this->cc : $this->_convertRecipientArray($this->cc->getAddresses());
		$response['bcc'] = $recipientsAsString ? (string) $this->bcc :  $this->_convertRecipientArray($this->bcc->getAddresses());
		$response['reply_to'] = (string) $this->reply_to;
		$response['message_id'] = $this->message_id;
		$response['date'] = $this->date;

		$response['to_string'] = (string) $this->to;

		if (!$recipientsAsString && empty($response['to']))
			$response['to'][] = array('email' => '', 'personal' => GO::t('no_recipients', 'email'));

		$response['full_from'] = (string) $this->from;
		$response['priority'] = intval($this->x_priority);
		$response['udate'] = $this->udate;
		$response['date'] = GO_Base_Util_Date::get_timestamp($this->udate);
		$response['size'] = $this->size;

		$response['attachments'] = array();

		$response['inlineAttachments'] = array();

		if($html)
			$response['htmlbody'] = $this->getHtmlBody();
		else
			$response['plainbody'] =$this->getPlainBody();

		$response['smime_signed'] = false;

		$attachments = $this->getAttachments();

		foreach($attachments as $a){
			$replaceCount = 0;

			
			if (!empty($a['content_id']))
				$response['htmlbody'] = str_replace('cid:' . $a['content_id'], $a['url'], $response['htmlbody'], $replaceCount);

			if ($a['name'] == 'smime.p7s') {
				$response['smime_signed'] = true;
				continue;
			}

			if(!$replaceCount)
				$response['attachments'][] = $a;
			else
				$response['inlineAttachments'][]=$a;
				
		}
		
		$response['blocked_images']=0;
		$response['xssDetected']=false;

		return $response;
	}

}