<h1><?php echo GO::t('login', 'sites'); ?></h1>								

<h2><?php echo GO::t('signup', 'sites'); ?></h2>
<a href="<?php echo $this->createUrl('/reservation/front/register'); ?>"><?php echo GO::t('signup', 'sites'); ?></a>

<h2><?php echo GO::t('login', 'sites'); ?></h2>
<p><?php echo GO::t('alreadysignedupText', 'sites'); ?></p>
<div class="form">
<?php echo GO_Sites_Components_Html::beginForm(); ?>	
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
	<?php if(GOS::site()->notifier->hasMessage('error')): ?>
		<div class="errorMessage">
			<?php echo GOS::site()->notifier->getMessage('error'); ?>
		</div>
	<?php endif; ?>
	<div class="row buttons">
		<?php echo GO_Sites_Components_Html::submitButton(GO::t('login', 'sites')); ?>
		<a href="<?php echo $this->createUrl('/sites/site/recoverpassword'); ?>"><?php echo GO::t('forgotPassword', 'sites'); ?></a>
	</div>
<?php echo GO_Sites_Components_Html::endForm(); ?>
	

</div>
