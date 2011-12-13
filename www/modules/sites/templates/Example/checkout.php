<?php
require('header.php');
?>
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">				
								
								<h1>Order confirmation</h1>
								<p>Please check if the data below is correct.</p>
								<?php 								
									$contact = GO::user()->createContact();
									$company = GO_Addressbook_Model_Company::model()->findByPk($contact->id);
									
									if(empty($company))
										$company = new GO_Addressbook_Model_Company();
								
									GO_Base_Html_Form::renderBegin('webshop/cart/checkout','confirm',true); 

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Name",
										"name" => "name",
										"value" => $company->name
									));

									GO_Base_Html_Input::render(array(
											"required" => true,
											"label" => "Email",
											"name" => "email",
											"value" => $company->email
										));

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Address",
										"name" => "address",
										"value" => $company->address
									));

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Number of house",
										"name" => "address_nr",
										"value" => $company->address
									));

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "ZIP/Postal code",
										"name" => "zip",
										"value" => $company->address_no
									));

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "City",
										"name" => "city",
										"value" => $company->city
									));

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "State/Province",
										"name" => "state",
										"value" => $company->state
									));

									GO_Base_Html_Select::render(array(
										"required" => true,
										'label' => 'Country',
										'value' => $company->country,
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
										"value" => $company->vat_no
									));
								?>
								<h1>Selected products</h1>
								<table class="cart">
									<?php
									$cart = new GO_Webshop_Util_Cart();
									$cart = $cart->getCart();

									foreach ($cart['products'] as $p) {
										$language = $p['product']->getLanguage();
										?>
										<tr>
											<td><?php echo $p['amount']; ?></td>
											<td><?php echo $language->name; ?></td>
											<td align="right">€&nbsp;<?php echo $p['product']->list_price*$p['amount']; ?></td>
										</tr>
										<?php
									}
									?>
									<tr><td colspan="3" class="minicart_total" style="text-align:right;font-weight:bold;">Total: € <?php echo number_format($cart['subtotal']); ?></td></tr>
								</table>
								<?php
									GO_Base_Html_Checkbox::render(array(
										"required" => true,
										"label" => 'I agree to the <a href="http://www.group-office.com/data/License+agreement" target="_blank">license terms and conditions</a>',
										"name" => "agreement",
										"value" => '1',
										"labelStyle" => "width:240px;"
									));
									
									GO_Base_Html_Submit::render(array(
										"label" => "",
										"name" => "submit",
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
						<h1>Secure login</h1>
						<p>SSL secured connection verified by Equifax Secure Inc. </p>
					</div>

<?php
require('footer.php');
?>