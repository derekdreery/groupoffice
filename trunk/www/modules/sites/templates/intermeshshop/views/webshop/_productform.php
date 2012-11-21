<div class="product">


	<h2><?php echo $product->getLanguage($webshop->language_id)->name; ?></h2>
	<p><?php echo $product->getLanguage($webshop->language_id)->description; ?></p>


	<p class="price"><b>&euro; <?php echo $product->list_price; ?></b></p>

	<div class="button-green" onmouseover="this.className='button-green-hover';"  onmouseout="this.className='button-green';">
		<div class="button-green-right">
			<a href="<?php echo $this->createUrl('/webshop/site/addProduct', array('product_id'=>$product->id)) ?>" class="button-green-center"> 
				Add to cart
			</a>

		</div>
	</div>
	
</div>
