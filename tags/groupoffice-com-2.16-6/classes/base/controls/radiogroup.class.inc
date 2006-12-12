<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.6 $ $Date: 2006/11/21 16:25:34 $

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Create a group of radio buttons
 * 
 * This class is used to create and draw radio-lists on the website. If it is
 * disabled it's color is hardcoded to #7d7d7d. This function uses JavaScript
 * when selection an item (<b>select_radio</b>).
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */

require_once($GO_CONFIG->class_path.'base/controls/html_element.class.inc');

class radiogroup extends html_element
{
  /**
   * The name of the radiolist.
   * 
   * @access private
   * @var string
   */
  var $group;
  /**
   * The value of the preselected item.
   * 
   * @access private
   * @var string
   */
  var $selected;
   
  /**
   * Create a new radio-list.
   * 
   * @access public
   * 
   * @param string $group is the name of this radio-list.
   * @param string $selected is the preselected item of the list.
   * 
   * @return void
   */
   
   var $outerHTML = '';
   
  function radiogroup($group, $selected='', $remind=true)
  {
    $this->group = $group;
    if($remind && isset($_POST['group']))
    {
    	$this->selected = smart_stripslashes($_POST['group']);
    }else
    {
    	$this->selected = $selected;
    }
  }

  /**
   * Get a new option-field for the radio-list.
   * 
   * This function returns a string which represents the new option-field
   * of the current radio-list.
   * 
   * @access public
   * 
   * @param string $value is the value of the item we add to the list.
   * @param string $text is the caption of this item.
   * @param bool $disabled can be used to disable this item.
   * @param string $attributes
   * 
   * @return void
   */
  function get_option($radiobutton, $text)
  {    
    if ( $this->selected == $radiobutton->attributes['value'] ) {
      $radiobutton->set_attribute('checked','checked');
    } 
    
    $radiobutton->set_attribute('name', $this->group);
    

		$span = new html_element('span',$text);
		
    if ( $text != '' ) {
      if (isset($radiobutton->attributes['disabled'])) {      	
      	$span->set_attribute('style', 'color: #7d7d7d');
      } else {
	      $span->set_attribute('onclick', 'javascript:select_radio(\''.$radiobutton->attributes['id'].'\');');
	      $span->set_attribute('style', 'cursor:default;');
      }
    }
    return $radiobutton->get_html().$span->get_html();
  }
}

class radiobutton extends input
{
	function radiobutton($id, $value)
	{
		$this->input('radio', '', $value, false);
		$this->set_attribute('class','');
		$this->set_attribute('id', $id);
	}
}
?>
