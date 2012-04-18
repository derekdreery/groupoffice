
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">				
								
								<h1>Software</h1>
								<?php
									echo $this->getPage()->content;	
									
									$stmt = GO_Billing_Model_Product::model()->find(GO_Base_Db_FindParams::newInstance()->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('category_id', 1)));

									while($product = $stmt->fetch()){
										$language = $product->getLanguage(2);
										include("_productform.php");
								}
								?>

							</div>
						</div>

					</div>


					<div class="subkader-right">
						<?php require($this->getRootTemplatePath().'sidebar.php'); ?>
					</div>
