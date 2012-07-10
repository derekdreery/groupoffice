<h1>Bedankt voor uw reservering</h1>

<?php if($reservation->isExpired()): ?>
	<p>Deze reservering is helaas verlopen en kan niet meer betaald worden</p>
	<?php else: ?>
<p>Wij hebben uw reservering geboekt. <br/>U dient de aanbetaling binnen 1 uur te volbrengen anders word uw reservering weer vrij gegeven.</p>

<p>U heeft nog tot <strong><?php echo $reservation->getTimeLeftToPay(); ?></strong> om uw reservering te bevestigen</p>

<p>Uw reserverings nummer is: <strong><?php echo $reservation->getNumber(); ?></strong><br />
	Het totaal te betalen bedrag is: <?php echo $reservation->getPriceText(); ?><Br />
	Het aanbetalings bedrag om de reservering te bevestigen is: <strong><?php echo $reservation->getPrepayPriceText(); ?></strong>
	
<p>
<a href="<?php echo $provider->getPaymentLinkHtml(); ?>">Klik hier om te betalen</a> 
</p>
<?php endif; ?>
<br><br>