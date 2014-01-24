<?php
$this->setPageTitle("New license");
?>		
			<div class="subkader-big-top">
						<div class="subkader-big-bottom">
							<div class="subkader-big-center">						
								

								<h1>Set license details</h1>
								
								<p>Fill in the form with the data that is provided in the gotest.php file.</p>
<p>Download the gotest.php file <a href="">here</a></p><br>
								
<?php if (!empty($license)): ?>
<?php echo \GO\Sites\Components\Html::form(); ?>
<div class="row">
	<?php echo \GO\Sites\Components\Html::activeLabelEx($license, 'host'); ?>
	<?php echo \GO\Sites\Components\Html::activeTextField($license, 'host'); ?>
	<?php echo \GO\Sites\Components\Html::error($license, 'host'); ?>
</div>
<div class="row">
	<?php echo \GO\Sites\Components\Html::activeLabelEx($license, 'ip'); ?>
	<?php echo \GO\Sites\Components\Html::activeTextField($license, 'ip'); ?>
	<?php echo \GO\Sites\Components\Html::error($license, 'ip'); ?>
</div>
<div class="row">
	<?php echo \GO\Sites\Components\Html::label('Internal Ip-address', 'internal_ip'); ?>
	<?php echo \GO\Sites\Components\Html::activeTextField($license,'internal_ip'); ?>
	<?php echo \GO\Sites\Components\Html::error($license, 'internal_ip'); ?>
</div>
<div class="row bottons">
	<?php echo \GO\Sites\Components\Html::submitButton('Save license'); ?>
	<?php echo \GO\Sites\Components\Html::button('Cancel', array("onclick"=>"document.location='".$this->createUrl("/licenses/site/licenselist")."';")); ?>
</div>
<?php echo \GO\Sites\Components\Html::endForm(); ?>

<?php else: ?>
		<p>An error occurred!</p>			
<?php endif; ?>
								
							</div>
						</div>

					</div>