<?php
require('header.php');
?>
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">				
								
								<h1>Select payment option</h1>
								
								<?php
								$webshop = $this->getWebshop();
								$providers = $webshop->getPaymentProviders();
								
								foreach($providers as $provider){
									echo '<h2>'.$provider->name().'</h2><p>'.$provider->description().'</p>';
									echo $provider->getPaymentLinkHtml($this->order);
								}
								
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