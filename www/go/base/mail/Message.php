<?php
/*
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */





//Load Swift utility class
require_once GO::config()->root_path.'go/vendor/swift/lib/classes/Swift.php';
//require_once GO::config()->root_path.'classes/mail/mimeDecode.class.inc';
//Swift must be run before GO now.
spl_autoload_unregister(array('GO', 'autoload'));	

//Start the autoloader
Swift::registerAutoload();


spl_autoload_register(array('GO', 'autoload'));	

//Load the init script to set up dependency injection
require_once GO::config()->root_path.'go/vendor/swift/lib/swift_init.php';

//make sure temp dir exists
$cacheFolder = new GO_Base_Fs_Folder(GO::config()->tmpdir);
$cacheFolder->create();

/**
 * This class is used to parse and write RFC822 compliant recipient lists
 * 
 * @package GO.base.mail
 * @version $Id: RFC822.class.inc 7536 2011-05-31 08:37:36Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @copyright Copyright Intermesh BV.
 */
class GO_Base_Mail_Message extends Swift_Message{
	
	private $_loadedBody;
	
	public function __construct($subject = null, $body = null, $contentType = null, $charset = null) {
		$ret = parent::__construct($subject, $body, $contentType, $charset);
		
		if($ret){
			$headers = $this->getHeaders();

			$headers->addTextHeader("X-Mailer", "Group-Office ".GO::config()->version);
			$headers->addTextHeader("X-MimeOLE", "Produced by Group-Office ".GO::config()->version);
		}
	}
	
	/**
   * Create a new Message.
   * @param string $subject
   * @param string $body
   * @param string $contentType
   * @param string $charset
   * @return GO_Base_Mail_Message
   */
  public static function newInstance($subject = null, $body = null,
    $contentType = null, $charset = null)
  {
    return new self($subject, $body, $contentType, $charset);
  }
	
	/**
	 * Load the message by mime data
	 * 
	 * @param String $mimeData
	 * @param array/string $replaceCallback A function that will be called with the body so you can replace tags in the body.
	 */
	public function loadMimeMessage($mimeData, $replaceCallback=false, $replaceCallbackArgs=array()){
		
		$decoder = new GO_Base_Mail_MimeDecode($mimeData);
		$structure = $decoder->decode(array(
				'include_bodies'=>true,
				'decode_headers'=>true,
				'decode_bodies'=>true,
		));
		
		if(!$structure)
			throw new Exception("Could not decode mime data:\n\n $mimeData");

		if(!empty($structure->headers['subject'])){
			$this->setSubject($structure->headers['subject']);
		}
		
		if(isset($structure->headers['disposition-notification-to']))
		{
			//$mail->ConfirmReadingTo = $structure->headers['disposition-notification-to'];
		}

		$to = isset($structure->headers['to']) && strpos($structure->headers['to'],'undisclosed')===false ? $structure->headers['to'] : '';
		$cc = isset($structure->headers['cc']) && strpos($structure->headers['cc'],'undisclosed')===false ? $structure->headers['cc'] : '';
		$bcc = isset($structure->headers['bcc']) && strpos($structure->headers['bcc'],'undisclosed')===false ? $structure->headers['bcc'] : '';
		
		$ap = new GO_Base_Mail_AddressParser();
		$to =$ap->parse_address_list($to);
		foreach($to as $toAddress){
			$this->addTo($toAddress['email'], $toAddress['personal']);
		}
		
		$cc =$ap->parse_address_list($cc);
		foreach($cc as $ccAddress){
			$this->addCc($ccAddress['email'], $ccAddress['personal']);
		}
		
		$bcc =$ap->parse_address_list($bcc);
		foreach($bcc as $ccAddress){
			$this->addBcc($bccAddress['email'], $bccAddress['personal']);
		}
		if(isset($structure->headers['from'])){
			$addresses=$ap->parse_address_list($structure->headers['from']);
			if(isset($addresses[0]))
			{
				$this->setFrom($addresses[0]['email'], $addresses[0]['personal']);
			}		
		}
		
		$this->_getParts($structure);
		
		if($replaceCallback){			
			array_unshift($replaceCallbackArgs, $this->_loadedBody);			
			
			$this->_loadedBody = call_user_func_array($replaceCallback, $replaceCallbackArgs);
		}
		
		
		
		$this->setHtmlAlternateBody($this->_loadedBody);
		
		return $this;
	}
	
	/**
	 * Set the HTML body and automatically create an alternate text body
	 * 
	 * @param String $htmlBody 
	 */
	public function setHtmlAlternateBody($htmlBody){
	
		//add body
		$this->setBody($htmlBody, 'text/html','UTF-8');
			
		//add text version of the HTML body
		$htmlToText = new GO_Base_Util_Html2Text($htmlBody);
		$this->addPart($htmlToText->get_text(), 'text/plain','UTF-8');

//		if(isset($this->text_part_body)){
//			//the body was already set so find the text version and replace it.
//			$children = (array) $this->message->getChildren();
//			foreach($children as $child){
//
//				if($child->getBody()==$this->text_part_body){
//					$this->text_part_body = $htmlToText->get_text();
//					$child->setBody($this->text_part_body);
//					break;
//				}					
//			}
//			//$this->text_body->setBody($htmlToText->get_text());
//		}else
//		{
//			$this->text_part_body =$htmlToText->get_text();
//			$this->message->addPart($this->text_part_body, 'text/plain','UTF-8');
//		}
		
	}
	
	private function _getParts($structure, $part_number_prefix='')
	{
		if (isset($structure->parts))
		{
			//$part_number=0;
			foreach ($structure->parts as $part_number=>$part) {

				//text part and no attachment so it must be the body
				if($structure->ctype_primary=='multipart' && $structure->ctype_secondary=='alternative' &&
				$part->ctype_primary == 'text' && $part->ctype_secondary=='plain')
				{
					//check if html part is there					
					if($this->_hasHtmlPart($structure)){						
						continue;
					}
				}


				if ($part->ctype_primary == 'text' && ($part->ctype_secondary=='plain' || $part->ctype_secondary=='html') && (!isset($part->disposition) || $part->disposition != 'attachment') && empty($part->d_parameters['filename']))
				{
					if (stripos($part->ctype_secondary,'plain')!==false)
					{
						$content_part = nl2br($part->body);
					}else
					{
						$content_part = $part->body;
					}
					$this->_loadedBody .= $content_part;
				}elseif($part->ctype_primary=='multipart')
				{

				}else
				{
					//attachment

					$dir=GO::config()->tmpdir.'attachments/';

					if(!is_dir($dir))
						mkdir($dir, 0755, true);

					//unset($part->body);
					//var_dump($part);
					//exit();

					if(!empty($part->ctype_parameters['name']))
					{
						$filename = $part->ctype_parameters['name'];
					}elseif(!empty($part->d_parameters['filename']) )
					{
						$filename = $part->d_parameters['filename'];
					}elseif(!empty($part->d_parameters['filename*']))
					{
						$filename=$part->d_parameters['filename*'];
					}else
					{
						$filename=uniqid(time());
					}

					$tmp_file = $dir.$filename;
					file_put_contents($tmp_file, $part->body);

					$mime_type = $part->ctype_primary.'/'.$part->ctype_secondary;

					if(isset($part->headers['content-id']))
					{
						$content_id=trim($part->headers['content-id']);
						if (strpos($content_id,'>'))
						{
							$content_id = substr($part->headers['content-id'], 1,strlen($part->headers['content-id'])-2);
						}
						$img = Swift_EmbeddedFile::fromPath($tmp_file);
						$img->setContentType($mime_type);
						$img->setId($content_id);
						$this->embed($img);
					}else
					{
					//echo $tmp_file."\n";
						$attachment = Swift_Attachment::fromPath($tmp_file,$mime_type);
						$this->attach($attachment);
					}
				}

				//$part_number++;
				if(isset($part->parts))
				{
					$this->_getParts($part, $part_number_prefix.$part_number.'.');
				}

			}
		}elseif(isset($structure->body))
		{
			//convert text to html
			if (stripos( $structure->ctype_secondary,'plain')!==false)
			{
				$text_part = nl2br($structure->body);
			}else
			{
				$text_part = $structure->body;
			}
			$this->_loadedBody .= $text_part;
		}
	}
	
	private function _hasHtmlPart($structure){
		if(isset($structure->parts)){
			foreach($structure->parts as $part){
				if($part->ctype_primary == 'text' && $part->ctype_secondary=='html')
					return true;
				else if($this->_hasHtmlPart($part)){
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Sometimes the browser changes absolute URL's into relative URL's when using
	 * wysiwyg html editors.
	 * 
	 * In outgoing messages we don't want them so we make them absolute again.
	 * 
	 * @param string $body
	 * @return type 
	 */
	private function _fixRelativeUrls($body){		
		return str_replace('href="?r=','href="'.GO::config()->full_url, $body);
	}
	
	private function _embedPastedImages($body){
		$regex = '/src="data:image\/([^;]+);([^,]+),([^"]+)/';
			
		preg_match_all($regex, $body, $allMatches,PREG_SET_ORDER);
		foreach($allMatches as $matches){
			if($matches[2]=='base64'){
				$extension = $matches[1];
				$tmpFile = GO_Base_Fs_File::tempFile('', $extension);
				$tmpFile->putContents(base64_decode($matches[3]));

				$img = Swift_EmbeddedFile::fromPath($tmpFile->path());
				$img->setContentType($tmpFile->mimeType());
				$contentId = $this->embed($img);

				$body = str_replace($matches[0],'src="'.$contentId, $body);
			}
		}
		
		return $body;
	}
	
	/**
	 * handleEmailFormInput
	 * 

	 * This method can be used in Models and Controllers. It puts the email body
	 * and inline (image) attachments from the client in the message, which can
	 * then be used for storage in the database or sending emails.
	 * 
	 * @param Array $params Must contain elements: body (string) and
	 * 
	 * inlineAttachments (string).
	 */
	public function handleEmailFormInput($params){
		
		if(!empty($params['subject']))
			$this->setSubject($params['subject']);		
		
		if(!empty($params['to'])){		
			$to = new GO_Base_Mail_EmailRecipients($params['to']);
			foreach($to->getAddresses() as $email=>$personal)
				$this->addTo($email,$personal);
		}
		if(!empty($params['cc'])){		
			$cc = new GO_Base_Mail_EmailRecipients($params['cc']);
			foreach($cc->getAddresses() as $email=>$personal)
				$this->addCc($email,$personal);
		}
		if(!empty($params['bcc'])){		
			$bcc = new GO_Base_Mail_EmailRecipients($params['bcc']);
			foreach($bcc->getAddresses() as $email=>$personal)
				$this->addBcc($email,$personal);
		}
		
		if(isset($params['alias_id'])){
			$alias = GO_Email_Model_Alias::model()->findByPk($params['alias_id']);	
			$this->setFrom($alias->email, $alias->name);
			
			if(!empty($params['notification']))
				$this->setReadReceiptTo(array($alias->email=>$alias->name));
		}
		
		if(isset($params['priority']))
			$this->setPriority ($params['priority']);
		
		
		if(isset($params['in_reply_to'])){
			$headers = $this->getHeaders();
			$headers->addTextHeader('In-Reply-To', $params['in_reply_to']);
			$headers->addTextHeader('References', $params['in_reply_to']);
		}	

		if($params['content_type']=='html'){
			
			$params['htmlbody'] = $this->_embedPastedImages($params['htmlbody']);
			
			//inlineAttachments is an array(array('url'=>'',tmp_file=>'relative/path/');
			if(!empty($params['inlineAttachments'])){
				$inlineAttachments = json_decode($params['inlineAttachments']);

				/* inline attachments must of course exist as a file, and also be used in
				 * the message body
				 */
				 if(count($inlineAttachments)){
					foreach ($inlineAttachments as $ia) {

						//$tmpFile = new GO_Base_Fs_File(GO::config()->tmpdir.$ia['tmp_file']);
						if(empty($ia->tmp_file)){
							throw new Exception("No temp file for inline attachment ".$ia->name);
						}

						$path = empty($ia->from_file_storage) ? GO::config()->tmpdir.$ia->tmp_file : GO::config()->file_storage_path.$ia->tmp_file;
						$tmpFile = new GO_Base_Fs_File($path);

						if ($tmpFile->exists()) {				
							//Different browsers reformat URL's to absolute or relative. So a pattern match on the filename.
							$filename = urlencode($tmpFile->name());
							$result = preg_match('/="([^"]*'.preg_quote($filename).'[^"]*)"/',$params['htmlbody'],$matches);
							if($result){
								$img = Swift_EmbeddedFile::fromPath($tmpFile->path());
								$img->setContentType($tmpFile->mimeType());
								$contentId = $this->embed($img);

								//$tmpFile->delete();

								$params['htmlbody'] = str_replace($matches[1], $contentId, $params['htmlbody']);
							}
						}else
						{
							throw new Exception("Error: inline attachment missing on server: ".$tmpFile->stripTempPath().".<br /><br />The temporary files folder is cleared on each login. Did you relogin?");
						}
					}
				}
			}
			$params['htmlbody']=$this->_fixRelativeUrls($params['htmlbody']);
			
			$this->setHtmlAlternateBody($params['htmlbody']);
		}else
		{
			$this->setBody($params['plainbody'], 'text/plain');
		}		
		
		if (!empty($params['attachments'])) {
			$attachments = json_decode($params['attachments']);
			foreach ($attachments as $att) {
				$path = empty($att->from_file_storage) ? GO::config()->tmpdir.$att->tmp_file : GO::config()->file_storage_path.$att->tmp_file;
				$tmpFile = new GO_Base_Fs_File($path);
				if ($tmpFile->exists()) {
					$file = Swift_Attachment::fromPath($tmpFile->path());
					$file->setContentType($tmpFile->mimeType());
					$this->attach($file);
					
					//$tmpFile->delete();
				}else
				{
					throw new Exception("Error: attachment missing on server: ".$tmpFile->stripTempPath().".<br /><br />The temporary files folder is cleared on each login. Did you relogin?");
				}
			}
		}
	}
	
}
