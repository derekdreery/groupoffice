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
require_once $GO_CONFIG->class_path.'mail/swift/lib/Swift.php';
require_once $GO_CONFIG->class_path.'mail/swift/lib/Swift/Connection/SMTP.php';
require_once $GO_CONFIG->class_path.'mail/swift/lib/Swift/Plugin/FileEmbedder.php';
require_once $GO_CONFIG->class_path.'mail/swift/lib/Swift/Cache/Disk.php';

require_once $GO_CONFIG->class_path.'mail/smtp_restrict.class.inc.php';

//You change the cache class using this call...
Swift_CacheFactory::setClassName("Swift_Cache_Disk");

//Then you set up the disk cache to write to a writable folder...
Swift_Cache_Disk::setSavePath($GO_CONFIG->tmpdir);


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

class GoSwift extends Swift{

	/**
	 * The Swift message to send
	 *
	 * @var Swift_Message
	 */
	public $message;

	/**
	 * The Swift recipients list
	 *
	 * @var Swift_RecipientList
	 */

	public $recipients;

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
	function __construct($email_to, $subject, $account_id=0, $priority = '3', $plain_text_body=null)
	{
		global $GO_CONFIG, $GO_MODULES;


		if($account_id>0)
		{
			require_once ($GO_MODULES->modules['email']['class_path']."email.class.inc");
			$email = new email();

			$this->account = $email->get_account($account_id);

			$this->smtp_host=$this->account['smtp_host'];
			
			$smtp_connection=new Swift_Connection_SMTP($this->account['smtp_host'], $this->account['smtp_port'], $this->account['smtp_encryption']);
			if(!empty($this->account['smtp_username']))
			{
				$smtp_connection->setUsername($this->account['smtp_username']);
				$smtp_connection->setPassword($this->account['smtp_password']);
			}
		}else
		{
			$this->smtp_host=$GO_CONFIG->smtp_server;
			$smtp_connection=new Swift_Connection_SMTP($GO_CONFIG->smtp_server, $GO_CONFIG->smtp_port);
			if(!empty($GO_CONFIG->smtp_username))
			{
				$smtp_connection->setUsername($GO_CONFIG->smtp_username);
				$smtp_connection->setPassword($GO_CONFIG->smtp_password);
			}
		}
		parent::__construct($smtp_connection);


		$this->message =& new Swift_Message($subject, $plain_text_body);
		$this->message->setPriority($priority);

		$this->message->headers->set("X-Mailer", "Group-Office ".$GO_CONFIG->version);
		$this->message->headers->set("X-MimeOLE", "Produced by Group-Office ".$GO_CONFIG->version);


		
		$this->set_to($email_to);

	}
	
	function set_to($email_to)
	{
		//Start a new list
		$this->recipients =& new Swift_RecipientList();

		$RFC822 = new RFC822();
		$to_addresses = $RFC822->parse_address_list($email_to);
		
		foreach($to_addresses as $address)
		{
			$this->recipients->addTo($address['email'], $address['personal']);
		}	
	}
	
	function set_recipients($recipientList)
	{
		$this->recipients=$recipientList;
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
		$this->message->attach(new Swift_Message_Part($body, 'text/'.$type, null, 'UTF-8'));

		if($type=='html')
		{
			//add text version of the HTML body
			$htmlToText = new Html2Text ($body);
			$this->message->attach(new Swift_Message_Part($htmlToText->get_text(), 'text/plain', null, 'UTF-8'));
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
	
	
	function get_mime($email_from=null, $name_from=null)
	{
		$name_from=!empty($name_from) ? $name_from : $this->account['name'];
		$email_from=!empty($email_from) ? $email_from : $this->account['email'];		
		
		$this->message->setFrom(new Swift_Address($email_from, $name_from));
		$this->message->setCc($this->recipients->getCc());
		$this->message->setBcc($this->recipients->getBcc());
		$this->message->setTo($this->recipients->getTo());		
		
		$data = $this->message->build();
		return $data->readFull();
		
	}
	
	function get_data($email_from=null, $name_from=null)
	{
		$name_from=!empty($name_from) ? $name_from : $this->account['name'];
		$email_from=!empty($email_from) ? $email_from : $this->account['email'];
		
		$this->message->setFrom(new Swift_Address($email_from, $name_from));
		
		$this->message->setCc($this->recipients->getCc());
		$this->message->setBcc($this->recipients->getBcc());
		$this->message->setTo($this->recipients->getTo());
		
		$data = $this->message->build();
		return $data->readFull();

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

	function sendmail($email_from=null, $name_from=null, $batch=false)
	{
		
		$smtp_restrict = new smtp_restrict();
		
		if(!$smtp_restrict->is_allowed($this->smtp_host))
		{
			global $lang;
			$msg = sprintf($lang['common']['max_emails_reached'], $this->smtp_host, $smtp_restrict->hosts[gethostbyname($this->smtp_host)]);
			throw new Exception($msg);
		}
		
		
		$name_from=!empty($name_from) ? $name_from : $this->account['name'];
		$email_from=!empty($email_from) ? $email_from : $this->account['email'];

		

		if($batch)
		{
			//$send_success = parent::batchSend($this->message,$this->recipients, new Swift_Address($email_from, $name_from));
			
			$this->batch =& new Swift_BatchMailer($this);	
			$this->batch->setSleepTime(10);
			$this->batch->setMaxTries(2);
			$this->batch->setMaxSuccessiveFailures(10);		
			$send_success=$this->batch->send($this->message, $this->recipients, new Swift_Address($email_from, $name_from));
			
		}else
		{
			$send_success = parent::send($this->message,$this->recipients, new Swift_Address($email_from, $name_from));
		}

		//for appending to send and link
		$this->message->setFrom(new Swift_Address($email_from, $name_from));
		
		
		$this->message->setCc($this->recipients->getCc());
		$this->message->setBcc($this->recipients->getBcc());
		$this->message->setTo($this->recipients->getTo());		
		
		
		if($send_success && $this->account && $this->account['type']=='imap' && !empty($this->account['sent']))
		{
			global $GO_CONFIG;
				
			require_once ($GO_CONFIG->class_path."mail/imap.class.inc");
			$imap = new imap();				
				
			if ($imap->open(
			$this->account['host'],
			$this->account['type'],
			$this->account['port'],
			$this->account['username'],
			$this->account['password'],
				'INBOX',
			0,
			$this->account['use_ssl'],
			$this->account['novalidate_cert'])) {
									
				$this->data = $this->message->build();
				$this->data = $this->data->readFull();

				if ($imap->append_message($this->account['sent'], $this->data,"\\Seen"))
				{
					if (!empty($this->reply_uid) && !empty($this->reply_mailbox))
					{
						$uid_arr = array($this->reply_uid);
						$imap->set_message_flag($this->reply_mailbox, $uid_arr, "\\Answered");
					}
					$imap->close();

				}
			}
		}
		return $send_success;
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
			$this->data = $this->message->build();
			$this->data = $this->data->readFull();
		}


		$fp = fopen($GO_CONFIG->file_storage_path.$link_message['path'],"w+");
		fputs ($fp, $this->data, strlen($this->data));
		fclose($fp);

		$email = new email();

		$link_message['from']=$this->message->headers->get('from');
		
		$to=$this->recipients->getTo();
		foreach ($to as $key => $value)
    {
      $to[$key] = $value->build();
    }

		
		$link_message['to']=implode(',',$to);
		$link_message['subject']=$this->message->headers->get('subject');
		$link_message['ctime']=$link_message['time']=time();
		$link_message['link_id'] = $email->link_message($link_message);

		foreach($links as $link)
		{
			$GO_LINKS->add_link(
			$link['link_id'],
			$link['link_type'],
			$link_message['link_id'],
			9);
		}
	}
}


class GoSwiftImport extends GoSwift{
	
	var $body='';

	public function __construct($mime, $add_body=true)
	{
		
		$RFC822 = new RFC822();
		
		
		$params['include_bodies'] = true;
		$params['decode_bodies'] = true;
		$params['decode_headers'] = true;
		$params['input'] = $mime;
	
	
		$structure = Mail_mimeDecode::decode($params);
		
		$from_email='';
		$from_name='';
	
		if(isset($structure->headers['from']) )
		{
			$addresses=$RFC822->parse_address_list($structure->headers['from']);
			if(isset($addresses[0]))
			{
				$from_email=$addresses[0]['email'];
				$from_name=$addresses[0]['personal'];				
			}
		}
		
		$subject = isset($structure->headers['subject']) ? $structure->headers['subject'] : '';
		
		if(isset($structure->headers['disposition-notification-to']))
		{
			//$mail->ConfirmReadingTo = $structure->headers['disposition-notification-to'];
		}
		
		$to = isset($structure->headers['to']) ? $structure->headers['to'] : '';
		$cc = isset($structure->headers['cc']) ? $structure->headers['cc'] : '';
		$bcc = isset($structure->headers['bcc']) ? $structure->headers['bcc'] : '';
		
		parent::__construct($to, $subject);
		
		//TODO add cc
		
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
						$img =& new Swift_Message_Image(new Swift_File($tmp_file),utf8_basename($tmp_file), $mime_type,$content_id);
						$this->message->attach($img);					
					}else
					{
						$file =& new Swift_File($tmp_file);
						$attachment =& new Swift_Message_Attachment($file,utf8_basename($tmp_file), $mime_type);
						$this->message->attach($attachment);
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
