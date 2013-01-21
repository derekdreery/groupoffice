<div class="subkader-small-top">
	<div class="subkader-small-bottom">
		<div class="subkader-small-center">				
			<!--								<h1>Shopping cart</h1>-->

			<?php if($cart->isEmpty()): ?>
				<p><?php echo GO::t('noproductsincart', 'webshop'); ?></p>
			<?php else: ?>

			<?php echo GO_Sites_Components_Html::beginForm('', 'post',array('id'=>'cart')); ?>

			<table class="cart">
				<tr>
					<th><?php echo GO::t('amount', 'webshop'); ?></th>
					<th><?php echo GO::t('productname', 'webshop'); ?></th>
					<th style="text-align:right"><?php echo GO::t('price','webshop'); ?></th>
				</tr>

				<?php foreach($cart->getItems() as $product): ?>

				<tr>
					<td>
						<?php echo GO_Sites_Components_Html::textField("product[".$product->getId()."]", $product->getAmount(), array('size'=>5)); ?>
					</td>
					<td><?php echo $product->getName(); ?></td>
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
					<td align="right"><b><?php echo $cart->getTotalVatText(); ?></b></td>
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


			<div onmouseout="this.className='button-green';" onmouseover="this.className='button-green-hover';" class="button-green">
				<div class="button-green-right">
					<a class="button-green-center" onclick="document.forms['cart'].submit();" href="#">
						<?php echo GO::t('updateamounts', 'webshop'); ?></a>
				</div>
			</div>
			<?php echo GO_Base_Html_Form::getHtmlEnd(); ?>

<?php endif; ?>

		</div>
	</div>
</div>

			
			
<div class="subkader-right">
	<h1>Secure login</h1>
	<p>SSL secured connection verified by Equifax Secure Inc. </p>
	<div class="button-green-side" onmouseover="this.className='button-green-side-hover';"  onmouseout="this.className='button-green-side';">
		<div class="button-green-side-right">
			<a href="<?php echo $this->createUrl('/webshop/site/checkout'); ?>" class="button-green-side-center"> 
				Continue checkout
			</a>
		</div>
	</div>
</div>