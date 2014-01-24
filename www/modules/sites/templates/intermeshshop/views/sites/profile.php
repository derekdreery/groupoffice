<?php
GOS::site()->scripts->registerScriptFile($this->getTemplateUrl() . 'js/jquery-1.7.2.min.js');
GOS::site()->scripts->registerScriptFile($this->getTemplateUrl() . 'js/profileToggle.js');
?>

<div class="subkader-small-top">
	<div class="subkader-small-bottom">
		<div class="subkader-small-center">						

			<h1><?php echo GOS::t('profileTitle'); ?></h1>								
			<p><?php echo GOS::t('profileText'); ?></p>

			<?php
			if (GOS::site()->notifier->hasMessage('success')) {
				echo '<div class="notification notice-ok">' . GOS::site()->notifier->getMessage('success') . '</div>';
			} else if (GOS::site()->notifier->hasMessage('error')) {
				echo '<div class="notification notice-error">' . GOS::site()->notifier->getMessage('error') . '</div>';
			}
			?>

			<div class="form">
				<?php echo \GO\Sites\Components\Html::beginForm(); ?>

				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($user, 'first_name'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($user, 'first_name'); ?>
					<?php echo \GO\Sites\Components\Html::error($user, 'first_name'); ?>
				</div>
				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($user, 'middle_name'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($user, 'middle_name'); ?>
					<?php echo \GO\Sites\Components\Html::error($user, 'middle_name'); ?>
				</div>
				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($user, 'last_name'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($user, 'last_name'); ?>
					<?php echo \GO\Sites\Components\Html::error($user, 'last_name'); ?>
				</div>
				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'sex'); ?>
					<div class="buttonList">
						<?php echo \GO\Sites\Components\Html::activeRadioButtonList($contact, 'sex', array('M' => GOS::t('male'), 'F' => GOS::t('female')), array('separator' => '')); ?>
					</div>
					<?php echo \GO\Sites\Components\Html::error($contact, 'sex'); ?>
				</div>
				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($user, 'email'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($user, 'email'); ?>
					<?php echo \GO\Sites\Components\Html::error($user, 'email'); ?>
				</div>



				

				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'cellular'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'cellular'); ?>
					<?php echo \GO\Sites\Components\Html::error($contact, 'cellular'); ?>
				</div>

				<br /><hr />
				<h1>Company details</h1>
				
				
				<div class="row">
					<?php echo \GO\Sites\Components\Html::label('Company', "Company_name", array('required' => true)); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($company, 'name'); ?>
					<?php echo \GO\Sites\Components\Html::error($company, 'name'); ?>
				</div>
				
				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($company, 'vat_no'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($company, 'vat_no'); ?>
					<?php echo \GO\Sites\Components\Html::error($company, 'vat_no'); ?>
				</div>

				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'department'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'department'); ?>
					<?php echo \GO\Sites\Components\Html::error($contact, 'department'); ?>
				</div>

				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($contact, 'function'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($contact, 'function'); ?>
					<?php echo \GO\Sites\Components\Html::error($contact, 'function'); ?>
				</div>
				
				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($company, 'phone'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($company, 'phone'); ?>
					<?php echo \GO\Sites\Components\Html::error($company, 'phone'); ?>
				</div>
				
				
				<br /><hr />
				<h2><?php echo GOS::t('addressDetails'); ?></h2>
					
				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($company, 'address'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($company, 'address'); ?>
					<?php echo \GO\Sites\Components\Html::error($company, 'address'); ?>
				</div>
				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($company, 'address_no'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($company, 'address_no'); ?>
					<?php echo \GO\Sites\Components\Html::error($company, 'address_no'); ?>
				</div>
				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($company, 'zip'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($company, 'zip'); ?>
					<?php echo \GO\Sites\Components\Html::error($company, 'zip'); ?>
				</div>

				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($company, 'city'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($company, 'city'); ?>
					<?php echo \GO\Sites\Components\Html::error($company, 'city'); ?>
				</div>

				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($company, 'state'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($company, 'state'); ?>
					<?php echo \GO\Sites\Components\Html::error($company, 'state'); ?>
				</div>

				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($company, 'country'); ?>
					<?php echo \GO\Sites\Components\Html::activeDropDownList($company, 'country', \GO::language()->getCountries()); ?>
					<?php echo \GO\Sites\Components\Html::error($company, 'country'); ?>
				</div>
				
				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($company, "postAddressIsEqual"); ?>
					<?php echo \GO\Sites\Components\Html::activeCheckBox($company, 'postAddressIsEqual'); ?>
				</div>

				<div class="post-address">
					<br /><hr />
					<h2><?php echo GOS::t('postAddressDetails'); ?></h2>




					<div class="row">
						<?php echo \GO\Sites\Components\Html::activeLabelEx($company, 'post_address'); ?>
						<?php echo \GO\Sites\Components\Html::activeTextField($company, 'post_address'); ?>
						<?php echo \GO\Sites\Components\Html::error($company, 'post_address'); ?>
					</div>
					<div class="row">
						<?php echo \GO\Sites\Components\Html::activeLabelEx($company, 'post_address_no'); ?>
						<?php echo \GO\Sites\Components\Html::activeTextField($company, 'post_address_no'); ?>
						<?php echo \GO\Sites\Components\Html::error($company, 'post_address_no'); ?>
					</div>
					<div class="row">
						<?php echo \GO\Sites\Components\Html::activeLabelEx($company, 'post_zip'); ?>
						<?php echo \GO\Sites\Components\Html::activeTextField($company, 'post_zip'); ?>
						<?php echo \GO\Sites\Components\Html::error($company, 'post_zip'); ?>
					</div>

					<div class="row">
						<?php echo \GO\Sites\Components\Html::activeLabelEx($company, 'post_city'); ?>
						<?php echo \GO\Sites\Components\Html::activeTextField($company, 'post_city'); ?>
						<?php echo \GO\Sites\Components\Html::error($company, 'post_city'); ?>
					</div>

					<div class="row">
						<?php echo \GO\Sites\Components\Html::activeLabelEx($company, 'post_state'); ?>
						<?php echo \GO\Sites\Components\Html::activeTextField($company, 'post_state'); ?>
						<?php echo \GO\Sites\Components\Html::error($company, 'post_state'); ?>
					</div>

					<div class="row">
						<?php echo \GO\Sites\Components\Html::activeLabelEx($company, 'post_country'); ?>
						<?php echo \GO\Sites\Components\Html::activeDropDownList($company, 'post_country', \GO::language()->getCountries()); ?>
						<?php echo \GO\Sites\Components\Html::error($company, 'post_country'); ?>
					</div>

				</div>


				

				<br /><hr />
				<h1><?php echo GOS::t('yourlogincredentials'); ?></h1>


				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($user, 'username'); ?>
					<?php echo \GO\Sites\Components\Html::activeTextField($user, 'username', array('disabled' => 'on')); ?>
					<?php echo \GO\Sites\Components\Html::error($user, 'username'); ?>
				</div>
				<div class="row">
					<?php echo \GO\Sites\Components\Html::label('Current password', 'currentPassword'); ?>
					<?php echo \GO\Sites\Components\Html::passwordField('currentPassword', "", array('autocomplete' => 'off')); ?>
				</div>
				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($user, 'password'); ?>
					<?php echo \GO\Sites\Components\Html::activePasswordField($user, 'password', array('autocomplete' => 'off')); ?>
					<?php echo \GO\Sites\Components\Html::error($user, 'password'); ?>
				</div>
				<div class="row">
					<?php echo \GO\Sites\Components\Html::activeLabelEx($user, 'passwordConfirm'); ?>
					<?php echo \GO\Sites\Components\Html::activePasswordField($user, 'passwordConfirm', array('autocomplete' => 'off')); ?>
					<?php echo \GO\Sites\Components\Html::error($user, 'passwordConfirm'); ?>
				</div>

				<div class="row buttons">
					<?php echo \GO\Sites\Components\Html::submitButton('Save'); ?>
				</div>
				<div style="clear:both;"></div>
				<?php echo \GO\Sites\Components\Html::endForm(); ?>
			</div>
		</div>
	</div>

</div>


<div class="subkader-right">
	<h1>Secure login</h1>
	<p>SSL secured connection verified by Equifax Secure Inc. </p>
</div>
