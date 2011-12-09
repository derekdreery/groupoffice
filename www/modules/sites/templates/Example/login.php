<?php
require('header.php');
?>
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						

								<h1>Are you here for the first time?</h1>								
								<a href="<?php echo self::pageUrl("register"); ?>">Click here to register once</a>
								<h1>Login if you are already registered</h1>
								
									<?php 
									GO_Base_Html_Form::renderBegin('sites/user/login',true);
									//echo GO_Base_Html_Error::getError();
									
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Username",
										"name" => "username",
										"value" => ''
									));
						
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Password",
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
									<a href="<?php echo self::pageUrl("lostpass"); ?>">Lost password?</a>
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