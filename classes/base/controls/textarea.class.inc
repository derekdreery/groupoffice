<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.3 $ $Date: 2006/11/21 16:25:34 $
 *
   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Create a textarea field
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */
require_once($GO_CONFIG->class_path.'base/controls/html_element.class.inc');

class textarea extends html_element
{
	var $value;
	
	function textarea($name, $value)
	{
		$this->tagname = 'textarea';		
		$this->set_linebreak("\n");
		
		$this->set_attribute('name', $name);
		$this->value = htmlspecialchars($value);
	}
	
	function get_html()
	{	
		if(!isset($this->attributes['class']))
		{
			$this->set_attribute('class','textbox');
		}
		
		$html = '<textarea';
		foreach($this->attributes as $name=>$value)
		{
			$html .= ' '.$name.'="'.$value.'"';
		}
		$html .= '>'.$this->value."</textarea>".$this->lb;		
		return $html;
	}
}
