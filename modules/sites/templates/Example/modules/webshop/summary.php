					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">				
								
								<h1>Summary</h1>
								<p>Please check if the data below is correct.</p>
								<p>&nbsp;</p>
								<div class="formrow"><b>Billing address</b></div>
								<div class="formrow"><div class="formlabel">Name:</div><div class="cell"><?php echo $customer->post_name; ?></div></div>	
								<div class="formrow"><div class="formlabel">Email:</div><div class="cell"><?php echo $customer->email; ?></div></div>
								<div class="formrow"><div class="formlabel">Address:</div><div class="cell"><?php echo $customer->post_address; ?></div></div>
								<div class="formrow"><div class="formlabel">Address no:</div><div class="cell"><?php echo $customer->post_address_no; ?></div></div>
								<div class="formrow"><div class="formlabel">Zip:</div><div class="cell"><?php echo $customer->post_zip; ?></div></div>
								<div class="formrow"><div class="formlabel">City:</div><div class="cell"><?php echo $customer->post_city; ?></div></div>
								<div class="formrow"><div class="formlabel">Country:</div><div class="cell"><?php echo $customer->post_country; ?></div></div>
								<div class="formrow"><div class="formlabel">VAT number:</div><div class="cell"><?php echo $customer->vat_no; ?></div></div><br/>
								<p>Note: If you have an European Vat number, please make sure that you have filled the Vat number field. Otherwise we must add Vat to the invoice.</p>
								

								<h1>Selected products</h1>
							
								<?php echo $this->cart->getTable();?>		
								
								<?php
									GO_Base_Html_Form::renderBegin('webshop/cart/checkout','confirmSummary',true); 
									
									GO_Base_Html_Hidden::render(array(
										"label" => "",
										"name" => "submitsummary",
										"value" => 'Confirm',
										"renderContainer" => false
									));
								
//									GO_Base_Html_Submit::render(array(
//										"label" => "",
//										"name" => "submitsummary",
//										"value" => 'Confirm',
//										"renderContainer" => false
//									));
									
//									GO_Base_Html_Reset::render(array(
//										"label" => "",
//										"name" => "reset",
//										"value" => 'Cancel',
//										"renderContainer" => false
//									));
									?>
								
								<div class="button-green" onmouseover="this.className='button-green-hover';"  onmouseout="this.className='button-green';" style="float:left; margin-right: 15px;">
									<div class="button-green-right">
										<a href="#" onclick="document.confirmSummary.submit()" class="button-green-center"> 
											Place your order
										</a>
									</div>
								</div>
								
								<div class="button-green-side" onmouseover="this.className='button-green-side-hover';"  onmouseout="this.className='button-green-side';" style="float:left;">
									<div class="button-green-side-right">
										<a href="<?php echo $this->pageUrl($this->webshop->getCheckoutPath()); ?>" class="button-green-side-center"> 
											Change order
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
