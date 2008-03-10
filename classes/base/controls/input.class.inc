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
	var $required=false;
	
	function get_post_value($name)
	{
		$name = str_replace(']','', $name);
		$var_names = explode('[',$name);

		for($i=0;$i<count($var_names);$i++)
		{
			if(isset($tmp))
			{
				if(!isset($tmp[$var_names[$i]]))
				{
					return false;
				}
				$tmp=$tmp[$var_names[$i]];
			}elseif(isset($_POST[$var_names[$i]])) {
				$tmp =$_POST[$var_names[$i]];
			}else {
				return false;
			}
		}

		if(isset($tmp))
		{
			return $tmp;
		}else {
			return false;
		}
	}
	
	function input($type, $name, $value='', $remind_value=true, $required=false)
	{
		$this->required=$required;
		$this->tagname = 'input';
		$this->set_linebreak("\n");
		
		$this->set_attribute('type', $type);
		$this->set_attribute('name', $name);
		$post_value = $this->get_post_value($name);
		if($remind_value && $post_value)
		{
			$this->set_attribute('value', htmlspecialchars(smart_stripslashes($post_value)));
		}else
		{
			$this->set_attribute('value', htmlspecialchars($value));
		}

		if($this->required && $_SERVER['REQUEST_METHOD']=='POST' && empty($post_value))
		{
			$this->set_attribute('class','textbox_error');
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
