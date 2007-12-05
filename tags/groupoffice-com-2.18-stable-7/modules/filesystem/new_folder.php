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
require_once ("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('filesystem');
require_once ($GO_LANGUAGE->get_language_file('filesystem'));

$GO_CONFIG->set_help_url($fs_help_url);

$task = isset($_REQUEST['task']) ? smart_stripslashes($_REQUEST['task']) : '';
$path = isset($_REQUEST['path']) ? smart_stripslashes($_REQUEST['path']) : '';
$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

require($GO_MODULES->modules['filesystem']['class_path'].'filesystem_treeview.class.inc');
$ftv = new filesystem_treeview('fs_treeview', $path);

$fs = new filesystem();

//echo substr($return_to,0,10);

if($task == 'create_folder')
{
	$name = smart_stripslashes($_POST['name']);
	if ($name == '') {
		$feedback = $error_missing_field;
	}
	elseif (!validate_input($name)) {
		$feedback = $invalid_chars.': " & ? / \\';
	}
	elseif (file_exists($ftv->path.'/'.$name)) {
		$feedback = $fbFolderExists;
	}
	elseif (!@ mkdir($ftv->path.'/'.$name, $GO_CONFIG->create_mode)) {
		$feedback = $strSaveError;
	} else {
		$GO_LOGGER->log('filesystem', 'NEW FOLDER '.$fs->strip_file_storage_path($ftv->path.'/'.$name));
		if(substr($return_to,0,10)=='javascript')
		{
			echo '<script type="text/javascript">'.$return_to.'</script>';
		}else
		{
			header('Location: '.$return_to);
		}
		exit();
	}
}


$GO_HEADER['body_arguments'] = 'onload="javascript:document.forms[0].name.focus();" onkeypress="javascript:executeOnEnter(event, \'save()\');"';


$form = new form('filesystem_form');

$form->add_html_element(new input('hidden', 'task', '', false));
$form->add_html_element(new input('hidden','return_to', $return_to));

$tabstrip = new tabstrip('folder_tab', $fbNewFolder);
$tabstrip->set_attribute('style','width:100%');
//$tabstrip->set_return_to($return_to);

if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $tabstrip->add_html_element($p);
}

$p = new html_element('p', '<b>'.$fbPath.'</b>: '.$fs->strip_file_storage_path($ftv->path));
$tabstrip->add_html_element($p);

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($strName.':*'));

$input = new input('text','name');
$input->set_attribute('style','width:250px;');

$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);
$tabstrip->add_html_element($table);




$tabstrip->add_html_element($ftv);




$tabstrip->add_html_element(new button($cmdOk, "javascript:save()"));
if(substr($return_to,0,10)=='javascript')
{
	$tabstrip->add_html_element(new button($cmdCancel, $return_to));
}else
{
	$tabstrip->add_html_element(new button($cmdCancel, "javascript:document.location='".$return_to."';"));
}

$form->add_html_element($tabstrip);

require_once ($GO_THEME->theme_path.'header.inc');
?>
<script type="text/javascript">
function save()
{
	document.forms[0].task.value='create_folder';
	document.forms[0].submit();
}
</script>
<?php
echo $form->get_html();
require_once ($GO_THEME->theme_path.'footer.inc');
?>

