					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">				
															
								<h1>Order confirmation</h1>
								<p>Please check if the data below is correct.</p>
								<?php 								
								
									GO_Base_Html_Form::renderBegin('webshop/cart/checkout','confirmCheckout',true); 

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Name",
										"name" => "post_name",
										"value" => $customer->post_name
									));

									GO_Base_Html_Input::render(array(
											"required" => true,
											"label" => "Email",
											"name" => "email",
											"value" => $customer->email
										));

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Address",
										"name" => "post_address",
										"value" => $customer->post_address
									));

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Number of house",
										"name" => "post_address_no",
										"value" => $customer->post_address_no
									));

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "ZIP/Postal code",
										"name" => "post_zip",
										"value" => $customer->post_zip
									));

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "City",
										"name" => "post_city",
										"value" => $customer->post_city
									));

									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "State/Province",
										"name" => "post_state",
										"value" => $customer->post_state
									));

									GO_Base_Html_Select::render(array(
										"required" => true,
										'label' => 'Country',
										'value' => $customer->post_country,
										'name' => "post_country",
										'options' => GO::language()->getCountries()
									));
								?>
								<p>Only enter the following field if you don't live in the Netherlands and you have a valid European Union VAT number.</p>
								<?php								
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "EU VAT No.:",
										"name" => "vat_no",
										"value" => $customer->vat_no
									));
								?>
								<h1>Selected products</h1>
								
								<?php echo $this->cart->getTable();?>						
								<?php
									GO_Base_Html_Checkbox::render(array(
										"required" => true,
										"label" => 'I agree to the <a href="http://www.group-office.com/data/License+agreement" target="_blank">license terms and conditions</a>',
										"name" => "agreement",
										"value" => '1',
										"empty_value" =>"",
										"labelStyle" => "width:240px;"
									));
									
//									GO_Base_Html_Submit::render(array(
//										"label" => "",
//										"name" => "submitcheckout",
//										"value" => 'Confirm',
//										"renderContainer" => false
//									));
//									
//									GO_Base_Html_Reset::render(array(
//										"label" => "",
//										"name" => "reset",
//										"value" => 'Cancel',
//										"renderContainer" => false
//									));
									
									GO_Base_Html_Hidden::render(array(
										"label" => "",
										"name" => "submitcheckout",
										"value" => 'Confirm',
										"renderContainer" => false
									));
									
									?>
									<div class="button-green" onmouseover="this.className='button-green-hover';"  onmouseout="this.className='button-green';" style="float:left; margin-right: 15px;">
										<div class="button-green-right">
											<a href="#" onclick="document.confirmCheckout.submit()" class="button-green-center"> 
												Continue
											</a>
										</div>
									</div>

									<div class="button-green-side" onmouseover="this.className='button-green-side-hover';"  onmouseout="this.className='button-green-side';" style="float:left;">
										<div class="button-green-side-right">
											<a href="<?php echo $this->pageUrl($this->webshop->getCartPath()); ?>" class="button-green-side-center"> 
												Go to cart
											</a>
										</div>
									</div>
								<div style="clear:both;"></div>
								
								<?php								
									
									GO_Base_Html_Form::renderEnd();
									
								?>
								
							</div>
						</div>

					</div>


					<div class="subkader-right">
						<h1>Secure login</h1>
						<p>SSL secured connection verified by Equifax Secure Inc. </p>
					</div>
