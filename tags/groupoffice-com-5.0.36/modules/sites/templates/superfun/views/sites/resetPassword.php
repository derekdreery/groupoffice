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
	<?php echo GO_Sites_Components_Html::beginForm(); ?>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabel($user, 'password'); ?>
		<?php echo GO_Sites_Components_Html::activePasswordField($user, 'password'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::label(GO::t('passwordConfirm'), null); ?>
		<?php echo GO_Sites_Components_Html::activePasswordField($user, 'passwordConfirm'); ?>
		<?php echo GO_Sites_Components_Html::error($user, 'passwordConfirm'); ?>
  </div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::submitButton('Wijzigen', array('class'=>'btn btn-primary')); ?>
		<?php echo GO_Sites_Components_Html::resetButton('Reset', array('class'=>'btn')); ?>
  </div>
	<?php echo GO_Sites_Components_Html::endForm(); ?>
	</div>
<?php endif; ?>