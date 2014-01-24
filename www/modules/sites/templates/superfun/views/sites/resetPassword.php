<?php if(GOS::site()->notifier->hasMessage('success')): ?>
	<h1>Wachtwoord gewijzigd</h1>								
	<p><?php echo GOS::site()->notifier->getMessage('success'); ?></p>
	<p>Klik <a href="<?php echo $this->createUrl('/sites/site/login'); ?>">Hier</a> om in te loggen.</p>
<?php elseif(GOS::site()->notifier->hasMessage('error')): ?>
	<p class="errorMessage">
		<?php echo GOS::site()->notifier->getMessage('error'); ?>
	</p>
<?php else: ?>
	<h1>Wachtwoord wijzigen</h1>								
	<p>Gebruik het onderstaand formulier om uw wachtwoord te wijzigen.</p>
	<div class="form">
	<?php echo \GO\Sites\Components\Html::beginForm(); ?>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::activeLabel($user, 'password'); ?>
		<?php echo \GO\Sites\Components\Html::activePasswordField($user, 'password'); ?>
	</div>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::label(\GO::t('passwordConfirm'), null); ?>
		<?php echo \GO\Sites\Components\Html::activePasswordField($user, 'passwordConfirm'); ?>
		<?php echo \GO\Sites\Components\Html::error($user, 'passwordConfirm'); ?>
  </div>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::submitButton('Wijzigen', array('class'=>'btn btn-primary')); ?>
		<?php echo \GO\Sites\Components\Html::resetButton('Reset', array('class'=>'btn')); ?>
  </div>
	<?php echo \GO\Sites\Components\Html::endForm(); ?>
	</div>
<?php endif; ?>