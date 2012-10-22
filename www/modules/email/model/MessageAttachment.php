<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: GO_Email_Model_LinkedEmail.php 7607 2011-09-01 15:38:01Z <<USERNAME>> $
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */

/**
 * E-mail message attachment model
 * 
 * @var string $name Filename of the attachment 
 * @var string $number Unique structure number. Eg. 1.1
 * @var string $content_id If it's an inline image it can have a content ID. The body can inlude an image tag with this content ID.
 * @var string $mime MIME content type
 * @var int $index Index number of the attachment
 * @var int $size Size in bytes
 * @var string $encoding Content encoding
 * @var string $disposition Can be attachment or inline.
 */
class GO_Email_Model_MessageAttachment extends GO_Base_Model{
	public $name="";
	public $number=0;
	public $content_id="";
	public $mime="application/octet-stream";
	public $index=0;
	public $size=0;
	public $encoding="";
	public $disposition="";
	
	public $_tmp_file;
	
	/**
	 * Returns the static model of the specified AR class.
	 * Every child of this class must override it.
	 * 
	 * @return GO_Email_Model_MessageAttachment the static model class
	 */
	public static function model($className=__CLASS__)
	{		
		return parent::model($className);
	}
	
	/**
	 * Create a new instance for an GO_Email_Model_ComposerMessage for example.
	 * 
	 * @param GO_Base_Fs_File $file The temporary file
	 * @return \GO_Email_Model_MessageAttachment 
	 */
	public function createFromTempFile(GO_Base_Fs_File $file){
		//		$a['name'] = $file->name();
		$a = new GO_Email_Model_MessageAttachment();
		$a->name=$file->name();
		$a->mime= $file->mimeType();
		
		$a->setTempFile($file);
		$a->size=$file->size();
		
		return $a;
	}
	/**
	 * Get the temporary file for this attachment
	 * 
	 * @return string Relative to GO::config()->tmp_dir 
	 */
	public function getTempFile(){
		return isset($this->_tmp_file) ? $this->_tmp_file : false;
	}
	
	/**
	 * Set the temporary file 
	 * 
	 * @param GO_Base_Fs_File $file
	 * @throws Exception 
	 */
	public function setTempFile(GO_Base_Fs_File $file){
		if(!$file->isTempFile())
			throw new Exception("File $file->name is not a temporary file");
		
		$this->_tmp_file = $file->stripTempPath();
	}
	
	/**
	 * Check if the tempfile is available
	 * 
	 * @return boolean 
	 */
	public function hasTempFile(){
		if(!isset($this->_tmp_file))
			return false;
		
		if($this->_tmp_file instanceof GO_Base_Fs_File)
			return $this->_tmp_file->exists();
		else
			return false;
	}
	
	
	/**
	 * Get the download URL
	 * @return string 
	 */
	public function getUrl(){
		if($this->getExtension()=='dat'){			
			return GO::url('email/message/tnefAttachmentFromTempFile', array('tmp_file'=>$this->getTempFile()));
		}else
		{		
			return GO::url('core/downloadTempFile', array('path'=>$this->getTempFile()));		
		}		
	}
	
	/**
	 * Check if the attachment is inline
	 * @return boolean 
	 */
	public function isInline(){
		
		//these don't work because you won't get temporary files when sending a message.
		//return !empty($this->content_id) && $this->disposition!='attachment';
		//return $this->disposition=='inline';
		
		return !empty($this->content_id) || $this->disposition=='inline';
	}
	
	/**
	 * Get all attributes. Useful to output to the client through JSON.
	 * 
	 * @return array 
	 */
	public function getAttributes(){
		return array(
				"url"=>$this->getUrl(),
				"name"=>$this->name,
				"number"=>$this->number,
				"content_id"=>$this->content_id,
				"mime"=>$this->mime,
				"tmp_file"=>$this->getTempFile(),
				"index"=>$this->index,
				"size"=>$this->size,
				"human_size"=>$this->getHumanSize(),
				"extension"=>$this->getExtension(),
				"encoding"=>$this->encoding,
				"disposition"=>$this->disposition,
		);
	}	
	
	/**
	 * Get the size formatted. eg. 128 kb
	 * @return string 
	 */
	public function getHumanSize(){
		return GO_Base_Util_Number::formatSize($this->size);
	}
	
	/**
	 * Get the file extension
	 * 
	 * @return string
	 */
	public function getExtension(){
		$file = new GO_Base_Fs_File($this->name);
		return $file->extension();
	}
	
	
	public function isVcalendar(){
		return $this->mime=='text/calendar' || $this->getExtension() == 'ics';
	}
}