<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.1 $ $Date: 2006/11/22 11:02:00 $
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
require_once($GO_CONFIG->class_path.'base/controls/autocomplete.class.inc');

class global_autocomplete
{
	var $autocomplete;
	var $name;
	
	
	function global_autocomplete($name, $link_id=0, $link_type=0, $link_text='', $form_name='0', $xml_fields=null)
	{
		global $GO_CONFIG;
		
		$this->name=$name;
	
		if(!isset($xml_fields))
			$xml_fields=array('show'=>'name','link_id'=>$name.'[link_id]','link_type'=>$name.'[link_type]');	

		$this->autocomplete  = new autocomplete(
		'global_autocomplete',
		false,
		'name',
		'',
		$form_name,
		$GO_CONFIG->control_url.'global_search_xml.php',
		'results',
		$xml_fields,
		true
		);
	}
	function get_html()
	{		
		$input1 = new input('hidden',$this->name.'[link_type]');
		$input2 = new input('hidden',$this->name.'[link_id]');
		
		return $input1->get_html().$input2->get_html().$this->autocomplete->get_html();		
	}
}
