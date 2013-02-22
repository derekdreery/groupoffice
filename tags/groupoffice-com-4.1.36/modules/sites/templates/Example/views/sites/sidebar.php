<h1>Shopping cart</h1>

<?php $cart = new GO_Webshop_Components_ShoppingCart(); ?>

<?php if($cart->isEmpty()): ?>
			<p><?php echo GO::t('noproductsincart','webshop'); ?></p>
<?php else: ?>
		<table class="cart">
			
			<?php foreach($cart->getItems() as $product): ?>
				<tr>
					<td><?php echo $product->getAmount(); ?></td>
					<td><?php echo $product->getName(); ?></td>
					<td align="right"><?php echo $product->getSumPriceText(); ?></td>
				</tr>
			<?php endforeach; ?>
			
			<?php if($cart->getDiscount() > 0): ?>
				<tr>
					<td>-1</td>
					<td><?php echo $cart->getDiscountPercentage(); ?>% reseller discount.</td>
					<td align="right"><?php echo $cart->getDiscountText(); ?></td>
				</tr>
			<?php endif; ?>


		<tr>
			<td colspan="2" align="right" class="minicart_total"><b><?php echo GO::t('total','webshop'); ?>:</b></td>
			<td align="right" class="minicart_total"><b><?php echo $cart->getTotalText(); ?></b></td>
		</tr>

	</table>

<?php endif; ?>

<div class="button-green-side" onmouseover="this.className='button-green-side-hover';"  onmouseout="this.className='button-green-side';">
	<div class="button-green-side-right">
		<a href="<?php echo $this->createUrl('/webshop/site/cart'); ?>" class="button-green-side-center"> 
			<?php echo GOS::t('webshop_checkout'); ?>
		</a>
	</div>
</div>