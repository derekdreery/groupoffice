<div class="subkader-small-top">
		<div class="subkader-small-bottom">
			<div class="subkader-small-center">						
				<h1><?php echo GO::t('forgotPassword', 'sites'); ?></h1>								
				<p><?php echo GO::t('forgotPasswordText', 'sites'); ?></p>
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
							<?php echo GO_Sites_Components_Html::submitButton(GO::t('submit', 'sites')); ?>
						</div>
					<?php echo GO_Sites_Components_Html::endForm(); ?>

				<?php endif; ?>
							
							<div style="clear:both"></div>
				</div>
			</div>
		</div>
</div>