					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">				
<!--								<h1>Shopping cart</h1>-->

								<?php echo $this->cart->getForm(); ?>
							</div>
						</div>
					</div>

					<div class="subkader-right">
						<h1>Secure login</h1>
						<p>SSL secured connection verified by Equifax Secure Inc. </p>
						<div class="button-green-side" onmouseover="this.className='button-green-side-hover';"  onmouseout="this.className='button-green-side';">
							<div class="button-green-side-right">
								<a href="<?php echo $this->pageUrl($this->webshop->getCheckoutPath()); ?>" class="button-green-side-center"> 
									Continue checkout
								</a>
							</div>
						</div>
					</div>
