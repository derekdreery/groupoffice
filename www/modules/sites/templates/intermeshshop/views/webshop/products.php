<div class="hoofd-kader-menu">

			<div class="hoofd-tab-left">
				<div class="hoofd-tab-right">
					<a class="hoofd-tab-center" href="#">
						Products
					</a>
				</div>
			</div>

		</div>		
		<div class="hoofd-kader-top"></div>

		<div class="hoofd-kader-center">
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">				
								
								<h1>Software</h1>
								<?php
									foreach($products as $product)
									{
										$this->renderPartial('_productform', array('product'=>$product));
									}
								?>

							</div>
						</div>

					</div>


					<div class="subkader-right">
						<?php require($this->getTemplatePath().'views/sites/sidebar.php'); ?>
					</div>
</div>
	<div class="hoofd-kader-bottom"></div>	