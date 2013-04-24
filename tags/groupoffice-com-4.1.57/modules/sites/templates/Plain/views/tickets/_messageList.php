<?php if ($pager->models): ?>

	

				<table class="ticket-models-table">
					<?php
					$i = 0;
					foreach ($pager->models as $message) {
						if ($i % 2 != 0)
							$style = 'greytable-odd';
						else
							$style = 'greytable-even';
						$i++;
						?>
						<tr class="ticketmodel-row <?php echo $style; ?>">
							<td><b><?php echo $message->posterName; ?></b> <?php echo GOS::t('tickets_messageSaid'); ?>:</td>
							<td align="right"><b><?php echo $message->getAttribute("ctime","formatted"); ?></b></td>
						</tr>
						<tr class="ticketmodel-row <?php echo $style; ?>">
							<td colspan="2"><?php echo $message->getAttribute("content","html"); ?></td>
						</tr>
						<?php if (!empty($message->attachments)): ?>
							<?php $files = $message->getFiles(); ?>
							<tr class="ticketmodel-row <?php echo $style; ?>">
								<td colspan="2">
									<div class="ticket-message-attachment"><b><?php echo GOS::t('tickets_messageFiles'); ?>:</b></div>
									<?php foreach ($files as $file => $obj): ?>
										<div class="ticket-message-attachment"><a target="_blank" href="<?php echo $this->createUrl('tickets/site/downloadAttachment',array('file'=>$obj->id,'ticket_number'=>$ticket->ticket_number,'ticket_verifier'=>$ticket->ticket_verifier)); ?>">
												<?php echo $file; ?>
											</a></div>
									<?php endforeach; ?>
								</td>
							</tr>
						<?php endif; ?>
							<?php if($message->has_status): ?>
								<tr class="ticketmodel-row <?php echo $style; ?>">
									<td colspan="2"><?php echo GO::t('status_change', 'tickets') . ': ' . GO_Tickets_Model_Status::getName($message->status_id); ?></td>
								</tr>
							<?php endif; ?>
					<?php } ?>
							
				</table>


	
			<?php $pager->render(); ?>

<?php endif; ?>