<?php \GO\Base\Html\Form::renderBegin('addressbook/site/addContact','addcontact',true); ?>

<!--	<div class="subkader-big-top">
		<div class="subkader-big-bottom">
			<div class="subkader-big-center">	-->
				
				<?php echo $this->notifications->render('addcontact'); ?>
				<br />
				
				<?php 
					\GO\Base\Html\Input::render(array(
						"name" => "first_name",
						"model"=> $this->contact,
						"required" => true
					));
					
					\GO\Base\Html\Hidden::render(array(
						"name" => "addressbook_id",
						"model"=> $this->contact,
						"value"=> 1272
					));
					
					\GO\Base\Html\Input::render(array(
						"model"=> $this->contact,
						"name" => "middle_name"
					));
					
					\GO\Base\Html\Input::render(array(
						"name" => "last_name",
						"model"=> $this->contact,
						"required" => true
					));
					
					\GO\Base\Html\Input::render(array(
						"name" => "email",
						"model"=> $this->contact,
						"required" => true
					));
					
					\GO\Base\Html\Input::render(array(
						"required" => true,
						"label" => "Confirm email",
						"name" => "confirm_email",
						"value" => ''
					));
					
					\GO\Base\Html\Input::render(array(
						"required" => true,
						"label" => "Address 1",
						"name" => "address",
						"value" => $this->contact->address
					));
					
					\GO\Base\Html\Input::render(array(
						"required" => true,
						"label" => "Address 2",
						"name" => "address_no",
						"value" => $this->contact->address_no
					));
					
					\GO\Base\Html\Input::render(array(
						"required" => true,
						"label" => "Town",
						"name" => "city",
						"value" => $this->contact->city
					));
					
					\GO\Base\Html\Input::render(array(
						"required" => true,
						"label" => "Area",
						"name" => "state",
						"value" => $this->contact->state
					));
					
					\GO\Base\Html\Input::render(array(
						"required" => true,
						"label" => "Postcode",
						"name" => "zip",
						"value" => $this->contact->zip
					));
					
					\GO\Base\Html\Select::render(array(
						"required" => true,
						'label' => 'Country',
						'value' => $this->contact->country,
						'name' => "country",
						'options' => \GO::language()->getCountries()
					));
					
					\GO\Customfields\Html\Field::render(array(
						'model'=>$this->contact,
						'name' => "col_17",
						'label' => 'faafaf'
					));
					
					\GO\Customfields\Html\Field::render(array(
						'model'=>$this->contact,
						'name' => "col_18"
					));
					
					\GO\Customfields\Html\Field::render(array(
						'model'=>$this->contact,
						'name' => "col_19"
					));
					
					\GO\Customfields\Html\Field::render(array(
						'model'=>$this->contact,
						'name' => "col_20"
					));
					
					\GO\Customfields\Html\Field::render(array(
						'model'=>$this->contact,
						'name' => "col_21",
						"empty_value" => ""
					));
					
					
				?>
				
				<div class="button-green" onmouseover="this.className='button-green-hover';"  onmouseout="this.className='button-green';" style="float:left; margin-right: 15px;">
					<div class="button-green-right">
						<a href="#" onclick="submitForm()" class="button-green-center"> 
							Send
						</a>
					</div>
				</div>
				<div style="clear:both"></div>
				<script type="text/javascript">
					function submitForm(){
//						if(document.createticket.message.value.replace(/\s/g,"") == "")
//							alert("The message cannot be empty!");
//						else
							document.addcontact.submit()
					}
				</script>
				
				</div>
			</div>
		</div>
	
<?php \GO\Base\Html\Form::renderEnd(); ?>


