<?php
require('header.php');
?>
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						

								<h1>Register at Intermesh Group-Office</h1>								
								<p>Fill out this form and click on 'Ok' to register. The fields marked with a * are required.</p>

								<form method="post">
									<p>
									<?php 
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "First Name",
										"name" => "first_name",
										"value" => ''
									));
									?>
									<br />
									<?php
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "Middle Name",
										"name" => "middle_name",
										"value" => ''
									));
									?>
									<br />
									<?php
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Last Name",
										"name" => "last_name",
										"value" => ''
									));
									?>
									<br />
									Gender
									<br />
									<?php
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Email",
										"name" => "email",
										"value" => ''
									));
									?>
									</p>
									<br />
									<p>
									<?php
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Address",
										"name" => "address",
										"value" => ''
									));
									?>
									<br />
									<?php
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Number of house",
										"name" => "address_nr",
										"value" => ''
									));
									?>
									<br />
									<?php
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "ZIP/Postal code",
										"name" => "zip",
										"value" => ''
									));
									?>
									<br />
									<?php
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "City",
										"name" => "city",
										"value" => ''
									));
									?>
									<br />
									<?php
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "State/Province",
										"name" => "state",
										"value" => ''
									));
									?>
									<br />
									<?php
									GO_Base_Html_Select::render(array(
										"required" => true,
										'label' => 'Country',
										'value' => '',
										'name' => "country",
										'options' => GO::language()->getCountries()
									));
									?>
									</p>
									<br />
									<p>
									<?php
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "Phone",
										"name" => "phone",
										"value" => ''
									));
									?>
									<br />
									<?php
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "Mobile",
										"name" => "mobile",
										"value" => ''
									));
									?>
									</p>
									<br />
									<p>
									<?php
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "Company",
										"name" => "company",
										"value" => ''
									));
									?>
									<br />
									<?php
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "Department",
										"name" => "department",
										"value" => ''
									));
									?>
									<br />
									<?php
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "Function",
										"name" => "function",
										"value" => ''
									));
									?>
									</p>
									<br />
									<p>
									<?php
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Username",
										"name" => "username",
										"value" => ''
									));
									?>
									<br />
									<?php
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Password",
										"name" => "password",
										"value" => ''
									));
									?>
									<br />
									<?php
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Confirm",
										"name" => "password_confirm",
										"value" => ''
									));
									?>
									</p>
									<br /><input type="submit" value="OK" /><input type="reset" value="Reset" /><input type="reset" value="Cancel" />
								</form>
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