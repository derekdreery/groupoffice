
			<?php echo $form->hiddenField($ticket, 'ticket_number'); ?>

			<div class="row">
				<?php echo $form->label($ticket, 'subject'); ?>
				<?php echo $form->textField($ticket, 'subject'); ?>
				<?php echo $form->error($ticket, 'subject'); ?>
			</div>
			<div class="row">
				<?php echo $form->label($ticket, 'type_id'); ?>
				<?php echo $form->dropDownList($ticket, 'type_id', $form->listData($ticketTypes, 'id', 'name')); ?>
				<?php echo $form->error($ticket, 'type_id'); ?>
			</div>
			<div class="row">
				<?php echo $form->label($ticket, 'first_name'); ?>
				<?php echo $form->textField($ticket, 'first_name'); ?>
				<?php echo $form->error($ticket, 'first_name'); ?>
			</div>
			<div class="row">
				<?php echo $form->label($ticket, 'middle_name'); ?>
				<?php echo $form->textField($ticket, 'middle_name'); ?>
				<?php echo $form->error($ticket, 'middle_name'); ?>
			</div>
			<div class="row">
				<?php echo $form->label($ticket, 'last_name'); ?>
				<?php echo $form->textField($ticket, 'last_name'); ?>
				<?php echo $form->error($ticket, 'last_name'); ?>
			</div>
			<div class="row">
				<?php echo $form->label($ticket, 'email'); ?>
				<?php echo $form->textField($ticket, 'email'); ?>
				<?php echo $form->error($ticket, 'email'); ?>
			</div>
			<div class="row">
				<?php echo $form->label($ticket, 'phone'); ?>
				<?php echo $form->textField($ticket, 'phone'); ?>
				<?php echo $form->error($ticket, 'phone'); ?>
			</div>
