<h1>Shopping cart</h1>
<?php

$cart = GO_Webshop_Model_Webshop::getByController($this)->getCart();


if (!$cart->hasProducts()) {
	?>	
	<p>There are no products in your shopping cart</p>
	<?php
} else {
	echo $cart->getCartTable();
	?>
	<div class="button-green-side" onmouseover="this.className='button-green-side-hover';"  onmouseout="this.className='button-green-side';">
		<div class="button-green-side-right">
			<a href="<?php echo self::pageUrl('cart'); ?>" class="button-green-side-center"> 
				Checkout
			</a>
		</div>
	</div>
		<?php
	}
	?>