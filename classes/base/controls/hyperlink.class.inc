<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.6 $ $Date: 2006/11/21 16:25:34 $
 *
   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Create a hyperlink
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */
require_once($GO_CONFIG->class_path.'base/controls/html_element.class.inc');

class hyperlink extends html_element
{
	var $innerHTML = '';		
	
	function hyperlink($href, $innerHTML, $title='')
	{
		$this->innerHTML=$innerHTML;
		
		if(!empty($href))
		{
			$this->set_attribute('href', $href);
		}
		
		if(!empty($title))
		{
			$this->set_attribute('title', $title);
		}
	}	
	
	function get_html()
	{	
		$html = '<a';
		foreach($this->attributes as $name=>$value)
		{
			$html .= ' '.$name.'="'.$value.'"';
		}
		$html .= '>'.$this->innerHTML.'</a>';
		
		return $html;
	}
}
?>
