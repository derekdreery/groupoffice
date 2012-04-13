
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">				
								<h1>Thank you!</h1>
								<p><br /></p>
								
								<?php
								echo GO_Billing_Utils::replaceOrderFields($this->order->status->getLanguage($this->order->language_id)->screen_template,$this->order);
//								switch($this->paymentStatus) {
//								 case GO_Webshop_Payment_PaymentProvider::STATUS_PENDING :
//									 echo '<p>Your order is PENDING.</p>';
//									 break;
//								 case GO_Webshop_Payment_PaymentProvider::STATUS_FAILED :
//									 echo '<p>There has gone something wrong with this order.</p>';
//									 echo '<p>Please contact us via the ticket system.</p>';
//									 break;
//								 case GO_Webshop_Payment_PaymentProvider::STATUS_PAID :
//									 echo '<p>Your order has been payed.</p>';
//									 echo '<p>You can download the license from the Download page.</p>';
//									 break;
//								 case GO_Webshop_Payment_PaymentProvider::STATUS_WAITING :
//									 echo '<p>We are awaiting of your payment.</p>';
//									 
//									
//									 
//									 break;
//									default:
//										echo $this->paymentStatus;
//										break;
//								}
								?>
								

							</div>
						</div>

					</div>


					<div class="subkader-right">
						<h1>Secure login</h1>
						<p>SSL secured connection verified by Equifax Secure Inc. </p>
					</div>
