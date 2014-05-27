<?php \Site::scripts()->registerCssFile(\Site::file('css/ticket.css')); ?>

<section class="page" id="ticket">

	<div class="external-ticket-page ticket">
		<div class="wrapper">

			<header>
				<h2><?php echo \GO::t('ticket','defaultsite').' '. $ticket->ticket_number; ?></h2>

				<?php if(!$ticket->isNew && !$ticket->isClosed()): ?>
					<form method="POST">
						<input type="hidden" value="close" name="close" />
						<input type="submit" class="green-button" value="Close Ticket" />
					</form>
				<?php endif; ?>
			</header>
			
			<article>
				<?php if(\GO::user()): ?>
					&lt;&lt;&nbsp;<a id="back-to-overview-button" href="<?php echo \Site::urlManager()->createUrl('tickets/externalpage/ticketlist'); ?>"><?php echo \GO::t('ticketBackToList','defaultsite'); ?></a>			
				<?php endif; ?>
				<br /><br />
				
				<h3><?php echo \GO::t('ticketInfo','defaultsite'); ?></h3>

				<table id="ticket-info" summary="Ticket Info">
					<tbody>
						<tr>
							<td><?php echo \GO::t('ticketSubject','defaultsite'); ?></td>
							<td><?php echo $ticket->subject; ?></td>
						</tr>
						<tr>
							<td><?php echo \GO::t('ticketType','defaultsite'); ?></td>
							<td><?php echo $ticket->type->name; ?></td>
						</tr>
						<tr>
							<td><?php echo \GO::t('ticketStatus','defaultsite'); ?></td>
							<td>
								<?php echo $ticket->status_id?$ticket->getStatusName():\GO::t('ticketStatusOpen','defaultsite'); ?>
							</td>
						</tr>
						<tr>
							<td><?php echo \GO::t('ticketPriority','defaultsite'); ?></td>
							<td><?php echo $ticket->priority?\GO::t('yes'):\GO::t('no'); ?></td>
						</tr>
						<tr>
							<td><?php echo \GO::t('ticketDate','defaultsite'); ?></td>
							<td><?php echo $ticket->getAttribute("ctime","formatted"); ?></td>
						</tr>
						<tr>
							<td><?php echo \GO::t('ticketAgent','defaultsite'); ?></td>
							<td><?php echo $ticket->agent?$ticket->agent->name:''; ?></td>
						</tr>

		<!--			
					Example for adding custom field.
					<tr>
						<td><?php //echo $ticket->getCustomfieldsRecord()->getAttributeLabelWithoutCategoryName('col_58'); ?></td>
						<td><?php //echo $ticket->getCustomfieldsRecord()->col_58; ?></td>
					</tr>
		-->
					</tbody>
				</table>


				<h3><?php echo \GO::t('ticketDiscussion','defaultsite'); ?></h3>

				<div id="ticket-discussion">

					<?php foreach($messages as $i => $message): ?>
					<article class="<?php echo ($i%2) ? 'even' : 'odd';; ?>"> 
						<span class="discussion-name"><?php echo $message->posterName; ?></span><span class="discussion-time"><?php echo $message->getAttribute("ctime","formatted"); ?></span>
						<p><?php echo $message->getAttribute("content","html"); ?></p>
						
						<?php if (!empty($message->attachments)): ?>
							<strong>Files:</strong>
							<?php foreach ($message->getFiles() as $file => $obj): ?>
								<a target="_blank" href="<?php echo \Site::urlManager()->createUrl('groupofficecom/ticket/downloadAttachment',array('file'=>$obj->id,'ticket_number'=>$ticket->ticket_number,'ticket_verifier'=>$ticket->ticket_verifier)); ?>">
									<?php echo $file; ?>
								</a>
							<?php endforeach; ?>
						<?php endif; ?>
							
						<?php if($message->has_status): ?>
							<strong>Status</strong>: <?php echo \GO\Tickets\Model\Status::getName($message->status_id); ?>
						<?php endif; ?>
					</article>
					<?php endforeach; ?>

				</div>	

				<?php if(!$ticket->isClosed()): ?>

					<?php $form = new \GO\Site\Widget\Form(); ?>
					<?php echo $form->beginForm(false,false,array('enctype'=>'multipart/form-data')); ?>

					<?php $uploader = new \GO\Site\Widget\Plupload\Widget(); ?>

					<h3><?php echo \GO::t('ticketYourMessage','defaultsite'); ?></h3>

					<?php echo $form->textArea($new_message,'content',array('required'=>true, 'id'=>'add-comment-field')); ?>
					<?php echo $uploader->render(); ?>
					<div class="button-bar">
						<?php echo $form->submitButton(\GO::t('ticketAddComment','defaultsite'), array('id'=>'add-comment-button', 'class'=>'black-button rounded-corners')); ?>
					</div>
					<?php echo $form->endForm(); ?>
				<?php endif; ?>
			
			</article>
				
		</div>
	</div>

</section>