<?php
require($GO_LANGUAGE->get_language_file('addressbook'));


require_once($GO_MODULES->modules['addressbook']['class_path'].'contacts_list.class.inc');
$con_list = new contacts_list('contacts_list', 0, $link_id, false, $this->form_name, $return_to);												
$html = $con_list->get_html();
if($con_list->count > 0)
{
	$link = new hyperlink($GO_MODULES->modules['addressbook']['url'], $contacts_contacts);
	$this->add_html_element(new html_element('h2', $link->get_html()));					
	
	$this->innerHTML .= $html;
	$this->delete_tasks .= $con_list->get_task_var().'.value="delete";';
	$this->unlink_tasks .= $con_list->get_task_var().'.value="unlink";';
}

require_once($GO_MODULES->modules['addressbook']['class_path'].'companies_list.class.inc');
$com_list = new companies_list('companies_list', 0, $link_id, false, $this->form_name, $return_to);	
$html = $com_list->get_html();

if($com_list->count>0)
{
	$link = new hyperlink($GO_MODULES->modules['addressbook']['url'], $ab_companies);
	$this->add_html_element(new html_element('h2', $link->get_html()));					
	
	$this->innerHTML .= $html;
	$this->delete_tasks .= $com_list->get_task_var().'.value="delete";';
	$this->unlink_tasks .= $com_list->get_task_var().'.value="unlink";';
}
