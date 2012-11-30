					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						

								<h1><?php echo GOS::t('firstTime'); ?></h1>								
								<a href="<?php echo $this->pageUrl("register"); ?>"><?php echo GOS::t('registerOnceClick'); ?></a>
								<h1><?php echo GOS::t('registeredLogin'); ?></h1>
								
									<?php 
									GO_Base_Html_Form::renderBegin('sites/user/login','login',true);
									//echo GO_Base_Html_Error::getError();
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => GOS::t('username'),
										"name" => "username",
										"value" => ''
									));
						
									GO_Base_Html_Password::render(array(
										"required" => true,
										"label" => GOS::t('password'),
										"name" => "password",
										"value" => ''
									));
									
									GO_Base_Html_Submit::render(array(
										"label" => "",
										"name" => "submit",
										"value" => 'OK',
										"renderContainer" => false
									));
									
									GO_Base_Html_Reset::render(array(
										"label" => "",
										"name" => "reset",
										"value" => 'Reset',
										"renderContainer" => false
									));
									
									GO_Base_Html_Form::renderEnd();
									?>
									<a href="<?php echo $this->pageUrl($this->getSite()->getLostPasswordPath()); ?>"><?php echo GOS::t('lostPassword'); ?>?</a>
							</div>
						</div>

					</div>


					<div class="subkader-right">
						<h1>Secure login</h1>
						<p>SSL secured connection verified by Equifax Secure Inc. </p>
					</div>
