
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">				
								<h1>Thank you!</h1>
								<p><br /></p>								
								<?php
								echo $order->replaceTemplateTags($order->status->getLanguage($order->language_id)->screen_template);
								?>
							</div>
						</div>

					</div>


					<div class="subkader-right">
						<h1>Secure login</h1>
						<p>SSL secured connection verified by Equifax Secure Inc. </p>
					</div>
