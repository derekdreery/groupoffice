<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.18 $ $Date: 2006/11/21 16:25:39 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

load_basic_controls();
load_control('treeview');

class filesystem_treeview extends html_element
{
	var $id;
	var $path;
	var $form_name;
	var $change_folder;
	var $treeview;
	
	function filesystem_treeview($id, $path, $return_zero=false, $form_name='0')
	{
		global $GO_USERS, $GO_SECURITY, $GO_CONFIG, $GO_MODULES;
		
		$this->id=$id;			
		$this->form_name=$form_name;
		$this->path = isset($_POST[$this->id]['path']) ? smart_stripslashes($_POST[$this->id]['path']) : $path;
		$this->change_folder = (isset($_POST[$this->id]['change_folder']) && $_POST[$this->id]['change_folder'] == 'true');		
			
		require_once($GO_CONFIG->class_path.'filesystem.class.inc');	
		$fs = new filesystem();
		
		$this->treeview = new treeview($id);


		$img = new image('users_small');
		$img->set_attribute('align','absmiddle');
		$img->set_attribute('style','border:0px;margin-right:3px;');
		
		$root_node = new treenode($this->treeview, 'users');
		
		$img = new image('user_small');
		$img->set_attribute('align','absmiddle');
		$img->set_attribute('style','border:0px;margin-right:3px;');
		
		if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
		{
			$home_path = $GO_CONFIG->file_storage_path.'users/';
		}else
		{
			$home_path = $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'].'/';
		}
		
		
		
		$link = new hyperlink($this->get_folder_handler($home_path), $img->get_html().$GLOBALS['fs_personal'], $GLOBALS['fs_personal']);
		
		$user_node = new treenode($this->treeview, $home_path, $link->get_html(), null, true);
		$user_node = $this->buildTree($user_node);
		$root_node->addNode($user_node);
		
		foreach ($fs->get_my_shares() as $sharer)
		{
			$GO_USERS->get_user($sharer);
			

			if ($GO_USERS->f('id')>0 && $GO_USERS->f('id') != $GO_SECURITY->user_id)
			{
				$middle_name = $GO_USERS->f('middle_name') == '' ? '' : $GO_USERS->f('middle_name').' ';
				$user['name'] = htmlspecialchars($GO_USERS->f('first_name').' '.$middle_name.$GO_USERS->f('last_name'));
				
				$img = new image('user_small');
				$img->set_attribute('align','absmiddle');
				$img->set_attribute('style','border:0px;margin-right:3px;');
				
				$user_node = new treenode($this->treeview, $GO_USERS->f('id'), $img->get_html().$user['name']);

				$share_count = $fs->get_shares($GO_USERS->f('id'));

				$shared_folders = array();
				$had_permission = false;
				while ($fs->next_record())
				{
					$share_path = $fs->f('path');
					if (file_exists($share_path))
					{
						if (is_dir($share_path))
						{
							$is_sub_dir = isset($last_folder) ? $fs->is_sub_dir($share_path, $last_folder) : false;

							if (!$had_permission || !$is_sub_dir)
							{
								if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $fs->f('acl_read')) || 
										$GO_SECURITY->has_permission($GO_SECURITY->user_id, $fs->f('acl_write')))
								{
									$had_permission = true;	
									
									$closed_img = new image('folderclosed');							
									$closed_img->set_attribute('align','absmiddle');
									$closed_img->set_attribute('style','border:0px;');
									
									$folder_name = basename($fs->f('path'));
									$short_name = cut_string($folder_name, 30);
									
									$closedFolderLink = new hyperlink($this->get_folder_handler($share_path), 
										$closed_img->get_html().htmlspecialchars($short_name), 
										htmlspecialchars($folder_name));
									
									/*$opened_img = new image('folderopen');
									$opened_img->set_attribute('align','absmiddle');
									$opened_img->set_attribute('style','border:0px;');
										
									$openedFolderLink = new hyperlink($this->get_folder_handler($share_path), 
										$opened_img->get_html().htmlspecialchars($short_name), 
										htmlspecialchars($folder_name));*/
										
									$last_folder = $fs->f('path');
									
								
									$folder_node = new treenode($this->treeview, $share_path, $closedFolderLink->get_html());
									$folder_node = $this->buildTree($folder_node);
									$user_node->addNode($folder_node);
								}else
								{
									$had_permission = false;								
								}
							}
						}
					}else
					{
						$fs2 = new filesystem();
						$fs2->delete_share($share_path);
					}
					/*if (!$is_sub_dir)
					{
						$last_folder = $fs->f('path');
					}*/
				}
				$root_node->addNode($user_node);
			}				
		}		
		$this->treeview->addRootNode($root_node);
	}	
	
	function get_html()
	{
		$this->add_html_element(new input('hidden',$this->id.'[path]', $this->path, false));
		$this->add_html_element(new input('hidden',$this->id.'[change_folder]', 'false', false));
		
		$this->innerHTML .= '<script type="text/javascript">function change_folder_'.$this->id.'(path){
			document.forms["'.$this->form_name.'"].elements["'.$this->id.'[change_folder]"].value="true";
			document.forms["'.$this->form_name.'"].elements["'.$this->id.'[expandId]"].value=path;
			document.forms["'.$this->form_name.'"].elements["'.$this->id.'[path]"].value=path;
			document.forms["'.$this->form_name.'"].submit();}</script>';
			
		$this->innerHTML .= $this->treeview->getTreeView();
		return $this->innerHTML;
	}
	
	function get_folder_handler($path=null)
	{
		$fh= 'javascript:change_folder_'.$this->id.'(';
		if(isset($this->path))
		{
			$fh .= '\''.htmlspecialchars(addslashes($path),ENT_QUOTES).'\'';
		}else
		{
			$fh .= 'this';
		}
		$fh .= ');';
		return $fh;
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
			$closed_img->set_attribute('align','absmiddle');
			$closed_img->set_attribute('style','border:0px;');
			
			$short_name = cut_string($folder['name'], 30);
			
			$closedFolderLink = new hyperlink($this->get_folder_handler($folder['path']), 
				$closed_img->get_html().htmlspecialchars($short_name), 
				htmlspecialchars($folder['name']));
				
			/*$opened_img->set_attribute('align','absmiddle');
			$opened_img->set_attribute('style','border:0px;');
				
			$openedFolderLink = new hyperlink($this->get_folder_handler($folder['path']), 
				$opened_img->get_html().htmlspecialchars($short_name), 
				htmlspecialchars($folder['name']));*/
				
			$subNode = new treenode($this->treeview, $folder['path'], $closedFolderLink->get_html());

			if($rootNode->open)
			{
				$subNode = $this->buildTree($subNode);				
			}
			$rootNode->addNode($subNode);
		}
		return $rootNode;	
	}
}
