<?php
/**
 * Copyright Intermesh 2005
 *  Author: Merijn Schering <mschering@intermesh.nl>
 *  Version: 1.1 Release date: 27 June 2005
 *
 *  This program is free software; you can redistribute it and/or modify it
 *  under the terms of the GNU General Public License as published by the
 *  Free Software Foundation; either version 2 of the License, or (at your
 *  option) any later version.
 */
 
$p = new html_element('p',$security_text);
$tabstrip->add_html_element($p);

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($security_current_password.':*'));
$input = new input('password', 'currentpassword');
$input->set_attribute('style','width:200px');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($security_new_password.':*'));
$input = new input('password', 'newpass1');
$input->set_attribute('style','width:200px');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($security_confirm_password.':*'));
$input = new input('password', 'newpass2');
$input->set_attribute('style','width:200px');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$tabstrip->add_html_element($table);

$button = new button($cmdSave, "javascript:_save('change_password')");
$tabstrip->add_html_element($button);
?>
