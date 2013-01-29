					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						
								<?php if($this->formok): ?>
									<h1><?php echo GOS::t('resetPasswordSuccessTitle'); ?></h1>								
									<p><?php echo GOS::t('resetPasswordSuccess'); ?></p>
								<?php else: ?>
									<h1><?php echo GOS::t('resetPassword'); ?></h1>								
									<p><?php echo GOS::t('resetPasswordText'); ?></p>
								
								<?php 
									GO_Base_Html_Form::renderBegin('sites/user/recover','resetpassword',true);
								
									GO_Base_Html_Password::render(array(
										"required" => true,
										"label" => GOS::t('password'),
										"name" => "password",
										"value" => ''
									));
									
									GO_Base_Html_Password::render(array(
										"required" => true,
										"label" => GOS::t('confirm'),
										"name" => "confirm",
										"value" => ''
									));
								
									GO_Base_Html_Submit::render(array(
										"label" => "",
										"name" => "resetpasswordsubmit",
										"value" => 'OK',
										"renderContainer" => false
									));

									GO_Base_Html_Reset::render(array(
										"label" => "",
										"name" => "cancel",
										"value" => 'Cancel',
										"renderContainer" => false
									));

									GO_Base_Html_Form::renderEnd();
									
								?>
								
								<?php endif; ?>
							</div>
						</div>

					</div>


					<div class="subkader-right">
						<h1>Secure login</h1>
						<p>SSL secured connection verified by Equifax Secure Inc. </p>
					</div>
