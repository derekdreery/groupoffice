<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.9 $ $Date: 2005/10/13 13:06:11 $
 *
   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Create any HTML element
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */

class html_element
{
	var $name = '';		
	var $value = '';		
	var $attributes = array();
	var $tagname ='';
	var $innerHTML='';
	var $outerHTML='';
	var $lb = "";
	var $processed=false;
	
	function html_element($tagname, $innerHTML='', $attributes = array())
	{
		$this->tagname = $tagname;
		$this->innerHTML=$innerHTML;

        foreach ($attributes as $key => $value) {

            $this->set_attribute($key, $value);

        }

	}
	
	function set_linebreak($lb)
	{
		$this->lb=$lb;
	}
	
	function set_attribute($name, $value)
	{
		$this->attributes[$name]=$value;
	}		
	
	function add_html_element(&$element)
	{
		$this->innerHTML .= $element->get_html();
	}
	
	function add_outerhtml_element(&$element)
	{
		$this->outerHTML .= $element->get_html();
	}
	
	function set_tooltip($tooltip, $show_event='onmouseover', $hide_event='onmouseout')
	{
		$this->set_attribute($show_event, $tooltip->show_command);
		$this->set_attribute($hide_event, $tooltip->hide_command);
	}
	
	function get_html()
	{
		if(!$this->processed)
		{
			$this->processed=true;
				
			$this->outerHTML .= '<'.$this->tagname;
			foreach($this->attributes as $name=>$value)
			{
				$this->outerHTML .= ' '.$name.'="'.htmlspecialchars($value).'"';
			}
			
			/*if($this->innerHTML == '' && $this->tagname!='script' && $this->tagname!='meta' && $this->tagname!='link' && $this->tagname!='iframe')
			{
				$this->outerHTML .= ' />';
			}else
			{*/
				$this->outerHTML .= '>'.$this->innerHTML.'</'.$this->tagname.'>'.$this->lb;
			//}
		}
		return $this->outerHTML;
	}
	
	function print_html()
	{
		echo $this->get_html();
	}
}
