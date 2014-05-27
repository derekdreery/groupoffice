<?php \Site::scripts()->registerCssFile(\Site::file('css/ticket.css')); ?>

<section class="page" id="ticket">

<div class="external-ticket-page newticket ticket">
	<div class="wrapper">
		
		<section>
		
			<header>
				<h2><?php echo \GO::t('ticketNewTicket','defaultsite'); ?></h2>
			</header>

			<?php if(\GO::user()): ?>
				&lt;&lt;&nbsp;<a id="back-to-overview-button" href="<?php echo \Site::urlManager()->createUrl('tickets/externalpage/ticketlist'); ?>"><?php echo \GO::t('ticketBackToList','defaultsite'); ?></a>			
			<?php endif; ?>

			<article>

				<?php
				$form = new \GO\Site\Widget\Form();
				$form->afterRequiredLabel = '&nbsp;*';
				?>
				<?php echo $form->beginForm(false,false,array('enctype'=>'multipart/form-data')); ?>

				<table id="ticket-info" summary="Ticket Info">
					<tbody>
						<tr>
							<td width="150"><?php echo $form->label($ticket, 'subject',array('label'=>\GO::t('ticketSubject','defaultsite'))); ?></td>
							<td><?php echo $form->textField($ticket, 'subject'); ?></td>
						</tr>
						<tr>
							<td><?php echo $form->label($ticket, 'type_id',array('label'=>\GO::t('ticketType','defaultsite'))); ?></td>
							<td><?php echo $form->dropDownList($ticket, 'type_id', $form->listData($ticketTypes, 'id', 'name')); ?></td>
						</tr>
						<tr>
							<td><?php echo $form->label($ticket, 'status',array('label'=>\GO::t('ticketStatus','defaultsite'))); ?></td>
							<td><?php echo \GO::t('ticketStatusOpen','defaultsite'); ?></td>
						</tr>
						<tr>
							<td><?php echo $form->label($ticket, 'priority',array('label'=>\GO::t('ticketPriority','defaultsite'))); ?></td>
							<td><?php echo $form->checkBox($ticket, 'priority'); ?></td>
						</tr>

						<!--			
						Example on how to add a custom field
						<tr>
							<td><?php // echo $form->label($ticket->customfieldsRecord, 'col_58'); ?></td>
							<td><?php // echo $form->textField($ticket->customfieldsRecord, 'col_58'); ?></td>
						</tr>
						-->
					</tbody>
				</table>


				<?php if(!$ticket->isClosed()): ?>
					<fieldset>
						<?php $uploader = new \GO\Site\Widget\Plupload\Widget(); ?>

						<legend><?php echo \GO::t('ticketYourMessage','defaultsite'); ?></legend>

						<?php echo $form->textArea($message,'content',array('required'=>true, 'id'=>'add-comment-field')); ?>
						<?php echo $uploader->render(); ?>
						<?php echo $form->submitButton($ticket->isNew?'Send':'Add Comment', array('id'=>'add-comment-button', 'class'=>'black-button rounded-corners')); ?>
					</fieldset>
				<?php endif; ?>

				<?php echo $form->endForm(); ?>

			</article>
			
		</section>
	</div>
</div>
	
</section>