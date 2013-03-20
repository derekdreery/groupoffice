	<div class="subkader-small-top">
		<div class="subkader-small-bottom">
			<div class="subkader-small-center">				

				<h1>Software</h1>
				<?php
					foreach($products as $product)
					{
						$this->renderPartial('_productform', array('product'=>$product, 'webshop'=>$webshop));
					}
				?>

			</div>
		</div>

	</div>


	<div class="subkader-right">
		<?php require($this->getTemplatePath().'views/sites/sidebar.php'); ?>
	</div>