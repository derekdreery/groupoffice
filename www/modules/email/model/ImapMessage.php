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
class GO_Email_Model_ImapMessage extends GO_Email_Model_ComposerMessage {
	
	/**
	 * By default the message will be marked as read when fetched.
	 * Set to true to leave it as unseen.
	 * 
	 * @var boolean 
	 */
	public $peek=true;
	
	
	/**
	 * To avoid memory problems we truncate extreme body lengths
	 * @var int 
	 */
	public $maxBodySize=56000;
	
	
//	/**
//	 * Set this to true to get temporary files when using toOutputArray() or
//	 * getAttachments. This is necessary when the output is prepared for sending 
//	 * with the composer.
//	 * 
//	 * @var boolean 
//	 */
//	public $createTempFilesForInlineAttachments=false;
	
//	/**
//	 * Set this to true to get temporary files when using toOutputArray() or
//	 * getAttachments. This is necessary when the output is prepared for sending 
//	 * with the composer.
//	 * 
//	 * @var boolean 
//	 */
//	public $createTempFilesForAttachments=false;
	
	
	public $cacheOnDestruct=false;
	
	
	private $_cache;
	
	
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
	 * Find's meesages in a given mailbox
	 * 
	 * @param GO_Email_Model_Account $account
	 * @param string $mailbox
	 * @param int $start
	 * @param int $limit
	 * @param sring $sortField See constants in GO_Base_Mail_Imap::SORT_*
	 * @param boolean $descending Sort descending
	 * @param string $query
	 * @return array
	 */
	public function find(GO_Email_Model_Account $account, $mailbox="INBOX", $start=0, $limit=50, $sortField=GO_Base_Mail_Imap::SORT_DATE , $descending=true, $query='ALL'){
		$imap = $account->openImapConnection($mailbox);
		$headersSet = $imap->get_message_headers_set($start, $limit, $sortField , $descending, $query);
		$results=array();
		foreach($headersSet as $uid=>$headers){
			$message = GO_Email_Model_ImapMessage::model()->createFromHeaders(
							$account, $mailbox, $headers);			
		
			$results[]=$message;
		}
		
		return $results;
		
	}
	
	/**
	 *
	 * @param GO_Email_Model_Account $account
	 * @param int $uid 
	 */
	public function findByUid(GO_Email_Model_Account $account, $mailbox, $uid) {

		$cacheKey='email:'.$account->id.':'.$mailbox.':'.$uid;
		
		$cachedMessage = isset($this->_cache[$cacheKey]) ? $this->_cache[$cacheKey] : GO::cache()->get($cacheKey);
		
		if($cachedMessage)
		{
			GO::debug("Returning message $cacheKey from cache");
			$cachedMessage->cacheOnDestruct=$cacheKey;
			return $cachedMessage;
		}else
		{
		
			$imapMessage = new GO_Email_Model_ImapMessage();
			$imap = $account->openImapConnection($mailbox);

			$attributes = $imap->get_message_header($uid, true);

			if (!$attributes)
				return false;

			$attributes['uid']=$uid;
			$attributes['account'] = $account;
			$attributes['mailbox'] = $mailbox;

			$imapMessage->setAttributes($attributes);

			$imapMessage->cacheOnDestruct=$cacheKey;
			
			$this->_cache[$cacheKey]=$imapMessage;
			
			return $imapMessage;
		}		
	}
	
	public function __destruct() {
		if($this->cacheOnDestruct){
			$cacheKey=$this->cacheOnDestruct;
			$this->cacheOnDestruct=false;
			GO::cache()->set($cacheKey, $this, 3600*24*2);
		}
	}
	
	
	public function createFromHeaders($account, $mailbox, $headers){
		$imapMessage = new GO_Email_Model_ImapMessage();
		
		$headers['account'] = $account;
		$headers['mailbox'] = $mailbox;

		$imapMessage->setAttributes($headers);

		return $imapMessage;
	}
	
	public function getAttributes($formatted=false){
		if(!$formatted)
			return $this->attributes;
		
		$attributes = $this->attributes;
		
		$from = $this->from->getAddress();
		$attributes['from']=$from["personal"];
		$attributes['sender']=$from["email"];
		
		foreach($this->to->getAddresses() as $email=>$personal)
			$attributes['to']=$personal.", ";
		
		
		$dayStart = mktime(0,0,0);
		//$dayEnd = mktime(0,0,0,date('m'),date('d')+1);
		
		if($this->udate<$dayStart)
			$attributes["date"]=GO_Base_Util_Date::get_timestamp($this->udate, false);
		else
			$attributes["date"]=date(GO::user()->time_format, $this->udate);		
		
		return $attributes;
	}

	/**
	 * Save the message source to a file.
	 * 
	 * @param string $path
	 * @return boolean 
	 */
	public function saveToFile($path) {
		$imap = $this->account->openImapConnection($this->mailbox);

		return $imap->save_to_file($this->uid, $path);
	}
	
	/**
	 *
	 * @return GO_Base_Mail_Imap 
	 */
	public function getImapConnection(){
		return $this->account->openImapConnection($this->mailbox);
	}
	
	private $_struct;
	
	private function _getStruct(){
		if(!isset($this->_struct)){
			
			$this->_struct = $this->getImapConnection()->get_message_structure($this->uid);
			
			if(count($this->_struct)==1) {
					$headerCt = explode('/', $this->content_type);

				if(count($headerCt)==2){
					//if there's only one part the IMAP server always seems to return the type as text/plain even though the headers say text/html
					//so use the header's content type.

					if($this->_struct[1]['subtype']=='plain'){
						$this->_struct[1]['type']=$headerCt[0];
						$this->_struct[1]['subtype']=$headerCt[1];
					}

					if(!empty($this->content_transfer_encoding) &&
						(empty($this->_struct[1]['encoding']) || $this->_struct[1]['encoding']=='7bit' || $this->_struct[1]['encoding']=='8bit')){
						$this->_struct[1]['encoding']=$this->content_transfer_encoding;
					}

					if(!empty($this->charset) && $this->_struct[1]['charset']=='us-ascii'){
						$this->_struct[1]['charset']=$this->charset;
					}
				}
			}

			//get a default charset to decode filenames of attachments that don't have
			//that value
			if(!empty($this->_struct[1]['charset']))
				$this->defaultCharset = strtolower($this->_struct[1]['charset']);
			
		}
		return $this->_struct;
	}
	
	private $_htmlBody;
	private $_plainBody;
	
	private $_plainParts;
	private $_htmlParts;
	
	private $_bodyPartNumbers;
	
	
	private function _loadBodyParts(){
		
		if(!isset($this->_bodyPartNumbers)){
			$this->_bodyPartNumbers=array();
		
			$imap = $this->getImapConnection();
			$struct = $this->_getStruct();
			
			//GO::debug($struct);

			$hasAlternative = $imap->has_alternative_body($struct);

			$this->_plainParts = $imap->find_body_parts($struct,'text', 'plain');
			$this->_htmlParts = $imap->find_body_parts($struct,'text', 'html');

			if(!$hasAlternative && count($this->_htmlParts['parts']) && count($this->_plainParts['parts'])){
				//this is not very neat but we found some text attachments as body parts. Let's correct that.

				if($this->_plainParts['parts'][0]['number']>$this->_htmlParts['parts'][0]['number']){
					$this->_plainParts=array('parts'=>array(), 'text_found'=>false);
				}else
				{
					$this->_htmlParts=array('parts'=>array(), 'text_found'=>false);
				}
			}


			for($i=0,$max=count($this->_plainParts['parts']);$i<$max;$i++)
			{				
				if(empty($this->_plainParts['parts'][$i]['charset']))
					$this->_plainParts['parts'][$i]['charset']=$this->defaultCharset;

				if($this->_plainParts['parts'][$i]['type']=='text')
					$this->_bodyPartNumbers[]=$this->_plainParts['parts'][$i]['number'];
			}
			for($i=0,$max=count($this->_htmlParts['parts']);$i<$max;$i++)
			{
				if(empty($this->_htmlParts['parts'][$i]['charset']))
					$this->_htmlParts['parts'][$i]['charset']=$this->defaultCharset;

				if($this->_htmlParts['parts'][$i]['type']=='text')
					$this->_bodyPartNumbers[]=$this->_htmlParts['parts'][$i]['number'];
			}
		}
	}
	
	/**
	 * Unset the flags when we wakeup from the cache. We can't know if the flags have been changed.
	 * When they are accessed they are fetched from the IMAP server in getSeen. 
	 * getFlag is not implemented because there was no need for it.
	 */
	public function __wakeup() {
		unset($this->seen);
		unset($this->flag);
	}
	
	protected function getSeen(){
		if(isset($this->attributes['seen'])){
			return $this->attributes['seen'];
		}else
		{			
			//when a message is retrieved from cache, we don't know if the seen flag has been changed.
			//so when this is requested we fetch it from the IMAP server.
			$imap = $this->getImapConnection();		
			$attributes = $imap->get_message_header($this->uid, true);
			$this->setAttributes($attributes);
			
			return $this->attributes['seen'];
		}
	}
	
//	private function _setSeen(){
//		
//		if(!$this->peek && empty($this->seen)){			
//			$this->seen =true;
//
//			$this->getImapConnection()->set_message_flag(array($this->uid), "\Seen");
//		}
//	}
	
	public function getHtmlBody($asText=false){		
		if(!isset($this->_htmlBody)){
			$imap = $this->getImapConnection();		
			$this->_loadBodyParts();
			
			$this->_htmlBody='';
			if($this->_htmlParts['text_found']){ //check if we found a html body
				//GO::debug($this->_htmlParts);
				foreach($this->_htmlParts['parts'] as $htmlPart){
					if($htmlPart['type']=='text'){

						if(!empty($this->_htmlBody))
							$this->_htmlBody.= '<br />';

						$this->_htmlBody .= GO_Base_Util_String::sanitizeHtml(GO_Base_Util_String::convertLinks($imap->get_message_part_decoded($this->uid, $htmlPart['number'],$htmlPart['encoding'], $htmlPart['charset'],$this->peek,$this->maxBodySize)));
					}else //if($this->isAttachment($htmlPart['number']))
					{
						$attachment =& $this->getAttachment($htmlPart['number']);
						$attachment->content_id='go-autogen-'.$htmlPart['number'];
						$this->_htmlBody .= '<img alt="'.$htmlPart['name'].'" src="cid:'.$attachment->content_id.'" style="display:block;margin:10px 0;" />';
					}
//					else
//					{
//						GO::debug("Missing from attachments: ".$htmlPart['number']);	
//					}
				}
				//$this->_htmlBody = GO_Base_Util_String::sanitizeHtml($this->_htmlBody);			
			}

			if(empty($this->_htmlBody) && !$asText){
				$this->_htmlBody = $this->getPlainBody(true);			
			}
		}else
		{
//			$this->_setSeen();
		}
		
		if($asText){
			$htmlToText = new  GO_Base_Util_Html2Text($this->_htmlBody);
			return $htmlToText->get_text();
		}
		
		return $this->_htmlBody;
	}
	
	public function getPlainBody($asHtml=false){

		$inlineImages=array();
		
		if(!isset($this->_plainBody)){
			$imap = $this->getImapConnection();		
			$this->_loadBodyParts();

			$this->_plainBody='';
			if($this->_plainParts['text_found']){ //check if we found a plain body

				foreach($this->_plainParts['parts'] as $plainPart){
					if($plainPart['type']=='text'){

						if(!empty($this->_plainBody))
							$this->_plainBody.= "\n";

						$this->_plainBody .= $imap->get_message_part_decoded($this->uid, $plainPart['number'],$plainPart['encoding'], $plainPart['charset'],$this->peek, $this->maxBodySize);
					}else
					{
						if($asHtml){
							//we have to put in this tag and replace it after we convert the text to html. Otherwise this html get's convert into htmlspecialchars.
							$this->_plainBody.='{inline_'.count($inlineImages).'}';
							
							$attachment =& $this->getAttachment($plainPart['number']);
							$attachment->content_id='go-autogen-'.$plainPart['number'];
							$inlineImages[]='<img alt="'.$plainPart['name'].'" src="cid:'.$attachment->content_id.'" style="display:block;margin:10px 0;" />';
						}
					}
				}			
			}
		}else
		{
			foreach($this->_plainParts['parts'] as $plainPart){
				if($plainPart['type']!='text'){					
					if($asHtml){					
						$attachment =& $this->getAttachment($plainPart['number']);
						$attachment->content_id='go-autogen-'.$plainPart['number'];
						$inlineImages[]='<img alt="'.$plainPart['name'].'" src="cid:'.$attachment->content_id.'" style="display:block;margin:10px 0;" />';
					}
				}
			}
		}
		
		$this->_plainBody = GO_Base_Util_String::normalizeCrlf($this->_plainBody);
		
		$this->extractUuencodedAttachments($this->_plainBody);
		
		if($asHtml){
			$body = $this->_plainBody;			
			$body = GO_Base_Util_String::text_to_html($body);
			
			for($i=0,$max=count($inlineImages);$i<$max;$i++){
				$body=str_replace('{inline_'.$i.'}', $inlineImages[$i], $body);
			}
			
			return $body;
		}else
		{
			if(empty($this->_plainBody)){
				return $this->getHtmlBody(true);
			}else
			{				
				return $this->_plainBody;
			}
		}
	}
	
	public function createTempFilesForAttachments($inlineOnly=false){
		$atts = $this->getAttachments();
		
		foreach($atts as $a){
			if(!$inlineOnly || $a->isInline()){
				$a->createTempFile();
			}
		}
	}
	
	
	private $_imapAttachmentsLoaded=false;
	
	/**
	 *
	 * @return \GO_Email_Model_ImapMessageAttachment [] 
	 */
	public function &getAttachments() {
		if(!$this->_imapAttachmentsLoaded){			
			
			$this->_imapAttachmentsLoaded=true;
			
			$imap = $this->getImapConnection();
			$this->_loadBodyParts();
			
			$parts = $imap->find_message_attachments($this->_getStruct(), $this->_bodyPartNumbers);
			//$parts = $imap->find_message_attachments($this->_getStruct());
			
			foreach ($parts as $part) {
				//ignore applefile's
				if($part['subtype']=='applefile')
					continue;
				
			//var_dump($part);
				
				$a = new GO_Email_Model_ImapMessageAttachment();
				$a->setImapParams($this->account, $this->mailbox, $this->uid);
				
				if (empty($part['name']) || $part['name'] == 'false') {
					if (!empty($part['subject'])) {
						$a->name = GO_Base_Fs_File::stripInvalidChars(GO_Base_Mail_Utils::mimeHeaderDecode($part['subject'])) . '.eml';
					} elseif ($part['type'] == 'message') {
						$a->name = isset($part['description']) ? GO_Base_Fs_File::stripInvalidChars($part['description']) . '.eml' : 'message.eml';
					} elseif ($part['subtype'] == 'calendar') {
						$a->name = GO::t('event','email') . '.ics';
					} else {
						if ($part['type'] == 'text') {
							$a->name = $part['subtype'] . '.txt';
						} else {
							$a->name = $part['type'] . '-' . $part['subtype'];
						}
					}
				} else {
					$a->name = $imap->mime_header_decode($part['name']);
				}
				
				$a->disposition = isset($part['disposition']) ? $part['disposition'] : '';
				$a->number = $part['number'];
				$a->content_id='';
				if (!empty($part["id"])) {
					//when an image has an id it belongs somewhere in the text we gathered above so replace the
					//source id with the correct link to display the image.

					$tmp_id = $part["id"];
					if (strpos($tmp_id,'>')) {
						$tmp_id = substr($part["id"], 1,-1);
					}
					$id = $tmp_id;
					$a->content_id=$id;
				}
				
//				$f = new GO_Base_Fs_File($a->name);
//				if(($this->createTempFilesForInlineAttachments && (!empty($a->content_id) || $a->disposition=='inline')) || ($this->createTempFilesForAttachments && empty($a->content_id))){
//					$tmpFile = new GO_Base_Fs_File($this->_getTempDir().$a->name);				
//					if(!$tmpFile->exists())
//						$imap->save_to_file($this->uid, $tmpFile->path(),  $part['number'], $part['encoding'], true);
//					
//					$a->setTempFile($tmpFile);
//				}
							
				$a->mime=$part['type'] . '/' . $part['subtype'];
				
				$a->index=count($this->attachments);
				$a->size=$part['size'];
				$a->encoding = $part['encoding'];
				
				$this->addAttachment($a);
			}			
		}	
		
		return $this->attachments;
	}
	
	
	public function getZipOfAttachmentsUrl(){
//		return GO::config()->host.'modules/email/'.
//		'zip_attachments.php?account_id='.$this->account->id.
//		'&mailbox='.urlencode($this->mailbox).
//		'&uid='.$this->uid.'&filename='.urlencode($this->subject);
//		
		$params = array(
					"account_id"=>$this->account->id,
					"mailbox"=>$this->mailbox,
					"uid"=>$this->uid					
			);
		
		return GO::url('email/message/zipAllAttachments', $params);
	}
//	
//	protected function getAttachmentUrl($attachment) {
//		
//		if(!empty($attachment['tmp_file']))
//			return GO::url('core/downloadTempFile', array('path'=>$attachment['tmp_file']));
//		
////		$mime = explode('/',$attachment['mime']);
////		
////		return  GO::config()->host."modules/email/attachment.php?".
////			"account_id=".$this->account->id.
////			"&amp;mailbox=".urlencode($this->mailbox).
////			"&amp;uid=".$this->uid.
////			"&amp;imap_id=".$attachment["number"].
////			"&amp;encoding=".$attachment["encoding"].
////			"&amp;type=".$mime[0].
////			"&amp;subtype=".$mime[1].
////			"&amp;filename=".urlencode($attachment["name"]);
//		
//		
//		$params = array(
//				"account_id"=>$this->account->id,
//				"mailbox"=>$this->mailbox,
//				"uid"=>$this->uid,
//				"number"=>$attachment['number'],				
//				"encoding"=>$attachment['encoding'],				
//				"filename"=>$attachment['name']
//		);
//		
//		return GO::url('email/message/attachment', $params);
//	}
//	
	
	
	
	
	public function getSource(){
		
	}
	
	/**
	 * Get the VCALENDAR object as SabreDav vobject component
	 * 
	 * @return Sabre_VObject_Component 
	 */
	public function getInvitationVcalendar(){

		
		$attachments = $this->getAttachments();
			
		foreach($attachments as $attachment){			
			if($attachment->isVcalendar()){
				$data = $this->getImapConnection()->get_message_part_decoded($this->uid, $attachment->number, $attachment->encoding);
				
				$vcalendar = GO_Base_VObject_Reader::read($data);
				if($vcalendar && isset($vcalendar->vevent[0]))
					return $vcalendar;
			}
		}
		return false;
	}
}