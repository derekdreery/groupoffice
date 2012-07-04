					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						

								<h1><?php echo $this->t('profileTitle'); ?></h1>								
								<p><?php echo $this->t('profileText'); ?></p>
								
								<?php echo $this->notifications->render('profile'); ?>
								
								<?php 
									GO_Base_Html_Form::renderBegin('sites/user/profile','profile',true);
									
									echo "<br /><hr />";
									echo '<h2>'.$this->t('yourlogincredentials').'</h2>';
									
									GO_Base_Html_Password::render(array(
										"required" => false,
										"label" => $this->t('currentPassword'),
										"name" => "currentPassword",
										"value" => ''
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false, // False because it cannot be changed so it will not be posted because this is a disabled field
										"model" => $user,
										"label" => $this->t('username'),
										"name" => "username",
										"extra" => "disabled"
									));

									GO_Base_Html_Password::render(array(
										"required" => false,
										"label" => $this->t('password'),
										"model" => $user,
										"name" => "password",
										"value" => ''
									));
									
									GO_Base_Html_Password::render(array(
										"required" => false,
										"label" => $this->t('confirm'),
										"name" => "passwordConfirm",
										"value" => ''
									));
									
									echo "<br /><hr />";
									echo '<h2>'.$this->t('generalInformation').'</h2>';
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('firstName'),
										"model" => $contact,
										"name" => "first_name"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"model" => $contact,
										"label" => $this->t('middleName'),
										"name" => "middle_name"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"model" => $contact,
										"label" => $this->t('lastName'),
										"name" => "last_name"
									));
									
									GO_Base_Html_Radio::render(array(
										"required" => true,
										"label" => $this->t('gender'),
										"model" => $contact,
										"name" => "sex",
										"options" => array(
												array("label"=>$this->t('male'),"value"=>"M"),
												array("label"=>$this->t('female'),"value"=>"F")
										)
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('email'),
										"model" => $contact,
										"name" => "email",
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"model" => $contact,
										"label" => $this->t('phone'),
										"name" => "home_phone"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"model" => $contact,
										"label" => $this->t('mobile'),
										"name" => "cellular"
									));
																	
									echo "<br /><hr />";
									echo '<h2>'.$this->t('companyInformation').'</h2>';
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"model" => $company,
										"label" => $this->t('company'),
										"name" => "name"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"model" => $contact,
										"label" => $this->t('department'),
										"name" => "department"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"model" => $contact,
										"label" => $this->t('function'),
										"name" => "function"
									));
																
									GO_Base_Html_Input::render(array(
										"required" => false,
										"model" => $company,
										"label" => $this->t('vat'),
										"name" => "vat_no"
									));			
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('address'),
										"model" => $company,
										"name" => "address"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('housenumber'),
										"model" => $company,
										"name" => "address_no"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('zip'),
										"model" => $company,
										"name" => "zip"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('city'),
										"model" => $company,
										"name" => "city"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => $this->t('state'),
										"model" => $company,
										"name" => "state"
									));
									
									GO_Base_Html_Select::render(array(
										"required" => true,
										"label" => $this->t('country'),
										"model" => $company,
										'name' => "country",
										'options' => GO::language()->getCountries()
									));

									GO_Base_Html_Checkbox::render(array(
										"required" => false,
										"label" => $this->t('addressIsPostAddress'),
										"model" => $company,
										"value" => "1",
										"name" => "post_address_is_address",
										"checked" => $company->post_address_is_address
									));
									
									echo '<div class="post-address"';
									echo !empty($company->post_address_is_address)?' style="display:none;"':'';
									echo '>';
									
									echo "<br /><hr />";
									echo '<h2>'.$this->t('postAddressDetails').'</h2>';
																											
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('postAddress'),
										"model" => $company,
										"name" => "post_address"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('postHousenumber'),
										"model" => $company,
										"name" => "post_address_no"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('postZip'),
										"model" => $company,
										"name" => "post_zip"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => $this->t('postCity'),
										"model" => $company,
										"name" => "post_city"
									));
									
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => $this->t('postState'),
										"model" => $company,
										"name" => "post_state"
									));
									
									GO_Base_Html_Select::render(array(
										"required" => true,
										"label" => $this->t('postCountry'),
										"model" => $company,
										'name' => "post_country",
										'options' => GO::language()->getCountries()
									));		
									
									echo '</div>';
									echo "<br /><hr /><br />";
									
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
