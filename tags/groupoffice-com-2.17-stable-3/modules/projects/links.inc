<?php
require_once($GO_MODULES->modules['projects']['class_path'].'projects_list.class.inc');
$pl = new projects_list('projects_list', $link_id,false, $form_name, $return_to);			
if($pl->count>0)
{		
	$link = new hyperlink($GO_MODULES->modules['projects']['url'], $GLOBALS['lang_modules']['projects']);
	$this->add_html_element(new html_element('h2', $link->get_html()));			
	$this->add_html_element($pl);
	
	$this->delete_tasks .= $pl->get_task_var().'.value="delete";';			
	$this->unlink_tasks .= $pl->get_task_var().'.value="unlink";';
}
