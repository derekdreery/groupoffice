<h1>Reservering bevestigen</h1>

<div class="form">
	<?php if(GOS::site()->notifier->hasMessage('error') ): ?>
	<div class="errorMessage">
		<?php echo GOS::site()->notifier->getMessage('error'); ?>
	</div>
	<?php endif; ?>
	
	<?php echo GO_Sites_Components_Html::beginForm(); ?>
<div class="customer-details">
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'customer_name'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($model, 'customer_name', array('size'=>30)); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'customer_name'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'customer_address'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($model, 'customer_address', array('size'=>40)); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'customer_address'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'customer_place'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($model, 'customer_place', array('size'=>30)); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'customer_place'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'customer_postalcode'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($model, 'customer_postalcode', array('size'=>10)); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'customer_postalcode'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'customer_phone'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($model, 'customer_phone', array('size'=>15)); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'customer_phone'); ?>
	</div>
	<div class="row">
		<?php echo GO_Sites_Components_Html::activeLabelEx($model, 'customer_email'); ?>
		<?php echo GO_Sites_Components_Html::activeTextField($model, 'customer_email', array('size'=>30)); ?>
		<?php echo GO_Sites_Components_Html::error($model, 'customer_email'); ?>
	</div>
</div>
	
<div class="reservation-details">
	<div class="row">
		<label>Datum</label><?php echo $model->date; ?>
		</div>
		<div class="row">
		<label>Aantal personen</label><?php echo $model->person_count; ?>
	</div>
</div>
	<?php echo GO_Sites_Components_Html::error($model, 'external_reservation_id'); ?>
	<?php echo GO_Sites_Components_Html::error($model, 'plannings'); ?>
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
			<td><b>TOTAAL</b></td>
			<td colspan="3"></td>
			<td><b><?php echo $model->getPriceText(); ?></b></td>
		</tr>
		<tr>
			<td>50% aanbetaling</td>
			<td colspan="3"></td>
			<td><?php echo $model->getPrepayPriceText(); ?></td>
		</tr>
	</tfoot>
	<tbody>
	<?php foreach($model->plannings as $planning): ?>
	<tr<?php echo ($planning->hasValidationErrors('resource_id')) ? ' class="errorMessage"' : '';  ?>>
		<td><?php echo $planning->activity->planboard->name; ?></td>
		<td><?php echo $planning->time_from; ?> - <?php echo $planning->timeTillText; ?></td>
		<td><?php echo $planning->getPersonCount(); ?></td>
		<td><?php echo $planning->resource->name; ?></td>
		<td><?php echo $planning->getPriceText(); ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>
	<div class="row checkbox">
		<?php echo GO_Sites_Components_Html::activeCheckBox($model, 'agreeTerms'); ?>
		<?php echo GO_Sites_Components_Html::label('Ik ga akkoord met de <a href="	http://localhost/groupoffice-4.0/www/?r=files/file/download&id=1&random_code=SY6DGyOcFhP&inline=false&security_token=529k6wqoiapdx7geu41f">Algemene voorwaarden</a>', 'GO_Reservation_Model_Reservation_agreeTerms'); ?>
		
		<?php echo GO_Sites_Components_Html::error($model, 'agreeTerms'); ?>
	</div>
	<div class="row buttons">
		<?php echo GO_Sites_Components_Html::submitButton('Bevestigen en aanbetalen'); ?>
	</div>
	<?php echo GO_Sites_Components_Html::endForm(); ?>
</div>