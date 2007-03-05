<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.19 $ $Date: 2006/11/21 16:25:34 $

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Create a checkbox.
 * 
 * This class is used to draw checkboxes on the website.
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */

require_once($GO_CONFIG->class_path.'base/controls/html_element.class.inc');

class checkbox extends html_element
{
	var $text;
	/**
   * Draw a new checkbox.
   * 
   * If you supply a name for this checkbox, it will be directly printed
   * on the page, else the constructor does nothing.
   * 
   * @access public
   * 
   * @param string $name
   * @param string $value
   * @param string $text
   * @param bool $checked defines if this checkbox is checked or not.
   * @param bool $disabled can be used to disable this checkbox.
   *
   * @return void
   */
	function checkbox($id='', $name='', $value='', $text='', $checked=false, $disabled=false)
	{
		$this->tagname = 'input';
		$this->set_linebreak("\n");

		$this->set_attribute('type', 'checkbox');
		//$this->set_attribute('class', 'textbox');
		$this->set_attribute('style','margin:0px;padding:0px;margin-right:3px');
		$this->set_attribute('name', $name);
		if(empty($id))
		{
			$id = uniqid(time());
		}
		$this->set_attribute('id', $id);
		$this->set_attribute('value', $value);
		if($disabled)
		{
			$this->set_attribute('disabled', 'disabled');
		}
		if($checked)
		{
			$this->set_attribute('checked', 'checked');
		}

		$this->text=$text;

	}

	function get_click_handler()
	{
		return "javascript:check_checkbox('".$this->attributes['id']."');";
	}

	/**
   * Create a checkbox.
   *
   * This function returns a string that can be used to print a button on the
   * website.
   * 
   * @access public
   * 
   * @param string $name
   * @param string $value
   * @param string $text
   * @param bool $checked defines if this checkbox is checked or not.
   * @param bool $disabled can be used to disable this checkbox.
   * @param string $attributes
   * 
   * @return string
   */
	function get_html()
	{
		if ( $this->text != '' ) {
			
			
			
			$div = new html_element('div', $this->text);
			$div->set_attribute('onselectstart', 'return false;');

			if ( isset($this->attributes['disabled']) ) {
				$div->set_attribute('style','display:inline;vertical-align:top;color:#7d7d7d;cursor:default;margin-right:5px;-moz-user-select:none');
			} else {
				$div->set_attribute('onclick', $this->get_click_handler());
				$div->set_attribute('style','display:inline;vertical-align:top;cursor:default;margin-right:5px;-moz-user-select:none');
			}
			
			return parent::get_html().$div->get_html();
		}else
		{
			return parent::get_html();
		}
	}
}
