<?php
$form = new \GO\Sites\Widgets\Form();
?>


<?php
if ($contact->id):
	?>
	<h1>Thank you!</h1>
	<p>Thank you! We received your details</p>

<?php else: ?>
	<p>Please fill in the form to contact us.</p>
	<div class="form">
		<?php echo \GO\Sites\Components\Html::beginForm(); ?>

		<?php
		$contact->addressbook_id = 1;
		echo \GO\Sites\Components\Html::activeHiddenField($contact, 'addressbook_id');
		?>

		<div class="row">
			<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'first_name'); ?>
			<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'first_name'); ?>
			<?php echo \GO\Sites\Components\Html::error($contact, 'first_name'); ?>
		</div>

		<div class="row">
			<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'middle_name'); ?>
			<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'middle_name'); ?>
			<?php echo \GO\Sites\Components\Html::error($contact, 'middle_name'); ?>
		</div>


		<div class="row">
			<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'last_name'); ?>
			<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'last_name'); ?>
			<?php echo \GO\Sites\Components\Html::error($contact, 'last_name'); ?>
		</div>
		<div class="row">
			<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'email'); ?>
			<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'email'); ?>
			<?php echo \GO\Sites\Components\Html::error($contact, 'email'); ?>
		</div><div class="row">
			<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'address'); ?>
			<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'address'); ?>
			<?php echo \GO\Sites\Components\Html::error($contact, 'address'); ?>
		</div><div class="row">
			<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'address_no'); ?>
			<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'address_no'); ?>
			<?php echo \GO\Sites\Components\Html::error($contact, 'address_no'); ?>
		</div><div class="row">
			<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'city'); ?>
			<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'city'); ?>
			<?php echo \GO\Sites\Components\Html::error($contact, 'city'); ?>
		</div><div class="row">
			<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'state'); ?>
			<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'state'); ?>
			<?php echo \GO\Sites\Components\Html::error($contact, 'state'); ?>
		</div><div class="row">
			<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'zip'); ?>
			<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'zip'); ?>
			<?php echo \GO\Sites\Components\Html::error($contact, 'zip'); ?>
		</div>
		<div class="row">
			<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'country'); ?>
			<?php echo \GO\Sites\Components\Html::activeDropDownList($contact, 'country', \GO::language()->getCountries()); ?>
			<?php echo \GO\Sites\Components\Html::error($contact, 'country'); ?>
		</div>
		<div class="row">
			<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'comment'); ?>
			<?php echo \GO\Sites\Components\Html::activeTextArea($contact, 'comment'); ?>
			<?php echo \GO\Sites\Components\Html::error($contact, 'comment'); ?>
		</div>
		<div class="row buttons">
			<?php echo \GO\Sites\Components\Html::submitButton('Send'); ?>
			<?php echo \GO\Sites\Components\Html::resetButton('Reset'); ?>
		</div>
		<div style="clear:both;"></div>
		<?php echo \GO\Sites\Components\Html::endForm(); ?>
	</div>

<?php endif; ?>

