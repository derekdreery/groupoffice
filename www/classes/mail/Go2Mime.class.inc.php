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
 */

/**
 * Require all mail classes that are used by this class
 */
require_once $GO_CONFIG->class_path.'mail/RFC822.class.inc';
require_once $GO_CONFIG->class_path.'mail/swift/lib/Swift.php';
require_once $GO_CONFIG->class_path.'mail/swift/lib/Swift/Connection/SMTP.php';
require_once($GO_CONFIG->class_path."html2text.class.inc");
require_once($GO_CONFIG->class_path."mail/mimeDecode.class.inc");
require_once($GO_CONFIG->class_path.'filesystem.class.inc');


/**
 * This class is used to convert mime objects to an array and vice versa
 *
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id$
 * @copyright Copyright Intermesh
 * @license Affero General Public License
 * @package go.mail
 * @uses Swift
 * @since Group-Office 3.0
 */


class Go2Mime
{
	var $body='';
	var $attachments=array();
	var $inline_attachments=array();
	var $notification=false;
	
	public function __construct()
	{
		
	}
	
	public function set_body($body)
	{
		$this->body=$body;
	}
	
	public function set_attachments($attachments)
	{
		$this->attachments=$attachments;
	}
	
	public function set_notification($email)
	{
		$this->notification=$email;
	}
	
	public function set_inline_attachments($attachments)
	{
		$this->inline_attachments=$attachments;
	}
	
	public function build_mime()
	{
		global $GO_CONFIG;
		
		$message =& new Swift_Message('', '');
		if($this->notification)
		{
			$message->requestReadReceipt($this->notification);
		}
		
		//var_dump($this->inline_attachments);
		foreach($this->inline_attachments as $inline_attachment)
		{
			if(isset($inline_attachment['data']))
			{
				$src_id = $message->attach(
						new Swift_Message_EmbeddedFile(
							$inline_attachment['data'],
							$inline_attachment['filename'],
							$inline_attachment['content_type']
						)
					);
			}else
			{				
				$img =& new Swift_Message_Image(new Swift_File($inline_attachment['tmp_file']));
				$src_id = $message->attach($img);
			}
			$this->body = str_replace($inline_attachment['url'], $src_id, $this->body);
			
			//Fix for IE because it makes relative URL's absolute.
			$this->body = str_replace(substr($GO_CONFIG->full_url,0,-strlen($GO_CONFIG->host)).'cid:', 'cid:', $this->body);		
		}
		
		$body =& new Swift_Message_Part($this->body, "text/html", null, "UTF-8");
		$message->attach($body);
		
		return $message->build()->readFull();		
	}
	
	
	public function mime2GO($mime, $inline_attachments_url='', $create_tmp_attachments=false, $part_number=''){
		
		$this->replacements = array();
		$this->inline_attachments_url=$inline_attachments_url;
		
		
		$params['include_bodies'] = true;
		$params['decode_bodies'] = true;
		$params['decode_headers'] = true;
		$params['input'] = $mime;

		$structure = Mail_mimeDecode::decode($params);
		
		if($part_number!='')
		{		
			$parts_arr = explode('.',$part_number);
			for($i=0;$i<count($parts_arr);$i++)
			{
				$structure = $structure->parts[$parts_arr[$i]];
			}
		}
				
		
		//var_dump($structure);
		
		
		$this->response['notification'] = isset($structure->headers['disposition-notification-to']) ? true : false;
		$this->response['subject']= empty($structure->headers['subject']) ? '' : $structure->headers['subject'];
		
		$this->response['from'] = isset($structure->headers['from']) ? htmlspecialchars($structure->headers['from']) : $_SESSION['GO_SESSION']['email'];
		$this->response['to'] = isset($structure->headers['to']) ? htmlspecialchars($structure->headers['to']) : '';
		$this->response['cc'] = isset($structure->headers['cc']) ? htmlspecialchars($structure->headers['cc']) : '';
		$this->response['bcc'] = isset($structure->headers['bcc']) ? htmlspecialchars($structure->headers['bcc']) : '';

		$this->response['full_from']=$this->response['from'];

		$this->response['priority']=3;


		if(isset($structure->headers['date']))		
			$this->response['date']=date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], strtotime($structure->headers['date']));
		else
			$this->response['date']=time();
			
		$this->response['size']=strlen($params['input']);

		$this->response['attachments']=array();
		$this->response['inline_attachments']=array();
		$this->response['body']='';
		
		
		
		$this->get_parts($structure, '', $create_tmp_attachments);
		
		for ($i=0;$i<count($this->replacements);$i++)
		{
			$this->response['body'] = str_replace('cid:'.$this->replacements[$i]['id'], $this->replacements[$i]['url'], $this->response['body']);
		}
		
		return $this->response;
	}
	
	private function get_parts($structure, $part_number_prefix='', $create_tmp_attachments=false)
	{
		global $GO_CONFIG;

		
		if (isset($structure->parts))
		{
			$part_number=0;
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
					if(isset($part->ctype_parameters['charset']) && strtoupper($part->ctype_parameters['charset'])!='UTF-8')
					{
						$content_part = iconv($part->ctype_parameters['charset'], 'UTF-8', $content_part);
					}
					$this->response['body'] .= $content_part;
				}
				//store attachements in the attachments array
				if (!empty($part->d_parameters['filename']) && empty($part->headers['content-id']))
				{
					$mime_attachment['index']=count($this->response['attachments']);
					$mime_attachment['size'] = isset($part->body) ? strlen($part->body) : 0;
					$mime_attachment['name'] = $part->d_parameters['filename'];
					$mime_attachment['extension'] = File::get_extension($part->d_parameters['filename']);
					$mime_attachment['mime'] = $part->ctype_primary.'/'.$part->ctype_secondary;
					$mime_attachment['transfer'] = $part->headers['content-transfer-encoding'];
					$mime_attachment['number'] = $part_number_prefix.$part_number;
					$mime_attachment['disposition'] = isset($part->disposition) ? $part->disposition : '';
					$mime_attachment['id'] = isset($part->headers['content-id']) ? $part->headers['content-id'] : '';
					
					if($create_tmp_attachments)
					{
						$mime_attachment['tmp_file']=$GO_CONFIG->tmpdir.'attachments/'.$part->d_parameters['filename'];
						filesystem::mkdir_recursive(dirname($mime_attachment['tmp_file']));
						
						file_put_contents($mime_attachment['tmp_file'], $part->body);
					}
					
					$this->response['attachments'][] = $mime_attachment;

				}elseif(isset($part->headers['content-id']))
				{
					
					$content_id = trim($part->headers['content-id']);
					if ($content_id != '')
					{
						if (strpos($content_id,'>'))
						{
							$content_id = substr($part->headers['content-id'], 1,strlen($part->headers['content-id'])-2);
						}
						$content_id = $content_id;

						//$path = 'mimepart.php?path='.urlencode($path).'&part_number='.$part_number;
						//replace inline images identified by a content id with the url to display the part by Group-Office
						$url_replacement['id'] = $content_id;
						$url_replacement['part_number'] = $part_number;
						$url_replacement['url'] = String::add_params_to_url($this->inline_attachments_url, 'part_number='.$part_number);
						
						if($create_tmp_attachments)
						{
							$url_replacement['tmp_file']=$GO_CONFIG->tmpdir.'attachments/'.$part->d_parameters['filename'];
							filesystem::mkdir_recursive(dirname($url_replacement['tmp_file']));
							
							file_put_contents($url_replacement['tmp_file'], $part->body);
						}
						
						$this->replacements[] = $url_replacement;
						$this->response['inline_attachments'][]=$url_replacement;
					}
				}
								
				if(isset($part->parts))
				{
					$this->get_parts($part, $part_number_prefix.$part_number.'.',$create_tmp_attachments);
				}
				$part_number++;				
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
			if(isset($structure->ctype_parameters['charset']) && strtoupper($structure->ctype_parameters['charset'])!='UTF-8')
			{
				$text_part = iconv($structure->ctype_parameters['charset'], 'UTF-8', $text_part);
			}
			$this->response['body'] .= $text_part;
		}
	}
}
?>