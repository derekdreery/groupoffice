<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.1 $ $Date: 2006/11/22 11:02:00 $
 *
   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Creates a table that uses tabs to show multiple screens
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */

require_once($GO_CONFIG->class_path.'base/controls/table.class.inc');


class datatable extends table
{
	var $footer;

	var $form_name = 'forms[0]';

	var $sort_index;
	var $sort_ascending = true;

	var $sql_sort_order='ASC';

	var $sort_table = array();

	var $start = 0;
	var $offset = 0;
	var $total_rows = 0;
	var $max_links=10;

	var $row_count = 0;

	var $autosort = false;

	var $task ='';

	var $selected = array();

	var $hidden_selected = array();

	var $remind_selected = false;

	var $multiselect = true;

	var $enabled_columns=array();

	var $allow_configuration=false;

	function datatable($id, $autosort=false, $form_name='0')
	{
		global $GO_CONFIG, $GO_SECURITY;

		$this->tagname = 'table';
		$this->set_linebreak("\n");

		$this->set_attribute('id', $id);
		$this->set_attribute('onselectstart', 'return false;');

		$_SESSION[$this->attributes['id']] =
		(isset($_SESSION[$this->attributes['id']]) && is_array($_SESSION[$this->attributes['id']])) ?
		$_SESSION[$this->attributes['id']] : array();



		$this->form_name = $form_name;
		$this->autosort = $autosort;

		$this->sort_index = $GO_CONFIG->get_setting('sort_index_'.$id, $GO_SECURITY->user_id);
		$this->sort_ascending = $GO_CONFIG->get_setting('sort_asc_'.$id, $GO_SECURITY->user_id);


		if(isset($_POST[$id]['sort_index']) && $_POST[$id]['sort_index'] != $this->sort_index)
		{
			$GO_CONFIG->save_setting(
			'sort_index_'.$id,
			smart_addslashes($_POST[$id]['sort_index']),
			$GO_SECURITY->user_id);

			$this->sort_index = smart_stripslashes($_POST[$id]['sort_index']);
		}

		if(isset($_POST[$id]['sort_ascending']) && $_POST[$id]['sort_ascending'] != $this->sort_ascending)
		{
			$GO_CONFIG->save_setting(
			'sort_asc_'.$id,
			smart_addslashes($_POST[$id]['sort_ascending']),
			$GO_SECURITY->user_id);

			$this->sort_ascending = smart_stripslashes($_POST[$id]['sort_ascending']);
		}

		$this->sql_sort_order = $this->sort_ascending ? 'ASC' : 'DESC';

		$_SESSION['GO_SESSION']['max_rows_list'] = isset($_SESSION['GO_SESSION']['max_rows_list']) ? $_SESSION['GO_SESSION']['max_rows_list'] : 25;

		$this->offset = isset($_POST[$this->attributes['id']]['offset']) ? $_POST[$this->attributes['id']]['offset'] : $_SESSION['GO_SESSION']['max_rows_list'];

		if(isset($_POST[$this->attributes['id']]['start']))
		{
			$this->start = $_SESSION[$this->attributes['id']]['start'] = $_POST[$this->attributes['id']]['start'];
		}else
		{
			$this->start =  isset($_SESSION[$this->attributes['id']]['start']) ? $_SESSION[$this->attributes['id']]['start'] : 0;

		}

		$this->task = isset($_POST[$this->attributes['id']]['task']) ? $_POST[$this->attributes['id']]['task'] : '';
		$this->selected = isset($_POST[$this->attributes['id']]['selected']) ? array_map('smart_stripslashes',$_POST[$this->attributes['id']]['selected']) : array();


		
	}
	
	function allow_configuration()
	{
		$this->allow_configuration=true;
		
		global $GO_CONFIG;
		$this->enabled_columns = $GO_CONFIG->get_setting('enabled_columns_'.$this->attributes['id']);
		if(!$this->enabled_columns)
		{
			$this->enabled_columns = array(0,1,2,3,4,5,6);
		}else {
			$this->enabled_columns = explode(',', $this->enabled_columns);
		}	
	}

	function enable_select()
	{
		$this->set_attribute('style','-moz-user-select:text');
		$this->set_attribute('onselectstart','');
	}

	function is_sort_index($sort_index=null)
	{
		if(!isset($sort_index))
		{
			$sort_index=$this->sort_index;
		}
		foreach($this->headings as $heading)
		{
			if($heading->sort_name==$this->sort_index)
			{
				return true;
			}
		}
		return false;
	}

	function get_header()
	{
		global $GO_CONFIG;
		return '<script type="text/javascript" src="'.$GO_CONFIG->host.'javascript/datatable.js"></script>';
	}

	function set_remind_selected($remind_selected)
	{
		$this->remind_selected = $remind_selected;
	}

	function set_multiselect($multiselect)
	{
		$this->multiselect=$multiselect;
	}

	function set_pagination($total_rows)
	{
		$this->total_rows = $total_rows;
	}

	function add_column($heading)
	{
		if(empty($this->sort_index))
		{
			if($this->autosort)
			{
				$this->sort_index = 0;
			}else
			{
				if(!empty($heading->sort_name))
				{
					$this->sort_index = $heading->sort_name;
				}
			}
		}
		$this->headings[] = $heading;
	}

	function unshift_column($heading)
	{
		if(empty($this->sort_index))
		{
			if($this->autosort)
			{
				$this->sort_index = 0;
			}else
			{
				if(!empty($heading->sort_name))
				{
					$this->sort_index = $heading->sort_name;
				}
			}
		}
		array_unshift($this->headings, $heading);
	}

	function add_footer($row)
	{
		$this->footer = $row;
	}

	function add_row($row)
	{
		$row->table= &$this;

		if($this->hover_effect)
		{
			$row->set_attribute('onmouseover','javascript:table_glow_row(this);');
			$row->set_attribute('onmouseout','javascript:table_unglow_row(this);');
		}

		$this->rows[]  = $row;
		if($this->autosort)
		{
			$this->sort_table[] = $row->cells[$this->sort_index]->sort_name;
		}
	}

	function sort($sort_index, $sort_ascending =true)
	{
		$this->sort_index = $sort_index;
		$this->sort_ascending = $sort_ascending ;
	}

	function get_count_selected_handler()
	{
		return "table_count_selected('".$this->form_name."','".$this->attributes['id']."');";
	}

	function get_unlink_handler()
	{
		return "javascript:table_confirm_unlink('".$this->form_name."','".$this->attributes['id']."', '".
		htmlspecialchars(addslashes($GLOBALS['strNoItemSelected']))."', '".
		htmlspecialchars(addslashes($GLOBALS['strSelectedItem']))."', '".
		htmlspecialchars(addslashes($GLOBALS['strSelectedItems']))."', '".
		htmlspecialchars(addslashes($GLOBALS['strUnlinkPrefix']))."', '".
		htmlspecialchars(addslashes($GLOBALS['strUnlinkSuffix']))."');";
	}

	function get_delete_handler($row_id=0, $confirm_delete=true)
	{
		if	($row_id > 0)
		{
			$str = "javascript:table_select_single('".$this->form_name."','".$this->attributes['id']."', '".$row_id."');";
		}else
		{
			$str = "javascript:";
		}
		if($confirm_delete)
		{
			$str .= "table_confirm_delete('".$this->form_name."','".$this->attributes['id']."', '".
			htmlspecialchars(addslashes($GLOBALS['strNoItemSelected']))."', '".
			htmlspecialchars(addslashes($GLOBALS['strSelectedItem']))."', '".
			htmlspecialchars(addslashes($GLOBALS['strSelectedItems']))."', '".
			htmlspecialchars(addslashes($GLOBALS['strDeletePrefix']))."', '".
			htmlspecialchars(addslashes($GLOBALS['strDeleteSuffix']))."');";
		}else {
			$str .= "table_delete('".$this->form_name."','".$this->attributes['id']."');";
		}

		return $str;
	}

	function set_page_one()
	{
		return 'document.forms[\''.$this->form_name.'\'].elements[\''.$this->attributes['id'].'[start]\'].value=0;';
	}

	function get_task_var()
	{
		return 'document.forms[\''.$this->form_name.'\'].elements[\''.$this->attributes['id'].'[task]\']';
	}

	function get_html()
	{

		

		if($this->remind_selected)
		{
			$this->hidden_selected = array_map('smart_stripslashes',$this->selected);
		}

		$table = '<input type="hidden" name="'.$this->attributes['id'].'[sort_ascending]" value="'.$this->sort_ascending.'" />'.$this->lb;
		$table .= '<input type="hidden" name="'.$this->attributes['id'].'[sort_index]"  value="'.$this->sort_index.'" />'.$this->lb;
		$table .= '<input type="hidden" name="'.$this->attributes['id'].'[offset]"  value="'.$this->offset.'" />'.$this->lb;
		$table .= '<input type="hidden" name="'.$this->attributes['id'].'[start]"  value="'.$this->start.'" />'.$this->lb;
		$table .= '<input type="hidden" name="'.$this->attributes['id'].'[task]"  value="" />'.$this->lb;



		if(!isset($this->attributes['class']))
		{
			$this->set_attribute('class','go_table');
		}

		$table .= '<table';
		foreach($this->attributes as $name=>$value)
		{
			$table .= ' '.$name.'="'.$value.'"';
		}
		$table .= '>'.$this->lb;

		$table .= $this->get_headings();
		$table .= $this->get_rows();
		if(isset($this->footer))
		{
			$table .= $this->footer->get_html();
		}
		$table .= $this->get_pagination();

		$table .= '</table>'.$this->lb;

		$table .= $this->get_hidden_rows();

		return $this->outerHTML.$table;
	}

	function get_headings()
	{
		global $GO_THEME, $GO_CONFIG;

		$select_all_checkbox = new input('checkbox', 'dummy');
		$select_all_checkbox->set_attribute('class','');
		$select_all_checkbox->set_attribute('style','margin:0px;padding:0px;');

		$select_all_checkbox->set_attribute('onclick',"javascript:table_select_all('".$this->form_name."','".$this->attributes['id']."',this.checked)");


		if ($this->sort_ascending)
		{
			$image = new image('arrow_up');
		}else
		{
			$image = new image('arrow_down');
		}
		$image->set_attribute('style','border:0px;margin-left:5;');

		$available_columns=array();
		$headings = '';
		foreach($this->headings as $key=>$heading)
		{
			$available_columns[]=$key.':'.base64_encode($heading->name);

			if(!$this->allow_configuration || in_array($key, $this->enabled_columns))
			{
				if(empty($heading->sort_name) && $this->autosort)
				{
					$heading->sort_name = $key;
				}

				$headings .= '<th';
				foreach($heading->attributes as $name=>$value)
				{
					$headings .= ' '.$name.'="'.$value.'"';
				}
				$headings .= '>';

				if(empty($heading->name) && !isset($checkbox_added) && $this->multiselect)
				{
					$checkbox_added=true;
					$headings .= $select_all_checkbox->get_html();
				}else {


					if(!isset($checkbox_added) && $this->multiselect)
					{
						$headings .= '<table cellpadding="0" cellspacing="0"><tr><td>'.$select_all_checkbox->get_html().'</td><td>';
					}

					if($heading->sort_name !== '')
					{
						$headings .= '<a href="javascript:table_sort(\''.$this->form_name.'\',\''.$this->attributes['id'].'\',\''.$heading->sort_name.'\',';
						if(($this->sort_ascending))
						{
							$headings .= '0';
						}else
						{
							$headings .= '1';
						}
						$headings .= ');">'.$heading->name;

						if($heading->sort_name == $this->sort_index)
						{
							$headings .= $image->get_html();
						}

						$headings .= '</a>';
					}else
					{
						$headings .= $heading->name;
					}

					if(!isset($checkbox_added) && $this->multiselect)
					{
						$checkbox_added = true;
						$headings .= '</td></tr></table>';
					}
				}
				$headings .= '</th>'.$this->lb;
			}
		}
		if(!empty($headings))
		{
			$headings = '<tr>'.$this->lb.$headings;

			if($this->allow_configuration)
			{
				$headings .= '<th style="width:20px;"><a href="javascript:popup(\''.$GO_CONFIG->control_url.'table_config.php?table_id='.urlencode($this->attributes['id']).'&available_columns='.implode(';',$available_columns).'\',\'400\',\'400\');">'.
				'<img src="'.$GO_THEME->images['configuration'].'" style="border:0px;height:16px;width:16px;" />'.
				'</a></th>'.$this->lb;


			}
			$headings .= '</tr>'.$this->lb;

			return $headings;
		}else
		{
			return '';
		}
	}

	function update_row_selection(&$row)
	{
		if($this->remind_selected && !empty($row->value))
		{
			$key = array_search(smart_addslashes($row->value), $this->hidden_selected);
			if (is_int($key))
			{
				unset($this->hidden_selected[$key]);
				$row->set_selected();
			}
		}
	}


	function get_rows()
	{
		$rows = '';
		if($this->autosort)
		{
			if($this->sort_ascending)
			{
				asort($this->sort_table);
			}else
			{
				arsort($this->sort_table);
			}

			foreach($this->sort_table as $key=>$value)
			{
				$this->update_row_selection($this->rows[$key]);
				$rows .= $this->rows[$key]->get_html();
			}
		}else
		{
			/*
			Removed foreach statement because this caused a lot of memory usage
			Too bad the state of the object is lost when get_html() is called now*/
			//foreach($this->rows as $row)
			while($row = array_shift($this->rows))
			{
				$this->update_row_selection($row);
				$rows .= $row->get_html();
			}
		}
		return $rows;
	}

	function get_hidden_rows()
	{
		$hidden_selected_rows = '';
		foreach($this->hidden_selected as $hidden_selected_value)
		{
			$input = new input('hidden', $this->attributes['id'].'[selected][]', $hidden_selected_value);
			$hidden_selected_rows .= $input->get_html();
		}
		return $hidden_selected_rows;
	}

	function get_pagination()
	{
		global $cmdShowAll, $cmdPrevious, $cmdNext;
		$links = '';

		if ($this->offset != 0)
		{
			if ($this->total_rows > $this->offset)
			{
				$links = '<table class="navLinks"><tr><td>';
				$next_start = $this->start+$this->offset;
				$previous_start = $this->start-$this->offset;

				if ($this->start != 0)
				{
					$links .= '<a href="javascript:table_change_page(\''.$this->form_name.'\',\''.$this->attributes['id'].'\',0, '.$this->offset.');">&lt&lt</a>&nbsp;';
					$links .= '<a href="javascript:table_change_page(\''.$this->form_name.'\',\''.$this->attributes['id'].'\','.$previous_start.', '.$this->offset.');">'.$cmdPrevious.'</a>&nbsp;';
				}else
				{
					$links .= '<font color="#cccccc">&lt&lt '.$cmdPrevious.'</font>&nbsp;';
				}

				$start_link = ($this->start-(($this->max_links/2)*$this->offset));
				$end_link = ($this->start+(($this->max_links/2)*$this->offset));

				if ($start_link < 0)
				{
					$end_link = $end_link - $start_link;
					$start_link=0;
				}
				if ($end_link > $this->total_rows)
				{
					$end_link = $this->total_rows;
				}

				if ($start_link > 0)
				{
					$links .= '...&nbsp;';
				}

				for ($i=$start_link;$i<$end_link;$i+=$this->offset)
				{
					$page = ($i/$this->offset)+1;
					if ($i==$this->start)
					{
						$links .= '<b><i>'.$page.'</i></b>&nbsp;';
					}else
					{
						$links .= '<a href="javascript:table_change_page(\''.$this->form_name.'\',\''.$this->attributes['id'].'\','.$i.','.$this->offset.');">'.$page.'</a>&nbsp;';
					}
				}

				if ($end_link < $this->total_rows)
				{
					$links .= '...&nbsp;';
				}

				$last_page = floor($this->total_rows/$this->offset)*$this->offset;

				if ($this->total_rows > $next_start)
				{
					$links .= '<a href="javascript:table_change_page(\''.$this->form_name.'\',\''.$this->attributes['id'].'\','.$next_start.', '.$this->offset.');">'.$cmdNext.'</a>&nbsp;';
					$links .= '<a href="javascript:table_change_page(\''.$this->form_name.'\',\''.$this->attributes['id'].'\','.$last_page.', '.$this->offset.');">&gt&gt</a>';
				}else
				{
					$links .= '<font color="#cccccc">'.$cmdNext.' &gt&gt</font>';
				}
				$links .= '</td><td align="right"><a class="normal" href="javascript:table_change_page(\''.$this->form_name.'\',\''.$this->attributes['id'].'\',0,0);">'.$cmdShowAll.'</a></td></tr></table>';
				$links =   '<tr><td colspan="99" height="20">'.$links.'</td></tr>';
			}
		}
		return $links;
	}
}
