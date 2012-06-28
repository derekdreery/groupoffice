<?php
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
	
	public function createFromTempFile(GO_Base_Fs_File $file){
		//		$a['name'] = $file->name();
		$a = new GO_Email_Model_MessageAttachment();
		$a->name=$file->name();
		$a->mime= $file->mimeType();
		
		$a->setTempFile($file);
		$a->size=$file->size();
		
		return $a;
	}
	
	public function getTempFile(){
		return isset($this->_tmp_file) ? $this->_tmp_file : false;
	}
	
	public function setTempFile(GO_Base_Fs_File $file){
		if(!$file->isTempFile())
			throw new Exception("File $file->name is not a temporary file");
		
		$this->_tmp_file = $file->stripTempPath();
	}
	
	public function hasTempFile(){
		return isset($this->_tmp_file);
	}
	
	public function getUrl(){
		if($this->getExtension()=='dat'){			
			return GO::url('email/message/tnefAttachmentFromTempFile', array('tmp_file'=>$this->getTempFile()));
		}else
		{		
			return GO::url('core/downloadTempFile', array('path'=>$this->getTempFile()));		
		}		
	}
	
	public function isInline(){
		return !empty($this->content_id) || $this->disposition=='inline';
	}
	
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
	
	public function getHumanSize(){
		return GO_Base_Util_Number::formatSize($this->size);
	}
	
	public function getExtension(){
		$file = new GO_Base_Fs_File($this->name);
		return $file->extension();
	}
}