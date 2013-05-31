<?php Site::scripts()->registerCssFile(Site::file('css/ticket.css')); ?>

<div class="external-ticket-page ticket">
	<div class="wrapper">
		
		<h2><?php echo GO::t('ticket','defaultsite').' '. $ticket->ticket_number; ?></h2>
		
		<h3><?php echo GO::t('ticketContactInfo','defaultsite'); ?></h3>
		
		<table class="table-contactinformation">
			<tr>
				<td><?php echo GO::t('ticketFirstname','defaultsite'); ?></td>
				<td><?php echo $ticket->first_name; ?></td>
			</tr>
			<tr>
				<td><?php echo GO::t('ticketMiddlename','defaultsite'); ?></td>
				<td><?php echo $ticket->middle_name; ?></td>
			</tr>
			<tr>
				<td><?php echo GO::t('ticketLastname','defaultsite'); ?></td>
				<td><?php echo $ticket->last_name; ?></td>
			</tr>
			<tr>
				<td><?php echo GO::t('ticketEmail','defaultsite'); ?></td>
				<td><?php echo $ticket->email; ?></td>
			</tr>
			<tr>
				<td><?php echo GO::t('ticketPhone','defaultsite'); ?></td>
				<td><?php echo $ticket->phone; ?></td>
			</tr>
		</table>
		
		<h3><?php echo GO::t('ticketInfo','defaultsite'); ?></h3>
		
		<table class="table-ticketinformation">
			<tr>
				<td><?php echo GO::t('ticketSubject','defaultsite'); ?></td>
				<td><?php echo $ticket->subject; ?></td>
			</tr>
			<tr>
				<td><?php echo GO::t('ticketType','defaultsite'); ?></td>
				<td><?php echo $ticket->type->name; ?></td>
			</tr>
			<tr>
				<td><?php echo GO::t('ticketStatus','defaultsite'); ?></td>
				<td><?php echo $ticket->status_id?$ticket->getStatusName():GO::t('ticketStatusOpen','defaultsite'); ?></td>
			</tr>
			<tr>
				<td><?php echo GO::t('ticketPriority','defaultsite'); ?></td>
				<td><?php echo $ticket->priority?GO::t('yes'):GO::t('no'); ?></td>
			</tr>
			<tr>
				<td><?php echo GO::t('ticketDate','defaultsite'); ?></td>
				<td><?php echo $ticket->getAttribute("ctime","formatted"); ?></td>
			</tr>
		</table>

		<div class="button-bar">
			<?php if(GO::user()): ?>
				<a id="back-to-overview-button" href="<?php echo Site::urlManager()->createUrl('tickets/externalpage/ticketlist'); ?>" class="button"><?php echo GO::t('ticketBackToList','defaultsite'); ?></a>
			<?php endif; ?>
			<?php if(!$ticket->isNew && !$ticket->isClosed()): ?>
			<form method="POST">
				<input type="hidden" value="close" name="close" />
				<input type="submit" id="close-ticket-button"  class="button" value="<?php echo GO::t('ticketCloseTicket','defaultsite'); ?>" />
			</form>
			<?php endif; ?>
				<div class="clear"></div>
		</div>
			
		<h3><?php echo GO::t('ticketDiscussion','defaultsite'); ?></h3>
		
		<table class="table-ticketmessages">
			
			<?php foreach($ticket->messages as $i => $message): ?>
			<tr class="<?php echo($i%2)?'even':'odd'; ?>">
				<td>
					<span class="ticketmessage-name"><?php echo $message->posterName; ?></span><span class="ticketmessage-time"><?php echo $message->getAttribute("ctime","formatted"); ?></span>
					<p class="ticketmessage-message"><?php echo $message->getAttribute("content","html"); ?></p>

					<?php if (!empty($message->attachments)): ?>
						<span class="ticketmessage-files"><?php echo GO::t('ticketFiles','defaultsite'); ?>:</span>
							<?php foreach ($message->getFiles() as $file => $obj): ?>
								<a target="_blank" href="<?php echo Site::urlManager()->createUrl('tickets/site/downloadAttachment',array('file'=>$obj->id,'ticket_number'=>$ticket->ticket_number,'ticket_verifier'=>$ticket->ticket_verifier)); ?>">
									<?php echo $file; ?>
								</a>
							<?php endforeach; ?>
					<?php endif; ?>

				<?php if($message->has_status): ?>
					<strong>Status</strong>: <?php echo GO_Tickets_Model_Status::getName($message->status_id); ?>
				<?php endif; ?>
				</td>
			</tr>
			<?php endforeach; ?>
			
		</table>	
			
		<?php if(!$ticket->isClosed()): ?>
		
			<?php $form = new GO_Site_Widget_Form(); ?>
			<?php echo $form->beginForm(false,false,array('enctype'=>'multipart/form-data')); ?>
		
			<?php $uploader = new GO_Site_Widget_Plupload_Widget(); ?>
		
			<h3><?php echo GO::t('ticketYourMessage','defaultsite'); ?></h3>

			<?php echo $form->textArea($new_message,'content',array('required'=>true)); ?>
			<?php echo $uploader->render(); ?>
			<div class="button-bar">
				<?php echo $form->submitButton(GO::t('ticketAddComment','defaultsite'),array('id'=>'submit-ticket-button', 'class'=>'button')); ?>
			</div>
			<?php echo $form->endForm(); ?>
		<?php endif; ?>
	</div>
</div>
