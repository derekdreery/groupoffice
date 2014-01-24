<h1>Wachtwoord vergeten</h1>								
<p>Vul uw email adres in het onderstaande formulier in en u ontvangt een link 
	om uw wachtwoord te herstellen binnen enkele minuten.</p>
<div class="form">
	
	<?php if(GOS::site()->notifier->hasMessage('success')): ?>
			<p class="successMessage"><?php echo GOS::site()->notifier->getMessage('success') ?></p>
		<?php else: ?>
	
	<?php echo \GO\Sites\Components\Html::beginForm(); ?>	
		<div class="row">
			<?php echo \GO\Sites\Components\Html::label('Email', null); ?>
			<?php echo \GO\Sites\Components\Html::textField('email'); ?>
			<?php if(GOS::site()->notifier->hasMessage('error')): ?>
				<div class="errorMessage"><?php echo GOS::site()->notifier->getMessage('error'); ?></div>
			<?php endif; ?>
		</div>
		<div class="row buttons">
			<?php echo \GO\Sites\Components\Html::submitButton('Verzenden', array('class'=>'btn btn-primary')); ?>
		</div>
	<?php echo \GO\Sites\Components\Html::endForm(); ?>

<?php endif; ?>
</div>