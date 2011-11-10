<?php
class GO_Email_Model_SavedMessage extends GO_Email_Model_Message {
	
	private $_loadedBody;
	private $_attachments=array();
	private $_tmpDir;
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Email_Model_SavedMessage
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function getFromMimeFile($path){
		$m = new GO_Email_Model_SavedMessage();
		$m->_loadMimeFromPath($path);
		return $m;
	}

	
	public function _loadMimeFromPath($path){
		$file = new GO_Base_Fs_File(GO::config()->file_storage_path.$path);
	
		$mimeData = $file->contents();
		
		$attributes['path']=$path;
		
		$decoder = new GO_Base_Mail_MimeDecode($mimeData);
		$structure = $decoder->decode(array(
				'include_bodies' => true,
				'decode_headers' => true,
				'decode_bodies' => true,
						));

		if (!$structure)
			throw new Exception("Could not decode mime data:\n\n $mimeData");

		$attributes=array();
		
		if (!empty($structure->headers['subject'])) {
			$attributes['subject'] = $structure->headers['subject'];
		}

		if (isset($structure->headers['disposition-notification-to'])) {
			//$mail->ConfirmReadingTo = $structure->headers['disposition-notification-to'];
		}

		$attributes['to']=isset($structure->headers['to']) && strpos($structure->headers['to'], 'undisclosed') === false ? $structure->headers['to'] : '';
		$attributes['cc'] = isset($structure->headers['cc']) && strpos($structure->headers['cc'], 'undisclosed') === false ? $structure->headers['cc'] : '';
		$attributes['bcc'] = isset($structure->headers['bcc']) && strpos($structure->headers['bcc'], 'undisclosed') === false ? $structure->headers['bcc'] : '';		
		$attributes['from'] = isset($structure->headers['from']) ? $structure->headers['from'] : '';

		
		$attributes['date']=isset($structure->headers['date']) ? $structure->headers['date'] : date('c');		
		$attributes['udate']=strtotime($attributes['date']);
		$attributes['size']=strlen($mimeData);
		
		$this->setAttributes($attributes);
		
		$this->_tmpDir=GO::config()->tmpdir.'saved_messages/'.str_replace('/','_',$path).'/';
		if(!is_dir($this->_tmpDir))
			mkdir($this->_tmpDir, 0755, true);

		$this->_getParts($structure);
		
		//TODO make style rules valid in the container.
		$this->_loadedBody=GO_Base_Util_String::sanitizeHtml($this->_loadedBody);
	}
	
	public function getAttachments(){
		return $this->_attachments;
	}
	
	public function getHtmlBody(){
		return $this->_loadedBody;
	}
	
	public function getTextBody(){
		
	}
	
	protected function getAttachmentUrl($attachment) {
		return GO::url('core/downloadTempFile', array('path',$attachment['tmp_file']));
	}

	private function _getParts($structure, $part_number_prefix='') {
		if (isset($structure->parts)) {
			//$part_number=0;
			foreach ($structure->parts as $part_number => $part) {

				//text part and no attachment so it must be the body
				if ($structure->ctype_primary == 'multipart' && $structure->ctype_secondary == 'alternative' &&
								$part->ctype_primary == 'text' && $part->ctype_secondary == 'plain') {
					//check if html part is there					
					if ($this->_hasHtmlPart($structure)) {
						continue;
					}
				}


				if ($part->ctype_primary == 'text' && ($part->ctype_secondary == 'plain' || $part->ctype_secondary == 'html') && (!isset($part->disposition) || $part->disposition != 'attachment') && empty($part->d_parameters['filename'])) {
					if (stripos($part->ctype_secondary, 'plain') !== false) {
						$content_part = nl2br($part->body);
					} else {
						$content_part = $part->body;
					}
					$this->_loadedBody .= $content_part;
				} elseif ($part->ctype_primary == 'multipart') {
					
				} else {
					//attachment

					if (!empty($part->ctype_parameters['name'])) {
						$filename = $part->ctype_parameters['name'];
					} elseif (!empty($part->d_parameters['filename'])) {
						$filename = $part->d_parameters['filename'];
					} elseif (!empty($part->d_parameters['filename*'])) {
						$filename = $part->d_parameters['filename*'];
					} else {
						$filename = uniqid(time());
					}

					$mime_type = $part->ctype_primary . '/' . $part->ctype_secondary;

					if (isset($part->headers['content-id'])) {
						$content_id = trim($part->headers['content-id']);
						if (strpos($content_id, '>')) {
							$content_id = substr($part->headers['content-id'], 1, strlen($part->headers['content-id']) - 2);
						}
					} else {
						$content_id='';						
					}
					
					if(!empty($part->body)){
						$tmp_file = $this->_tmpDir.$filename;
						file_put_contents($tmp_file, $part->body);
					}else
					{
						$tmp_file=false;
					}
					
					$f = new GO_Base_Fs_File($filename);
					
					
					$a['name']=$filename;
					$a['number']=$part_number_prefix.$part_number;
					$a['content_id']=$content_id;
					$a['mime']=$mime_type;
					$a['tmp_file']=str_replace(GO::config()->tmpdir,'',$tmp_file);
					$a['index']=count($this->_attachments);
					$a['size']=isset($part->body) ? strlen($part->body) : 0;
					$a['human_size']= GO_Base_Util_Number::formatSize($a['size']);
					$a['extension']=  $f->extension();
					$a['encoding'] = isset($part->headers['content-transfer-encoding']) ? $part->headers['content-transfer-encoding'] : '';
					$a['disposition'] = isset($part->disposition) ? $part->disposition : '';
					$a['url']=$this->getAttachmentUrl($a);
					
					$this->_attachments[$a['number']]=$a;
					
				}

				//$part_number++;
				if (isset($part->parts)) {
					$this->_getParts($part, $part_number_prefix . $part_number . '.');
				}
			}
		} elseif (isset($structure->body)) {
			//convert text to html
			if (stripos($structure->ctype_secondary, 'plain') !== false) {
				$text_part = nl2br($structure->body);
			} else {
				$text_part = $structure->body;
			}
			$this->_loadedBody .= $text_part;
		}
	}

	private function _hasHtmlPart($structure) {
		if (isset($structure->parts)) {
			foreach ($structure->parts as $part) {
				if ($part->ctype_primary == 'text' && $part->ctype_secondary == 'html')
					return true;
				else if ($this->_hasHtmlPart($part)) {
					return true;
				}
			}
		}
		return false;
	}

}