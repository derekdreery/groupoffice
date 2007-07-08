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

$table = new table();

if ($GO_CONFIG->allow_themes == true)
{
	$row = new table_row();
	$row->add_cell(new table_cell($pref_theme.':'));

	$select = new select('theme', $user['theme']);
	$themes = $GO_THEME->get_themes();
	foreach($themes as $theme)
	{
		$select->add_value($theme, $theme);
	}
	$row->add_cell(new table_cell($select->get_html()));
	$table->add_row($row);
}

$row = new table_row();
$row->add_cell(new table_cell($pref_startmodule.':'));

$select = new select('start_module', $user['start_module']);
$GO_MODULES->get_modules();
while ($GO_MODULES->next_record())
{
	if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $GO_MODULES->f('acl_read')) ||
	$GO_SECURITY->has_permission($GO_SECURITY->user_id, $GO_MODULES->f('acl_write'))
	)
	{
		$language_file = $GO_LANGUAGE->get_language_file($GO_MODULES->f('id'));
	  if(file_exists($language_file))
	  {
	  	require_once($language_file);
	  }
		$lang_var = isset($lang_modules[$GO_MODULES->f('id')]) ?
		$lang_modules[$GO_MODULES->f('id')] : $GO_MODULES->f('id');
		$select->add_value($GO_MODULES->f('id'), $lang_var);
	}
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($pref_max_rows_list.':'));

$select = new select('max_rows_list', $user['max_rows_list']);

$select->add_value('10','10');
$select->add_value('15','15');
$select->add_value('20','20');
$select->add_value('25','25');
$select->add_value('30','30');
$select->add_value('50','50');
$select->add_value('75','75');
$select->add_value('100','100');
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($pref_name_order.':'));

$select = new select('sort_name', $user['sort_name']);
$select->add_value('first_name', $strFirstName);
$select->add_value('last_name', $strLastName);

$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$tabstrip->add_html_element($table);

$div = new html_element('div');
$div->add_html_element(new checkbox('use_checkbox_select','use_checkbox_select','1', $pref_use_checkbox_select, $user['use_checkbox_select']));
$tabstrip->add_html_element($div);

$tabstrip->add_html_element(new button($cmdSave, "javascript:_save('save_look')"));
