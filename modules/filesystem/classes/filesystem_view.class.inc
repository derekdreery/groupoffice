<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.9 $ $Date: 2006/11/21 16:25:40 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

load_basic_controls();

class filesystem_view extends table
{		
	var $fsl;
	var $tv=null;
	var $path;
	
	function filesystem_view($id, $path, $handler='', $return_zero=false, $form_name='0', $treeview=true)
	{
		global $GO_USERS, $GO_SECURITY, $GO_CONFIG, $GO_MODULES;
		
		require_once($GO_MODULES->modules['filesystem']['class_path'].'filesystem_treeview.class.inc');
		require_once($GO_MODULES->modules['filesystem']['class_path'].'filesystem_list.class.inc');
		
		$this->fsl = new filesystem_list('fs_list', $path, $handler, $return_zero, $form_name);
		$this->path = $this->fsl->path;
		
		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();		
		
		
		if($treeview)
		{
			$this->tv = new filesystem_treeview('fs_treeview', $path, $return_zero, $form_name);			
			
			if($this->tv->change_folder)
			{
				$this->path=$this->tv->path;
				$this->fsl->set_path($this->tv->path);
			}elseif(!$this->tv->treeview->isSubmitted())
			{			
				$this->tv->treeview->setOpen($this->path);
				$parentFolder = dirname($this->path);
				while(!$this->tv->treeview->nodeIsOpen($parentFolder))
				{
					$this->tv->treeview->setOpen($parentFolder);
					$parentFolder = dirname($this->path);
				}
			}
		}
	}
	
	function set_path($path)
	{
		$this->fsl->path=$this->path=$path;
		if(is_object($this->tv))
		{
			$this->tv->path=$path;
		}
	}
	
	function get_html()
	{
		
		$this->set_attribute('style','width:100%');		
		$row = new table_row();		

		if(is_object($this->tv))
		{
			$cell =new table_cell($this->tv->get_html());
			$cell->set_attribute('valign','top');
			$cell->set_attribute('style','padding-right: 25px;white-space:nowrap;');
			$row->add_cell($cell);		
		}
		$cell =new table_cell($this->fsl->get_html());		
		$cell->set_attribute('valign','top');
		$cell->set_attribute('style','width:100%');
		$row->add_cell($cell);
		
		$this->add_row($row);

	
		return parent::get_html();
	}
	
	
	function buildTree($rootNode)
	{	
		$fs = new filesystem();
		
		$folders = $fs->get_folders_sorted($rootNode->id);
		
		foreach($folders as $folder)
		{
			if($fs->get_share($folder['path']))
			{
				$closed_img = new image('shared_folder_closed');
				$opened_img = new image('shared_folder_open');
			}else
			{
				$closed_img = new image('folderclosed');
				$opened_img = new image('folderopen');
			}
			$closed_img->set_attribute('align','middle');
			$closed_img->set_attribute('style','border:0px;');
			
			$short_name = cut_string($folder['name'], 30);
			
			$closedFolderLink = new hyperlink($this->fsl->get_folder_handler($folder['path']), 
				$closed_img->get_html().htmlspecialchars($short_name), 
				htmlspecialchars($folder['name']));
				
			$opened_img->set_attribute('align','middle');
			$opened_img->set_attribute('style','border:0px;');
				
			$openedFolderLink = new hyperlink($this->fsl->get_folder_handler($folder['path']), 
				$opened_img->get_html().htmlspecialchars($short_name), 
				htmlspecialchars($folder['name']));
				
			$subNode = new treenode($folder['path'], $closedFolderLink->get_html(), $openedFolderLink->get_html());
			
			$subNode = $this->buildTree($subNode);

			$rootNode->addNode($subNode);
		}
		return $rootNode;	
	}
}
