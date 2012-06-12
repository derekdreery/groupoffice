					<div class="subkader-big-top">
						<div class="subkader-big-bottom">
							<div class="subkader-big-center">						
								

								<h1>License details</h1>
								<?php echo $this->getPage()->content; ?>
								
								<?php 
								if(!empty($this->license)){
									echo '<p>The information that is provided for the license: <b>' .$this->license->name. '</b></p>';
									echo '<p><br /></p>';
									
									if($this->license->getAttribute('upgrades','raw') > time())
										echo '<p>This license is available till: <b>'. $this->license->upgrades.'</b></p>';
									else
											echo '<p>The support contract for this license is <b><font color="red">expired</font></b>. Go to the <a href="'.$this->pageUrl('invoices').'">Invoices</a> page to renew your support contract.</p>';
									
									echo '<p>Hostname for this license: <b>'.$this->license->host.'</b></p>';
									echo '<p>Ip-addressfor this license: <b>'.$this->license->ip.'</b></p>';
									
									$packages = $this->license->packages;
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
