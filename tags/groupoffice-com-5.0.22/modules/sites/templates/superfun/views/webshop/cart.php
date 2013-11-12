<?php if($cart->isEmpty()): ?>
<p>Leeg</p>
<?php else: ?>

<?php echo GO_Sites_Components_Html::beginForm('webshop/cart/updatePrices', 'cart', true); ?>

<table class="cart">
	<tr>
		<th>Aantal</th>
		<th>Artikel naam</th>
		<th style="text-align:right">Prijs</th>
	</tr>

	<?php foreach($cart->getItems() as $product): ?>
	<tr>
		<td><?php echo GO_Sites_Components_Html::activeTextField($product, "[$product->id]amount"); ?></td>
		<td><?php echo $product->geItem()->getLanguage(1)->name; ?></td>
		<td align="right"><?php echo $product->activity->priceText; ?> </td>
	</tr>
	<?php endforeach; ?>

	<tr>
		<td colspan="2" align="right" class="minicart_total"><b>Subtotaal:</b></td>
		<td align="right" class="minicart_total"><b><?php echo $cart->getSubTotal(); ?></b></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><b>BTW:</b></td>
		<td align="right"><b><?php echo $cart->getTotalVat(); ?></b></td>
	</tr>
	<tr>
		<td colspan="2" align="right" class="minicart_total"><b>Totaal</b></td>
		<td align="right" class="minicart_total"><b><?php echo $cart->getTotal(); ?></b></td>
	</tr>
</table>


<a class="button-green-center" href="#">Aantal bewerken</a>


<?php endif; ?>