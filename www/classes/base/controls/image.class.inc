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
 * Create an image
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */
require_once($GO_CONFIG->class_path.'base/controls/html_element.class.inc');

class image extends html_element
{
	function image($index, $src='')
	{
		global $GO_THEME;
		
		$this->tagname = 'img';		
		$this->set_linebreak("\n");
		
		if(empty($src))
		{		
			$this->set_attribute('src', $GO_THEME->images[$index]);
		}else
		{
			$this->set_attribute('src', $src);
		}
	}
}
?>
