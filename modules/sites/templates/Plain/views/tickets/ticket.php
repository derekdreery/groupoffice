<?php

GOS::site()->scripts->registerCssFile($this->getTemplateUrl().'css/ticket.css');

$form = new GO_Sites_Widgets_Form();

if($ticket->isNew){
	$this->renderPartial('_ticketForm',array('ticket'=>$ticket,'ticketTypes'=>$ticketTypes,'form'=>$form));
	$this->renderPartial("_ticketSidebar");
}else{	
	$this->renderPartial('_ticketPanel',array('ticket'=>$ticket));
}

echo '<div style="clear:both"></div>';

$this->renderPartial('_messageForm',array('ticket'=>$ticket,'message'=>$message,'uploader'=>$uploader,'form'=>$form));

echo $form->endForm();

if(!$ticket->isNew)
	$this->renderPartial('_messageList',array('ticket'=>$ticket,'pager'=>$pager));