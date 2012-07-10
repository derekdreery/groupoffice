<h1>Betalings resultaat</h1>

<?php echo GO_Sites_Components_Html::errorSummary($reservation); ?>

<?php if(GOS::site()->notifier->hasMessage('success')): ?>
	<?php echo GOS::site()->notifier->getMessage('success'); ?>
	<?php if($reservation->payment_result == GO_Reservation_Components_MultiSafePay::STATUS_PAID): ?>
	<p>Uw aanbetaling is ontvangen en uw reservering is definitief gemaakt.</p>
	<?php else: ?>
	<p>Helaas is de betaling (nog) niet met success volbracht. U dient uw aanbetaling 
		een uur na reservering te plaatsen anders word deze weer uit het systeem verwijderd.</p>
	<?php endif; ?>

<?php else: ?>
	<p class="errorMessage">Er is iets fout gegaan tijdens het opslaan van de status van uw order.</p>
<?php endif; ?>

