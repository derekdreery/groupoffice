<?php

class GO_Email_Model_ComposerMessage extends GO_Email_Model_Message {
	

	public function addAttachment(GO_Base_Fs_File $file) {
		
		$file->move(GO::config()->getTempFolder());
		
		$a['name'] = $file->name();
		$a['number'] = "";
		$a['content_id'] = "";
		$a['mime'] = $file->mimeType();
		//TODO composer should use tmp_file too.
//		$a['tmp_name'] = $file->path();
		$a['tmp_file'] = $file->stripTempPath();
		$a['index'] = count($this->_attachments);
		$a['size'] = $file->size();
		$a['human_size'] = $file->humanSize();
		$a['extension'] = $file->extension();
		$a['encoding'] = '';
		$a['disposition'] = '';
		$a['url'] = GO::url('core/downloadTempFile',array('path'=>$file->name()));
		$this->attachments[] = $a;
	}
	
	public function addTo($email){
		if(!isset($this->attributes['to'])){
			$this->attributes['to'] = new GO_Base_Mail_EmailRecipients();
		}
		
		$this->attributes['to']->addRecipient($email);
	}
	
	public function addCc($email){
		if(!isset($this->attributes['cc'])){
			$this->attributes['cc'] = new GO_Base_Mail_EmailRecipients();
		}
		
		$this->attributes['cc']->addRecipient($email);
	}
	
	public function addBcc($email){
		if(!isset($this->attributes['bcc'])){
			$this->attributes['bcc'] = new GO_Base_Mail_EmailRecipients();
		}
		
		$this->attributes['bcc']->addRecipient($email);
	}

	public function getHtmlBody() {
		return '';
	}
	
	public function getPlainBody() {
		return '';
	}
	
	public function getSource() {
	
	}
}