<h1><?php echo $this->getPageTitle(); ?></h1>	

<div class="form">
<?php echo GO_Sites_Components_Html::beginForm(); ?>
	<h2>Uw gegevens</h2>
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
	<h2>Login informatie</h2>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'username'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($model, 'username'); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'username'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'password'); ?>
		<?php echo GO_Sites_Components_Html::activePasswordField($model, 'password'); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'password'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::label(GO::t('passwordConfirm'), null); ?>
		<?php echo GO_Sites_Components_Html::activePasswordField($model, 'passwordConfirm'); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'passwordConfirm'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'email'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($model, 'email'); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'email'); ?>
	</div>
	<h2>Contact informatie</h2>
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

	<div class="row buttons">
		<?php echo GO_Sites_Components_Html::submitButton('Register'); ?>
	</div>

<?php echo GO_Sites_Components_Html::endForm(); ?>
</div>