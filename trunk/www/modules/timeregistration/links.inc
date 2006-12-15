<?php
require_once($GO_MODULES->modules['timeregistration']['class_path'].'projects_list.class.inc');
$pl = new timeregistration_list('timeregistration_list', $link_id,false, $form_name, $return_to);			
if($pl->count>0)
{		
	$link = new hyperlink($GO_MODULES->modules['timeregistration']['url'], $GLOBALS['lang_modules']['timeregistration']);
	$this->add_html_element(new html_element('h2', $link->get_html()));			
	$this->add_html_element($pl);
	
	$this->delete_tasks .= $pl->get_task_var().'.value="delete";';			
	$this->unlink_tasks .= $pl->get_task_var().'.value="unlink";';
}
?>
