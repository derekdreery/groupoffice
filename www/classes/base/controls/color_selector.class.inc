<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.12 $ $Date: 2006/11/21 16:25:34 $

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Prints a color selector with custom colors
 *
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */
require_once($GO_CONFIG->class_path.'base/controls/html_element.class.inc');

class color_selector extends html_element
{
  
  var $form_name = 'forms[0]';

	/**
	* Prints the color selector
	*
	* @param String $name The name of the control
	* @param String $selected_color The selected color code
	* @param String $form_name The form name where the color selector is in
	* @access  public
	* @retrun void
	*/	
  function color_selector($id, $name, $value, $form_name ='0')
  {
    global $GO_THEME;
    
    $this->value= $value;
    $this->name= $name;
    $this->form_name= $form_name;    
    
    $this->set_attribute('id', $id);    
  }
  
  function get_header()
	{

	}
  
  function get_html()
  {    
  	global $GO_CONFIG;
  	
    $input = new input('hidden', $this->name, $this->value, false);    
    
    $return = $input->get_html();
        
    $link = new hyperlink('javascript:popup(\''.$GO_CONFIG->control_url.'color_selector.php?id='.urlencode($this->attributes['id']).'&name='.urlencode($this->name).'&form_name='.urlencode($this->form_name).'\',\'420\',\'360\');', '&nbsp;');
    $link->set_attribute('style', 'display:block;border:1px solid black;width:16px;height:16px;background-color:'.$this->value);
   	$link->set_attribute('id', 'color_link_'.$this->attributes['id']);
    
    $return .= $link->get_html();   
	   	   
	return $return;
  }
}
?>
