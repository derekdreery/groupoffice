<?php if(GOS::site()->notifier->hasMessage('success')): ?>
	<h1>Wachtwoord gewijzigd</h1>								
	<p><?php echo GOS::site()->notifier->getMessage('success'); ?></p>
	<p><a href="<?php echo $this->createUrl('/sites/site/login'); ?>"><?php echo GO::t('login','sites'); ?></a></p>
<?php elseif(GOS::site()->notifier->hasMessage('error')): ?>
	<p class="errorMessage">
		<?php echo GOS::site()->notifier->getMessage('error'); ?>
	</p>
<?php else: ?>
	<h1><?php echo GO::t('changePassword','sites'); ?></h1>								
	<p><?php echo GO::t('changePasswordText','sites'); ?></p>
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
		<?php echo GO_Sites_Components_Html::submitButton(GO::t('submit','sites')); ?>
		<?php echo GO_Sites_Components_Html::resetButton('Reset'); ?>
  </div>
	<?php echo GO_Sites_Components_Html::endForm(); ?>
	</div>
<?php endif; ?>