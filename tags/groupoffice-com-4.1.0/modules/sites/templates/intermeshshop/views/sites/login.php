					
<div class="subkader-small-top">
		<div class="subkader-small-bottom">
			<div class="subkader-small-center">						

				<h1><?php echo GOS::t('firstTime'); ?></h1>								
				<a href="<?php echo $this->createUrl("/sites/site/register"); ?>"><?php echo GOS::t('registerOnceClick'); ?></a>
				<h1><?php echo GOS::t('registeredLogin'); ?></h1>
				
				<?php
				if (GOS::site()->notifier->hasMessage('error')) {
					echo '<div class="notification notice-error">' . GOS::site()->notifier->getMessage('error') . '</div>';
				}
				?>

				<?php echo GO_Sites_Components_Html::beginForm(); ?>	

				<div class="row formrow">					
					<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'username'); ?>
					<?php echo GO_Sites_Components_Html::activeTextField($model, 'username'); ?>
					<?php echo GO_Sites_Components_Html::error($model, 'username'); ?>
				</div>
				<div class="row formrow">
					<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'password'); ?>
					<?php echo GO_Sites_Components_Html::activePasswordField($model, 'password'); ?>
					<?php echo GO_Sites_Components_Html::error($model, 'password'); ?>
				</div>					
				<div class="row buttons">
					<?php echo GO_Sites_Components_Html::submitButton('OK'); ?>
					<?php echo GO_Sites_Components_Html::resetButton('Reset'); ?>
				</div>
				<?php echo GO_Sites_Components_Html::endForm(); ?>
				<div style="clear:both;"></div>
					<a href="<?php echo $this->createUrl('/sites/site/recoverpassword'); ?>"><?php echo GOS::t('lostPassword'); ?>?</a>
			</div>
		</div>

	</div>


	<div class="subkader-right">
		<h1>Secure login</h1>
		<p>SSL secured connection verified by Equifax Secure Inc. </p>
	</div>

