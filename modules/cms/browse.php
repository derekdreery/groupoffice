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

load_basic_controls();
load_control('datatable');
load_control('dynamic_tabstrip');

//authenticate the user
$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('cms');

require_once($GO_MODULES->class_path.'cms.class.inc');
$cms = new cms();

$GO_THEME->load_module_theme('cms');

//get the language file
require_once($GO_LANGUAGE->get_language_file('cms'));
if(isset($_REQUEST['site_id']))
{
	$_SESSION['site_id'] =$_REQUEST['site_id'];
}

if(!$site = $cms->get_site($_SESSION['site_id'] ))
{
  header('Location: index.php');
}

//$template = $cms->get_template($site['template_id']);

if (!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $site['acl_write']))
{
  require_once($GO_THEME->theme_path."header.inc");
  require_once($GO_CONFIG->root_path.'error_docs/403.inc');
  require_once($GO_THEME->theme_path."footer.inc");
  exit();
}

//set the folder id we are in
if(isset($_REQUEST['folder_id']))
{
	$folder_id = $_REQUEST['folder_id'];
}else
{
 	$folder_id = $site['root_folder_id'];	 	
}

$link_back = $GO_MODULES->url.'browse.php?site_id='.$_SESSION['site_id'] .'&folder_id='.$folder_id;


$datatable = new datatable('cms_table');
if($datatable->task == 'delete')
{
	foreach($datatable->selected as $item_id)
	{
		if(substr($item_id,0,1) == 'f')
		{
			$cms->delete_file(substr($item_id,1));
		}else
		{
			$cms->delete_folder(substr($item_id,1));
		}
	}
	
	$cms->reset_priorities($folder_id);
}

//what to do before output
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
switch ($task)
{
	case 'save':
		$name = smart_addslashes(trim($_POST['name']));
		if ($name == '')
		{
			$feedback = $error_missing_field;
		}elseif(!$folder=$cms->get_folder($folder_id))
		{
			$feedback = $strSaveError;
		}else
		{
			$disabled = isset($_POST['disabled']) ? '1' : '0';
			$multipage = isset($_POST['multipage']) ? '1' : '0';
			
			$old_folder = $cms->get_folder($folder_id);
			if (isset($_POST['go_auth']))
			{
				if ($old_folder['acl'] == 0)
				{
					if (!$acl = $GO_SECURITY->get_new_acl())
					{
						die($strAclError);
					}
				}else
				{
					$acl = $old_folder['acl'];
				}
			}else
			{
				$acl = 0;
				if($old_folder['acl'] > 0)
				{
					$GO_SECURITY->delete_acl($old_folder['acl']);
				}
			}

			if (!$cms->update_folder($folder_id, $name, $disabled, $multipage, $_POST['template_item_id'], $acl))
			{
				$feedback = $strSaveError;
			}
		}
	break;
  case 'upload':
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
      $task = 'list';
      if (isset($_FILES['file']))
      {
				for ($i=0;$i<count($_FILES['file']['tmp_name']);$i++)
				{
				  if (is_uploaded_file($_FILES['file']['tmp_name'][$i]))
				  {
				    $name = $_FILES['file']['name'][$i];
				    $x=0;
				    while ($cms->file_exists($folder_id, $name))
				    {
				      $x++;
				      $name = strip_extension($_FILES['file']['name'][$i]).' ('.$x.').'.get_extension($_FILES['file']['name'][$i]);
				    }

				    $fp = fopen($_FILES['file']['tmp_name'][$i], 'r');
				    $content = addslashes(fread($fp, $_FILES['file']['size'][$i]));
				    fclose($fp);
				    if (eregi('htm', get_extension($name)))
				    {
				      $content = $cms->get_body($content);
				    }
				    $file_id = $cms->add_file($folder_id, $name, $content);
				    unlink($_FILES['file']['tmp_name'][$i]);
				  }
				}
      }
    }
    break;

  case 'add_folder':
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
      $name = smart_addslashes(trim($_POST['name']));
      if ($name == '')
      {
				$feedback = '<p class="Error">'.$error_missing_field;
      }elseif($cms->folder_exists($folder_id, $name))
      {
				$feedback = '<p class="Error">Mapnaam bestaat al</p>';
      }elseif(!$cms->add_folder($folder_id, $name,
					isset($_POST['disabled']), $_POST['template_item_id']))
      {
				$feedback = '<p class="Error">'.$strSaveError.'</p>';
      }else
      {
				$task = '';
      }
    }
    break;
    
  case  'cut':  
  	$_SESSION['copy_folders'] = array();
    $_SESSION['copy_files'] = array();
    $_SESSION['cut_files'] =  array();
    $_SESSION['cut_folders'] = array();
    
  	foreach($datatable->selected as $item_id)
  	{
  		if(substr($item_id,0,1) == 'f')
  		{
  			$_SESSION['cut_files'][] = substr($item_id,1);
  		}else
  		{
  			$_SESSION['cut_folders'][] = substr($item_id,1);
  		}
  	}
    $task = '';
    break;

  case 'copy':
    $_SESSION['copy_folders'] = array();
    $_SESSION['copy_files'] = array();
    $_SESSION['cut_files'] =  array();
    $_SESSION['cut_folders'] = array();
    
  	foreach($datatable->selected as $item_id)
  	{
  		if(substr($item_id,0,1) == 'f')
  		{
  			$_SESSION['copy_files'][] = substr($item_id,1);
  		}else
  		{
  			$_SESSION['copy_folders'][] = substr($item_id,1);
  		}
  	}
    $task = '';
    break;

  case 'paste':
    while ($file = smart_stripslashes(array_shift($_SESSION['cut_files'] )))
    {
      $cms->move_file($file, $folder_id);
    }
    while ($file = smart_stripslashes(array_shift($_SESSION['copy_files'])))
    {
      $cms->copy_file($file, $folder_id);
    }
    while ($folder = smart_stripslashes(array_shift($_SESSION['cut_folders'])))
    {
      $cms->move_folder($folder, $folder_id);
    }
    while ($folder = smart_stripslashes(array_shift($_SESSION['copy_folders'])))
    {
      $cms->copy_folder($folder, $folder_id);
    }
    break;
}

//set the page title for the header file
$page_title = $lang_modules['cms'];


if($task == 'add_folder')
{
	$GO_HEADER['body_arguments'] = 'onload="javascript:document.forms[0].name.focus();" onkeypress="javascript:executeOnEnter(event, \'document.forms[0].submit()\');"';
}
$GO_HEADER['head'] = $datatable->get_header();


//require the header file. This will draw the logo's and the menu
require_once($GO_THEME->theme_path."header.inc");
echo '<form name="cms" method="post" action="'.$_SERVER['PHP_SELF'].'" enctype="multipart/form-data">';
switch ($task)
{
  case 'upload':
    require_once('upload.inc');
    break;

  case 'add_folder':
    require_once('add_folder.inc');
    break;

  default:
    require_once('files.inc');
    break;
}
echo '</form>';

require_once($GO_THEME->theme_path."footer.inc");
