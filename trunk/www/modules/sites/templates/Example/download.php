<?php
require('header.php');
?>
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						
								
								<h1>Licenses</h1>
								<?php echo $this->page->content; ?>
								
								<?php 
								if(!empty($this->licences)){
									
								}else{
									echo '<p>You don\'t have any licenses yet. Your purchased licenses will be available for download here when you purchase a software product.</p>';
								}
								?>
								
							</div>
						</div>

					</div>


					<div class="subkader-right">
						<?php require('sidebar.php'); ?>
					</div>

<?php
require('footer.php');
?>




