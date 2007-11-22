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

/*
   prints the folders in a tree
   $folders is an array of associve arrays containing an 'id' and 'name'
 */

load_control('treeview');

function buildTree($rootNode)
{
  global $tv, $folders_sort, $cms_settings;

	$cms = new cms();
	
	$cms->get_folders($rootNode->id, $folders_sort, $cms_settings['sort_order']);
	
	while($cms->next_record())
	{
		$closed_img = new image('folderclosed');							
		$closed_img->set_attribute('align','absmiddle');
		$closed_img->set_attribute('style','border:0px;');
		
		$short_name = cut_string($cms->f('name'), 30);
		
		$closedFolderLink = new hyperlink($_SERVER['PHP_SELF'].'?folder_id='.$cms->f('id'), 
			$closed_img->get_html().htmlspecialchars($short_name), 
			htmlspecialchars($cms->f('name')));
		
		/*$opened_img = new image('folderopen');
		$opened_img->set_attribute('align','middle');
		$opened_img->set_attribute('style','border:0px;');
			
		$openedFolderLink = new hyperlink($_SERVER['PHP_SELF'].'?folder_id='.$cms->f('id'), 
			$opened_img->get_html().htmlspecialchars($short_name), 
			htmlspecialchars($cms->f('name')));*/

		$subNode = new treeNode($tv, $cms->f('id'), $closedFolderLink->get_html());
		if($rootNode->open)
		{
			$subNode = buildTree($subNode);
		}		
		$rootNode->addNode($subNode);						
  }
  return $rootNode;
}


//adjust sorting because folders because they lack some columns
switch ($cms_settings['sort_field'])
{
  case 'cms_files.priority':
    $folders_sort = "priority";
    break;

  case 'cms_files.time':
    $folders_sort = "time";
    break;

  default:
    $folders_sort = "name";
    break;
}


$tv = new treeview('cms_treeview');

if(isset($_REQUEST['folder_id']))
{
	$tv->setOpen($_REQUEST['folder_id']);
}

//end of treeview code


if($root_folder = $cms->get_folder($site['root_folder_id']))
{
	$img = new image('site');
	$img->set_attribute('style','border:0px;height:16px;width:16px;margin-right:5px');
	$img->set_attribute('align','middle');
	
	$short_name = cut_string($root_folder['name'], 30);
	$link = new hyperlink($_SERVER['PHP_SELF'].'?folder_id='.$site['root_folder_id'], $img->get_html().$short_name);
	$link->set_attribute('title',$root_folder['name']);
	
	$rootNode = new treeNode($tv, $site['root_folder_id'], $link->get_html(),null,true);
  $rootNode = buildTree($rootNode);
  $tv->addRootNode($rootNode);
}



echo $tv->getTreeview();
