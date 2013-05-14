<?php
class GO_Site_Widget_Contactform_Widget extends GO_Site_Components_Widget {

	public $receipt;						//send to email
	public $emailFieldOptions=array();		//html attributes for email field
	public $messageFieldOptions=array();	//html attributes for message field
	public $fieldSeparator = '';			//html between input fields
	public $submitButtonText = 'Send';		//text in submit button
	
	protected $formModel;
	protected $form;
	
	public function init() {
		$this->formModel = new GO_Site_Widget_ContactForm_ContactForm();
		$this->formModel->receipt = isset($this->receipt) ? $this->receipt : GO::config()->webmaster_email;
		$this->formModel->name = GO::user() ? GO::user()->name : 'Website Guest';

		$this->form = new GO_Site_Widget_Form();
	}
	
	public function render()
	{
		$result = '';
		if(isset($_POST['ContactForm']) ) {
			$this->formModel->email=$_POST['ContactForm']['email'];
			$this->formModel->message=$_POST['ContactForm']['message'];
			if($this->formModel->send()) {
				return "Send successfull"; 
			} else
				$result .= "Error sending message";
		}

		
		$result .= $this->form->beginForm();

		$result .= $this->form->textField($this->formModel, 'email', $this->emailFieldOptions);
		$result .= $this->form->error($this->formModel, 'email');
		$result .= $this->fieldSeparator;

		$result .= $this->form->textArea($this->formModel, 'message', $this->messageFieldOptions);
		$result .= $this->form->error($this->formModel, 'message');
		$result .= $this->fieldSeparator;

		$result .=$this->form->submitButton($this->submitButtonText);
		$result .= $this->form->endForm();

		return $result;
	}
}