<?php
require('header.php');
?>
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">				
								
								<h1>Order confirmation</h1>
								<p>Please check if the data below is correct.</p>
								<?php 
								GO_Base_Html_Form::renderBegin('sites/user/recover','confirm',true); 
								
								GO_Base_Html_Input::render(array(
									"required" => true,
									"label" => "Name",
									"name" => "name",
									"value" => ""
								));
								
								GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Email",
										"name" => "email",
										"value" => ""
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
								?>
								<p>Only enter the following field if you don't live in the Netherlands and you have a valid European Union VAT number.</p>
								<?php
									GO_Base_Html_Input::render(array(
										"required" => false,
										"label" => "EU VAT No.:",
										"name" => "vat",
										"value" => ''
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
											<td align="right">€&nbsp;<?php echo $p['product']->list_price; ?></td>
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
										"value" => ''
									));
									
									GO_Base_Html_Submit::render(array(
										"label" => "",
										"name" => "submit",
										"value" => 'Agree',
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
						<div class="button-green-side" onmouseover="this.className='button-green-side-hover';"  onmouseout="this.className='button-green-side';">
							<div class="button-green-side-right">
								<a href="<?php echo self::pageUrl('continuecheckout'); ?>" class="button-green-side-center"> 
									Continue checkout
								</a>
							</div>
						</div>
					</div>

<?php
require('footer.php');
?>