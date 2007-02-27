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

class user_autocomplete
{
	var $autocomplete;
	
	function user_autocomplete($name, $user_id, $form_name='0', $link_back=null, $xml_fields=null, $overwrite_values=false, $submit_on_enter=false)
	{
		global $GO_USERS, $GO_MODULES;
		
	
		if(!isset($xml_fields))
			$xml_fields=array('name'=>'user_name','id'=>$name);
			
		if(!isset($link_back))
			$link_back=$_SERVER['HTTP_REFERER'];
		
		$user = $GO_USERS->get_user($user_id);
		if($user)
		{
			$user_name = format_name($user['last_name'], $user['first_name'],$user['middle_name'],'first_name');
		}else {
			$user_name = '';
		}
		
		if($GO_MODULES->modules['users']['read_permission'])
		{
			$lookup_url = $GO_MODULES->modules['users']['url'].'user.php?return_to='.urlencode($link_back).'&user_id=';
		}else {
			global $GO_CONFIG;
			
			$lookup_url = $GO_CONFIG->control_url.'user.php?return_to='.urlencode($link_back).'&id=';
		}

		$this->autocomplete  = new autocomplete(
		$name,
		false,
		'user_name',
		$user_name,
		$form_name,
		$GO_MODULES->modules['users']['url'].'search_xml.php',
		'user',
		$xml_fields,
		$overwrite_values,
		$submit_on_enter
		);
		$this->autocomplete ->add_bound_hidden_value($name, $user_id,$lookup_url);

	}
	function get_html()
	{
		return $this->autocomplete->get_html();		
	}
}
