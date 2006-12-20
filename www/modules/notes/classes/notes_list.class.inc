<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.8 $ $Date: 2006/11/23 12:39:28 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
load_control('datatable');

class notes_list extends datatable
{
	var $count;
	
	function notes_list($id, $user_id, $link_id=0, $return_zero=true, $form_name='0', $return_to='')
	{
		global $GO_CONFIG, $GO_SECURITY, $GO_MODULES, $GO_LANGUAGE, $GO_THEME, $GO_LINKS;
		
		$this->datatable($id, false, $form_name);
		$this->set_attribute('style','width:100%;margin-bottom:20px;margin-top:0px;');
		
		
		$return_to = !empty($return_to) ? $return_to : $_SERVER['PHP_SELF'];
		
		require($GO_LANGUAGE->get_language_file('notes'));

		require_once($GO_MODULES->modules['notes']['class_path']."notes.class.inc");
		$notes = new notes();

		if($this->task == 'delete')
		{
			foreach($this->selected as $delete_note_id)
			{
				$delete_note_id =str_replace('4:', '', $delete_note_id);
				$notes->delete_note($delete_note_id);
			}
		}elseif($this->task=='unlink')
		{
			global $GO_LINKS;
			
			foreach($this->selected as $note_id)
			{
				$note_id =str_replace('4:', '', $note_id);
				if($note = $notes->get_note($note_id))
				{
					$GO_LINKS->delete_link($link_id, $note['link_id']);
				}
			}
		}
		
		$links = $link_id > 0 ? $GO_LINKS->get_links($link_id, 4) : false;
		
		if (isset($feedback))
		{
			$p = new html_element('p', $feedback);
			$p->set_attribute('class','Error');
			$this->add_html_element($p);
		}
		

		$this->add_column(new table_heading($GLOBALS['strName'], 'name'));
		$this->add_column(new table_heading($GLOBALS['strModifiedAt'], 'mtime'));

		$th = new table_heading('&nbsp;');
		//$this->add_column($th);
		
	$this->count = $notes->get_notes($user_id, 
		$links, 
		$this->sort_index, 
		$this->sql_sort_order, 
		$this->start, 
		$this->offset);

		$this->set_pagination($this->count);

		if($this->count > 0)
		{
			while($notes->next_record())
			{
				$row = new table_row('4:'.$notes->f('id'));
				
				$row->set_attribute('ondblclick', 
							"javascript:window.location.href='".					
							$GO_MODULES->modules['notes']['url'].'note.php?note_id='.$notes->f('id').
							'&return_to='.urlencode($return_to)."';");	
				
				if($notes->f('content') != '')
				{
					$img = new image('plus_node');
					$img->set_attribute('style','border:0px;margin-right:5px;');
					$img->set_attribute('onclick','javascript:toggle_note(this);');
					$img->set_attribute('id',$notes->f('id'));
							
							
					$cell = new table_cell($img->get_html().htmlspecialchars($notes->f('name')));
					$div = new html_element('div', text_to_html($notes->f('content')));
					$div->set_attribute('class', 'description');
					$div->set_attribute('style','display:none');
					$div->set_attribute('id',$notes->f('id').'_div');
					$cell->add_html_element($div);
				}else
				{
					$img = new image('blank');
					$img->set_attribute('style','border:0px;margin-right:5px;');
					$cell = new table_cell($img->get_html().htmlspecialchars($notes->f('name')));
				}
				$row->add_cell($cell);
				
				$cell = new table_cell(get_timestamp($notes->f('mtime')));
				$cell->set_attribute('valign','top');
				$row->add_cell($cell);
	
				$this->add_row($row);	
			}		
		}else
		{
			if($return_zero)
			{
				$row = new table_row();
				$cell = new table_cell($no_no_notes);
				$cell->set_attribute('colspan','99');
				$row->add_cell($cell);
				$this->add_row($row);
			}else
			{
				$this->innerHTML = '';
				return false;
			}
		}
		
		$this->outerHTML .= '<script type="text/javascript">
		function toggle_note(img)
		{
			
			if(document.getElementById(img.id+"_div").style.display=="none")
			{
				img.src="'.$GO_THEME->images['min_node'].'";
				document.getElementById(img.id+"_div").style.display="block";
			}else
			{
				img.src="'.$GO_THEME->images['plus_node'].'";
				document.getElementById(img.id+"_div").style.display="none";
			}
		}
		
		
		</script>';
	}
}
