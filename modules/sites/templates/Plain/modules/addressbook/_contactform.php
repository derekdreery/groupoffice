<?php GO_Base_Html_Form::renderBegin('addressbook/site/addContact','addcontact',true); ?>
				
	<?php echo $this->notifications->render('addcontact'); ?>

	<?php 
		GO_Base_Html_Input::render(array(
			"name" => "first_name",
			"model"=> $this->contact,
			"required" => true
		));

		GO_Base_Html_Hidden::render(array(
			"name" => "addressbook_id",
			"model"=> $this->contact,
			"value"=> 1
		));

		GO_Base_Html_Input::render(array(
			"name" => "last_name",
			"model"=> $this->contact,
			"required" => true
		));

		GO_Base_Html_Input::render(array(
			"name" => "email",
			"model"=> $this->contact,
			"required" => true
		));

		GO_Base_Html_Input::render(array(
			"required" => true,
			"label" => "Confirm email",
			"name" => "confirm_email",
			"value" => ''
		));

		GO_Base_Html_Input::render(array(
			"required" => true,
			"label" => "Address 1",
			"name" => "address",
			"value" => $this->contact->address
		));

		GO_Base_Html_Input::render(array(
			"label" => "Address 2",
			"name" => "address_no",
			"value" => $this->contact->address_no
		));

		GO_Base_Html_Input::render(array(
			"required" => true,
			"label" => "Town",
			"name" => "city",
			"value" => $this->contact->city
		));

		GO_Base_Html_Input::render(array(
			"required" => true,
			"label" => "Area",
			"name" => "state",
			"value" => $this->contact->state
		));

		GO_Base_Html_Input::render(array(
			"required" => true,
			"label" => "Postcode",
			"name" => "zip",
			"value" => $this->contact->zip
		));

		GO_Base_Html_Select::render(array(
			"required" => true,
			'label' => 'Country',
			'value' => $this->contact->country,
			'name' => "country",
			'options' => GO::language()->getCountries()
		));

		GO_Customfields_Html_Field::render(array(
			'model'=>$this->contact,
			'name' => "col_3",
			'label' => 'Where did you hear about us'
		));

		GO_Customfields_Html_Field::render(array(
			'model'=>$this->contact,
			'name' => "col_4",
			'label' => 'What size investment do you anticipate making'
		));

		GO_Customfields_Html_Field::render(array(
			'model'=>$this->contact,
			'name' => "col_5",
			'label' => 'Are you able to invest (at least £1000) within'
		));

		GO_Customfields_Html_Field::render(array(
			'model'=>$this->contact,
			'name' => "col_6",
			'label' => 'We only send out small numbers of investment memorandum for each investment opportunity. So that we only send you relevant material please tell us about any preferences you have as to type of property / period of investment you are looking for.  Or conversely anything you will not consider'
		));

		GO_Customfields_Html_Field::render(array(
			'model'=>$this->contact,
			'name' => "col_7",
			"empty_value" => "",
			"required" => true,
			'label' => 'I have read and agree to the terms and conditions on this website'
		));
		
		GO_Base_Html_Submit::render(array(
			"name" => "submit",
			"value" => "submit"
		));
?>
	
<?php GO_Base_Html_Form::renderEnd(); ?>