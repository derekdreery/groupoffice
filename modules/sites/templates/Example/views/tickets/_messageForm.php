<div class="subkader-big-top">
	<div class="subkader-big-bottom">
		<div class="subkader-big-center">		
			
			<?php if($ticket->status_id != GO_Tickets_Model_Ticket::STATUS_CLOSED): ?>
				<div class="row">
					<?php echo $form->label($message,'content',array('required'=>true)); ?>
					<?php echo $form->textArea($message,'content',array('cols'=>95,'required'=>true, 'class'=>'message-field')); ?>
					<?php echo $form->error($message,'content'); ?>
				</div>
				<div class="row">
					<?php $uploader->render(); ?>
				</div>
			<?php echo $form->submitButton('Send'); ?>
			<?php echo GO::user()?'<a href="'.$this->createUrl('/tickets/site/ticketlist').'" class="button-to-ticketlist"><input type="button" value="To ticketlist"></button></a>':''; ?>
			
			<?php echo !$ticket->isNew?$form->submitButton('Close Ticket',array('name'=>'CloseTicket', 'class'=>'button-close-ticket')):''; ?>
			<?php else: ?>

				<p><?php echo GOS::t('tickets_ticketIsClosed'); ?></p>
				<?php echo GO::user()?'<a href="'.$this->createUrl('/tickets/site/ticketlist').'" class="button-to-ticketlist"><input type="button" value="To ticketlist"></button></a>':''; ?>
			<?php endif; ?>

		</div>
	</div>
</div>