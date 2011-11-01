<?php
require('header.php');
?>
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						

								<h1>Software</h1>								
								<?php
								
								$stmt = GO_Billing_Model_Product::model()->find(GO_Base_Db_FindParams::newInstance()->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('category_id', 1)));
								
								while($product = $stmt->fetch()){
									$language = $product->getLanguage(2);
									?>
									<div class="product">
									<h2><?php echo $language->name; ?></h2>
									<p><?php echo $language->description; ?></p>


									<p class="price"><b>&euro; <?php echo $product->list_price; ?></b></p>

									<div class="button-green" onmouseover="this.className='button-green-hover';"  onmouseout="this.className='button-green';">
										<div class="button-green-right">
											<a href="<?php echo GO::url("webshop/cart/add",array('product_id'=>$product->id)); ?>" class="button-green-center"> 
												Add to cart
											</a>

										</div>
									</div>
								</div>
									<?php
								}
								?>
							</div>
						</div>

					</div>


					<div class="subkader-right">
						<?php require('sidebar.php'); ?>
					</div>

<?php
require('footer.php');
?>