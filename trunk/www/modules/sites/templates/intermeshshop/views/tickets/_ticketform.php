<?php GO_Base_Html_Form::renderBegin('tickets/site/createticket','createticket',true); ?>

<?php if($ticket->isNew): ?>
	<div class="subkader-small-top">
		<div class="subkader-small-bottom">
			<div class="subkader-small-center">	
				<?php 
					GO_Base_Html_Hidden::render(array(
						"required" => false,
						"label" => "",
						"name" => "ticket_number",
						"value" => 0
					));

					GO_Base_Html_Input::render(array(
						"required" => true,
						"label" => "Subject",
						"name" => "subject",
						"value" => $ticket->subject
					));

					GO_Base_Html_Select::render(array(
						"required" => true,
						"label" => "Type",
						"name" => "type_id",
						"value" => $ticket->type_id,
						"options" => $tickettypes
					));

					GO_Base_Html_Input::render(array(
							"required" => true,
							"label" => "First Name",
							"name" => "first_name",
							"value" => $ticket->first_name
						));

					GO_Base_Html_Input::render(array(
						"required" => false,
						"label" => "Middle Name",
						"name" => "middle_name",
						"value" => $ticket->middle_name
					));

					GO_Base_Html_Input::render(array(
						"required" => true,
						"label" => "Last Name",
						"name" => "last_name",
						"value" => $ticket->last_name
					));

					GO_Base_Html_Input::render(array(
						"required" => false,
						"label" => "Address",
						"name" => "address",
						"value" => $ticket->address
					));

					GO_Base_Html_Input::render(array(
						"required" => false,
						"label" => "Address no",
						"name" => "address_no",
						"value" => $ticket->address_no
					));

					GO_Base_Html_Input::render(array(
						"required" => false,
						"label" => "Zipcode",
						"name" => "zip",
						"value" => $ticket->zip
					));

					GO_Base_Html_Input::render(array(
						"required" => false,
						"label" => "City",
						"name" => "city",
						"value" => $ticket->city
					));

					GO_Base_Html_Input::render(array(
						"required" => false,
						"label" => "State/Province",
						"name" => "state",
						"value" => $ticket->state
					));

					GO_Base_Html_Select::render(array(
						"required" => true,
						'label' => 'Country',
						'value' => $ticket->country,
						'name' => "country",
						'options' => GO::language()->getCountries()
					));

					GO_Base_Html_Input::render(array(
						"required" => true,
						"label" => "Email",
						"name" => "email",
						"value" => $ticket->email
					));

					GO_Base_Html_Input::render(array(
						"required" => false,
						"label" => "Phone",
						"name" => "phone",
						"value" => $ticket->phone
					));
				?>
				</div>
			</div>
		</div>
		<?php include "sidebar_ticket.php";?>
	<?php else:?>
		<?php 
			GO_Base_Html_Hidden::render(array(
				"required" => true,
				"label" => "",
				"name" => "ticket_number",
				"value" => $ticket->ticket_number
			));
		?>
	<?php endif; ?>


<div style="clear:both"></div>

	<div class="subkader-big-top">
		<div class="subkader-big-bottom">
			<div class="subkader-big-center">	
				
				<?php
					
					if($ticket->status_id != '-1'){
						
						echo "<b>Your Message</b>";
						GO_Base_Html_Textarea::render(array(
							"required" => true,
							//"label" => "Message",
							"name" => "message",
							"value" => "",
							"extra" => 'style="width:800px;"'
						));

						$uploader->render();

						GO_Base_Html_Hidden::render(array(
							"label" => "",
							"name" => "submitticket",
							"value" => 'Confirm',
							"renderContainer" => false
						));
					}else{
						echo "<p>".$this->t('tickets_ticketIsClosed')."</p><p></p>";
					}
				?>

				
				<?php if($ticket->status_id != '-1'): ?>
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
								var el = document.createElement("input");
								el.type = "hidden";
								el.name = "closeticket";
								el.value = "true";
								form.appendChild(el);

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
				
				<?php if($ticket->status_id != '-1' && !$ticket->isNew): ?>
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
<?php GO_Base_Html_Form::renderEnd(); ?>


