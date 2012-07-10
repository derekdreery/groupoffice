<div class="hoofd-kader-menu">

			<div class="hoofd-tab-left">
				<div class="hoofd-tab-right">
					<a class="hoofd-tab-center" href="#">
						Login
					</a>
				</div>
			</div>

		</div>		
		<div class="hoofd-kader-top"></div>

		<div class="hoofd-kader-center">

						
				<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">						

								<h1><?php echo GOS::t('firstTime'); ?></h1>								
								<a href="<?php echo $this->createUrl("/sites/user/register"); ?>"><?php echo GOS::t('registerOnceClick'); ?></a>
								<h1><?php echo GOS::t('registeredLogin'); ?></h1>
								
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
									<a href="<?php echo $this->createUrl('/sites/user/lostpassword'); ?>"><?php echo GOS::t('lostPassword'); ?>?</a>
							</div>
						</div>

					</div>


					<div class="subkader-right">
						<h1>Secure login</h1>
						<p>SSL secured connection verified by Equifax Secure Inc. </p>
					</div>
					
			</div>
	<div class="hoofd-kader-bottom"></div>	
