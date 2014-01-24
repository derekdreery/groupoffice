<h1><?php echo $this->getPageTitle(); ?></h1>	

<div class="form">
<?php echo \GO\Sites\Components\Html::beginForm(); ?>
	<h2>Uw gegevens</h2>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'first_name'); ?>
		<?php echo \GO\Sites\Components\Html::activeTextField($model, 'first_name'); ?>
		<?php echo \GO\Sites\Components\Html::error($model, 'first_name'); ?>
	</div>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'middle_name'); ?>
		<?php echo \GO\Sites\Components\Html::activeTextField($model, 'middle_name'); ?>
		<?php echo \GO\Sites\Components\Html::error($model, 'middle_name'); ?>
	</div>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'last_name'); ?>
		<?php echo \GO\Sites\Components\Html::activeTextField($model, 'last_name'); ?>
		<?php echo \GO\Sites\Components\Html::error($model, 'last_name'); ?>
	</div>
	<h2>Login informatie</h2>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'username'); ?>
		<?php echo \GO\Sites\Components\Html::activeTextField($model, 'username'); ?>
		<?php echo \GO\Sites\Components\Html::error($model, 'username'); ?>
	</div>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'password'); ?>
		<?php echo \GO\Sites\Components\Html::activePasswordField($model, 'password'); ?>
		<?php echo \GO\Sites\Components\Html::error($model, 'password'); ?>
	</div>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::label(\GO::t('passwordConfirm'), null); ?>
		<?php echo \GO\Sites\Components\Html::activePasswordField($model, 'passwordConfirm'); ?>
		<?php echo \GO\Sites\Components\Html::error($model, 'passwordConfirm'); ?>
	</div>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'email'); ?>
		<?php echo \GO\Sites\Components\Html::activeTextField($model, 'email'); ?>
		<?php echo \GO\Sites\Components\Html::error($model, 'email'); ?>
	</div>
	<h2>Contact informatie</h2>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'address'); ?>
		<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'address'); ?>
		<?php echo \GO\Sites\Components\Html::error($contact, 'address'); ?>
	</div>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'address_no'); ?>
		<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'address_no'); ?>
		<?php echo \GO\Sites\Components\Html::error($contact, 'address_no'); ?>
	</div>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'city'); ?>
		<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'city'); ?>
		<?php echo \GO\Sites\Components\Html::error($contact, 'city'); ?>
	</div>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'zip'); ?>
		<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'zip'); ?>
		<?php echo \GO\Sites\Components\Html::error($contact, 'zip'); ?>
	</div>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'home_phone'); ?>
		<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'home_phone'); ?>
		<?php echo \GO\Sites\Components\Html::error($contact, 'home_phone'); ?>
	</div>

	<div class="row buttons">
		<?php echo \GO\Sites\Components\Html::submitButton('Register'); ?>
	</div>

<?php echo \GO\Sites\Components\Html::endForm(); ?>
</div>