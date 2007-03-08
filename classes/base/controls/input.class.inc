<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.5 $ $Date: 2006/11/21 16:25:34 $
 *
   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Create an form input field
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */
require_once($GO_CONFIG->class_path.'base/controls/html_element.class.inc');

class input extends html_element
{
	function input($type, $name, $value='', $remind_value=true)
	{
		$this->tagname = 'input';
		$this->set_linebreak("\n");
		
		$this->set_attribute('type', $type);
		$this->set_attribute('name', $name);
		if($remind_value && isset($_POST[$name]))
		{
			$this->set_attribute('value', htmlspecialchars(smart_stripslashes($_POST[$name])));
		}else
		{
			$this->set_attribute('value', htmlspecialchars($value));
		}
	}
	
	function get_html()
	{	
		if(!isset($this->attributes['class']) && $this->attributes['type'] != 'hidden')
		{
			$this->set_attribute('class','textbox');
		}
		
		$html = '<input';				
		foreach($this->attributes as $name=>$value)
		{
			$html .= ' '.$name.'="'.$value.'"';
		}
		$html .= ' />'.$this->lb;
		
		return $html;
	}
}
