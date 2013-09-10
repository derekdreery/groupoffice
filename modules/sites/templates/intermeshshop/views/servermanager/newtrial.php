<?php echo $this->getPage()->content; ?>

<?php GO_Base_Html_Form::renderBegin('servermanager/site/newtrial', 'newtrial', true); ?>

<?php echo $this->notifications->render('newtrial'); ?>

<?php

GO_Base_Html_Input::render(array(
		"label" => "Domain name",
		"name" => "name",
		"required" => true
));

GO_Base_Html_Input::render(array(
		"label" => "Title",
		"name" => "title",
		"required" => true
));


GO_Base_Html_Input::render(array(
		"label" => "First name",
		"name" => "first_name",
		"required" => true
));

GO_Base_Html_Input::render(array(
		"label" => "Middle name",
		"name" => "middle_name",
		"required" => true
));

GO_Base_Html_Input::render(array(
		"label" => "Last name",
		"name" => "last_name",
		"required" => true
));

GO_Base_Html_Input::render(array(
		"label" => "E-mail",
		"name" => "email",
		"required" => true
));

GO_Base_Html_Submit::render(array(
		"name" => "submit",
		"value" => "Create trial!"
));
?>

<?php GO_Base_Html_Form::renderEnd(); ?>