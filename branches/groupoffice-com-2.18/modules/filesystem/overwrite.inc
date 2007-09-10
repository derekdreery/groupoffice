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

if ($task == 'overwrite')
{
	$task = 'paste';
}

$form->add_html_element(new input('hidden', 'task', $task, false));
$form->add_html_element(new input('hidden', 'overwrite_destination_path', $overwrite_destination_path, false));
$form->add_html_element(new input('hidden', 'overwrite_source_path', $overwrite_source_path, false));
$form->add_html_element(new input('hidden', 'overwrite', ' false', false));
$form->add_html_element(new input('hidden', 'overwrite_all', ' false', false));
if(isset( $_REQUEST['return_to']))
{
	$form->add_html_element(new input('hidden', 'return_to', $_REQUEST['return_to'], false));
}


$p = new html_element('h2');
$img = new image('questionmark');
$img->set_attribute('align','middle');
$img->set_attribute('style','border:0px;margin-right:10px;');
$p->add_html_element($img);
$p->innerHTML .=$fbConfirmOverwrite;
$form->add_html_element($p);

$form->add_html_element(new html_element('p', $strOverwritePrefix."'".basename($overwrite_destination_path)."'".$strOverwriteSuffix));

$form->add_html_element(new button($cmdOk,'javascript:overwrite_file(true);'));
$form->add_html_element(new button($cmdCancel,'javascript:overwrite_file(false);'));
$form->add_html_element(new button($cmdYesToAll,'javascript:overwrite_all_files();'));
?>


<script type="text/javascript" language="javascript">
function overwrite_file(overwrite)
{
	if (overwrite)
	{
		document.forms[0].overwrite.value = "true";
	}
	document.forms[0].submit();
}

function overwrite_all_files()
{
	document.forms[0].overwrite_all.value = "true";
	document.forms[0].overwrite.value = "true";
	document.forms[0].submit();
}
</script>
