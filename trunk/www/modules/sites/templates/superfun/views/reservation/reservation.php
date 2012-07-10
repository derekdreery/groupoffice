<h1>Geplaatste reservering</h1>

<div class="form">

<div class="reservation">
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabel($reservation, 'id'); ?>
		<?php echo $reservation->getNumber(); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabel($reservation, 'customer_name'); ?>
		<?php echo $reservation->customer_name; ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabel($reservation, 'customer_address'); ?>
		<?php echo $reservation->customer_address; ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabel($reservation, 'customer_place'); ?>
		<?php echo $reservation->customer_place; ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabel($reservation, 'customer_postalcode'); ?>
		<?php echo $reservation->customer_postalcode; ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabel($reservation, 'customer_phone'); ?>
		<?php echo $reservation->customer_phone; ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabel($reservation, 'customer_email'); ?>
		<?php echo $reservation->customer_email; ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabel($reservation, 'date'); ?>
		<?php echo $reservation->dateText; ?>
	</div>
	<div class="row">
		<label>Betalings status</label>
		<?php echo $reservation->getStatusText(); ?>
	</div>
	
</div>
	
	<table class="cart-overview">
		<thead>
	<tr>
		<th>Artikel</th>
		<th>Tijd</th>
		<th>Aantal</th>
		<th>Resource</th>
		<th>Bedrag</th>
	</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3">&nbsp;</td>
			<td>Totaal kosten</td>
			<td><strong><?php echo $reservation->getPriceText(); ?></strong></td>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($reservation->plannings as $planning): ?>
		<tr>
			<td><?php echo $planning->activity->planboard->name; ?></td>
			<td><?php echo $planning->time_from; ?> - <?php echo $planning->time_till; ?></td>
			<td><?php echo $planning->getPersonCount(); ?></td>
			<td><?php echo $planning->resource->name; ?></td>
			<td><?php echo $planning->getPriceText(); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	</table>
	
</div>
