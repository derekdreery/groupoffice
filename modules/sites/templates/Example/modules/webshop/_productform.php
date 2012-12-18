<div class="product">
	
	<?php GO_Base_Html_Form::renderBegin($this->getPage()->getUrl(),'add_to_cart_'.$product->id); ?>

	<?php 
		GO_Base_Html_Hidden::render(array(
			"required" => true,
			"label" => "",
			"name" => 'product_id',
			"value" => $product->id,
			"renderContainer" => false
		));
		?>
	
	<h2><?php echo $language->name; ?></h2>
	<p><?php echo $language->description; ?></p>


	<p class="price"><b>&euro; <?php echo $product->list_price; ?></b></p>

	<div class="button-green" onmouseover="this.className='button-green-hover';"  onmouseout="this.className='button-green';">
		<div class="button-green-right">
			<a href="#" onclick="document.add_to_cart_<?php echo $product->id;?>.submit()" class="button-green-center"> 
				Add to cart
			</a>

		</div>
	</div>
	
	<?php GO_Base_Html_Form::renderEnd(); ?>
	
</div>
