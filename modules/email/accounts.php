<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

require_once("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

load_basic_controls();
load_control('datatable');

require_once($GO_CONFIG->class_path."mail/imap.class.inc");
require_once($GO_MODULES->class_path."email.class.inc");
require_once($GO_LANGUAGE->get_language_file('email'));

$mail = new imap();
$email = new email();
$datatable = new datatable('accounts');

$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];

switch ($datatable->task) {

    case 'delete':
        //delete accounts if requested        
			foreach ($datatable->selected as $delete_account_id) {
				if (!$email->delete_account($GO_SECURITY->user_id, $delete_account_id)) {
				    $feedback = $strDeleteError;
				}
			}
    break;

    case 'set_default':
        $tmp = $datatable->selected;
        if (count($tmp) > 0) {
            $email->set_as_default($tmp[0], $GO_SECURITY->user_id);
        }
    break;
}

$GO_HEADER['head'] = $datatable->get_header();

require_once($GO_THEME->theme_path."header.inc");

$form = new form('accounts');

$form->add_html_element(new input('hidden', 'task'));
$form->add_html_element(new input('hidden', 'close', 'false'));
$form->add_html_element(new input('hidden', 'return_to', $return_to));
$form->add_html_element(new input('hidden', 'link_back', $link_back));
$form->add_html_element(new input('hidden', 'delete_account_id'));

$menu = new button_menu();

$menu->add_button('account', $ml_new_account, 'account.php?return_to='.urlencode($link_back));
$menu->add_button('delete_big', $strDeleteItem, $datatable->get_delete_handler());
$menu->add_button('em_refresh', $ml_set_default, 'javascript:document.forms[\''.$datatable->form_name.'\'].elements[\''.$datatable->attributes['id'].'[task]'.'\'].value=\'set_default\'; document.forms[\''.$datatable->form_name.'\'].submit();');

$menu->add_button('close', $cmdClose, htmlspecialchars($return_to));


$datatable->add_column(new table_heading($strHost));
$datatable->add_column(new table_heading($strEmail));
$datatable->add_column(new table_heading($strDefault));

$count = $email->get_accounts($GO_SECURITY->user_id);

if ($count > 0) {


    $count = $email->get_accounts($GO_SECURITY->user_id);

    while ($email->next_record()) {

        $row = new table_row($email->f('id'));

        $row->set_attribute('ondblclick', "javascript:document.location='account.php?account_id=".$email->f('id')."&return_to=".urlencode($link_back)."'");
       

        $row->add_cell(new table_cell($email->f('host')));
        $row->add_cell(new table_cell($email->f('email')));
        
        $cell = new table_cell();
        
        if($email->f('standard') == '1')
        {
        	$img = new image('ok');
        	$cell->add_html_element($img);
        }
        $row->add_cell($cell);

        $datatable->add_row($row);

    }

} else {

	$row = new table_row();
	$cell = new table_cell($ml_no_accounts);
	$cell->set_attribute('colspan','99');
	$row->add_cell($cell);
	$datatable->add_row($row);		

}

$form->add_html_element($menu);
$form->add_html_element($datatable);

echo $form->get_html();

require_once($GO_THEME->theme_path."footer.inc");
