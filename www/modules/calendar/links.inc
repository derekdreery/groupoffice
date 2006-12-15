<?php
require_once($GO_MODULES->modules['calendar']['class_path'].'events_list.class.inc');
$tl = new events_list('todos_list', true, false,0,0,0, $link_id, false, $form_name, $return_to, true);

if(count($tl->rows))
{
	$link = new hyperlink($GO_MODULES->modules['calendar']['url'], $GLOBALS['lang_modules']['todos']);
	$this->add_html_element(new html_element('h2', $link->get_html()));			
	$this->add_html_element($tl);
	
	$this->delete_tasks .= $tl->get_task_var().'.value="delete";';
	$this->unlink_tasks .= $tl->get_task_var().'.value="unlink";';
}		
require_once($GO_MODULES->modules['calendar']['class_path'].'events_list.class.inc');
$el = new events_list('events_list',false,true,0, 0, 0,$link_id, false, $form_name, $return_to);

if(count($el->rows))
{
	$link = new hyperlink($GO_MODULES->modules['calendar']['url'], $GLOBALS['lang_modules']['calendar']);
	$this->add_html_element(new html_element('h2', $link->get_html()));			
	$this->add_html_element($el);
	
	$this->delete_tasks .= $el->get_task_var().'.value="delete";';
	$this->unlink_tasks .= $el->get_task_var().'.value="unlink";';
}				
?>
