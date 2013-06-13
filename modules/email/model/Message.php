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
 * @property string $internal_date Date received
 * @property string $date Date sent
 * @property int $udate Unix time stamp sent
 * @property int $internal_udate Unix time stamp received
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
	
	/**
	 * True iff the actual message's body is larger than the maximum allowed. See
	 * also how GO_Base_Mail_Imap::max_read is used.
	 * @var boolean
	 */
	protected $_bodyTruncated;
	
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
		
		$getter = 'get'.$name;
		if(method_exists($this, $getter))
			return $this->$getter();
		else	if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
	}
	
	public function __set($name, $value){
		$setter = 'set'.$name;
		if(method_exists($this, $setter))
			return $this->$setter($name, $value);
		else
			$this->attributes[$name]=$value;
	}
	
	public function __isset($name) {
		$value = $this->__get($name);
		return isset($value);
	}
	
	public function __unset($name) {
		unset($this->attributes[$name]);
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
		
		
	$this->attributes['x_priority']= isset($this->attributes['x_priority']) ? strtolower($this->attributes['x_priority']) : 3;
		switch($this->attributes['x_priority']){
			case 'high':
				$this->attributes['x_priority']=1;
				break;
			
			case 'low':
				$this->attributes['x_priority']=5;
				break;
			
			case 'normal':
				$this->attributes['x_priority']=3;
				break;
			
			default:
				$this->attributes['x_priority']= intval($this->attributes['x_priority']);
				break;
		}
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
	 * @return array GO_Email_Model_MessageAttachment
	 * 
	 */
	
	public function &getAttachments() {
		return $this->attachments;
	}
	
	public function addAttachment(GO_Email_Model_MessageAttachment $a){
		$this->attachments[$a->number]=$a;
	}
	
	public function isAttachment($number){
		$att = $this->getAttachments();		
		return isset($att[$number]);
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
		if(!isset($att[$number]))
			return false;
		else
			return $att[$number];
//			false}else{
//			//throw new Exception("Attachment number $number not found");
//			return ;
//		}
	}
	
	
	protected function extractUuencodedAttachments(&$body)
	{
		$body = str_replace("\r", '', $body);
		$regex = "/(begin ([0-7]{3}) (.+))\n(.+)\nend/Us";

		preg_match_all($regex, $body, $matches);

    
    for ($i = 0; $i < count($matches[3]); $i++) {
//			$boundary	= $matches[1][$i];
//			$fileperm	= $matches[2][$i];
			$filename	= trim($matches[3][$i]);

			//$size = strlen($matches[4][$i]);

			$file = GO_Base_Fs_File::tempFile($filename);
			$file->putContents(convert_uudecode($matches[4][$i]));
			
			$a = GO_Email_Model_MessageAttachment::model()->createFromTempFile($file);
			$a->number = "UU".$i;
			$this->addAttachment($a);
			
//			$this->attachments["UU".$i]=array(
//				"url"=>GO::url('core/downloadTempFile', array('path'=>$file->stripTempPath())),
//				'name'=>$filename,
//				"content_id"=>"",
//				"mime"=>$file->mimeType(),
//				'disposition'=>'attachment',
//				'encoding'=>'',
//				"tmp_file"=>$file->path(),
//				"index"=>-1,				
//				'size'=>$file->size(),
//				'human_size'=>$file->humanSize(),
//				"extension"=>$file->extension()
//			);
    }
		
    //remove it from the body.
    $body = preg_replace($regex, "", $body);
	}

//	/** 
//	 * Return the URL to display the attachment
//	 * 
//	 * @param array $attachment See getAttachments
//	 * @return string 
//	 */
//	protected function getAttachmentUrl($attachment) {
//		return '';
//	}
//	
	private function _convertRecipientArray($r){
		$new = array();
		foreach($r as $email=>$personal)
			$new[]=array('email'=>$email, 'personal'=>(string) $personal);
		
		return $new;
	}
	
	public function getZipOfAttachmentsUrl(){
		return '';
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
	public function toOutputArray($html=true, $recipientsAsString=false, $noMaxBodySize=false) {

		$from = $this->from->getAddresses();		

		$response['notification'] = $this->disposition_notification_to;
		
		//seen is expensive because it can't be recovered from cache.
		// We'll use the grid to check if a message was seen or not.
		//$response['seen']=$this->seen;
				
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
		$response['zip_of_attachments_url']=$this->getZipOfAttachmentsUrl();

		$response['inlineAttachments'] = array();
		
		if($html) {
			$response['htmlbody'] = $this->getHtmlBody(false,$noMaxBodySize);
			$response['subject'] = htmlspecialchars($this->subject,ENT_COMPAT,'UTF-8');
		} else {
			$response['plainbody'] =$this->getPlainBody(false,$noMaxBodySize);
			$response['subject'] = $this->subject;
		}

		$response['body_truncated'] = $this->bodyIsTruncated();
		
		$response['smime_signed'] = isset($this->content_type_attributes['smime-type']) && $this->content_type_attributes['smime-type']=='signed-data';	

		$attachments = $this->getAttachments();

		foreach($attachments as $att){
			$replaceCount = 0;
			
			$a = $att->getAttributes();				
			
			//add unique token for detecting precense of inline attachment when we submit the message in handleFormInput
			$a['token']=md5($a['tmp_file']);
			$a['url'] .= '&amp;token='.$a['token'];				

			
			if ($html && !empty($a['content_id']))
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
	
	/**
	 * Returns true iff message body has exceeded maximum size.
	 * @return boolean
	 */
	public function bodyIsTruncated() {
		return $this->_bodyTruncated;
	}
	
}