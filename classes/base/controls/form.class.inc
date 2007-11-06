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
 * Create a from
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */
require_once($GO_CONFIG->class_path.'base/controls/html_element.class.inc');

class form extends html_element
{	
	function form($name='form', $method='post', $action='')
	{
		$this->tagname = 'form';		
		$this->set_linebreak("\n");
		$this->set_attribute('name', $name);
		$this->set_attribute('method', $method);
		if($action == '')
		{
			$action = $_SERVER['PHP_SELF'];
		}
		$this->set_attribute('action', $action);	
	}
}
