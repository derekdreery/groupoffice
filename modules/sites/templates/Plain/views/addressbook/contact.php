<?php
$form = new GO_Sites_Widgets_Form();
?>


<?php
if ($contact->id):
	?>
	<h1>Thank you!</h1>
	<p>Thank you! We received your details</p>

<?php else: ?>
	<p>Please fill in the form to contact us.</p>
	<div class="form">
		<?php echo GO_Sites_Components_Html::beginForm(); ?>

		<?php
		$contact->addressbook_id = 1;
		echo GO_Sites_Components_Html::activeHiddenField($contact, 'addressbook_id');
		?>

		<div class="row">
			<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'first_name'); ?>
			<?php echo GO_Sites_Components_Html::activeTextField($contact, 'first_name'); ?>
			<?php echo GO_Sites_Components_Html::error($contact, 'first_name'); ?>
		</div>

		<div class="row">
			<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'middle_name'); ?>
			<?php echo GO_Sites_Components_Html::activeTextField($contact, 'middle_name'); ?>
			<?php echo GO_Sites_Components_Html::error($contact, 'middle_name'); ?>
		</div>


		<div class="row">
			<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'last_name'); ?>
			<?php echo GO_Sites_Components_Html::activeTextField($contact, 'last_name'); ?>
			<?php echo GO_Sites_Components_Html::error($contact, 'last_name'); ?>
		</div>
		<div class="row">
			<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'email'); ?>
			<?php echo GO_Sites_Components_Html::activeTextField($contact, 'email'); ?>
			<?php echo GO_Sites_Components_Html::error($contact, 'email'); ?>
		</div><div class="row">
			<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'address'); ?>
			<?php echo GO_Sites_Components_Html::activeTextField($contact, 'address'); ?>
			<?php echo GO_Sites_Components_Html::error($contact, 'address'); ?>
		</div><div class="row">
			<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'address_no'); ?>
			<?php echo GO_Sites_Components_Html::activeTextField($contact, 'address_no'); ?>
			<?php echo GO_Sites_Components_Html::error($contact, 'address_no'); ?>
		</div><div class="row">
			<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'city'); ?>
			<?php echo GO_Sites_Components_Html::activeTextField($contact, 'city'); ?>
			<?php echo GO_Sites_Components_Html::error($contact, 'city'); ?>
		</div><div class="row">
			<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'state'); ?>
			<?php echo GO_Sites_Components_Html::activeTextField($contact, 'state'); ?>
			<?php echo GO_Sites_Components_Html::error($contact, 'state'); ?>
		</div><div class="row">
			<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'zip'); ?>
			<?php echo GO_Sites_Components_Html::activeTextField($contact, 'zip'); ?>
			<?php echo GO_Sites_Components_Html::error($contact, 'zip'); ?>
		</div>
		<div class="row">
			<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'country'); ?>
			<?php echo GO_Sites_Components_Html::activeDropDownList($contact, 'country', GO::language()->getCountries()); ?>
			<?php echo GO_Sites_Components_Html::error($contact, 'country'); ?>
		</div>
		<div class="row">
			<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'comment'); ?>
			<?php echo GO_Sites_Components_Html::activeTextArea($contact, 'comment'); ?>
			<?php echo GO_Sites_Components_Html::error($contact, 'comment'); ?>
		</div>
		<div class="row buttons">
			<?php echo GO_Sites_Components_Html::submitButton('Send'); ?>
			<?php echo GO_Sites_Components_Html::resetButton('Reset'); ?>
		</div>
		<div style="clear:both;"></div>
		<?php echo GO_Sites_Components_Html::endForm(); ?>
	</div>

<?php endif; ?>

