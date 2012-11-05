

<div class="subkader-small-top">
						<div class="subkader-small-bottom">
							<div class="subkader-small-center">		
								
								<h1><?php echo GOS::t('registerTitle'); ?></h1>								
								<p><?php echo GOS::t('registerText'); ?></p>

<div class="form">
<?php echo GO_Sites_Components_Html::beginForm(); ?>

	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($user, 'first_name'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($user, 'first_name'); ?>
		<?php echo GO_Sites_Components_Html::error($user, 'first_name'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($user, 'middle_name'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($user, 'middle_name'); ?>
		<?php echo GO_Sites_Components_Html::error($user, 'middle_name'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($user, 'last_name'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($user, 'last_name'); ?>
		<?php echo GO_Sites_Components_Html::error($user, 'last_name'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'sex'); ?>
		<div class="buttonList">
			<?php echo GO_Sites_Components_Html::activeRadioButtonList($contact, 'sex', array('male'=>GOS::t('male'),'female'=>GOS::t('female')), array('separator'=>'')); ?>
		</div>
		<?php echo GO_Sites_Components_Html::error($contact, 'sex'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($user, 'email'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($user, 'email'); ?>
		<?php echo GO_Sites_Components_Html::error($user, 'email'); ?>
	</div>
	
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'address'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'address'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'address'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'address_no'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'address_no'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'address_no'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'zip'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'zip'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'zip'); ?>
	</div>
	
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'city'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'city'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'city'); ?>
	</div>
	
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'state'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'state'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'state'); ?>
	</div>
	
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'country'); ?>
		<?php echo GO_Sites_Components_Html::activeDropDownList($contact, 'country', GO::language()->getCountries()); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'country'); ?>
	</div>
	
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'home_phone'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'home_phone'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'home_phone'); ?>
	</div>
	
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'cellular'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'cellular'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'cellular'); ?>
	</div>
	
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'company'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'company'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'company'); ?>
	</div>
	
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'department'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'department'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'department'); ?>
	</div>
	
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'function'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($contact, 'function'); ?>
		<?php echo GO_Sites_Components_Html::error($contact, 'function'); ?>
	</div>
	
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'vat_no'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($company, 'vat_no'); ?>
		<?php echo GO_Sites_Components_Html::error($company, 'vat_no'); ?>
	</div>
	
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($user, 'username'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($user, 'username'); ?>
		<?php echo GO_Sites_Components_Html::error($user, 'username'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($user, 'password'); ?>
		<?php echo GO_Sites_Components_Html::activePasswordField($user, 'password'); ?>
		<?php echo GO_Sites_Components_Html::error($user, 'password'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($user, 'passwordConfirm'); ?>
		<?php echo GO_Sites_Components_Html::activePasswordField($user, 'passwordConfirm'); ?>
		<?php echo GO_Sites_Components_Html::error($user, 'passwordConfirm'); ?>
	</div>

	<div class="row buttons">
		<?php echo GO_Sites_Components_Html::submitButton('Register'); ?>
		<?php echo GO_Sites_Components_Html::resetButton('Reset'); ?>
	</div>
	<div style="clear:both;"></div>
<?php echo GO_Sites_Components_Html::endForm(); ?>
						</div>

					</div></div></div>
			

					<div class="subkader-right">
						<h1>Secure login</h1>
						<p>SSL secured connection verified by Equifax Secure Inc. </p>
					</div>
	
