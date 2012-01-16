					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						
								<?php if($this->formok): ?>
									<h1>Reset password was successful.</h1>								
									<p>Your password is successfully reset.</p>
								<?php else: ?>
								<h1>Reset password</h1>								
								<p>Fill in the form below to reset your password.</p>
								
								<?php 
									GO_Base_Html_Form::renderBegin('sites/user/recover','resetpassword',true);
								
									GO_Base_Html_Password::render(array(
										"required" => true,
										"label" => "Password",
										"name" => "password",
										"value" => ''
									));
									
									GO_Base_Html_Password::render(array(
										"required" => true,
										"label" => "Confirm",
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
