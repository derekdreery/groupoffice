<?php
require('header.php');
?>
					<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						

								<?php echo $this->page->content; ?>
								
								<?php 
								if(!empty($this->invoices)){
									echo '<table width="100%">';
									echo '<tr>';
									echo '<th align="left">Invoice no.</th><th align="left">Date</th><th align="left">Status</th><th></th><th></th>';
									echo '</tr>';
									
									foreach($this->invoices as $invoice){
										if(!empty($invoice->status)){
											echo '<tr>';
												echo '<td>'.$invoice->order_id.'</td><td>'.date('d-m-Y',$invoice->ctime).'</td><td>'.$invoice->status->name.'</td>';
												echo'<td>';
												if(!empty($invoice->ptime))
													echo date('d-m-Y',$invoice->ptime);
												else
													echo '<a href="'.$this::pageUrl($this->webshop->payment_path,array('order_id'=>$invoice->id)).'">Pay</a>';
												echo '</td><td>';
												
												echo '<a href="'.GO::url('webshop/webshop/invoicePdf',array('order_id'=>$invoice->id)).'">Download</a>';
												
												echo '</td>';
											echo '</tr>';
										}
									}
									echo '</table>';
								}else{
									echo 'There are no invoices found!';
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