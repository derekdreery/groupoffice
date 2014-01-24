
				<h1><?php echo GOS::t('registeredLogin'); ?></h1>
				
				<?php
				if (GOS::site()->notifier->hasMessage('error')) {
					echo '<div class="notification notice-error">' . GOS::site()->notifier->getMessage('error') . '</div>';
				}
				?>

				<?php echo \GO\Sites\Components\Html::beginForm(); ?>	

				<div class="row formrow">					
					<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'username'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($model, 'username'); ?>
					<?php echo \GO\Sites\Components\Html::error($model, 'username'); ?>
				</div>
				<div class="row formrow">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'password'); ?>
					<?php echo \GO\Sites\Components\Html::activePasswordField($model, 'password'); ?>
					<?php echo \GO\Sites\Components\Html::error($model, 'password'); ?>
				</div>					
				<div class="row buttons">
					<?php echo \GO\Sites\Components\Html::submitButton('OK'); ?>
					<?php echo \GO\Sites\Components\Html::resetButton('Reset'); ?>
				</div>
				<?php echo \GO\Sites\Components\Html::endForm(); ?>
				<div style="clear:both;"></div>
					<a href="<?php echo $this->createUrl('/sites/site/lostpassword'); ?>"><?php echo GOS::t('lostPassword'); ?>?</a>



<?php if(GOS::site()->config->tickets_allow_anonymous === true): ?>

				

				<h1><?php echo GOS::t('tickets_CreateWithoutLogin'); ?></h1>						
				<p><?php echo sprintf(GOS::t('tickets_ClickCreateWithoutLogin'),$this->createUrl('/tickets/site/newTicket')) ?></p>


<?php endif; ?>

