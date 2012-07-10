					<div class="subkader-big-top">
						<div class="subkader-big-bottom">
							<div class="subkader-big-center">						
								

								<h1>Set license details</h1>
								
								<p>Fill in the form with the data that is provided in the gotest.php file.</p>
<p>Download the gotest.php file <a href="">here</a></p>
								
								<?php 
								if(!empty($this->license)){
									GO_Base_Html_Form::renderBegin('licenses/site/setLicense','confirm',true); 

									GO_Base_Html_Input::render(array(
										"required" => true,
										"label" => "Hostname",
										"name" => "host",
										"value" => $this->license->host
									));

									GO_Base_Html_Input::render(array(
											"required" => true,
											"label" => "External Ip-address",
											"name" => "ip",
											"value" => $this->license->ip
									));
									
									GO_Base_Html_Input::render(array(
											"required" => false,
											"label" => "Internal Ip-address",
											"name" => "intip",
											"value" => ""
									));
									
									GO_Base_Html_Submit::render(array(
										"label" => "",
										"name" => "submitlicense",
										"value" => 'Save license',
										"renderContainer" => false
									));
									
									GO_Base_Html_Reset::render(array(
										"label" => "",
										"name" => "reset",
										"value" => 'Cancel',
										"renderContainer" => false
									));
									
									GO_Base_Html_Form::renderEnd();
									
								}else{
									echo '<p>An error occurred!</p>';
								}
								?>
								
							</div>
						</div>

					</div>
<!--

					<div class="subkader-right">
						<?php // require($this->getRootTemplatePath().'sidebar.php'); ?>
					</div>-->
