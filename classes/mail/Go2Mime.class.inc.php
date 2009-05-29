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
require_once $GO_CONFIG->class_path.'mail/swift/lib/swift_required.php';
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

		$message = Swift_Message::newInstance();
		if($this->notification)
		{
			$message->setReadReceiptTo($this->notification);
		}

		//var_dump($this->inline_attachments);
		foreach($this->inline_attachments as $inline_attachment)
		{
			if(isset($inline_attachment['data']))
			{
				$img = Swift_EmbeddedFile::newInstance($inline_attachment['data'],
				$inline_attachment['filename'],
				$inline_attachment['content_type']);								
				
			}else
			{
				$img = Swift_EmbeddedFile::fromPath($inline_attachment['tmp_file']);
				$img->setContentType(File::get_mime($inline_attachment['tmp_file']));				
			}
			$src_id = $message->embed($img);
				
			//Browsers reformat URL's so a pattern match
			//$this->body = str_replace($inline_attachment['url'], $src_id, $this->body);
			$just_filename = utf8_basename($inline_attachment['url']);
			$this->body = preg_replace('/="[^"]*'.preg_quote($just_filename).'"/', '="'.$src_id.'"', $this->body);
		}

		$message->setBody($this->body, "text/html");

		return $message->toString();
	}


	public function mime2GO($mime, $inline_attachments_url='', $create_tmp_attachments=false, $part_number=''){

		global $lang, $GO_LANGUAGE;
		
		require_once($GO_LANGUAGE->get_language_file('email'));
		
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
		
		$RFC822 = new RFC822();

		$from = isset($structure->headers['from']) ? $structure->headers['from'] : '';
		$addresses = $RFC822->parse_address_list($from);

		$this->response['notification'] = isset($structure->headers['disposition-notification-to']) ? true : false;
		$this->response['subject']= empty($structure->headers['subject']) ? '' : $structure->headers['subject'];
		$this->response['from'] = isset($addresses[0]['personal']) ? htmlspecialchars($addresses[0]['personal'],ENT_QUOTES, 'UTF-8') : '';
		$this->response['sender']= isset($addresses[0]['email']) ? htmlspecialchars($addresses[0]['email'],ENT_QUOTES, 'UTF-8') : '';
		$this->response['to'] = isset($structure->headers['to']) ? $structure->headers['to'] : '';
		$this->response['cc'] = isset($structure->headers['cc']) ? $structure->headers['cc'] : '';
		$this->response['bcc'] = isset($structure->headers['bcc']) ? $structure->headers['bcc'] : '';
		
		
		if(!empty($this->response['to']))
		{
			$addresses=$RFC822->parse_address_list($this->response['to']);
			$to=array();
			foreach($addresses as $address)
			{
				$to[] = array('email'=>htmlspecialchars($address['email'], ENT_QUOTES, 'UTF-8'),
						'name'=>htmlspecialchars($address['personal'], ENT_QUOTES, 'UTF-8'));
			}
			$this->response['to']=$to;
		}else
		{
			$this->response['to']=array('email'=>'', 'name'=> $lang['email']['no_recipients']);
		}

		$cc=array();
		if(!empty($this->response['cc']))
		{
			$addresses=$RFC822->parse_address_list($this->response['cc']);
			foreach($addresses as $address)
			{
				$cc[] = array('email'=>htmlspecialchars($address['email'], ENT_QUOTES, 'UTF-8'),
						'name'=>htmlspecialchars($address['personal'], ENT_QUOTES, 'UTF-8'));
			}
		}
		$this->response['cc']=$cc;

		$bcc=array();
		if(!empty($this->response['bcc']))
		{
			$addresses=$RFC822->parse_address_list($this->response['bcc']);
			foreach($addresses as $address)
			{
				$bcc[] = array('email'=>htmlspecialchars($address['email'], ENT_QUOTES, 'UTF-8'),
						'name'=>htmlspecialchars($address['personal'], ENT_QUOTES, 'UTF-8'));
			}
		}
		$this->response['bcc']=$bcc;

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
						$content_part = String::convert_html($part->body);
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
					$mime_attachment['human_size'] = Number::format_size($mime_attachment['size']);
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
						$url_replacement['part_number'] = $part_number_prefix.$part_number;
						$url_replacement['url'] = String::add_params_to_url($this->inline_attachments_url, 'part_number='.$url_replacement['part_number']);

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