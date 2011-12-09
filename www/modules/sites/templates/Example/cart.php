<?php
require('header.php');
?>
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">				
								
<!--								<h1>Shopping cart</h1>-->
								
								<?php $cart = new GO_Webshop_Util_Cart(); ?>

								<?php if(!$cart->hasProducts()):?>	
									<p>There are no products in your shopping cart</p>
								<?php else : ?>
									<?php GO_Base_Html_Form::renderBegin('sites/user/recover','cart',true); ?>
									<table class="cart">
										<tr>
										<th>Amount</th><th>Name</th><th>Price</th>
										</tr>
									<?php	$cart = $cart->getCart(); ?>

									
									<?php foreach ($cart['products'] as $p): ?>
										<?php $language = $p['product']->getLanguage(); ?>
										<tr>
											<td>
												<?php 
													GO_Base_Html_Input::render(array(
														"required" => true,
														"label" => "",
														"name" => "product[".$p['product']->id."]",
														"value" => $p['amount'],
														"renderContainer" => false,
														"class" => "amount"
													));
												?>
											</td>
											<td><?php echo $language->name; ?></td>
											<td align="left">€&nbsp;<?php echo $p['product']->list_price; ?></td>
										</tr>
										<?php endforeach;?>
								<?php	endif; ?>
									<tr><td colspan="3" class="minicart_total" style="text-align:right;font-weight:bold;">Total: € <?php echo number_format($cart['subtotal']); ?> <a target="_blank" href="http://finance.yahoo.com/currency/convert?amt=199&amp;from=EUR&amp;to=USD&amp;submit=Convert">(Convert currency)</a></td></tr>
									</table>
								<div onmouseout="this.className='button-green';" onmouseover="this.className='button-green-hover';" class="button-green">
								<div class="button-green-right">
									<a class="button-green-center" onclick="document.cart.submit();" href="#"> 
									Update amounts
									</a>
								</div>
							</div>
							<?php GO_Base_Html_Form::renderEnd(); ?>
							</div>
						</div>

					</div>


					<div class="subkader-right">
						<h1>Secure login</h1>
						<p>SSL secured connection verified by Equifax Secure Inc. </p>
						<div class="button-green-side" onmouseover="this.className='button-green-side-hover';"  onmouseout="this.className='button-green-side';">
							<div class="button-green-side-right">
								<a href="<?php echo self::pageUrl('checkout'); ?>" class="button-green-side-center"> 
									Continue checkout
								</a>
							</div>
						</div>
					</div>

<?php
require('footer.php');
?>