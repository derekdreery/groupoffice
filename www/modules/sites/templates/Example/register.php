<?php
require('header.php');
?>
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						

								<h1>Register at Intermesh Group-Office</h1>								
								<p>Fill out this form and click on 'Ok' to register. The fields marked with a * are required.</p>
								
								<?php 
									GO_Base_Html_Form::renderBegin('sites/user/register',true);
								//	echo GO_Base_Html_Error::getError();
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "First Name",
										"name" => "first_name",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "Middle Name",
										"name" => "middle_name",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Last Name",
										"name" => "last_name",
										"value" => ''
									));
									
									GO_Base_Html_Radio::render(array(
										"required" => true,
										"label" => "Gender",
										"name" => "gender",
										"value" => 'male',
										"options" => array(
												array("label"=>"Male","value"=>"male"),
												array("label"=>"Female","value"=>"female")
										)
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Email",
										"name" => "email",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Address",
										"name" => "address",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Number of house",
										"name" => "address_nr",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "ZIP/Postal code",
										"name" => "zip",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "City",
										"name" => "city",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "State/Province",
										"name" => "state",
										"value" => ''
									));
									
									GO_Base_Html_Select::render(array(
										"required" => true,
										'label' => 'Country',
										'value' => 'NL',
										'name' => "country",
										'options' => GO::language()->getCountries()
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "Phone",
										"name" => "phone",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "Mobile",
										"name" => "mobile",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "Company",
										"name" => "company",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "Department",
										"name" => "department",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "Function",
										"name" => "function",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Username",
										"name" => "username",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Password",
										"name" => "password",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Confirm",
										"name" => "passwordConfirm",
										"value" => ''
									));
									
									
									
									GO_Base_Html_Submit::render(array(
										"label" => "",
										"name" => "submit",
										"value" => 'OK',
										"renderContainer" => false
									));
									
									GO_Base_Html_Reset::render(array(
										"label" => "",
										"name" => "reset",
										"value" => 'Reset',
										"renderContainer" => false
									));
									
									GO_Base_Html_Reset::render(array(
										"label" => "",
										"name" => "cancel",
										"value" => 'Cancel',
										"renderContainer" => false
									));

									GO_Base_Html_Form::renderEnd();
								?>
							</div>
						</div>

					</div>


					<div class="subkader-right">
						<h1>Secure login</h1>
						<p>SSL secured connection verified by Equifax Secure Inc. </p>
					</div>

<?php
require('footer.php');
?>