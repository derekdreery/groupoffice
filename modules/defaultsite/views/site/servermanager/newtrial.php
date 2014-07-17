

<?php
//if (GOS::site()->notifier->hasMessage('error')) {
//	echo '<div class="notification notice-error">' . GOS::site()->notifier->getMessage('error') . '</div>';
//}
?>

<?php echo \GO\Sites\Components\Html::beginForm(); ?>	

<div class="row formrow">					
	<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'name'); ?>
	<?php echo \GO\Sites\Components\Html::activeTextField($model, 'name'); ?>
	<span ><?php echo '.'.\GO::config()->servermanager_wildcard_domain; ?></span>
	<?php echo \GO\Sites\Components\Html::error($model, 'name'); ?>
</div>
<div class="row formrow">
	<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'title'); ?>
	<?php echo \GO\Sites\Components\Html::activeTextField($model, 'title'); ?>
	<?php echo \GO\Sites\Components\Html::error($model, 'title'); ?>
</div>		

<div class="row formrow">
	<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'first_name'); ?>
	<?php echo \GO\Sites\Components\Html::activeTextField($model, 'first_name'); ?>
	<?php echo \GO\Sites\Components\Html::error($model, 'first_name'); ?>
</div>	

<div class="row formrow">
	<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'last_name'); ?>
	<?php echo \GO\Sites\Components\Html::activeTextField($model, 'last_name'); ?>
	<?php echo \GO\Sites\Components\Html::error($model, 'last_name'); ?>
</div>	

<div class="row formrow">
	<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'email'); ?>
	<?php echo \GO\Sites\Components\Html::activeTextField($model, 'email'); ?>
	<?php echo \GO\Sites\Components\Html::error($model, 'email'); ?>
</div>	

<div class="row buttons">
	<?php echo \GO\Sites\Components\Html::submitButton('Create trial!'); ?>
	<?php echo \GO\Sites\Components\Html::resetButton('Reset'); ?>
</div>

<div style="clear:both"></div>
<?php echo \GO\Sites\Components\Html::endForm(); ?>