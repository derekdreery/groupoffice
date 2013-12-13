<?php

class GO_Site_Widget_Contactform_ContactForm extends GO_Base_Model {
	/**
	 * @var string email from input
	 */
	public $email;
	
	/**
	 * @var string name input 
	 */
	public $name;
	
	/**
	 * @var string message input
	 */
	public $message;
	
	/**
	 * @var string email to input
	 */
	public $receipt;
	
	/**
	 * Returns the validation rules of the model.
	 * @return array validation rules
	 */
	public function validate()
	{
		if(empty($this->name))
			$this->setValidationError('name', sprintf(GO::t('attributeRequired'),'name'));
		if(empty($this->email))
			$this->setValidationError('email', sprintf(GO::t('attributeRequired'),'email'));
		if(empty($this->message))
			$this->setValidationError('message', sprintf(GO::t('attributeRequired'),'message'));
		if(!GO_Base_Util_Validate::email($this->email))
			$this->setValidationError('email', GO::t('invalidEmailError'));
			
		return parent::validate();
	}
	
	/**
	 * send an email to webmaster_email in config
	 * @return boolean true when successfull
	 */
	public function send(){
		
		if(!$this->validate())
			return false;
		$message = GO_Base_Mail_Message::newInstance();
		$message->setSubject("Groupoffice contact form");
		$message->setBody($this->message);
		$message->addFrom($this->email, $this->name);
		$message->addTo($this->receipt);
		return GO_Base_Mail_Mailer::newGoInstance()->send($message);
	}
}
