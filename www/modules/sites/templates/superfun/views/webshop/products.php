<h1>Software</h1>
<?php foreach($products as $product): ?>
	<div class="product">

	<?php echo GO_Sites_Components_Html::beginForm('/addprodcut'); ?>

		<?php echo GO_Sites_Components_Html::activeHiddenField($product, 'id'); ?>

		<h2><?php echo $product->getLanguage(1)->name; ?></h2>
		<p><?php echo $product->getLanguage(1)->description; ?></p>


		<p class="price"><b>&euro; <?php echo $product->list_price; ?></b></p>

		<?php echo GO_Sites_Components_Html::submitButton('Add to cart'); ?>

	<?php echo GO_Sites_Components_Html::endForm(); ?>

</div>
<?php endforeach; ?>