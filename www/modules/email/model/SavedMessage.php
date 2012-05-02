<?php
class GO_Email_Model_SavedMessage extends GO_Email_Model_ComposerMessage {
	
	private $_loadedBody;
	
	private $_tmpDir;
	/**
	 * Returns the static model of the specified AR class.
	 * Every child of this class must override it.
	 * 
	 * @return GO_Email_Model_SavedMessage the static model class
	 */
	public static function model($className=__CLASS__)
	{		
		return parent::model($className);
	}

	/**
	 * Get a model instance loaded from  MIME data string.
	 * 
	 * @param string $mimeData MIME data string
	 * @return GO_Email_Model_SavedMessage 
	 */
	public function createFromMimeData($mimeData) {
		$m = new GO_Email_Model_SavedMessage();		
		$m->setMimeData($mimeData);
		return $m;
	}

	/**
	 * Reads a MIME file and creates a SavedMessage model from it.
	 * 
	 * @param string $path Where the MIME file is stored
	 * @return GO_Email_Model_SavedMessage
	 */
	public function createFromMimeFile($path, $isTempFile=false) {
		
		$fullPath = $isTempFile ? GO::config()->tmpdir.$path : GO::config()->file_storage_path.$path;
		
		$file = new GO_Base_Fs_File($fullPath);
		$mimeData = $file->contents();
		
		return $this->createFromMimeData($mimeData);
	}
	
	/**
	 * Reads MIME data and creates a SavedMessage model from it.
	 * @param string $mimeData The MIME data string.
	 * @return GO_Email_Model_SavedMessage 
	 */
	public function setMimeData($mimeData) {
	
//		if (!empty($path))
//			$attributes['path'] = $path;
		
		$decoder = new GO_Base_Mail_MimeDecode($mimeData);
		$structure = $decoder->decode(array(
				'include_bodies' => true,
				'decode_headers' => true,
				'decode_bodies' => true
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
		
		$this->_getParts($structure);
		
		//$this->_loadedBody=  GO_Base_Util_String::clean_utf8($this->_loadedBody);
		//
		//$this->_loadedBody = str_replace("\x80","€", $this->_loadedBody);
		//TODO make style rules valid in the container.
		$this->_loadedBody=GO_Base_Util_String::sanitizeHtml($this->_loadedBody);
	}
	
	private function _getTempDir(){
		$this->_tmpDir=GO::config()->tmpdir.'saved_messages/'.md5(serialize($this->attributes)).'/';
		if(!is_dir($this->_tmpDir))
			mkdir($this->_tmpDir, 0755, true);
		return $this->_tmpDir;
	}
	
	public function getHtmlBody(){
		return $this->_loadedBody;
	}
	
	public function getPlainBody() {
		return GO_Base_Util_String::html_to_text($this->_loadedBody);
	}
	
	public function getSource(){
		return '';
	}
	
	public function getZipOfAttachmentsUrl(){
		return GO::url("savemailas/linkedEmail/zipOfAttachments", array("tmpdir"=>str_replace(GO::config()->tmpdir, '', $this->_getTempDir())));
	}
	
	
	protected function getAttachmentUrl($attachment) {
		
		$file = new GO_Base_Fs_File($attachment['name']);
		
		if($file->extension()=='dat'){			
			return GO::url('email/message/tnefAttachmentFromTempFile', array('tmp_file'=>$attachment['tmp_file']));
		}else
		{		
			return GO::url('core/downloadTempFile', array('path'=>$attachment['tmp_file']));
		}
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
					
					$body = GO_Base_Util_String::clean_utf8($part->body);
					
					if (stripos($part->ctype_secondary, 'plain') !== false) {
						$content_part = nl2br($body);
					} else {
						$content_part = $body;
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
					
					$tmp_file = new GO_Base_Fs_File($this->_getTempDir().$filename);
					if(!empty($part->body)){
						$tmp_file = new GO_Base_Fs_File($this->_getTempDir().$filename);
						if(!$tmp_file->exists())
							$tmp_file->putContents($part->body);
					}else
					{
						$tmp_file=false;
					}
					
					$f = new GO_Base_Fs_File($filename);
					
					
					$a['name']=$filename;
					$a['number']=$part_number_prefix.$part_number;
					$a['content_id']=$content_id;
					$a['mime']=$mime_type;
					$a['tmp_file']=$tmp_file ? $tmp_file->stripTempPath() : false;
					$a['index']=count($this->attachments);
					$a['size']=isset($part->body) ? strlen($part->body) : 0;
					$a['human_size']= GO_Base_Util_Number::formatSize($a['size']);
					$a['extension']=  $f->extension();
					$a['encoding'] = isset($part->headers['content-transfer-encoding']) ? $part->headers['content-transfer-encoding'] : '';
					$a['disposition'] = isset($part->disposition) ? $part->disposition : '';
					$a['url']=$this->getAttachmentUrl($a);
					
					$this->attachments[$a['number']]=$a;
					
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
			$text_part = GO_Base_Util_String::clean_utf8($text_part);
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

//	protected function _getAttachedImages($mimeNode) {
//		$imageInfos = array();
//
//		if (!empty($mimeNode->ctype_primary) && $mimeNode->ctype_primary=='image') {
//			$imageInfos[] = array(
//					'filename' => $mimeNode->d_parameters['filename'],
//					'image' => $mimeNode->body
//			);
//		}
//		if (!empty($mimeNode->parts) && is_array($mimeNode->parts)) {
//			foreach ($mimeNode->parts as $part) {
//				$imageInfos = array_merge($imageInfos,$this->_getAttachedImages($part));
//			}
//		}
//		
//		return $imageInfos;
//	}
//	
//	/**
//	 * Returns information of the images, if any.
//	 * @return Array Array of elements of type array("url"=>img src tag,
//	 * "path"=>image location on server)
//	 */
//	public function getEmbeddedImages() {
//		$imagePaths = array();
////		preg_match_all('!<[\s]*img[\s][.]*src[\s]*="([^"]*)"!',$this->getHtmlBody(),$matches);
////		foreach ($matches[1] as $src) {
////			$pathArr = explode('&amp;path=',$src);
////			$imagePaths[] = urldecode($pathArr[1]);
////		}
//		
//		return $imagePaths;
//	}
//
//	/**
//	 * 
//	 */
//	public function toOutputArray($html=true) {
//		$response = parent::toOutputArray();
//		$response['inlineImages'] = $this->inlineImages;
//		return $response;
//	}
}