		
			<div class="subkader-big-top">
						<div class="subkader-big-bottom">
							<div class="subkader-big-center">						
								

								<h1>Set license details</h1>
								
								<p>Fill in the form with the data that is provided in the gotest.php file.</p>
<p>Download the gotest.php file <a href="">here</a></p><br>
								
<?php if (!empty($license)): ?>
<?php echo GO_Sites_Components_Html::form(); ?>
<div class="row">
	<?php echo GO_Sites_Components_Html::activeLabelEx($license, 'host'); ?>
	<?php echo GO_Sites_Components_Html::activeTextField($license, 'host'); ?>
	<?php echo GO_Sites_Components_Html::error($license, 'host'); ?>
</div>
<div class="row">
	<?php echo GO_Sites_Components_Html::activeLabelEx($license, 'ip'); ?>
	<?php echo GO_Sites_Components_Html::activeTextField($license, 'ip'); ?>
	<?php echo GO_Sites_Components_Html::error($license, 'ip'); ?>
</div>
<div class="row">
	<?php echo GO_Sites_Components_Html::label('Internal Ip-address', 'intip'); ?>
	<?php echo GO_Sites_Components_Html::activeTextField($license,'intip'); ?>
	<?php echo GO_Sites_Components_Html::error($license, 'intip'); ?>
</div>
<div class="row bottons">
	<?php echo GO_Sites_Components_Html::submitButton('Save license'); ?>
	<?php echo GO_Sites_Components_Html::resetButton('Cancel'); ?>
</div>
<?php echo GO_Sites_Components_Html::endForm(); ?>

<?php else: ?>
		<p>An error occurred!</p>			
<?php endif; ?>
								
							</div>
						</div>

					</div>