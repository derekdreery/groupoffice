<?php
require('header.php');
?>
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">				
								<h1>Baa</h1>
								<p>DIIBAA</p>
								<?php
								// switch($payment status){
								// case '' :
								// echo '<p>iets</p>';
								// break;
								// case '' :
								// echo '<p>iets</p>';
								// break;
								// case '' :
								// echo '<p>iets</p>';
								// break;
								//}
								?>
								

							</div>
						</div>

					</div>


					<div class="subkader-right">
						<h1>Secure login</h1>
						<p>SSL secured connection verified by Equifax Secure Inc. </p>
						<div class="button-green-side" onmouseover="this.className='button-green-side-hover';"  onmouseout="this.className='button-green-side';">
							<div class="button-green-side-right">
								<a href="<?php echo self::pageUrl('checkout'); ?>" class="button-green-side-center"> 
									Continue checkout
								</a>
							</div>
						</div>
					</div>

<?php
require('footer.php');
?>