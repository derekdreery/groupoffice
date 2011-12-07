<h1>Shopping cart</h1>
<?php
$cart = new GO_Webshop_Util_Cart();

if (!$cart->hasProducts()) {
	?>	
	<p>There are no products in your shopping cart</p>
	<?php
} else {
	?>
	<table class="cart">
	<?php
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
	<div class="button-green-side" onmouseover="this.className='button-green-side-hover';"  onmouseout="this.className='button-green-side';">
		<div class="button-green-side-right">
			<a href="<?php echo self::pageUrl('checkout'); ?>" class="button-green-side-center"> 
				Checkout
			</a>
		</div>
	</div>
		<?php
	}
	?>