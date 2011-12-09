<?php
require('header.php');
?>
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						

								<h1>Recover password</h1>								
								<p>Enter your e-mail address. If a valid user account with that e-mail address is found, your username and a new password will be sent to your e-mail address.</p>
								
								<?php 
									GO_Base_Html_Form::renderBegin('sites/user/recover','recover',true);
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Email",
										"name" => "email",
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
										"name" => "cancel",
										"value" => 'Cancel',
										"renderContainer" => false
									));

									GO_Base_Html_Form::renderEnd();
								?>
							</div>
						</div>

					</div>


					<div class="subkader-right">
						<h1>Secure login</h1>
						<p>SSL secured connection verified by Equifax Secure Inc. </p>
					</div>

<?php
require('footer.php');
?>