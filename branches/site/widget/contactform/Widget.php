<?php
class GO_Site_Widget_Contactform_Widget extends GO_Site_Components_Widget {

	/**
	 * @var string send to
	 */
	public $receipt;
	
	/**
	 * @var string name from
	 */
	public $name="Website guest";
	
	/**
	 * @var array rendered field eg posible: (name, email)
	 */
	public $fields = array('email');
	
	public function render($return=false)
	{
		$contactForm = new GO_Site_Widget_ContactForm_ContactForm();
		$contactForm->receipt = isset($this->receipt) ? $this->receipt : GO::config()->webmaster_email;
		$contactForm->name = GO::user() ? GO::user()->name : $this->name;
		if(isset($_POST['ContactForm'])) {
			$contactForm->email=$_POST['ContactForm']['email'];
			$contactForm->message=$_POST['ContactForm']['message'];
			if($contactForm->send()) {
				echo "Send successfull"; 
				return;
			} else
				echo "Error sending message";
		}
		$this->renderForm($contactForm);
	}
	
	protected function renderForm($contactForm) {
		$form = new GO_Site_Widget_Form();
		foreach($this->fields as $fieldName) {
			echo $form->textField($contactForm, $fieldName);
			echo $form->error($contactForm, $fieldName);
		}
		echo $form->textArea($contactForm, 'message', array('rows'=>"4",'cols'=>"50"));
		echo $form->error($contactForm, 'message');
		echo $form->submitButton('Send');
		echo $form->endForm();
	}
}