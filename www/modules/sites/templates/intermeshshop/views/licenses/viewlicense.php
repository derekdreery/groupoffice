<div class="hoofd-kader-menu">

			<div class="hoofd-tab-left">
				<div class="hoofd-tab-right">
					<a class="hoofd-tab-center" href="#">
						License details
					</a>
				</div>
			</div>

		</div>		
		<div class="hoofd-kader-top"></div>
		<div class="hoofd-kader-center">
			
	
			<div class="subkader-big-top">
						<div class="subkader-big-bottom">
							<div class="subkader-big-center">						
								

								<h1>License details</h1>
								
								<?php 
								if(!empty($license)){
									echo '<p>The information that is provided for the license: <b>' .$license->name. '</b></p>';
									echo '<p><br /></p>';
									
									if($license->getAttribute('upgrades','raw') > time())
										echo '<p>This license is available till: <b>'. $license->upgrades.'</b></p>';
									else
											echo '<p>The support contract for this license is <b><font color="red">expired</font></b>. Go to the <a href="'.$this->createUrl('/billing/site/invoices').'">Invoices</a> page to renew your support contract.</p>';
									
									echo '<p>Hostname for this license: <b>'.$license->host.'</b></p>';
									echo '<p>Ip-address for this license: <b>'.$license->ip.'</b></p>';
									
									$packages = $license->packages;
									$package_count = $packages->rowCount();
									
									echo '<p>This license has <b>'.$package_count.'</b> package(s):</p>';
									
									while($package=$packages->fetch()) {
										echo '<p> - '.$package->name.'</p>';
									}

									echo '<p><br /></p>';
									echo '<p>Is this information not correct anymore?</p>';
									echo '<p>Then please create a support ticket with the information of this license and with the changes that are needed.</p>';
									
									
									
								}else{
									echo '<p></p>';
								}
								?>
								
							</div>
						</div>

					</div>

<!--					<div class="subkader-right">
						<?php // require($this->getRootTemplatePath().'sidebar.php'); ?>
					</div>-->

		</div>
	<div class="hoofd-kader-bottom"></div>	