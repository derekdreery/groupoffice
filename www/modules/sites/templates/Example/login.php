<?php
require('header.php');
?>
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						

								<h1>Are you here for the first time?</h1>								
								<a href="<?php echo self::pageUrl("register"); ?>">Click here to register once</a>
								<h1>Login if you are already registered</h1>
								
								<form method="post">
									<?php 
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Username",
										"name" => "username",
										"value" => ''
									));
									?>
									<br />
									<?php
									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Password",
										"name" => "password",
										"value" => ''
									));
									?>
									<a href="<?php echo self::pageUrl("lostpass"); ?>">Lost password?</a>
									<br /><input type="submit" value="Login" /><input type="reset" value="Cancel" />
								</form>
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