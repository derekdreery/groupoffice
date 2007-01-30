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

//authenticate the user
$GO_SECURITY->authenticate();

load_basic_controls();
load_control('htmleditor');

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('filesystem');

//get the language file
require_once($GO_LANGUAGE->get_language_file('filesystem'));


$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$path = isset($_REQUEST['path']) ? smart_stripslashes($_REQUEST['path']) : '';

if($task=='save')
{	
	$name = trim(smart_stripslashes($_POST['name']));
	
	if($name == '')
	{
		$feedback = $error_missing_field;
	}else
	{
		if(!eregi('htm', get_extension($name)))
		{
			$name .= '.html';
		}
		
		if(!isset($_POST['content']) && file_exists($path.'/'.$name))
		{
			$feedback =$fbNameExists;
		}else
		{
			if(!isset($_POST['content']))
			{
				$path .= '/'.$name;
			}			
		 	if($fd = fopen($path,'w+'))
		 	{
		 		if(isset($_POST['content']))
		 		{
		 			fwrite($fd, smart_stripslashes($_POST['content']));
		 		}
				fclose($fd);
			}
		}
	}
}

//create filesystem  object
require_once ($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();

if(!is_dir($path))
{
	if(!$fs->has_write_permission($GO_SECURITY->user_id, dirname($path)))
	{
		header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
		exit();
	}
	
	$name = basename($path);
	
	$content = '';
	if(!$fd = fopen($path,'r'))
	{
			$feedback = $strDataError;
	}else
	{
		while (!feof($fd)) {
			$content .= fread($fd, 32768);
		}
		fclose($fd);
	}
}else
{
	if(!$fs->has_write_permission($GO_SECURITY->user_id, $path))
	{
		header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
	exit();
	}
	
  require_once($GO_THEME->theme_path."header.inc");
  require_once("add_file.inc");
  require_once($GO_THEME->theme_path."footer.inc");
  exit();
}

//require the header file. This will draw the logo's and the menu
require_once($GO_THEME->theme_path."header.inc");



$form = new form('editor_form');
$form->add_html_element(new input('hidden','path',$path, false));
$form->add_html_element(new input('hidden','task','',false));
$form->add_html_element(new input('hidden','name',$name));

$table = new table();
$table->set_attribute('style','height:100%;width:100%;');

if (isset($feedback)) 
{
	$row = new table_row();
	$cell = new table_cell($feedback);
	$cell->set_attribute('class', 'Error');
	$row->add_cell($cell);
	$table->add_row($row);
}

$row = new table_row();

$menu = new button_menu();
$menu->add_button('save_big', $cmdSave, "javascript:document.editor_form.task.value='save';document.editor_form.submit();");
$menu->add_button('close', $cmdClose, "javascript:confirm_close('".$GO_MODULES->url."index.php?task=properties&path=".urlencode(addslashes($path))."')");
$row->add_cell(new table_cell($menu->get_html()));
$table->add_row($row);

$htmleditor = new htmleditor('content');
$htmleditor->Value		= $content;
$htmleditor->ToolbarSet='email';

$htmleditor->SetConfig('ImageBrowser',true);
$htmleditor->SetConfig('ImageBrowserURL', $GO_MODULES->modules['cms']['url'].'select_image.php');

$row = new table_row();
$cell = new table_cell($htmleditor->CreateHtml());
$cell->set_attribute('style','height:100%;width:100%;');
$row->add_cell($cell);
$table->add_row($row);

$form->add_html_element($table);
echo $form->get_html();

?>
<script type="text/javascript">


function confirm_close(URL)
{
  document.location=URL;
}

</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
