					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						

								<h1><?php echo GOS::t('profileTitle'); ?></h1>								
								<p><?php echo GOS::t('profileText'); ?></p>
								
								<?php 
								if(GOS::site()->notifier->hasMessage('success')){
									echo GOS::site()->notifier->getMessage('success');
								}  else if(GOS::site()->notifier->hasMessage('error')) {
									echo '<div class="notification notice-error">'.GOS::site()->notifier->getMessage('error').'</div>';
								}								
								?>
								
								<?php 
									GO_Base_Html_Form::renderBegin('sites/user/profile','profile',true);
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => GOS::t('firstName'),
										"model" => $contact,
										"name" => "first_name"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"model" => $contact,
										"label" => GOS::t('middleName'),
										"name" => "middle_name"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"model" => $contact,
										"label" => GOS::t('lastName'),
										"name" => "last_name"
									));
									
									GO_Base_Html_Radio::render(array(
										"required" => true,
										"label" => GOS::t('gender'),
										"model" => $contact,
										"name" => "sex",
										"options" => array(
												array("label"=>GOS::t('male'),"value"=>"M"),
												array("label"=>GOS::t('female'),"value"=>"F")
										)
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => GOS::t('email'),
										"model" => $contact,
										"name" => "email",
									));
									?>
									<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'email'); ?>
									<?php echo GO_Sites_Components_Html::activeTextField($contact, 'email'); ?>
									<?php echo GO_Sites_Components_Html::error($contact, 'email'); ?>
									<?php
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => GOS::t('address'),
										"model" => $contact,
										"name" => "address"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => GOS::t('housenumber'),
										"model" => $contact,
										"name" => "address_no"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => GOS::t('zip'),
										"model" => $contact,
										"name" => "zip"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => GOS::t('city'),
										"model" => $contact,
										"name" => "city"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => GOS::t('state'),
										"model" => $contact,
										"name" => "state"
									));
									
									GO_Base_Html_Select::render(array(
										"required" => true,
										"label" => GOS::t('country'),
										"model" => $contact,
										'name' => "country",
										'options' => GO::language()->getCountries()
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"model" => $contact,
										"label" => GOS::t('phone'),
										"name" => "home_phone"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"model" => $contact,
										"label" => GOS::t('mobile'),
										"name" => "cellular"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"model" => $company,
										"label" => GOS::t('company'),
										"name" => "name"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"model" => $contact,
										"label" => GOS::t('department'),
										"name" => "department"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"model" => $contact,
										"label" => GOS::t('function'),
										"name" => "function"
									));
																
									GO_Base_Html_Input::render(array(
										"required" => false,
										"model" => $company,
										"label" => GOS::t('vat'),
										"name" => "vat_no"
									));
									echo "<br /><hr />";
									echo '<h1>'.GOS::t('yourlogincredentials').'</h1>';
									
									GO_Base_Html_Password::render(array(
										"required" => false,
										"label" => GOS::t('currentPassword'),
										"name" => "currentPassword",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false, // False because it cannot be changed so it will not be posted because this is a disabled field
										"model" => $user,
										"label" => GOS::t('username'),
										"name" => "username",
										"extra" => "disabled"
									));

									GO_Base_Html_Password::render(array(
										"required" => false,
										"label" => GOS::t('password'),
										"model" => $user,
										"name" => "password",
										"value" => ''
									));
									
									GO_Base_Html_Password::render(array(
										"required" => false,
										"label" => GOS::t('confirm'),
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
