<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.50 $ $Date: 2006/11/21 16:25:34 $
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

require_once($GO_CONFIG->class_path.'base/controls/html_element.class.inc');
 
 class table extends html_element
{
	var $rows = array();
	var $headings = array();
	var $hover_effect=false;
	
	function table($id='')
	{
		$this->tagname = 'table';		
		$this->set_linebreak("\n");
		
		if($id != '')
		{
			$this->set_attribute('id', $id);
		}
	}
	
	function set_hover_effect()
	{
		$this->hover_effect=true;
	}
	
	function add_column($heading)
	{
		$this->headings[] = $heading;
	}
	
	function add_row($row)
	{
		if($this->hover_effect)
		{
			$row->set_attribute('onmouseover','javascript:table_glow_row(this);');
			$row->set_attribute('onmouseout','javascript:table_unglow_row(this);');
		}
		if(isset($this->attributes['id']))
		{
			$row->table_id=$this->attributes['id'];
		}
		$this->rows[] = $row;
	}
	
	function get_html()
	{	
		$html = '<table';
		foreach($this->attributes as $name=>$value)
		{
			$html .= ' '.$name.'="'.$value.'"';
		}
		$html .= '><tbody>'.$this->lb;
				
		$html .= $this->get_headings();
		$html .= $this->get_rows();
		$html .= '</tbody></table>'.$this->lb;			
		return $this->outerHTML.$html;			
	}
	
	function get_headings()
	{
		global $GO_THEME;
		
		$headings = '';
		if(count($this->headings) > 0)
		{			
			foreach($this->headings as $key=>$heading)
			{
				$headings .= '<th';
				foreach($heading->attributes as $name=>$value)
				{
					$headings .= ' '.$name.'="'.$value.'"';
				}
				$headings .= '>'.$heading->name.'</th>'.$this->lb;
			}
			$headings = '<tr>'.$this->lb.$headings.'</tr>'.$this->lb;
		}
		return $headings;
	}
	
	function get_rows()
	{
		$rows = '';
		foreach($this->rows as $row)
		{
			$rows .= $row->get_html();
		}

		return $rows;
	}
}

class table_row extends html_element
{
	var $id;
	var $cells = array();
	var $table;
	var $selected = false;
	
	/*
	* Constructor.
	*
	* @param id id tag of the tr element. Must be unique
	* @param value Often the same as id but sometimes you want to have duplicate values.
	*
	*	 @return void
	*/
	
	function table_row($id='', $value= '')
	{
		if($value == '')
		{
			$value = $id;
		}
		
		$this->value = $value;
	
		$this->tagname = 'tr';		
		$this->set_linebreak("\n");
		
		if($id != '')
		{
			$this->set_attribute('id', $id);
		}
	}
	
	function set_selected()
	{
		$this->selected = true;
	}
		
	function add_cell($cell)
	{
		$this->cells[] = $cell;
	}
	
	function unshift_cell($cell)
	{
		array_unshift($this->cells, $cell);
	}

	function get_html()
	{
		$row = '';
		if(!empty($this->attributes['id']) && isset($this->table))
		{
			$checkbox = new checkbox('dtcb_'.$this->attributes['id'], $this->table->attributes['id'].'[selected][]', $this->value);
			$checkbox->set_attribute('onclick', 'javascript:table_update_class(this, \''.addslashes($this->attributes['id']).'\')');
			if($this->selected)
			{
				$checkbox->set_attribute('checked','checked');
				$this->set_attribute('class', 'SelectedRow');
			}			
			$checkbox->set_attribute('style', 'display:none;');
			$row .=$checkbox->get_html().$this->lb;
		}
		
		$row .= '<tr';
		
		if(!empty($this->attributes['id']) && 		
		!isset($this->attributes['onclick']) && isset($this->table)
		)
		{
			$this->set_attribute('onclick', 'javascript:table_select(event, \''.
				$this->table->form_name.'\',\''.
				$this->table->attributes['id'].'\',\''.
				addslashes($this->attributes['id']).'\','.
				($this->table->multiselect ? 'true' : 'false').','.
				$_SESSION['GO_SESSION']['use_checkbox_select'].');');
		}
				
		foreach($this->attributes as $name=>$value)
		{
			$row .= ' '.$name.'="'.$value.'"';
		}
		$row .= '>'.$this->lb;
		
		/*
		Removed foreach statement because this caused a lot of memory usage
		Too bad the state of the object is lost when get_html() is called now*/
		//foreach($this->cells as $cell)
		$key=0;
		$cells=array();
		if(isset($this->table) && $this->table->allow_configuration)
		{
			while($cell = array_shift($this->cells))
			{			
				if( in_array($key, $this->table->enabled_columns))
				{			
					$cells[]=$cell;
				}
				$key++;
			}
			$this->cells=&$cells;
		}
		
		while($cell = array_shift($this->cells))
		{
			if(count($this->cells)==0 && isset($this->table) && $this->table->allow_configuration)			
			{
				if(isset($cell->attributes['colspan']))
				{
					$cell->attributes['colspan']++;
				}else {
					$cell->attributes['colspan']=2;
				}
			}	
			$row .= $cell->get_html();			
		}		
		$row .= '</tr>'.$this->lb;		
		return $row;
	}
}

class table_cell extends html_element
{
	var $sort_name = '';
	var $innerHTML = '';
	
	function table_cell($sort_name='', $innerHTML='')
	{
		$this->tagname = 'td';		
		$this->set_linebreak("\n");
		
		$this->sort_name = $sort_name;
		$this->innerHTML = empty($innerHTML) ? $sort_name : $innerHTML;
	}

	function get_html()
	{
			$html = '<td';			
			foreach($this->attributes as $name=>$value)
			{
				$html .= ' '.$name.'="'.$value.'"';
			}
			$html .= '>'.$this->lb.$this->innerHTML.'</td>'.$this->lb;
			
			return $html;
	}
}

class table_heading extends html_element
{
	var $sort_name = '';
	var $name = '';
	
	function table_heading($name='', $sort_name='')
	{
		$this->tagname = 'th';
		$this->set_linebreak("\n");
		
		$this->sort_name = $sort_name;
		$this->name = $this->innerHTML = $name;
	}
}
?>
