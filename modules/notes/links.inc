<?php
require_once($GO_MODULES->modules['notes']['class_path'].'notes_list.class.inc');
		
$nl = new notes_list('notes_list', 0, $link_id, false, $form_name, $return_to);	

if($nl->count>0)
{				
	$link = new hyperlink($GO_MODULES->modules['notes']['url'], $GLOBALS['lang_modules']['notes']);
	$this->add_html_element(new html_element('h2', $link->get_html()));			
	$this->add_html_element($nl);
	
	$this->delete_tasks .= $nl->get_task_var().'.value="delete";';
	$this->unlink_tasks .= $nl->get_task_var().'.value="unlink";';
}
?>
