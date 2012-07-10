<h1><?php echo $this->getPageTitle(); ?></h1>	
<?php if(GOS::site()->notifier->hasMessage('success')): ?>
	<p class="successMessage"><?php echo GOS::site()->notifier->getMessage('success'); ?></p>
<?php elseif(GOS::site()->notifier->hasMessage('error')): ?>
	<p class="errorMessage"><?php echo GOS::site()->notifier->getMessage('error'); ?></p>
<?php endif; ?>


<div class="form">
<?php echo GO_Sites_Components_Html::beginForm(); ?>
	<h2>Mijn gegevens</h2>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'first_name'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($model, 'first_name'); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'first_name'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'middle_name'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($model, 'middle_name'); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'middle_name'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'last_name'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($model, 'last_name'); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'last_name'); ?>
	</div>
	<h2>Wachtwoord wijzigen</h2>
	<div class="row">
		<?php echo GO_Sites_Components_Html::label('Huidige wachtwoord', false); ?>
		<?php echo GO_Sites_Components_Html::passwordField('currentPassword'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabel($model, 'password'); ?>
		<?php echo GO_Sites_Components_Html::passwordField('GO_Base_Model_User[password]', ''); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::label(GO::t('passwordConfirm'), null); ?>
		<?php echo GO_Sites_Components_Html::activePasswordField($model, 'passwordConfirm'); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'passwordConfirm'); ?>
	</div>
	<h2>Adres details</h2>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'email'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($model, 'email'); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'email'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'address'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'address'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'address'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'address_no'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'address_no'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'address_no'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'city'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'city'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'city'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'zip'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'zip'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'zip'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'home_phone'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'home_phone'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'home_phone'); ?>
	</div>
	<div class="row checkbox">
		<?php $hasNewsletter = array_key_exists(1, $contact->addresslists->fetchKeyValueArray('id','name')); ?>
		<?php echo GO_Sites_Components_Html::hiddenField('address_list_id', 1); //Address list to add use to when checkbox is checked ?>
		<?php echo GO_Sites_Components_Html::checkBox('newsletter', $hasNewsletter); ?>
		<?php echo GO_Sites_Components_Html::label('Mij aanmelden voor de nieuwsbrief', 'newsletter'); ?>
	</div>

	<div class="row buttons">
		<?php echo GO_Sites_Components_Html::submitButton('Opslaan'); ?>
	</div>
	<h2>Mijn reserveringen</h2>
	<table>
		<tr>
			<th>Nummer</th>
			<th>Datum</th>
			<th>Kosten</th>
			<th>Status</th>
		</tr>
		<?php foreach($reservations as $reservation): ?>
		<?php $reservation->plannings = $reservation->plannings_statement->fetchAll(); //fetch plannings for getPrice() ?>
		<tr>
			<td><?php echo GO_Sites_Components_Html::link($reservation->getNumber(), $this->createUrl('reservation/front/reservation', array('id'=>$reservation->id))); ?></td>
			<td><?php echo $reservation->getDateText(); ?></td>
			<td><?php echo $reservation->getPriceText(); ?></td>
			<td><?php echo $reservation->getStatusText(); ?>
			<?php echo GO_Sites_Components_Html::link('Afdrukken', $this->createUrl('reservation/front/print', array('id'=>$reservation->id))); ?></td>
		</tr>
		<?php endforeach; ?>

	</table>

<?php echo GO_Sites_Components_Html::endForm(); ?>
</div>