<?php
GOS::site()->scripts->registerCssFile($this->getTemplateUrl().'css/ticket.css');

$this->setPageTitle("Support");
?>
<?php if(GO::modules()->tickets): ?>
	<div class="subkader-small-top">
		<div class="subkader-small-bottom">
			<div class="subkader-small-center">		
				
				As a user of the Professional version you'll get premium technical support through our ticket system. 
				A technician will respond within 8 working hours but probably much faster.<br><br>
				Please check our troubleshooting page before posting your question:<br>
				<a href="http://www.group-office.com/wiki/Troubleshooting">http://www.group-office.com/wiki/Troubleshooting</a><br>

				<p>&nbsp;</p>
				<p>Click <a href="<?php echo $this->createUrl("tickets/site/newTicket"); ?>">here</a> to create a new ticket.</p>
				<p>&nbsp;</p>
			</div>
		</div>
	</div>
<?php $this->renderPartial('_ticketSidebar'); ?>
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
					<p>You don't have any tickets yet.</p>
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
	
