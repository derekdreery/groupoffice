<?php if(GO::modules()->tickets): ?>
	<div class="subkader-small-top">
		<div class="subkader-small-bottom">
			<div class="subkader-small-center">		
				<?php echo $this->getPage()->content; ?>
				<p>&nbsp;</p>
				<p>Click <a href="<?php echo $this->pageUrl("ticket"); ?>">here</a> to create a new ticket.</p>
				<p>&nbsp;</p>
			</div>
		</div>
	</div>
<?php include "sidebar_ticket.php";?>
	<div style="clear:both;"></div>
	<?php if($pager->models): ?>
		<div class="subkader-big-top">
			<div class="subkader-big-bottom">
				<div class="subkader-big-center">		
					<table class="ticket-models-table">
						<th></th><th>Ticket-no</th><th>Name</th><th>Status</th><th>Agent</th><th>Created</th>
						<?php $i = 0; ?>
							<?php foreach($pager->models as $ticket): ?>
							<?php
								if($i%2!=0)
									$style = 'greytable-odd';
								else
									$style = 'greytable-even';
								$i++;

								$linktoticket = '<a href="'.$this->pageUrl("ticket",array("ticket_number"=>$ticket->ticket_number)).'">';
						?>
						<tr class="ticketmodel-row <?php echo $style; ?>">
							<td width="20px"><?php echo $linktoticket; ?>
								<?php 
									if($ticket->status != GO_Tickets_Model_Ticket::STATUS_CLOSED && $ticket->unseen){
										echo "<span class='image-new-message'></span>";
									}
								?></a></td>
							<td width="80px">
								<?php echo $linktoticket; ?>
								<?php echo $ticket->ticket_number; ?>
								</a>
							</td>
							<td>
								<?php echo $linktoticket;?>
								<?php echo $ticket->subject;?>
								</a>
							</td>
							<td width="180px">
								<?php echo $linktoticket; ?>
								<?php echo $ticket->getStatusName(); ?>
								</a>
							</td>
							<td width="180px">
								<?php echo $linktoticket; ?>
								<?php echo $ticket->agent?$ticket->agent->name:""; ?>
								</a>
							</td>
							<td width="100px" style="white-space: nowrap;">
								<?php echo $linktoticket; ?>
								<?php echo $ticket->ctime; ?>
								</a>
							</td>
						</tr>
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
					<p>You don\'t have any tickets yet.</p>
				</div>
			</div>
		</div>
	<?php endif; ?>
<?php else: ?>
	<div class="subkader-big-top">
		<div class="subkader-big-bottom">
			<div class="subkader-big-center">				
				<p>You don't have any active licenses. Please activate the license first on the 'Download' page.</p>
			</div>
		</div>
	</div>
<?php endif;?>						
