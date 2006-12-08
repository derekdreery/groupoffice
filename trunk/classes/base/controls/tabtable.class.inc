<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.17 $ $Date: 2005/06/19 22:19:28 $
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

class tabtable
{
  var $tabs = array();
  var $id;
  var $width;
  var $height;
  var $tabwidth;
  //arguments are extra vars that are passed in the links from the tabs
  var $arguments;
  var $submit_form;
  var $active_tab;
  var $form_name;

  //css classes

  var $css_tab = 'Tab';
  var $css_active_tab = 'Activetab';
  var $css_tab_vert = 'TabVert';
  var $css_active_tab_vert = 'ActivetabVert';
  var $css_table = 'tabtable';
  var $css_tab_background = 'TabBackground';
  var $css_tab_background_vert = 'TabBackgroundVert';

  //constructor sets basic properties
  function tabtable($id, $title='', $width='', $height='', $tabwidth='120', $arguments='', $submit_form=false, $align='left', $valign='top', $form_name='forms[0]', $tab_direction='horizontal')
  {
    $this->id = $id;
    $this->title = $title;
    $this->width = $width;
    $this->height = $height;
    $this->tabwidth = $tabwidth;
    $this->submit_form = $submit_form;
    $this->arguments = $arguments;
    $this->align=$align;
    $this->valign=$valign;
    $this->form_name=$form_name;
    $this->tab_direction=$tab_direction;    
    $this->active_tab = isset($_REQUEST[$this->id]) ? $_REQUEST[$this->id] : null;
  }
  
  function set_classnames($table, $tab, $active_tab, $tab_background)
  {
    $this->css_table= $table;
    $this->css_tab= $tab;
    $this->css_active_tab = $active_tab;
    $this->css_tab_background = $tab_background;
  }

  //hint: Id could be a document to include
  function add_tab($id, $name)
  {
    $this->tabs[$id] = $name;
    if(!isset($this->active_tab) || $this->active_tab == '') $this->active_tab = $id;
    
  }

  //set the active tab manually call this before the print_head() function
  function set_active_tab($index)
  {
  	if(isset($this->tabs[$index]))
  	{
     	$this->active_tab = $index;
  	}	    
  }



  /*last three functions should be called in this order
    $tabtable->print_head();
    require_once($tabtable->get_active_tab_id());
    $tabtable->print_foot();
   */
  function print_head($return_to='')
  {
  	global $GO_THEME;

		//Security check so nobody passes /etc/passwd for example to the tabtable
		//Some pages use require($tabtable->get_active_tab_id();
		//var_dump($this->active_tab);
  	if(isset($this->active_tab) && $this->active_tab != '' && !isset($this->tabs[$this->active_tab]))
  	{
  		exit('Error, non existing tab set by user');
  	}
  	
    $active_tab_ref = $this->id;
    
    if ($this->tab_direction == 'vertical')
    {
      $this->css_active_tab = $this->css_active_tab_vert;
      $this->css_tab = $this->css_tab_vert;
      $this->css_tab_background = $this->css_tab_background_vert;
    }

    $tab_count = count($this->tabs);
    if ($tab_count > 0 && $this->submit_form)
    {
      echo '
	<script type="text/javascript">
	function change_tab(activetab)
	{';
	  #document.'.$this->form_name.'.action.value = document.forms[0].action.value+"'.$this->arguments.'";
	echo  '
		document.'.$this->form_name.'.action.value = document.'.$this->form_name .'.action.value+"'.$this->arguments.'";
	  document.'.$this->form_name.'.'.$active_tab_ref.'.value = activetab;
	  document.'.$this->form_name.'.submit();
	}
      </script>
	';
    }
    echo '<input type="hidden" name="'.$active_tab_ref.'" value="'.$this->active_tab.'" />';
    echo '<table border="0" cellpadding="0" cellspacing="0" class="'.$this->css_table.'" width="'.$this->width.'" height="'.$this->height.'">';
    if ($this->title != '')
    {
      echo '<tr><th';
      if ($this->tab_direction == 'vertical')
		{
			echo ' colspan="3"';
		}
      echo '>'.$this->title.'</th>';
      if($return_to != '')
      {
      	echo '<th align="right"><a href="'.$return_to.'"><img src="'.$GO_THEME->images['close_small'].'" border="0" /></a></th>';
      }
      echo '</tr>';
    }

    $remaining_width= $this->width - ($tab_count * $this->tabwidth);
    if ($tab_count > 0)
    {
      echo '<tr><td class="'.$this->css_tab_background.'" valign="top" colspan="2">';
      echo '<table border="0" cellpadding="0" cellspacing="0">';
      if ($this->tab_direction == 'horizontal')
      {
	echo '<tr>';
      }
      //draw tabs and select the active doc
      foreach ($this->tabs as $key => $value)
      {
	if ($this->tab_direction == 'vertical')
	{
	  echo '<tr>';
	}
	if ($key == $this->active_tab)
	{
	  echo '<td class="'.$this->css_active_tab.'" width="'.$this->tabwidth.'" nowrap>'.$value.'</td>';
	}else
	{
	  if ($this->submit_form)
	  {
	    echo '<td class="'.$this->css_tab.'" width="'.$this->tabwidth.'" nowrap><a class="'.$this->css_tab.'" href="javascript:change_tab(\''.$key.'\')">'.$value.'</a></td>';
	  }else
	  {
	    echo '<td class="'.$this->css_tab.'" width="'.$this->tabwidth.'" nowrap><a class="'.$this->css_tab.'" href="'.$_SERVER['PHP_SELF'].'?'.$active_tab_ref.'='.urlencode($key).$this->arguments.'">'.$value.'</a></td>';
	  }
	}
	if ($this->tab_direction == 'vertical')
	{
	  echo '</tr>';
	}
      }
      if ($this->tab_direction == 'horizontal')
      {
	echo '</tr>';
      }
      echo '</table>';
      echo '</td>';
    }

    if ($this->tab_direction == 'horizontal')
    {
      echo '</tr><tr>';
    }
    echo '<td height="100%" valign="'.$this->valign.'" width="100%" align="'.$this->align.'" style="padding: 10px;">';

  }

  function get_active_tab_id()
  {
  	return $this->active_tab;
  }

  function print_foot()
  {
    echo '</td></tr></table>';
  }
}
?>
