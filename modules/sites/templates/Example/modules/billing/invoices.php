<div class="subkader-big-top">
	<div class="subkader-big-bottom">
		<div class="subkader-big-center">						
			<h1>Your invoices</h1>
			<?php echo $this->getPage()->content; ?>
			<p>You can find your invoices below.</p>
			<p>Click on "pay" to go directly to the payment page and click on "Download" to download the invoice as a PDF file.</p>
			
		</div>
	</div>
</div>

<?php if($pager->models): ?>

	<div class="subkader-big-top">
		<div class="subkader-big-bottom">
			<div class="subkader-big-center">		
				<table class="models-table" width="100%" style="border-collapse: collapse;">
					<tr>
						<th align="left">Invoice no.</th><th align="left">Date</th><th align="left">Status</th><th></th><th></th>
					</tr>
					<?php $i = 0; ?>
					<?php foreach($pager->models as $invoice): ?>
						<?php if(!empty($invoice->status)): ?>
							<?php
								if($i%2!=0)
									$style = 'greytable-odd';
								else
									$style = 'greytable-even';
								$i++;
							?>
							<tr class="model-row <?php echo $style; ?>" style="border-collapse: collapse;">
								<td><?php echo $invoice->order_id; ?></td>
								<td><?php echo $invoice->btime; ?></td>
								<td><?php echo $invoice->status->getName($this->webshop->language_id); ?></td>
								<td>
									<?php if(!empty($invoice->ptime)): ?>
										<?php echo $invoice->ptime; ?>
									<?php else: ?>
										<a href="<?php echo $this->webshop->getPaymentUrl($invoice->id); ?>">Pay</a>
									<?php endif; ?>
								</td>
								<td><a target="_blank" href="<?php echo GO::url('billing/order/sitePdf',array('id'=>$invoice->id)); ?>">Download</a></td>
							</tr>
						<?php endif; ?>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div>

	<div class="subkader-big-top">
		<div class="subkader-big-bottom">
			<div class="subkader-big-center">				
				<?php $pager->render(); ?>
			</div>
		</div>
	</div>

<?php else: ?>

	<div class="subkader-big-top">
		<div class="subkader-big-bottom">
			<div class="subkader-big-center">
				<p>There are no invoices found.</p>
			</div>
		</div>
	</div>

<?php endif; ?>
