<?php echo GO_Sites_Components_Html::beginForm('', 'POST',array('name'=>'createticket')); ?>

<?php if($ticket->isNew): ?>
	<div class="subkader-small-top">
		<div class="subkader-small-bottom">
			<div class="subkader-small-center">	
				
			 <div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($ticket, 'subject'); ?>
					<?php echo GO_SiteS_Components_Html::activeTextField($ticket, 'subject'); ?>
					<?php echo GO_Sites_Components_Html::error($ticket, 'subject'); ?>
			 </div>
			<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($ticket, 'type_id'); ?>
					<?php echo GO_SiteS_Components_Html::activeDropDownList($ticket, 'type_id', $tickettypes); ?>
					<?php echo GO_Sites_Components_Html::error($ticket, 'type_id'); ?>
			 </div>
			<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($ticket, 'first_name'); ?>
					<?php echo GO_SiteS_Components_Html::activeTextField($ticket, 'first_name'); ?>
					<?php echo GO_Sites_Components_Html::error($ticket, 'first_name'); ?>
			 </div>
			<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabel($ticket, 'middle_name'); ?>
					<?php echo GO_SiteS_Components_Html::activeTextField($ticket, 'middle_name'); ?>
					<?php echo GO_Sites_Components_Html::error($ticket, 'middle_name'); ?>
			 </div>
			<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($ticket, 'last_name'); ?>
					<?php echo GO_SiteS_Components_Html::activeTextField($ticket, 'last_name'); ?>
					<?php echo GO_Sites_Components_Html::error($ticket, 'last_name'); ?>
			 </div>
			<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabel($ticket, 'address'); ?>
					<?php echo GO_SiteS_Components_Html::activeTextField($ticket,'address'); ?>
					<?php echo GO_Sites_Components_Html::error($ticket, 'address'); ?>
			 </div>
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabel($ticket, 'address_no'); ?>
					<?php echo GO_SiteS_Components_Html::activeTextField($ticket,'address_no'); ?>
					<?php echo GO_Sites_Components_Html::error($ticket, 'address_no'); ?>
			  </div>
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabel($ticket, 'zip'); ?>
					<?php echo GO_SiteS_Components_Html::activeTextField($ticket,'zip'); ?>
					<?php echo GO_Sites_Components_Html::error($ticket, 'zip'); ?>
			  </div>
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabel($ticket, 'city'); ?>
					<?php echo GO_SiteS_Components_Html::activeTextField($ticket,'city'); ?>
					<?php echo GO_Sites_Components_Html::error($ticket, 'city'); ?>
			  </div>
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabel($ticket, 'state'); ?>
					<?php echo GO_SiteS_Components_Html::activeTextField($ticket,'state'); ?>
					<?php echo GO_Sites_Components_Html::error($ticket, 'state'); ?>
			  </div>
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($ticket, 'country'); ?>
					<?php echo GO_SiteS_Components_Html::activeDropDownList($ticket, 'country', GO::language()->getCountries()); ?>
					<?php echo GO_Sites_Components_Html::error($ticket, 'country'); ?>
			 </div>
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabelEx($ticket, 'email'); ?>
					<?php echo GO_SiteS_Components_Html::activeTextField($ticket,'email'); ?>
					<?php echo GO_Sites_Components_Html::error($ticket, 'email'); ?>
			  </div>
				<div class="row">
					<?php echo GO_Sites_Components_Html::activeLabel($ticket, 'phone'); ?>
					<?php echo GO_SiteS_Components_Html::activeTextField($ticket,'phone'); ?>
					<?php echo GO_Sites_Components_Html::error($ticket, 'phone'); ?>
			  </div>
				<?php echo GO_Sites_Components_Html::hiddenField('closeticket', 0); ?>
			</div>
		</div>
	</div>
		<?php $this->renderPartial("sidebar_ticket"); ?>

	<?php endif; ?>


<div style="clear:both"></div>

	<div class="subkader-big-top">
		<div class="subkader-big-bottom">
			<div class="subkader-big-center">	
				
				<?php if($ticket->status_id != GO_Tickets_Model_Ticket::STATUS_CLOSED): ?>
						<div class="row">
							<b><?php echo $message->getAttributeLabel('content'); ?></b>
							<?php echo GO_Sites_Components_Html::error($message, 'content'); ?>
							<?php echo GO_Sites_Components_Html::activeTextArea($message,'content', array('style'=>'width:800px;', 'class'=>'textarea')); ?>
						</div>
						<div class="row">
							<?php $uploader->render(); ?>
						</div>
					<?php else: ?>
						<p><?php echo $this->t('tickets_ticketIsClosed'); ?></p><p></p>
					<?php endif; ?>

				
				<?php if($ticket->status_id != GO_Tickets_Model_Ticket::STATUS_CLOSED): ?>
					<div class="button-green" onmouseover="this.className='button-green-hover';"  onmouseout="this.className='button-green';" style="float:left; margin-right: 15px;">
						<div class="button-green-right">
							<a href="#" onclick="submitForm(false)" class="button-green-center"> 
								<?php echo GOS::t('tickets_ticketSend'); ?>
							</a>
						</div>
					</div>
				
					<script type="text/javascript">
						function submitForm(close){

							var form = document.forms['createticket'];

							var submit = true;

							if(close==true){
								document.createticket.closeticket.value = true;

								if(document.createticket.message.value.replace(/\s/g,"") != ""){
									var answer = confirm("<?php echo GOS::t('tickets_ticketCloseQuestion'); ?>")
									if(!answer)
										submit=false;
								}
							}

							if(submit)
								form.submit();

						}
					</script>
				<?php endif; ?>
				
				<?php if(GO::user()): ?>
					<div class="button-green-side" onmouseover="this.className='button-green-side-hover';"  onmouseout="this.className='button-green-side';" style="float:left;">
						<div class="button-green-side-right">
							<a href="<?php echo $this->createUrl('/tickets/site/ticketlist');?>" class="button-green-side-center"> 
								<?php echo GOS::t('tickets_ticketBack'); ?>
							</a>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if($ticket->status_id != GO_Tickets_Model_Ticket::STATUS_CLOSED && !$ticket->isNew): ?>
					<div class="button-green" onmouseover="this.className='button-green-hover';"  onmouseout="this.className='button-green';" style="float:right; ">
						<div class="button-green-right">
							<a href="#" onclick="submitForm(true)" class="button-green-center"> 
								<?php echo GOS::t('tickets_ticketClose'); ?>
							</a>
						</div>
					</div>
				
					
				<?php endif; ?>
				
			<div style="clear:both;"></div>
		</div>
	</div>
</div>
<?php echo GO_Sites_Components_Html::endForm(); ?>


