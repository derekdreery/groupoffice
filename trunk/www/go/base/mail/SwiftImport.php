<?php


class GO_Base_Mail_SwiftImport extends GO_Base_Mail_Swift{

	var $body='';

	public function __construct($mime, $add_body=true, $alias_id=0, $transport=null)
	{
		$RFC822 = new GO_Base_Mail_AddressParser();

		$params['include_bodies'] = true;
		$params['decode_bodies'] = true;
		$params['decode_headers'] = true;
		$params['input'] = $mime;

		$structure = GO_Base_Mail_MimeDecode::decode($params);

		$subject = isset($structure->headers['subject']) ? $structure->headers['subject'] : '';

		if(isset($structure->headers['disposition-notification-to']))
		{
			//$mail->ConfirmReadingTo = $structure->headers['disposition-notification-to'];
		}

		$to = isset($structure->headers['to']) && strpos($structure->headers['to'],'undisclosed')===false ? $structure->headers['to'] : '';
		$cc = isset($structure->headers['cc']) && strpos($structure->headers['cc'],'undisclosed')===false ? $structure->headers['cc'] : '';
		$bcc = isset($structure->headers['bcc']) && strpos($structure->headers['bcc'],'undisclosed')===false ? $structure->headers['bcc'] : '';


		parent::__construct($to, $subject,0,$alias_id,'3',null, $transport);
		
		
		$RFC822 = new GO_Base_Mail_AddressParser();
		$cc_addresses = $RFC822->parse_address_list($cc);
		$recipients=array();
		foreach($cc_addresses as $address)
		{
			$recipients[$address['email']]=$address['personal'];
		}

		$this->message->setCc($recipients);
		
		
		$bcc_addresses = $RFC822->parse_address_list($bcc);
		$recipients=array();
		foreach($bcc_addresses as $address)
		{
			$recipients[$address['email']]=$address['personal'];
		}

		$this->message->setBcc($recipients);


		if(empty($alias_id) && isset($structure->headers['from']) )
		{
			$addresses=$RFC822->parse_address_list($structure->headers['from']);
			if(isset($addresses[0]))
			{
				$this->set_from($addresses[0]['email'], $addresses[0]['personal']);
			}
		}

		//go_debug($structure);


		$this->get_parts($structure);

		if($add_body)
			$this->set_body($this->body);

	}

	private function has_html_part($structure){
		if(isset($structure->parts)){
			foreach($structure->parts as $part){
				go_debug($part->ctype_primary.'/'.$part->ctype_secondary);
				if($part->ctype_primary == 'text' && $part->ctype_secondary=='html')
					return true;
				else if($this->has_html_part($part)){
					return true;
				}
			}
		}
		return false;
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
					//check if html part is there					
					if($this->has_html_part($structure)){						
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
					$this->body .= $content_part;
				}elseif($part->ctype_primary=='multipart')
				{

				}else
				{
					//attachment

					$dir=$GLOBALS['GO_CONFIG']->tmpdir.'attachments/';

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
						$img->setContentType(File::get_mime($mime_type));
						$img->setId($content_id);
						$this->message->embed($img);
					}else
					{
					//echo $tmp_file."\n";
						$attachment = Swift_Attachment::fromPath($tmp_file,File::get_mime($tmp_file));
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
			if (stripos( $structure->ctype_secondary,'plain')!==false)
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
