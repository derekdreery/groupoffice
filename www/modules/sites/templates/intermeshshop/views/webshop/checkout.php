<div class="hoofd-kader-menu">

			<div class="hoofd-tab-left">
				<div class="hoofd-tab-right">
					<a class="hoofd-tab-center" href="#">
						Shopping cart
					</a>
				</div>
			</div>

		</div>		
		<div class="hoofd-kader-top"></div>
		<div class="hoofd-kader-center">
			
			<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">				
															
								<h1>Order confirmation</h1>
								<p>Please check if the data below is correct.</p>								
								
								<?php echo GO_Sites_Components_Html::beginForm('','post',array('id'=>'confirmCheckout')); ?>
								<div class="row">
								<?php echo GO_Sites_Components_Html::activeLabel($order, 'post_name'); ?>
								<?php echo GO_Sites_Components_Html::activeTextField($order, 'post_name'); ?>
								</div>
								<div class="row">
								<?php echo GO_Sites_Components_Html::activeLabel($order, 'email'); ?>
								<?php echo GO_Sites_Components_Html::activeTextField($order, 'email'); ?>
								</div>
								<div class="row">
								<?php echo GO_Sites_Components_Html::activeLabel($order, 'post_address'); ?>
								<?php echo GO_Sites_Components_Html::activeTextField($order, 'post_address'); ?>
								</div>
								<div class="row">
								<?php echo GO_Sites_Components_Html::activeLabel($order, 'post_address_no'); ?>
								<?php echo GO_Sites_Components_Html::activeTextField($order, 'post_address_no'); ?>
								</div>
								<div class="row">
								<?php echo GO_Sites_Components_Html::activeLabel($order, 'post_zip'); ?>
								<?php echo GO_Sites_Components_Html::activeTextField($order, 'post_zip'); ?>
								</div>
								<div class="row">
								<?php echo GO_Sites_Components_Html::activeLabel($order, 'post_city'); ?>
								<?php echo GO_Sites_Components_Html::activeTextField($order, 'post_city'); ?>
								</div>
								<div class="row">
								<?php echo GO_Sites_Components_Html::activeLabel($order, 'post_state'); ?>
								<?php echo GO_Sites_Components_Html::activeTextField($order, 'post_state'); ?>
								</div>
								<div class="row">
								<?php echo GO_Sites_Components_Html::activeLabel($order, 'post_country'); ?>
								<?php echo GO_Sites_Components_Html::activeDropDownList($order, 'post_country', GO::language()->getCountries()); ?>
								</div>

								<p>Only enter the following field if you don't live in the Netherlands and you have a valid European Union VAT number.</p>
								<div class="row">
								<?php echo GO_Sites_Components_Html::activeLabel($order, 'vat_no'); ?>
								<?php echo GO_Sites_Components_Html::activeTextField($order, 'vat_no'); ?>	
						</div>
								<h1>Selected products</h1>
								
			<table class="cart">
				<tr>
					<th><?php echo GO::t('amount', 'webshop'); ?></th>
					<th><?php echo GO::t('productname', 'webshop'); ?></th>
					<th style="text-align:right"><?php echo GO::t('price','webshop'); ?></th>
				</tr>

				<?php foreach($cart->getItems() as $product): ?>

				<tr>
					<td>
						<?php echo $product->getAmount(); ?>
					</td>
					<td><?php echo $product->getItem()->getLanguage(2)->name; ?></td>
					<td align="right"><?php echo $product->getSumPriceText(); ?></td>
				</tr>
				<?php endforeach; ?>

				<?php if($cart->getDiscountPercentage() > 0): ?>
				<tr>
					<td>&nbsp;</td>
					<td><?php echo $cart->getDiscountPercentage(); ?>% reseller discount.</td>
					<td align="right"><?php echo $cart->getDiscountText(); ?></td>
				</tr>
				<?php endif; ?>

				<?php if($cart->vatApplicable()): ?>

				<tr>
					<td colspan="2" align="right" class="minicart_total"><b><?php echo GO::t('subtotal','webshop');?>:</b></td>
					<td align="right" class="minicart_total"><b><?php echo $cart->getSubTotalTexT(); ?></b></td>
				</tr>

				<tr>
					<td colspan="2" align="right"><b>Vat:</b></td>
					<td align="right"><b><?php echo $cart->getVatText(); ?></b></td>
				</tr>
				<?php endif; ?>

				<tr>
					<td colspan="2" align="right" class="minicart_total"><b><?php echo GO::t('total', 'webshop'); ?>:</b></td>
					<td align="right" class="minicart_total"><b><?php echo $cart->getTotalTexT(); ?></b></td>
				</tr>

				<tr>
					<td colspan="3" align="right"><a href="http://finance.yahoo.com/currency/convert?amt='.$this->_total.'&amp;from=EUR&amp;to=USD&amp;submit=Convert" target="_blank">(Convert currency)</a></td>
				</tr>

			</table>
								
						<div class="row">
								
							<?php echo GO_Sites_Components_Html::activeCheckBox($order, 'agreeTerms'); ?>
							<?php echo GO_Sites_Components_Html::activeLabel($order, 'agreeTerms', array('class'=>'checkbox')); ?>
							<?php echo GO_Sites_Components_Html::error($order, 'agreeTerms'); ?>
								</div>
								<div class="row-buttons">
								<?php echo GO_Sites_Components_Html::submitButton('Continue'); ?>

									<div class="button-green-side" onmouseover="this.className='button-green-side-hover';"  onmouseout="this.className='button-green-side';" style="float:left;">
										<div class="button-green-side-right">
											<a href="<?php echo $this->createUrl('/webshop/site/cart'); ?>" class="button-green-side-center"> 
												Go to cart
											</a>
										</div>
									</div>
								</div>
								<?php echo GO_Sites_Components_Html::endForm(); ?>
								<div style="clear:both;"></div>
							</div>
						</div>

					</div>


					<div class="subkader-right">
						<h1>Secure login</h1>
						<p>SSL secured connection verified by Equifax Secure Inc. </p>
					</div>
		</div>
	<div class="hoofd-kader-bottom"></div>	