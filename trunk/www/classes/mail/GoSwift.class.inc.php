<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package go.mail
 * @uses Swift
 * @since Group-Office 3.0
 */

/**
 * Require all mail classes that are used by this class
 */

require_once($GO_CONFIG->class_path."html2text.class.inc");
require_once $GO_CONFIG->class_path.'mail/RFC822.class.inc';
require_once $GO_CONFIG->class_path.'mail/mimeDecode.class.inc';
require_once $GO_CONFIG->class_path.'mail/swift/lib/swift_required.php';
require_once($GO_CONFIG->class_path.'mail/swift/lib/classes/Swift/Mime/ContentEncoder/RawContentEncoder.php');
require_once $GO_CONFIG->class_path.'mail/smtp_restrict.class.inc.php';

//HOWTO DO THIS WITH 4?
//You change the cache class using this call...
//Swift_CacheFactory::setClassName("Swift_Cache_Disk");

//Then you set up the disk cache to write to a writable folder...
//Swift_Cache_Disk::setSavePath($GO_CONFIG->tmpdir);


/**
 * This class can be used to send an e-mail. It extends the 3rd party Swift class.
 * Swift documentation can be found here:
 *
 * {@link http://www.swiftmailer.org/wikidocs/"target="_blank Documentation}
 *
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id$
 * @copyright Copyright Intermesh
 * @license GNU General Public License
 * @package go.mail
 * @uses Swift
 * @since Group-Office 3.0
 */

class GoSwift extends Swift_Mailer{

	/**
	 * The Swift message to send
	 *
	 * @var Swift_Message
	 */
	public $message;
	
	
	/**
	 * Array with failed e-mail addresses after send.
	 * @var Array
	 */
	public $failed_recipients = array();

	/**
	 * The raw message data to store in the sent folder or for a linked message
	 *
	 * @var String
	 * @access private
	 */
	private $data;

	/**
	 * When repied to a message it flags the orignal message with ANSWERED
	 *
	 * @var String
	 * @access private
	 */
	private $reply_mailbox;

	/**
	 * When repied to a message it flags the orignal message with ANSWERED
	 *
	 * @var String
	 * @access private
	 */
	private $reply_uid;
	
	/**
	 * When repied to a message it flags the orignal message with ANSWERED
	 *
	 * @var String
	 * @access private
	 */
	private $draft_uid;
	

	/**
	 * The account record as an array. see table em_accounts
	 *
	 * @var array
	 */
	public $account;
	
	private $smtp_host;



	/**
	 * Constructor. This will create a Swift instance and a Swift message public property.
	 *
	 * @param String $email_to The reciepents in a comma separated string
	 * @param String $subject The subject of the e-mail
	 * @param Int $account_id The account id from the em_accounts table. Used for smtp server and sent items
	 * @param String $priority The priority can be 3 for normal, 1 for high or 5 for low.
	 */
	function __construct($email_to, $subject, $account_id=0, $alias_id=0, $priority = '3', $plain_text_body=null, $transport=null)
	{
		global $GO_CONFIG, $GO_MODULES;


		if($account_id>0 || $alias_id>0)
		{
			require_once ($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
			$email = new email();

			$this->account = $email->get_account($account_id, $alias_id);
			
			if(!isset($transport))
			{
				$encryption = empty($this->account['smtp_encryption']) ? null : $this->account['smtp_encryption'];
				$transport = new Swift_SmtpTransport($this->account['smtp_host'], $this->account['smtp_port'], $encryption);
				if(!empty($this->account['smtp_username']))
				{
					$transport->setUsername($this->account['smtp_username'])
						->setPassword($this->account['smtp_password'])
						;
				}
			}
		}else
		{
			$this->account=false;		

			if(isset($transport))
			{
				$encryption = empty($GO_CONFIG->smtp_encryption) ? null : $GO_CONFIG->smtp_encryption;
				$transport=new Swift_SmtpTransport($GO_CONFIG->smtp_server, $GO_CONFIG->smtp_port, $encryption);
				if(!empty($GO_CONFIG->smtp_username))
				{
					$transport->setUsername($GO_CONFIG->smtp_username)
						->setPassword($GO_CONFIG->smtp_password);
				}
			}
		}

		$this->smtp_host=$transport->getHost();
		parent::__construct($transport);


		//$this->message =  $pgp ? Swift_Pgp_Message::newInstance($subject, $plain_text_body) :  Swift_Message::newInstance($subject, $plain_text_body);
		$this->message = Swift_Message::newInstance($subject, $plain_text_body);
		$this->message->setPriority($priority);
		
		if($this->account)
		{
			$this->message->setFrom(array($this->account['email']=>$this->account['name']));
		}
		
		
		$headers = $this->message->getHeaders();		
		
		$headers->addTextHeader("X-Mailer", "Group-Office ".$GO_CONFIG->version);
		$headers->addTextHeader("X-MimeOLE", "Produced by Group-Office ".$GO_CONFIG->version);

		$this->set_to($email_to);
	}
	
	function set_to($email_to)
	{
		$RFC822 = new RFC822();
		$to_addresses = $RFC822->parse_address_list($email_to);
		
		$recipients=array();
		foreach($to_addresses as $address)
		{
			$recipients[$address['email']]=$address['personal'];			
		}
		
		$this->message->setTo($recipients);	
	}
	
	function &get_message(){
		return $this->message;
	}

	/**
	 * Sets the message body
	 *
	 * @param String $body The message body in HTML or text
	 * @param String $type Can be html or text
	 */

	function set_body($body,$type='html')
	{
		global $GO_CONFIG;
		
		if($type=='html')
		{
			//replace URL's with anchor tags
			//$body = preg_replace('/[\s\n;]{1}http(s?):\/\/([^\b<\n]*)/', "<a href=\"http$1://$2\">http$1://$2</a>", $body);
		}		
		//add body
		$this->message->setBody($body, 'text/'.$type);
	
		if($type=='html')
		{
			//add text version of the HTML body
			$htmlToText = new Html2Text ($body);
			$this->message->addPart($htmlToText->get_text(), 'text/plain','UTF-8');
		}
	}

	/**
	 * If this message is a reply to another message then you must supply the UID and the mailbox
	 * of the original message. The account id must be passed to the constructor for this to work.
	 *
	 * @param String $reply_uid
	 * @param String $reply_mailbox
	 */

	function set_reply_to($reply_uid, $reply_mailbox)
	{
		$this->reply_uid=$reply_uid;
		$this->reply_mailbox=$reply_mailbox;
	}
	
	function set_draft($draft_uid)
	{
		$this->draft_uid=$draft_uid;
	}
	
	function set_from($email_from,$name_from)
	{
		$this->message->setFrom(array($email_from=>$name_from));
	}

	/**
	 * Sends the email.
	 *
	 * @param String $email_from The from e-mail address. If you don't supply this then you must supply the account_id to the constructor
	 * @param String $name_from The from name. If you don't supply this then you must supply the account_id to the constructor
	 * @param boolean $batch If you set this to true it will use the Swift batchSend method. See the swift docs.
	 * @throws Swift_ConnectionException If sending fails for any reason.
	 * @return int The number of successful recipients
	 */
	

	function sendmail($batch=false)
	{
		$smtp_restrict = new smtp_restrict();
		
		if(!$smtp_restrict->is_allowed($this->smtp_host))
		{
			global $lang;
			$msg = sprintf($lang['common']['max_emails_reached'], $this->smtp_host, $smtp_restrict->hosts[gethostbyname($this->smtp_host)]);
			throw new Exception($msg);
		}		
		
		
		$this->failed_recipients=array();
		if($batch)
		{
			//$send_success = parent::batchSend($this->message,$this->recipients, new Swift_Address($email_from, $name_from));
			
			/*$this->batch =& new Swift_BatchMailer($this);	
			$this->batch->setSleepTime(10);
			$this->batch->setMaxTries(2);
			$this->batch->setMaxSuccessiveFailures(10);		
			$send_success=$this->batch->send($this->message, $this->recipients, new Swift_Address($email_from, $name_from));*/
			
			$send_success = parent::batchSend($this->message,$this->failed_recipients);
			
		}else
		{
			$send_success = parent::send($this->message,$this->failed_recipients);
		}		
		
		if($send_success && $this->account && $this->account['type']=='imap' && !empty($this->account['sent']))
		{
			global $GO_CONFIG, $GO_MODULES;				
			
			require_once ($GO_CONFIG->class_path."mail/imap.class.inc");
			require_once ($GO_MODULES->modules['email']['class_path']."cached_imap.class.inc.php");
			$imap = new cached_imap();		

			$mailbox = empty($this->draft_uid) ? 'INBOX' : $this->account['drafts'];
				
			if ($imap->open($this->account,$mailbox)) {									
				
				$this->data=$this->message->toString();

				if ($imap->append_message($imap->utf7_imap_encode($this->account['sent']), $this->data,"\\Seen"))
				{
					if (!empty($this->reply_uid) && !empty($this->reply_mailbox))
					{
						$uid_arr = array($this->reply_uid);
						$imap->set_message_flag($this->reply_mailbox, $uid_arr, "\\Answered");
						
						$cached_message['folder_id']=$imap->folder['id'];
						$cached_message['uid']=$this->reply_uid;
						$cached_message['answered']='1';
						$imap->update_cached_message($cached_message);						
					}
					
					if(!empty($this->draft_uid))
					{
						$imap->delete(array($this->draft_uid));
					}					
					
					$imap->close();
				}
			}
		}
		return $send_success;
	}
	
	function implodeSwiftAddressArray($swiftArr)
	{
		$fromArr=array();
		foreach($swiftArr as $address=>$personal)
		{
			if(empty($personal))
			{
				$fromArr[]=$address;
			}else
			{
				$fromArr[]=RFC822::write_address($address, $personal);
			}
		}
		
		return implode(',',$fromArr);
	}
	
	/**
	 * Links the message to items in Group-Office. Must be called after send()
	 *
	 * @param array $links Format Array(Array(link_id=>1, link_type=>1));
	 * @return void
	 */
	function link_to(Array $links)
	{
		global $GO_CONFIG, $GO_LINKS;


		$link_message['path']='email/'.date('mY').'/sent_'.time().'.eml';
			
		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();
		$fs->mkdir_recursive($GO_CONFIG->file_storage_path.dirname($link_message['path']));

		if(empty($this->data))
		{
			$this->data = $this->message->toString();		
		}

		$fp = fopen($GO_CONFIG->file_storage_path.$link_message['path'],"w+");
		fputs ($fp, $this->data, strlen($this->data));
		fclose($fp);

		$email = new email();
		
		require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
		$search = new search();
		

		$link_message['from']=$this->implodeSwiftAddressArray($this->message->getFrom());
		$link_message['to']=$this->implodeSwiftAddressArray($this->message->getTo());
		$link_message['subject']=$this->message->getSubject();
		$link_message['ctime']=$link_message['time']=time();
		
		

		foreach($links as $link)
		{
			$sr = $search->get_search_result($link['link_id'], $link['link_type']);					
			if($sr)
			{
				$link_message['acl_read']=$sr['acl_read'];
				$link_message['acl_write']=$sr['acl_write'];		
				$link_message['link_id'] = $email->link_message($link_message);
			
				$GO_LINKS->add_link(
				$link['link_id'],
				$link['link_type'],
				$link_message['link_id'],
				9);
			}
		}
	}
}


class GoSwiftImport extends GoSwift{
	
	var $body='';

	public function __construct($mime, $add_body=true, $alias_id=0, $transport=null)
	{
		
		$RFC822 = new RFC822();
		
		
		$params['include_bodies'] = true;
		$params['decode_bodies'] = true;
		$params['decode_headers'] = true;
		$params['input'] = $mime;
	
	
		$structure = Mail_mimeDecode::decode($params);
		

		$subject = isset($structure->headers['subject']) ? $structure->headers['subject'] : '';
		
		if(isset($structure->headers['disposition-notification-to']))
		{
			//$mail->ConfirmReadingTo = $structure->headers['disposition-notification-to'];
		}
		
		$to = isset($structure->headers['to']) && strpos($structure->headers['to'],'undisclosed')===false ? $structure->headers['to'] : '';
		$cc = isset($structure->headers['cc']) && strpos($structure->headers['cc'],'undisclosed')===false ? $structure->headers['cc'] : '';
		$bcc = isset($structure->headers['bcc']) && strpos($structure->headers['bcc'],'undisclosed')===false ? $structure->headers['bcc'] : '';

		
		parent::__construct($to, $subject,0,$alias_id,'3',null, $transport);
		
		
		if(isset($structure->headers['from']) )
		{
			$addresses=$RFC822->parse_address_list($structure->headers['from']);
			if(isset($addresses[0]))
			{
				$this->set_from($addresses[0]['email'], $addresses[0]['personal']);								
			}
		}
		
		
		$this->get_parts($structure);
		
		if($add_body)
			$this->set_body($this->body);
		
	}

	private function get_parts($structure, $part_number_prefix='')
	{
		global $GO_CONFIG, $GO_MODULES;

		if (isset($structure->parts))
		{
			//$part_number=0;
			foreach ($structure->parts as $part_number=>$part) {

				//text part and no attachment so it must be the body
				if($structure->ctype_primary=='multipart' && $structure->ctype_secondary=='alternative' &&
				$part->ctype_primary == 'text' && $part->ctype_secondary=='plain')
				{
					continue;
				}


				if ($part->ctype_primary == 'text' && (!isset($part->disposition) || $part->disposition != 'attachment') && empty($part->d_parameters['filename']))
				{
					if (eregi('plain', $part->ctype_secondary))
					{
						$content_part = nl2br($part->body);
					}else
					{
						$content_part = $part->body;
					}
					$this->body .= $content_part;
				}elseif($part->ctype_primary=='multipart')
				{
					
				}else
				{
					//attachment 
					
					$dir=$GO_CONFIG->tmpdir.'attachments/';
					
					if(!is_dir($dir))
						mkdir($dir, 0755, true);
						
					//unset($part->body);						
					//var_dump($part);
					//exit();
						
					$tmp_file = $dir.$part->d_parameters['filename'];					
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
						$img->setContentType(File::get_mime($mime_type));
						$img->setId($content_id);					
						$this->message->embed($img);						
					}else
					{						
						$attachment = Swift_Attachment::fromPath($tmp_file,File::get_mime($tmp_file)); 
						$swift->message->attach($attachment);
					}
				}

				//$part_number++;
				if(isset($part->parts))
				{
					$this->get_parts($part, $part_number_prefix.$part_number.'.');
				}

			}
		}elseif(isset($structure->body))
		{
			//convert text to html
			if (eregi('plain', $structure->ctype_secondary))
			{
				$text_part = nl2br($structure->body);
			}else
			{
				$text_part = $structure->body;
			}
			$this->body .= $text_part;
		}
	}
}
