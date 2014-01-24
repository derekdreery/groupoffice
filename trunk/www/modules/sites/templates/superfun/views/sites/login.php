<h1>Inloggen</h1>								

<h2>Aanmelden</h2>
<p>Maakt u voor het eerst gebruik van ons reversering systeem klik dan hieronder om een account aan te maken</p>
<a class="btn btn-primary" href="<?php echo $this->createUrl('/reservation/front/register'); ?>">Aanmelden</a>

<h2>Inloggen</h2>
<p>Indien u al eerder gebruik heeft gemaakt van ons syteem kunt u zich hieronder inloggen</p>
<div class="form">
<?php echo \GO\Sites\Components\Html::beginForm(); ?>	
	<div class="row">
		<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'username'); ?>
		<?php echo \GO\Sites\Components\Html::activeTextField($model, 'username'); ?>
		<?php echo \GO\Sites\Components\Html::error($model, 'username'); ?>
	</div>
	<div class="row">
		<?php echo \GO\Sites\Components\Html::activeLabelEx($model, 'password'); ?>
		<?php echo \GO\Sites\Components\Html::activePasswordField($model, 'password'); ?>
		<?php echo \GO\Sites\Components\Html::error($model, 'password'); ?>
	</div>
	<?php if(GOS::site()->notifier->hasMessage('error')): ?>
		<div class="errorMessage">
			<?php echo GOS::site()->notifier->getMessage('error'); ?>
		</div>
	<?php endif; ?>
	<div class="row buttons">
		<?php echo \GO\Sites\Components\Html::submitButton('Inloggen', array('class'=>'btn btn-primary')); ?>
		<a class="btn" href="<?php echo $this->createUrl('/sites/site/recoverpassword'); ?>">Wachtwoord vergeten?</a>
	</div>
<?php echo \GO\Sites\Components\Html::endForm(); ?>
	

</div>
