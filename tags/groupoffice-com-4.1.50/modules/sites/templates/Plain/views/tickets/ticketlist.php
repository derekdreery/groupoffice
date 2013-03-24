<?php
GOS::site()->scripts->registerCssFile($this->getTemplateUrl().'css/ticket.css');

$this->setPageTitle("Support");
?>
<?php if(GO::modules()->tickets): ?>

<?php $this->renderPartial('_ticketSidebar'); ?>
	<div style="clear:both;"></div>
	
	<?php if($pager->models): ?>
	

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

								$linktoticket = '<a href="'.$this->createUrl("tickets/site/showTicket",array("ticket_number"=>$ticket->ticket_number,"ticket_verifier"=>$ticket->ticket_verifier)).'">';
						?>
						<tr class="ticketmodel-row <?php echo $style; ?>">
							<td width="20px"><?php echo $linktoticket; ?>	<?php echo ($ticket->status_id != GO_Tickets_Model_Ticket::STATUS_CLOSED && $ticket->unseen)?"<span class='image-new-message'></span>":'';?></a></td>
							<td width="80px"><?php echo $linktoticket; ?>	<?php echo $ticket->ticket_number; ?>	</a></td>
							<td><?php echo $linktoticket;?><?php echo $ticket->subject;?></a></td>
							<td width="180px"><?php echo $linktoticket; ?><?php echo $ticket->getStatusName(); ?></a></td>
							<td width="180px"><?php echo $linktoticket; ?><?php echo $ticket->agent?$ticket->agent->name:""; ?></a></td>
							<td width="100px" style="white-space: nowrap;"><?php echo $linktoticket; ?><?php echo $ticket->getAttribute("ctime","formatted"); ?></a></td>
						</tr>
						<?php endforeach; ?>
					</table>
						<?php $this->renderPartial('_filtermenu'); ?>

		
					<?php $pager->render(); ?>

	<?php else: ?>
		
					<p>You don't have any tickets yet.</p>

	<?php endif; ?>
<?php else: ?>
			
				<p>You don't have any active licenses. Please activate the license first on the 'Download' page.</p>
	
<?php endif;?>		
	
