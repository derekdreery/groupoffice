					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						

								<h1><?php echo $this->t('profileTitle'); ?></h1>								
								<p><?php echo $this->t('profileText'); ?></p>
								
								<?php 
									GO_Base_Html_Form::renderBegin('sites/user/profile','profile',true);
																		
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('firstName'),
										"model" => $contact,
										"name" => "first_name",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"model" => $contact,
										"label" => $this->t('middleName'),
										"name" => "middle_name",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"model" => $contact,
										"label" => $this->t('lastName'),
										"name" => "last_name",
										"value" => ''
									));
									
									GO_Base_Html_Radio::render(array(
										"required" => true,
										"label" => $this->t('gender'),
										"name" => "gender",
										"value" => 'male',
										"options" => array(
												array("label"=>$this->t('male'),"value"=>"male"),
												array("label"=>$this->t('female'),"value"=>"female")
										)
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('email'),
										"name" => "email",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('address'),
										"name" => "address",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('housenumber'),
										"name" => "address_no",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('zip'),
										"name" => "zip",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('city'),
										"name" => "city",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => $this->t('state'),
										"name" => "state",
										"value" => ''
									));
									
									GO_Base_Html_Select::render(array(
										"required" => true,
										"label" => $this->t('country'),
										'value' => 'NL',
										'name' => "country",
										'options' => GO::language()->getCountries()
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => $this->t('phone'),
										"name" => "phone",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => $this->t('mobile'),
										"name" => "mobile",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('company'),
										"name" => "company",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => $this->t('department'),
										"name" => "department",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => $this->t('function'),
										"name" => "function",
										"value" => ''
									));
																
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => $this->t('vat'),
										"name" => "vat_no",
										"value" =>''
									));
									echo "<br /><hr />";
									echo '<h1>'.$this->t('yourlogincredentials').'</h1>';
									GO_Base_Html_Input::render(array(
										"required" => false, // False because it cannot be changed so it will not be posted because this is a disabled field
										"model" => $user,
										"label" => $this->t('username'),
										"name" => "username",
										"extra" => "disabled"
									));

									GO_Base_Html_Password::render(array(
										"required" => true,
										"label" => $this->t('password'),
										"model" => $user,
										"name" => "password",
										"value" => ''
									));
									
									GO_Base_Html_Password::render(array(
										"required" => true,
										"label" => $this->t('confirm'),
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
