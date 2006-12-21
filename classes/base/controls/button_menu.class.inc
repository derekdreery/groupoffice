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
 * Creates a menu bar with icons and text underneath it
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */

require_once($GO_CONFIG->class_path.'base/controls/table.class.inc');

class button_menu extends table
{

	var $row;

	function button_menu($id='')
	{
		$this->tagname = 'table';		
		$this->set_linebreak("\n");
		
		if($id != '')
		{
			$this->set_attribute('id', $id);
		}
		
		$this->row = new table_row();

	}
	
	function add_button($image_id, $text, $href, $attributes=array())
	{
		$image = new image($image_id);		
		$image->set_attribute('style', 'width:32px;height:32px;border:0px;');
		$hyperlink = new hyperlink($href, $image->get_html().'<br />'.$text);
		$hyperlink->attributes=array_merge($hyperlink->attributes,$attributes);
		$cell = new table_cell($hyperlink->get_html());
		$cell->set_attribute('class', 'ModuleIcons');
		
		$this->row->add_cell($cell);	
	}
	
	function get_html()
	{	
		$this->add_row($this->row);
		return parent::get_html();
	}
	
}
