<h1>Wachtwoord vergeten</h1>								
<p>Vul uw email adres in het onderstaande formulier in en u ontvangt een link 
	om uw wachtwoord te herstellen binnen enkele minuten.</p>
<div class="form">
	
	<?php if(GOS::site()->notifier->hasMessage('success')): ?>
			<p class="successMessage"><?php echo GOS::site()->notifier->getMessage('success') ?></p>
		<?php else: ?>
	
	<?php echo GO_Sites_Components_Html::beginForm(); ?>	
		<div class="row">
			<?php echo GO_Sites_Components_Html::label('Email', null); ?>
			<?php echo GO_Sites_Components_Html::textField('email'); ?>
			<?php if(GOS::site()->notifier->hasMessage('error')): ?>
				<div class="errorMessage"><?php echo GOS::site()->notifier->getMessage('error'); ?></div>
			<?php endif; ?>
		</div>
		<div class="row buttons">
			<?php echo GO_Sites_Components_Html::submitButton('Verzenden', array('class'=>'btn btn-primary')); ?>
		</div>
	<?php echo GO_Sites_Components_Html::endForm(); ?>

<?php endif; ?>
</div>