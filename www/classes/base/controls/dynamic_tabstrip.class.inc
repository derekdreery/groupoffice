<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.4 $ $Date: 2006/11/21 16:25:34 $
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

class dynamic_tabstrip extends table
{
  var $tabs = array();
  var $value;
  var $form_name;
  var $title = '';
  var $return_to = '';
  
  var $tabwidth;
  var $tab_direction;
  
  var $tabstrip_content='';
  
  var $submitted=false;
  //css classes

  var $css_tab = 'Tab';
  var $css_value = 'Activetab';
  var $css_tab_vert = 'TabVert';
  var $css_value_vert = 'ActivetabVert';
  var $css_table = 'tabtable';
  var $css_tab_background = 'TabBackground';
  var $css_tab_background_vert = 'TabBackgroundVert';

  //constructor sets basic properties
  function dynamic_tabstrip($id, $title='', $tabwidth='120', $form_name='0', $tab_direction='horizontal')
  {
  	$this->tagname = 'table';		
		$this->set_linebreak("\n");
		
    $this->id = $id;
    $this->title = $title;
    $this->tabwidth = $tabwidth;
    $this->tab_direction=$tab_direction;
    $this->form_name=$form_name;
    
    $this->value = isset($_REQUEST[$this->id]) ? $_REQUEST[$this->id] : null;
  }
  
  function set_return_to($return_to)
  {
  	$this->return_to = $return_to;
  }
   
  function set_classnames($table, $tab, $value, $tab_background)
  {
    $this->css_table= $table;
    $this->css_tab= $tab;
    $this->css_value = $value;
    $this->css_tab_background = $tab_background;
  }

  function add_tab($id, &$name, &$html_element)
  {
    $this->tabs[$id] = $name;
    
    $div = new html_element('div');
    $div->set_attribute('id',$id);    
    $div->add_html_element($html_element);

    if(!isset($this->value) || $this->value == '') $this->value = $id;
    
    if($this->value != $id)
    {
    	$div->set_attribute('style', 'display:none');
    }
    $this->add_html_element($div);
    
  }

  function set_active_tab($index)
  {
     	$this->value = $index;
  }
  
  function get_tab_handler($tab_id)
  {
  	return 'javascript:change_tab_'.$this->id.'(\''.addslashes($tab_id).'\');';
  }
  
  function get_tab_handler_for_function($varname)
  {
  	return 'change_tab_'.$this->id.'('.$varname.');';
  }
  
  function get_html()
  {
    $value_ref = $this->id;
    
    if ($this->tab_direction == 'vertical')
    {
      $this->css_value = $this->css_value_vert;
      $this->css_tab = $this->css_tab_vert;
      $this->css_tab_background = $this->css_tab_background_vert;
    }

    $tab_count = count($this->tabs);

    if ($tab_count > 0)
    {
    	$this->add_outerhtml_element(new input('hidden', $this->id, $this->value, false));    
      
      $this->innerHTML .= '
	<script type="text/javascript">
	function change_tab_'.$this->id.'(activetab)
	{
		document.getElementById("tab_"+document.forms["'.$this->form_name.'"].elements["'.$this->id.'"].value).className="Tab";
		document.getElementById("tab_"+activetab).className="ActiveTab";
		
		document.getElementById(document.forms["'.$this->form_name.'"].elements["'.$this->id.'"].value).style.display="none";
		document.getElementById(activetab).style.display="block";
		document.forms["'.$this->form_name.'"].elements["'.$this->id.'"].value=activetab;
	}
  </script>
	';
    }
   
        
    $this->set_attribute('class', $this->css_table);
    $this->set_attribute('cellpadding', '0');
    $this->set_attribute('cellspacing', '0');
    $this->set_attribute('border', '0');

    if ($this->title != '')
    {
    	$th = new table_heading($this->title);
    	 if ($this->tab_direction == 'vertical')
			{
				$th->set_attribute('colspan','3');
			}
    	$this->add_column($th);
    	
      if($this->return_to != '')
      {
      	$close_image = new image('close_small');
      	$close_image->set_attribute('style','border:0px;');
      	
      	$hyperlink = new hyperlink($this->return_to, $close_image->get_html());
      	$th = new table_heading($hyperlink->get_html());
      	$th->set_attribute('style','text-align:right');
      	$this->add_column($th);
      }
    }
    if ($tab_count > 0)
    {
    	$tabtable = new table();
    	$tabtable->set_attribute('cellpadding', '0');
    	$tabtable->set_attribute('cellspacing', '0');
    	$tabtable->set_attribute('border', '0');
    	
    	if ($this->tab_direction == 'horizontal')
      {
      	$tabrow = new table_row();
      }
      foreach ($this->tabs as $key => $value)
      {
      	if ($this->tab_direction == 'vertical')
				{
				  $tabrow = new table_row();
				}
				$tabcell = new table_cell($value);
				$tabcell->set_attribute('id','tab_'.$key);
				$tabcell->set_attribute('nowrap', 'true');
				$tabcell->set_attribute('onclick', $this->get_tab_handler($key));
				$tabcell->set_attribute('width', $this->tabwidth);
				if ($key == $this->value)
				{
					$tabcell->set_attribute('class', $this->css_value);
				}else
				{					
					$tabcell->set_attribute('class', $this->css_tab);
				}
				$tabrow->add_cell($tabcell);			
				if ($this->tab_direction == 'vertical')
				{
				  $tabtable->add_row($tabrow);
				}			
      }	
    	if ($this->tab_direction == 'horizontal')
      {
				$tabtable->add_row($tabrow);
      }    
    
    	$row = new table_row();
    	$cell = new table_cell($tabtable->get_html());
    	$cell->set_attribute('class', $this->css_tab_background);
    	$cell->set_attribute('valign', 'top');
    	//$cell->set_attribute('colspan', '2');
    	$row->add_cell($cell);
    	
    	if($this->tab_direction == 'horizontal')
	  	{
  			$this->add_row($row);
  			$row = new table_row();
  		}
    }else
    {
    	$row = new table_row();
    }
    	
  	$content_cell = new table_cell($this->innerHTML);
  	$content_cell->set_attribute('style', 'width:100%;height:100%;padding:10px;vertical-align:top;');
  	if($this->tab_direction == 'horizontal')
	  {
  		$content_cell->set_attribute('colspan','99');
  	}else
  	{
	  	$content_cell->set_attribute('colspan','2');
  	}
	
  	$row->add_cell($content_cell);    
  	
  	
  	$this->add_row($row);
  
    return parent::get_html();    
  }

  function get_active_tab_id()
  {
  	return $this->value;
  }
  
  function get_next_tab_id()
  {
  	foreach($this->tabs as $id=>$text)
  	{
  		if(isset($next))
  		{
  			return $id;
  		}
  		if($id == $this->value)
  		{
  			$next = true;
  		}
  	}
  	return false;
  }
}
