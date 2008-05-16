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

$form->add_html_element(new input('hidden', 'create_folder', '', false));

$tabstrip = new tabstrip('folder_tab', $fbNewFolder);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to($_SERVER['PHP_SELF']."?path=".$urlencoded_path);

if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $tabstrip->add_html_element($p);
}

$p = new html_element('p', $fv->path);
$tabstrip->add_html_element($p);

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($strName.':*'));

$input = new input('text','name');
$input->set_attribute('style','width:250px;');

$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);
$tabstrip->add_html_element($table);


$ftv = new filesystem_treeview('fs_treeview', $fv->path);

$tabstrip->add_html_element($ftv);




$tabstrip->add_html_element(new button($cmdOk, "javascript:save()"));
$tabstrip->add_html_element(new button($cmdCancel, "javascript:document.location='".$_SERVER['PHP_SELF']."?path=".$urlencoded_path."';"));

$form->add_html_element($tabstrip);
?>
<script type="text/javascript">
function save()
{
	document.forms[0].create_folder.value='true';
	document.forms[0].task.value='new_folder';
	document.forms[0].submit();
}
</script>
