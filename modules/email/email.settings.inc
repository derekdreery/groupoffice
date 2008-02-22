<?php
/**
 * Copyright Intermesh 2005
 *  Author: Merijn Schering <mschering@intermesh.nl>
 *  Version: 1.0 Release date: 29 June 2005
 *
 *  This program is free software; you can redistribute it and/or modify it
 *  under the terms of the GNU General Public License as published by the
 *  Free Software Foundation; either version 2 of the License, or (at your
 *  option) any later version.
 */
require_once($GO_LANGUAGE->get_language_file('email'));
require_once($GO_MODULES->modules['email']['class_path']."email.class.inc");
$email = new email();

$user_id = isset($user_id) ? $user_id : $GO_SECURITY->user_id;

if($task == 'save_email_settings')
{
	$settings['user_id'] = $user_id;
	$settings['send_format'] = isset($_POST['send_format']) ? $_POST['send_format'] : '0';
	$settings['add_recievers'] = isset($_POST['add_recievers']) ? $_POST['add_recievers'] : '0';
	$settings['add_senders'] = isset($_POST['add_senders']) ? $_POST['add_senders'] : '0';
	$settings['request_notification'] = isset($_POST['request_notification']) ? $_POST['request_notification'] : '0';	
	$settings['add_recievers'] = isset($_POST['add_recievers']) ? $_POST['add_recievers'] : '0';
	$settings['add_senders'] = isset($_POST['add_senders']) ? $_POST['add_senders'] : '0';
	$settings['charset'] = smart_addslashes($_POST['charset']);
	$settings['show_preview'] = isset($_POST['show_preview']) ? '1' : '0';
	$settings['open_popup'] = isset($_POST['open_popup']) ? '1' : '0';
	$settings['beep'] = isset($_POST['beep']) ? '1' : '0';
	$email->update_settings($settings);
}

$settings = $email->get_settings($user_id);

$table = new table();

if (isset($GO_MODULES->modules['addressbook']) && $GO_MODULES->modules['addressbook']['read_permission'])
{ 	
	require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
	$ab = new addressbook();
	
	if ($ab->get_user_addressbooks($GO_SECURITY->user_id) == 0)
	{
		$ab->get_addressbook();
		$ab->get_user_addressbooks($GO_SECURITY->user_id);
	}
	
	$addressbooks = new select('add_recievers',$settings['add_recievers']);
	$addressbooks->add_value(0, $ml_no_reciever_add);
	while ($ab->next_record())
	{
		$addressbooks->add_value($ab->f('id'), $ab->f('name'));
	}	
	
	$row = new table_row();
	$cell = new table_cell($ml_add_recievers.':');
	$row->add_cell($cell);
	$cell = new table_cell($addressbooks->get_html());
	$row->add_cell($cell);	
	$table->add_row($row);
	
	$row = new table_row();
	$cell = new table_cell();
	$cell->set_attribute('colspan','2');	
	$cell->add_html_element(new checkbox('add_senders', 'add_senders', true, $ml_add_senders, $settings['add_senders']));
	$row->add_cell($cell);
	$table->add_row($row);
}

$row = new table_row();
$cell = new table_cell();
$cell->set_attribute('colspan','2');	
$cell->add_html_element(new checkbox('request_notification','request_notification', true, $ml_always_ask_notification, $settings['request_notification']));
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$cell = new table_cell();
$cell->set_attribute('colspan','2');	
$cell->add_html_element(new checkbox('show_preview','show_preview', true, $ml_show_preview, $settings['show_preview']));
$row->add_cell($cell);
$table->add_row($row);


$row = new table_row();
$cell = new table_cell();
$cell->set_attribute('colspan','2');	
$cell->add_html_element(new checkbox('open_popup','open_popup', true, $ml_open_popup, $settings['open_popup']));
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$cell = new table_cell();
$cell->set_attribute('colspan','2');	
$cell->add_html_element(new checkbox('beep','beep', true, $ml_beep, $settings['beep']));
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($ml_format.':'));
$select = new select('send_format', $settings['send_format']);
$select->add_value('text/PLAIN', $ml_text_format);
$select->add_value('text/HTML', $ml_html_format_is);
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);


if(function_exists('iconv'))
{
	$row = new table_row();
	$row->add_cell(new table_cell($ml_charset.':'));
	$select = new select('charset', $settings['charset']);
	$select->add_value('UTF-8', 'Unicode (UTF-8)');
	$select->add_value('ISO-8859-1', 'Western (ISO-8859-1)');
	$row->add_cell(new table_cell($select->get_html()));
	$table->add_row($row);
}




$tabstrip->add_html_element($table);


$p = new html_element('p', $ml_windows_mail);
$p->set_attribute('style','margin-top:10px;margin-bottom:5px;');
$link = new hyperlink($GO_MODULES->modules['email']['url'].'register_email.php', $ml_download_reg);
$link->set_attribute('class','normal');

$tabstrip->add_html_element($p);
$tabstrip->add_html_element($link);
$tabstrip->innerHTML .= '<br /><br />';


$tabstrip->add_html_element(new button($cmdSave, "javascript:_save('save_email_settings');"));

$tabstrip->innerHTML .= '
<script type="text/javascript">
function toggle_table(visible)
{
	var table = get_object(\'vacation_table\');
	if(visible)
	{	
		table.style.display=\'\';
		document.forms[0].vacation_subject.focus();
	}else
	{
		table.style.display=\'none\';
	}
}
</script>';
