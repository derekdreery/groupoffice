<h1>Reserveren</h1>
<p>Met dit formulier kunt u bij ons een reservering plaatsen</p>

<?php if(GOS::site()->notifier->hasMessage('error')): ?>
<div class="errorMessage">
	<?php echo GOS::site()->notifier->getMessage('error'); ?>
	<?php //echo GO_Sites_Components_Html::error($model, 'external_reservation_id'); ?>
</div>
<p>Het reservering systeem kan tijdelijk geen toegang krijgen tot onze database. Probeer het later opnieuw.</p>
<?php else: ?>

<div class="form">
	<?php echo GO_Sites_Components_Html::beginForm(); ?>
	
	<div class="reservation">
		<div class="row">
			<?php echo GO_Sites_Components_Html::activeLabel($model, 'date'); ?>
			<?php echo GO_Sites_Components_Html::activeTextField($model, 'date'); ?>
			<?php echo GO_Sites_Components_Html::error($model, 'date'); ?>
		</div>
	<script>
		$(function() {
			$( "#GO_Reservation_Model_Reservation_date" ).attr('readonly', 'readonly');
			$( "#GO_Reservation_Model_Reservation_date" ).css('background-color','white');
			$( "#GO_Reservation_Model_Reservation_date" ).datepicker({ 
				dateFormat: "dd-mm-yy", 
				firstDay: 1, 
				prevText: "<", 
				nextText: '>',
				minDate: 1,
				monthNames: ["januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december"],
				dayNamesMin: ["zo", "ma", "di", "wo", "do", "vr", "za"]
			});
		});
	</script>
		<div class="row">
			<label>Tijd van</label>
			<?php echo GO_Sites_Components_Html::activeDropDownList($model, 'time_from_h', $model->hours, array('prompt'=>'uur')); ?>:
			<?php echo GO_Sites_Components_Html::activeDropDownList($model, 'time_from_m', $model->minutes); ?>
			<?php echo GO_Sites_Components_Html::error($model, 'time_from_h'); ?>
		</div>
		<div class="row">
			<label>Tijd tot</label>
			<?php echo GO_Sites_Components_Html::activeDropDownList($model, 'time_till_h', $model->hours, array('prompt'=>'uur')); ?>:
			<?php echo GO_Sites_Components_Html::activeDropDownList($model, 'time_till_m', $model->minutes); ?>
			<?php echo GO_Sites_Components_Html::error($model, 'time_till_h'); ?>
		</div>
		<div class="row">
			<?php echo GO_Sites_Components_Html::activeLabel($model, 'person_count'); ?>
			<?php echo GO_Sites_Components_Html::activeTextField($model, 'person_count', array('size'=>5, 'maxlength'=>4)); ?>
			<?php echo GO_Sites_Components_Html::error($model, 'person_count'); ?>
		</div>
		<div class="row buttons">
			<?php echo GO_Sites_Components_Html::submitButton('Controleer beschikbaarheid'); ?>
		</div>
	</div>
	
	<?php echo GO_Sites_Components_Html::endForm(); ?>
	
	<?php if(!empty($activities)): ?>
	<div class="planning-details">
		
		<?php foreach($activities as $activity): ?>
		
		<?php $schema = $activity->getSchema(); ?>
		<?php if($schema!==null && $schema->getPlannables() != array()): ?>
		
		<table>
			<caption><?php echo $activity->planboard->name . " ". $activity->getPriceText(); ?></caption>
			<tbody>
				<tr>
					<th>Van</th>
					<th>Tot</th>
						<?php if($activity->getPriceDifferenceChild()): ?>
							<th>Kind</th>
							<th>Volw.</th>
						<?php else: ?>
							<th colspan="2">Personen</th>
						<?php endif; ?>
					<th>Beschikbaar?</th>
					<th>&nbsp;</th>
				</tr>
				  <?php foreach($schema->getPlannables() as $plannable): ?>
						<tr>
						<?php echo GO_Sites_Components_Html::beginForm($this->createUrl('reservation/front/insertCartItem')); ?>
							<td>
								<?php echo GO_Sites_Components_Html::activeHiddenField($plannable, 'activity_id'); ?>
								<?php echo GO_Sites_Components_Html::activeHiddenField($plannable, 'time_from'); ?>
								<?php echo $plannable->time_from; ?></td>
							<td>
								<?php echo $plannable->getTimeTillText(); ?>
							</td>
								<?php if($activity->getPriceDifferenceChild()): ?>
									<td><?php echo GO_Sites_Components_Html::activeDropDownList($plannable, 'count_children', $activity->getPersonOptions(), array( 'disabled'=>!$plannable->isAvailable())); ?></td>
									<td><?php echo GO_Sites_Components_Html::activeDropDownList($plannable, 'count_adults', $activity->getPersonOptions(), array('disabled'=>!$plannable->isAvailable())); ?></td>
								<?php else: ?>
									<td colspan="2">
										<?php //echo GO_Sites_Components_Html::activeTextField($plannable, 'count_adults', array('size'=>'4', 'disabled'=>!$plannable->isAvailable())); ?>
										<?php echo GO_Sites_Components_Html::activeDropDownList($plannable, 'count_adults', $activity->getPersonOptions(), array('disabled'=>!$plannable->isAvailable())); ?>
									</td>
								<?php endif; ?>
							<td><?php echo $plannable->getAvailableText(); ?></td>
							<td><?php echo GO_Sites_Components_Html::submitButton('Toevoegen', array('disabled'=>!$plannable->isAvailable())); ?></td>
						<?php echo GO_Sites_Components_Html::endForm(); ?>
						</tr>
						<?php endforeach; ?>
				</tbody>
			</table>
			<?php endif; ?>
						
		<?php endforeach; ?>
			
	</div>

	<?php endif; ?>
	
</div>

<?php endif; ?>