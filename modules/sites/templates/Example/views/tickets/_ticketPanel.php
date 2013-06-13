<div class="feature-large">
	<div class="ticket-top-container">
		<div class="ticket-details">
			<div class="ticket-details-title"><?php echo GOS::t('tickets_ticketTicketInformation'); ?></div>
			<div class="ticket-details-text">
				<table>
					<tr><td>Subject:</td><td><?php echo $ticket->subject; ?></td>	</tr>
					<tr><td>Number:</td><td><?php echo $ticket->ticket_number; ?></td></tr>
					<tr><td>Type:</td><td><?php echo $ticket->type->name; ?></td>	</tr>
					<tr><td>Status:</td><td><?php echo $ticket->getStatusName(); ?></td></tr>
					<tr><td>Priority:</td><td><?php echo $ticket->priority ? 'Yes' : 'No'; ?></td></tr>
					<tr><td>Created:</td><td><?php echo $ticket->getAttribute("ctime","formatted"); ?></td>	</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="feature-large">
	<div class="ticket-user-info">
		<div class="ticket-user-title"><?php echo GOS::t('tickets_ticketYourInformation'); ?></div>
		<div class="ticket-user-text">

			<?php if($ticket->contact): ?>
			
				<table>
					<tr><td>Name:</td><td><?php echo $ticket->contact->name; ?></td></tr>
					<tr><td>Address:</td><td><?php echo $ticket->contact->address; ?> <?php echo $ticket->contact->address_no; ?></td>	</tr>
					<tr><td>Zip/City:</td><td><?php echo $ticket->contact->zip; ?> <?php echo $ticket->contact->city; ?></td></tr>
					<tr><td>Telephone:</td><td><?php echo $ticket->contact->work_phone; ?></td>	</tr>
					<tr><td>Mobile:</td><td><?php echo $ticket->contact->cellular; ?></td></tr>
					<tr><td>Email:</td><td><?php echo $ticket->contact->email; ?></td></tr>
				</table>
			
			<?php else: ?>
			
				<table>
					<tr><td>Name:</td><td><?php echo $ticket->contactName; ?></td></tr>
					<tr><td>Address:</td><td>-</td></tr>
					<tr><td>Zip/City:</td><td>-</td></tr>
					<tr><td>Telephone:</td><td><?php echo $ticket->phone; ?></td></tr>
					<tr><td>Email:</td><td><?php echo $ticket->email; ?></td></tr>
				</table>
			
			<?php endif; ?>
			
		</div>
	</div>
</div>

<div class="feature-large">
	<div class="ticket-agent-info">
		<div class="ticket-agent-title"><?php echo GOS::t('tickets_ticketYourAgent'); ?></div>
		
		<?php if ($ticket->agent): ?>
		
			<?php if (!empty($ticket->agent->contact->photoURL)): ?>
				<div class="ticket-agent-photo"><img src="<?php echo $ticket->agent->contact->photoURL; ?>"></div>
			<?php endif; ?>	
		
			<div class="ticket-agent-text"><?php echo $ticket->agent->name; ?></div>
			
		<?php else: ?>
			
			<div class="ticket-agent-text"><?php echo GOS::t('tickets_ticketNoAgent'); ?></div>
			
		<?php endif; ?>		
			
	</div>
</div>