<?php
GOS::site()->scripts->registerScriptFile($this->getTemplateUrl() . 'js/jquery-1.7.2.min.js');
GOS::site()->scripts->registerScriptFile($this->getTemplateUrl() . 'js/profileToggle.js');
?>

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
						<?php echo GO_Sites_Components_Html::activeRadioButtonList($contact, 'sex', array('M' => GOS::t('male'), 'F' => GOS::t('female')), array('separator' => '')); ?>
					</div>
					<?php echo GO_Sites_Components_Html::error($contact, 'sex'); ?>
				</div>
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($user, 'email'); ?>
					<?php echo GO_Sites_Components_Html::activeTextField($user, 'email'); ?>
					<?php echo GO_Sites_Components_Html::error($user, 'email'); ?>
				</div>



				

				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($contact, 'cellular'); ?>
					<?php echo GO_Sites_Components_Html::activeTextField($contact, 'cellular'); ?>
					<?php echo GO_Sites_Components_Html::error($contact, 'cellular'); ?>
				</div>

				<br /><hr />
				<h1>Company details</h1>
				
				
				<div class="row">
					<?php echo GO_Sites_Components_Html::label('Company', "Company_name", array('required' => true)); ?>
					<?php echo GO_Sites_Components_Html::activeTextField($company, 'name'); ?>
					<?php echo GO_Sites_Components_Html::error($company, 'name'); ?>
				</div>
				
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'vat_no'); ?>
					<?php echo GO_Sites_Components_Html::activeTextField($company, 'vat_no'); ?>
					<?php echo GO_Sites_Components_Html::error($company, 'vat_no'); ?>
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
					<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'phone'); ?>
					<?php echo GO_Sites_Components_Html::activeTextField($company, 'phone'); ?>
					<?php echo GO_Sites_Components_Html::error($company, 'phone'); ?>
				</div>
				
				
				<br /><hr />
				<h2><?php echo GOS::t('addressDetails'); ?></h2>
					
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'address'); ?>
					<?php echo GO_Sites_Components_Html::activeTextField($company, 'address'); ?>
					<?php echo GO_Sites_Components_Html::error($company, 'address'); ?>
				</div>
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'address_no'); ?>
					<?php echo GO_Sites_Components_Html::activeTextField($company, 'address_no'); ?>
					<?php echo GO_Sites_Components_Html::error($company, 'address_no'); ?>
				</div>
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'zip'); ?>
					<?php echo GO_Sites_Components_Html::activeTextField($company, 'zip'); ?>
					<?php echo GO_Sites_Components_Html::error($company, 'zip'); ?>
				</div>

				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'city'); ?>
					<?php echo GO_Sites_Components_Html::activeTextField($company, 'city'); ?>
					<?php echo GO_Sites_Components_Html::error($company, 'city'); ?>
				</div>

				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'state'); ?>
					<?php echo GO_Sites_Components_Html::activeTextField($company, 'state'); ?>
					<?php echo GO_Sites_Components_Html::error($company, 'state'); ?>
				</div>

				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'country'); ?>
					<?php echo GO_Sites_Components_Html::activeDropDownList($company, 'country', GO::language()->getCountries()); ?>
					<?php echo GO_Sites_Components_Html::error($company, 'country'); ?>
				</div>
				
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($company, "postAddressIsEqual"); ?>
					<?php echo GO_Sites_Components_Html::activeCheckBox($company, 'postAddressIsEqual'); ?>
				</div>

				<div class="post-address">
					<br /><hr />
					<h2><?php echo GOS::t('postAddressDetails'); ?></h2>




					<div class="row">
						<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'post_address'); ?>
						<?php echo GO_Sites_Components_Html::activeTextField($company, 'post_address'); ?>
						<?php echo GO_Sites_Components_Html::error($company, 'post_address'); ?>
					</div>
					<div class="row">
						<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'post_address_no'); ?>
						<?php echo GO_Sites_Components_Html::activeTextField($company, 'post_address_no'); ?>
						<?php echo GO_Sites_Components_Html::error($company, 'post_address_no'); ?>
					</div>
					<div class="row">
						<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'post_zip'); ?>
						<?php echo GO_Sites_Components_Html::activeTextField($company, 'post_zip'); ?>
						<?php echo GO_Sites_Components_Html::error($company, 'post_zip'); ?>
					</div>

					<div class="row">
						<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'post_city'); ?>
						<?php echo GO_Sites_Components_Html::activeTextField($company, 'post_city'); ?>
						<?php echo GO_Sites_Components_Html::error($company, 'post_city'); ?>
					</div>

					<div class="row">
						<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'post_state'); ?>
						<?php echo GO_Sites_Components_Html::activeTextField($company, 'post_state'); ?>
						<?php echo GO_Sites_Components_Html::error($company, 'post_state'); ?>
					</div>

					<div class="row">
						<?php echo GO_Sites_Components_Html::activeLabelEx($company, 'post_country'); ?>
						<?php echo GO_Sites_Components_Html::activeDropDownList($company, 'post_country', GO::language()->getCountries()); ?>
						<?php echo GO_Sites_Components_Html::error($company, 'post_country'); ?>
					</div>

				</div>


				<br /><hr />
				<h1><?php echo GOS::t('yourlogincredentials'); ?></h1>


				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($user, 'username'); ?>
					<?php echo GO_Sites_Components_Html::activeTextField($user, 'username', array('autocomplete'=>'off')); ?>
					<?php echo GO_Sites_Components_Html::error($user, 'username'); ?>
				</div>
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($user, 'password'); ?>
					<?php echo GO_Sites_Components_Html::activePasswordField($user, 'password', array('autocomplete'=>'off')); ?>
					<?php echo GO_Sites_Components_Html::error($user, 'password'); ?>
				</div>
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($user, 'passwordConfirm'); ?>
					<?php echo GO_Sites_Components_Html::activePasswordField($user, 'passwordConfirm', array('autocomplete'=>'off')); ?>
					<?php echo GO_Sites_Components_Html::error($user, 'passwordConfirm'); ?>
				</div>

				<div class="row buttons">
					<?php echo GO_Sites_Components_Html::submitButton('Register'); ?>
					<?php echo GO_Sites_Components_Html::resetButton('Reset'); ?>
				</div>
				<div style="clear:both;"></div>
				<?php echo GO_Sites_Components_Html::endForm(); ?>
			</div>

		</div>
	</div>
</div>


<div class="subkader-right">
	<h1>Secure login</h1>
	<p>SSL secured connection verified by Equifax Secure Inc. </p>
</div>

