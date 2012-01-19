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
	public $peek=false;
	
	
	/**
	 * Set this to true to get temporary files when using toOutputArray() or
	 * getAttachments. This is necessary when the output is prepared for sending 
	 * with the composer.
	 * 
	 * @var boolean 
	 */
	public $createTempFilesForInlineAttachments=false;
	
	/**
	 * Set this to true to get temporary files when using toOutputArray() or
	 * getAttachments. This is necessary when the output is prepared for sending 
	 * with the composer.
	 * 
	 * @var boolean 
	 */
	public $createTempFilesForAttachments=false;
	
	
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

		$attributes['uid']=$uid;
		$attributes['account'] = $account;
		$attributes['mailbox'] = $mailbox;

		$imapMessage->setAttributes($attributes);

		return $imapMessage;
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

				$this->_bodyPartNumbers[]=$this->_plainParts['parts'][$i]['number'];
			}
			for($i=0,$max=count($this->_htmlParts['parts']);$i<$max;$i++)
			{
				if(empty($this->_htmlParts['parts'][$i]['charset']))
					$this->_htmlParts['parts'][$i]['charset']=$this->defaultCharset;

				$this->_bodyPartNumbers[]=$this->_htmlParts['parts'][$i]['number'];
			}
		}
	}
	
	public function getHtmlBody($asText=false){		
		if(!isset($this->_htmlBody)){
			$imap = $this->getImapConnection();		
			$this->_loadBodyParts();
			
			$this->_htmlBody='';
			if($this->_htmlParts['text_found']){ //check if we found a html body

				foreach($this->_htmlParts['parts'] as $htmlPart){
					if($htmlPart['type']=='text'){

						if(!empty($this->_htmlBody))
							$this->_htmlBody.= '<br />';

						$this->_htmlBody .= $imap->get_message_part_decoded($this->uid, $htmlPart['number'],$htmlPart['encoding'], $htmlPart['charset'],$this->peek,512000);
					}else
					{
						$attachment = $this->getAttachment($htmlPart['number']);
						
						$this->_htmlBody .= '<img alt="'.$htmlPart['name'].'" src="'.$this->getAttachmentUrl($attachment).'" style="display:block;margin:10px 0;" />';
					}
				}
				$this->_htmlBody = GO_Base_Util_String::sanitizeHtml($this->_htmlBody);			
			}

			if(empty($this->_htmlBody) && !$asText){
				$this->_htmlBody = $this->getPlainBody(true);			
			}
		}
		
		if($asText){
			$htmlToText = new  GO_Base_Util_Html2Text($this->_htmlBody);
			return $htmlToText->get_text();
		}
		
		return $this->_htmlBody;
	}
	
	public function getPlainBody($asHtml=false){
		
		if(!isset($this->_plainBody)){
			$imap = $this->getImapConnection();		
			$this->_loadBodyParts();


			$inlineImages=array();
			$this->_plainBody='';
			if($this->_plainParts['text_found']){ //check if we found a plain body

				foreach($this->_plainParts['parts'] as $plainPart){
					if($plainPart['type']=='text'){

						if(!empty($this->_plainBody))
							$this->_plainBody.= '<br />';

						$this->_plainBody .= $imap->get_message_part_decoded($this->uid, $plainPart['number'],$plainPart['encoding'], $plainPart['charset'],$this->peek,512000);
					}else
					{
						if($asHtml){
							$this->_plainBody.='{inline_'.count($inlineImages).'}';
							
							$attachment = $this->getAttachment($plainPart['number']);
							$inlineImages[]='<img alt="'.$plainPart['name'].'" src="'.$attachment['url'].'" style="display:block;margin:10px 0;" />';
						}
					}
				}			
			}
		}
		
		if($asHtml){
			$body = $this->_plainBody;
			for($i=0,$max=count($inlineImages);$i<$max;$i++){
				$body=str_replace('{inline_'.$i.'}', $inlineImages[$i], $body);
			}
			return GO_Base_Util_String::text_to_html($body);
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
	
	private function _getTempDir(){
		$this->_tmpDir=GO::config()->tmpdir.'imap_messages/'.$this->account->id.'-'.$this->mailbox.'-'.$this->uid.'/';
		if(!is_dir($this->_tmpDir))
			mkdir($this->_tmpDir, 0755, true);
		return $this->_tmpDir;
	}

	 /*
	 * @return array
	 * 
	 * $a['url']='';
	 *  $a['name']=$filename;
			$a['number']=$part_number_prefix.$part_number;
			$a['content_id']=$content_id;
			$a['mime']=$mime_type;
			$a['tmp_file']=false;
			$a['index']=count($this->attachments);
			$a['size']=isset($part->body) ? strlen($part->body) : 0;
			$a['human_size']= GO_Base_Util_Number::formatSize($a['size']);
			$a['extension']=  $f->extension();
			$a['encoding'] = isset($part->headers['content-transfer-encoding']) ? $part->headers['content-transfer-encoding'] : '';
			$a['disposition'] = isset($part->disposition) ? $part->disposition : ''; 
	 */
	
	private $_imapAttachmentsLoaded=false;
	
	public function getAttachments() {
		if(!$this->_imapAttachmentsLoaded){			
			
			$this->_imapAttachmentsLoaded=true;
			
			$imap = $this->getImapConnection();
			$this->_loadBodyParts();
			
			$parts = $imap->find_message_attachments($this->_getStruct(), $this->_bodyPartNumbers);
			
			foreach ($parts as $part) {

				if (empty($part['name']) || $part['name'] == 'false') {
					if (!empty($part['subject'])) {
						$a['name'] = GO_Base_Fs_File::stripInvalidChars(GO_Base_Mail_Utils::mimeHeaderDecode($part['subject'])) . '.eml';
					} elseif ($part['type'] == 'message') {
						$a['name'] = isset($part['description']) ? GO_Base_Fs_File::stripInvalidChars($part['description']) . '.eml' : 'message.eml';
					} elseif ($part['subtype'] == 'calendar') {
						$a['name'] = GO::t('event','email') . '.ics';
					} else {
						if ($part['type'] == 'text') {
							$a['name'] = $part['subtype'] . '.txt';
						} else {
							$a['name'] = $part['type'] . '-' . $part['subtype'];
						}
					}
				} else {
					$a['name'] = $imap->mime_header_decode($part['name']);
				}
				
				$a['number'] = $part['number'];
				$a['content_id']='';
				if (!empty($part["id"])) {
					//when an image has an id it belongs somewhere in the text we gathered above so replace the
					//source id with the correct link to display the image.

					$tmp_id = $part["id"];
					if (strpos($tmp_id,'>')) {
						$tmp_id = substr($part["id"], 1,-1);
					}
					$id = $tmp_id;
					$a['content_id']=$id;
				}
				
				$f = new GO_Base_Fs_File($a['name']);
				if(($this->createTempFilesForInlineAttachments && !empty($a['content_id'])) || ($this->createTempFilesForAttachments && empty($a['content_id']))){
					$tmpFile = new GO_Base_Fs_File($this->_getTempDir().$a['name']);				
					if(!$tmpFile->exists())
						$imap->save_to_file($this->uid, $tmpFile->path(),  $part['number'], $part['encoding'], true);
					
					$a['tmp_file']=$tmpFile->stripTempPath();
				}else
				{
					$a['tmp_file']=false;
				}
							
				$a['mime']=$part['type'] . '/' . $part['subtype'];
				
				$a['index']=count($this->attachments);
				$a['size']=$part['size'];
				$a['human_size']= GO_Base_Util_Number::formatSize($a['size']);
				$a['extension']=  $f->extension();
				$a['encoding'] = $part['encoding'];
				$a['disposition'] = $part['disposition'];
				$a['url']=$this->getAttachmentUrl($a);
				
				$this->attachments[$a['number']]=$a;
			}			
		}	
		
		return $this->attachments;
	}
	
	
	
	
	protected function getAttachmentUrl($attachment) {
		
		if(!empty($attachment['tmp_file']))
			return GO::url('core/downloadTempFile', array('path'=>$attachment['tmp_file']));
		
		$mime = explode('/',$attachment['mime']);
		
		return  GO::config()->host."modules/email/attachment.php?".
			"account_id=".$this->account->id.
			"&amp;mailbox=".urlencode($this->mailbox).
			"&amp;uid=".$this->uid.
			"&amp;imap_id=".$attachment["number"].
			"&amp;encoding=".$attachment["encoding"].
			"&amp;type=".$mime[0].
			"&amp;subtype=".$mime[1].
			"&amp;filename=".urlencode($attachment["name"]);
		
		
		if(!empty($attachment['tmp_file']))
			return GO::url('core/downloadTempFile', array('path',$attachment['tmp_file']));
		
		$mime = explode('/', $attachment['mime']);
		
		$params = array(
				"account_id"=>$this->account->id,
				"mailbox"=>$this->mailbox,
				"uid"=>$this->uid,
				"encoding"=>$attachment['encoding'],
				"mime"=>$attachment['mime'],
				"filename"=>$attachment['name'],

		);
		
		return GO::url('email/message/attachment', $params);
	}
	
	
	
	
	
	public function getSource(){
		
	}

}