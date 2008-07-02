<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.13 $ $Date: 2006/11/21 16:25:34 $

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Create a button.
 * 
 * This class is used to draw buttons on the website. The button uses the
 * following two styles:
 * - <b>button</b> is the default style used for drawing the button.
 * - <b>button_mo</b> is the onmouseover style used for drawing the button.
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */

require_once($GO_CONFIG->class_path.'base/controls/input.class.inc');

class button extends input
{
  /**
   * Constructor: Draw a new button.
   * 
   * If you supply a caption for this button it will be directly printed
   * on the page, else the constructor does nothing.
   * 
   * @see get_button()
   * 
   * @access public
   * 
   * @param string $text is the caption of this button.
   * @param string $action is some JavaScript code which should be executed
   *        when the button is pressed.
   * @param string $size is the size (in px) of this button.
   * 
   * @return void
   */
   
   var $width;
   
  function button($value, $onclick, $width='100px')
  {
  	$this->width=$width;
  	$this->tagname = 'input';		
		$this->set_linebreak("\n");
		
		$this->set_attribute('type', 'button');		
		$this->set_attribute('value', htmlspecialchars($value, ENT_QUOTES));
		$this->set_attribute('onclick',$onclick);
		$this->set_attribute('class','button');
		$this->set_attribute('onmouseover',"javascript:this.className='button_mo';");
		$this->set_attribute('onmouseout',"javascript:this.className='button';");		
  }

  /**
   * Create a button.
   * 
   * This function returns a string that can be used to print a button on the
   * website. The string will look like the following:
   * <code>
   * <input type="button" class="button" style="width: $size px;" \\
   * value="$text" onclick="$action" \\
   * onmouseover="javascript:this.className='button_mo';" \\
   * onmouseout="javascript:this.className='button';" />
   * </code>
   * 
   * @access public
   * 
   * @param string $text is the caption of the button.
   * @param string $action is some JavaScript code which should be executed
   *        when the button is pressed.
   * @param string $size is the size (in px) of this button.
   * 
   * @return string
   */
  
  function get_html()
  {
  	if(!isset($this->attributes['style']))
  	{
  		$this->set_attribute('style','width:'.$this->width.';');
  	}
  	return parent::get_html();
  }
}
