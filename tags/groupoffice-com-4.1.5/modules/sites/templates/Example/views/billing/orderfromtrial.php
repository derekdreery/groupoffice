					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						

								<h1>Thank you for choosing Group-Office!</h1>
								
								Please provide us with your billing address and the number of users and diskspace you desire.
								
								<?php 								
								
									GO_Base_Html_Form::renderBegin('webshop/cart/checkout','confirm',true); 
									
									GO_Base_Html_Hidden::render(array(
										"name" => "reference",
										"value" => $_REQUEST['reference']
									));

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Name",
										"name" => "name",
										"value" => $this->customer->name
									));

									GO_Base_Html_Input::render(array(
											"required" => true,
											"label" => "Email",
											"name" => "email",
											"value" => $this->customer->email
										));

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Address",
										"name" => "address",
										"value" => $this->customer->address
									));

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Number of house",
										"name" => "address_no",
										"value" => $this->customer->address_no
									));

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "ZIP/Postal code",
										"name" => "zip",
										"value" => $this->customer->zip
									));

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "City",
										"name" => "city",
										"value" => $this->customer->city
									));

									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "State/Province",
										"name" => "state",
										"value" => $this->customer->state
									));

									GO_Base_Html_Select::render(array(
										"required" => true,
										'label' => 'Country',
										'value' => $this->customer->country,
										'name' => "country",
										'options' => GO::language()->getCountries()
									));
								?>
								<p>Only enter the following field if you don't live in the Netherlands and you have a valid European Union VAT number.</p>
								<?php								
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "EU VAT No.:",
										"name" => "vat_no",
										"value" => $this->customer->vat_no
									));
									
									GO_Base_Html_Select::render(array(
										"required" => true,
										'label' => 'Payment period',
										'value' => "Year",
										'name' => "payment_period",
										'options' => array(
											"year"=>"Year",
											"halfyear"=>"Half year",
											"quarter"=>"Quarter"
										)
									));
									
									GO_Base_Html_Select::render(array(
										"required" => true,
										'label' => 'Number of users (exluding the admin user)',
										'value' => "1",
										'name' => "number_of_users",
										'options' => array(
											"1"=>"1 user (€ 10 p.m.)",
											"2"=>"2 users (€ 19 p.m.)",
											"3"=>"3 users (€ 28 p.m.)",
											"4"=>"4 users (€ 36 p.m.)",
											"5"=>"5 users (€ 43 p.m.)",
											"6"=>"6 users (€ 50 p.m.)",
											"7"=>"7 users (€ 57 p.m.)",
											"8"=>"8 users (€ 63 p.m.)",
											"9"=>"9 users (€ 69 p.m.)",
											"10"=>"10 users (€ 74 p.m.)",
											"15"=>"15 users (€ 95 p.m.)",
											"20"=>"20 users (€ 109 p.m.)",
											"25"=>"25 users (€ 123 p.m.)",
											"30"=>"30 users (€ 134 p.m.)",
											"35"=>"35 users (€ 141 p.m.)",
											"40"=>"40 users (€ 146 p.m.)",
											"45"=>"45 users (€ 148 p.m.)",
											"50"=>"50 users (€ 150 p.m.)"
										)
									));
									
									?>
									<p>512MB per user for a mailbox is included. You'll need some additional space to store files in Group-Office. Diskspace costs € 2,- per GB per month.</p>
								<?php								
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Amount of diskspace (GB)",
										"name" => "diskspace",
										"value" => '1'
									));
	
									GO_Base_Html_Submit::render(array(
										"label" => "",
										"name" => "submitorderfromtrial",
										"value" => 'Confirm',
										"renderContainer" => false
									));
									
									GO_Base_Html_Reset::render(array(
										"label" => "",
										"name" => "reset",
										"value" => 'Cancel',
										"renderContainer" => false
									));
									
									GO_Base_Html_Form::renderEnd();
									
								?>
							</div>
						</div>
					</div>

					<div class="subkader-right">
						<?php require($this->getRootTemplatePath().'sidebar.php'); ?>
					</div>
