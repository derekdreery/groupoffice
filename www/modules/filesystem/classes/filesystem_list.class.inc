<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.20 $ $Date: 2006/12/01 13:58:22 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

load_control('datatable');

class filesystem_list extends datatable
{	
	var $id;
	var $path;
	var $handler;
	var $return_zero;
	var $submitted=false;
		
	function filesystem_list($id, $path, $handler='', $return_zero=false, $form_name='0')
	{
		$this->id=$id;
		$this->path = isset($_POST[$this->id]['path']) ? smart_stripslashes($_POST[$this->id]['path']) : $path;
		$this->handler=$handler;
		$this->return_zero=$return_zero;
		$this->submitted = (isset($_POST[$this->id]['submitted']) && $_POST[$this->id]['submitted'] == 'true');				
		$this->datatable($id, true, $form_name);
	}
	
	function set_path($path)
	{
		$this->path = $path;
	}
	
	function get_folder_handler($path=null)
	{
		$fh= 'javascript:change_folder_'.$this->id.'(';
		if(isset($path))
		{
			$fh .= '\''.htmlspecialchars(addslashes($path),ENT_QUOTES).'\'';
		}else
		{
			$fh .= 'this';
		}
		$fh .= ');';
		return $fh;
	}

	function get_html()
	{
		global $GO_CONFIG, $GO_LANGUAGE, $GO_MODULES, $GO_SECURITY;

		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();		
		
		$fs_settings = $fs->get_settings($GO_SECURITY->user_id);
		
		$this->add_outerhtml_element(new input('hidden',$this->id.'[path]', $this->path, false));
		$this->add_outerhtml_element(new input('hidden',$this->id.'[submitted]', 'false', false));
		
		$this->outerHTML .= '<script type="text/javascript">function change_folder_'.$this->id.'(path){
			document.forms["'.$this->form_name.'"].elements["'.$this->id.'[submitted]"].value="true";
			document.forms["'.$this->form_name.'"].elements["'.$this->id.'[path]"].value=path;
			document.forms["'.$this->form_name.'"].submit();}</script>';
		
		$this->set_attribute('style','width:100%;margin-bottom:20px;margin-top:0px;');
		
		if($this->task == 'delete')
		{
			foreach($this->selected as $file) {
				$file = smart_stripslashes($file);
				if (!$fs->delete($file)) {
					$feedback = $GLOBALS['strAccessDenied'];
					break;
				}
			}
		}
		
		if (isset($feedback))
		{
			$p = new html_element('p', $feedback);
			$p->set_attribute('class','Error');
			$this->add_outerhtml_element($p);
		}
		
	
		require($GO_LANGUAGE->get_language_file('filesystem'));

		$this->add_column(new table_heading($GLOBALS['strName']));
		$this->add_column(new table_heading($strType));
		$this->add_column(new table_heading($strSize));
		$this->add_column(new table_heading($GLOBALS['strDate']));

		$folders = $fs->get_folders($this->path);
		$files = $fs->get_files($this->path);

		foreach($folders as $folder)
		{
			if ($fs->get_share($folder['path'])) {
				$image = new image('shared_folder');
			} else {
				$image = new image('folder');
			}
			$image->set_attribute('align','absmiddle');
			$image->set_attribute('style','height:16;width:16;border:0px;margin-right:5px;');
			
			$sort_prefix = $this->sort_ascending ? '0' : '1';
			
			
			$row = new table_row($folder['path']);
			
			$row->set_attribute('ondblclick',  $this->get_folder_handler($folder['path']));
			
			$cell = new table_cell($sort_prefix.$folder['name'], $image->get_html().$folder['name']);
			if(isset($_SESSION['cut_files']) && in_array($folder['path'],$_SESSION['cut_files']))
			{
				$cell->set_attribute('class','cutItems');
			}
			
			$row->add_cell($cell);
			$row->add_cell(new table_cell($fbFolder));
			$row->add_cell(new table_cell('-'));
			$row->add_cell(new table_cell(date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], get_time($folder['mtime']))));
			$this->add_row($row);
		}

		$total_size = 0;
		foreach($files as $file)
		{
			$total_size += $file['size'];
			$short_name = cut_string(strip_extension($file['name']), 30);
			$extension = get_extension($file['name']);	
			
			$image = new image(false, get_filetype_image($extension));
			$image->set_attribute('align','absmiddle');
			$image->set_attribute('style','height:16;width:16;border:0px;margin-right:5px;');
			
			$sort_prefix = $this->sort_ascending ? '1' : '0';	

			$row = new table_row($file['path']);
			
			if(empty($this->handler))
			{			
				if($fs_settings['open_properties']=='1' || eregi('htm', $extension) || eregi('eml', $extension))
				{
					$row->set_attribute('ondblclick', "javascript:window.location.href='".
						$GO_MODULES->modules['filesystem']['url'].'index.php?task=properties&path='.urlencode($file['path'])."';");	
				}else
				{
					if($fs->is_viewable_in_browser($file['path']))
					{
						$row->set_attribute('ondblclick', "javascript:window.open('".
							$GO_MODULES->modules['filesystem']['url'].'download.php?mode=inline&path='.urlencode($file['path'])."');");	
					}else
					{
						$row->set_attribute('ondblclick', "javascript:window.location.href='".
							$GO_MODULES->modules['filesystem']['url'].'download.php?mode=download&path='.urlencode($file['path'])."';");	
					}
				}
			}else
			{
				$row->set_attribute('ondblclick', "javascript:window.location.href='".add_params_to_url($this->handler, 'path='.urlencode($file['path']))."';");	
			}
		
			$cell = new table_cell($sort_prefix.$file['name'], $image->get_html().htmlspecialchars($file['name']));
			if(isset($_SESSION['cut_files']) && in_array($file['path'],$_SESSION['cut_files']))
			{
				$cell->set_attribute('class','cutItems');
			}
			$row->add_cell($cell);
			$row->add_cell(new table_cell(get_filetype_description($extension)));
			$row->add_cell(new table_cell($file['size'],format_size($file['size'])));
			$row->add_cell(new table_cell(date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], get_time($file['mtime']))));
			$this->add_row($row);
		}

		$row = new table_row();
		$cell = new table_cell((count($folders)+count($files)).' '.$fbItems.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$fbFolderSize.': '.format_size($total_size));
		$cell->set_attribute('colspan','99');
		$cell->set_attribute('class','small');
		$cell->set_attribute('height','18');
		$row->add_cell($cell);
		
		if(count($this->rows))
		{
			$this->add_footer($row);
		}else
		{
			if($this->return_zero)
			{
				$this->add_row($row);
			}else
			{
				return '';
			}
		}
		return parent::get_html();
	}
}
