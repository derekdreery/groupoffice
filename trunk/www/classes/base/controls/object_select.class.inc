<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.6 $ $Date: 2006/03/20 16:07:17 $

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Create a select box for objects in Group-Office
 * 
 * This class is used to draw a select field on the website.
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */
class object_select
{
	var $value;
	var $name;
	var $form_name;
	var $selected_type;

	var $name_field_value;

	var $name_field;
	var $value_field;
	var $type_field;

	var $types;
	var $uniqid;
	var $return_to;

	/**
   * Draw a new select field.
   * 
   * ...
   * 
   * @access public
   * 
   * @param string $selected_type
   * @param string $form_name
   * @param string $name
   * @param string $value
   * @param bool $types
   * @param string $return_to
   * 
   * @return void
   */
	function object_select(
	$selected_type, $form_name, $name, $value, $types=false, $return_to='', $handler='')
	{
		global $GO_CONFIG;
		
		if(empty($handler))
		{
			$handler = $GO_CONFIG->control_url.'select/add.php';	
		}
		$this->handler = $handler;
		
		if( !$types ) {
			$this->types = array( $selected_type );
		} else {
			$this->types=$types;
		}
		$this->type_field = $name.'[type]';
		$this->name_field = $name.'[name]';
		$this->value_field = $name.'[value]';


		$this->selected_type = $selected_type;
		$this->form_name = $form_name;
		$this->name = $name;
		$this->value = $value;
		$this->return_to = $return_to == '' ? $_SERVER['REQUEST_URI'] : $return_to;

		$this->uniqid = uniqid( time() );
	}
	
	function set_return_to($return_to)
	{
		$this->return_to = $return_to;
	}

	/**
   * Print a link.
   * 
   * ...
   * 
   * @access public
   * 
   * @param string $text
   *
   * @return void
   */
	function print_link ($text )
	{
		echo $this->get_link( $text );
	}

	/**
   * Generate a GO-Link
   * 
   * Returns a string that can used to draw a link.
   * 
   * @access public
   * 
   * @param string $text
   * 
   * @return string
   */
	function get_link( $text )
	{
		global $GO_THEME, $GO_CONFIG;

		$argument = '';
		for( $i=0; $i<count($this->types); $i++ ) {
			switch( $this->types[$i] ) {
				case 'user':
				$argument .= '&show_users=true';
				break;

				case 'contact':
				$argument .= '&show_contacts=true';
				break;

				case 'company':
				$argument .= '&show_companies=true';
				break;

				case 'project':
				$argument .= '&show_projects=true';
				break;

				case 'file':
				$argument .= '&show_files=true';
				break;

				default:
				exit("Unsupported type '".$this->selected_type."' in select class");
				break;
			}
		}

		$link = 'javascript:popup(\''.$GO_CONFIG->control_url.'select/select.php?'.
		'search_type='.$this->selected_type.'&multiselect=false&GO_HANDLER='.
		base64_encode(
		$this->handler.'?type='.$this->selected_type.
		'&id_field=document.forms[\''.$this->form_name.'\'].elements[\''.addslashes($this->value_field).'\']' .
		'&name_field=document.forms[\''.$this->form_name.'\'].elements[\''.addslashes($this->name_field).'\']' .
		'&type_field=document.forms[\''.$this->form_name.'\'].elements[\''.addslashes($this->type_field).'\']').
		'&pass_value=id&handler_base64_encoded=true&require_email_address=false'.$argument.'\',\'620\',\'400\');';

		return '<a class="normal" href="'.$link.'"><img align="absmiddle" src="'.
		$GO_THEME->images['addressbook_small'].'" width="16" height="16" '.
		'border="0" style="margin-right: 5px;" />'.$text.'</a>';
	}

	/**
   */
	function get_field($width='300')
	{
		global $GO_THEME, $GO_CONFIG, $strDeleteItem, $GO_USERS, $GO_MODULES;

		$name_field_value = '';
		if ( $this->value > 0 || $this->value != '' ) {
			switch ( $this->selected_type ) {
				case 'user':
				if ( $user = $GO_USERS->get_user( $this->value ) ) {					
					$name = format_name($user['last_name'], $user['first_name'], $user['middle_name']);
					$name_field_value = htmlspecialchars($name);
				}
				break;

				case 'contact':
				$ab = new addressbook();
				if ( $contact = $ab->get_contact( $this->value ) ) {
					$name = format_name($contact['last_name'], $contact['first_name'], $contact['middle_name']);
					$name_field_value = htmlspecialchars($name);
				}
				break;

				case 'company':
				$ab = new addressbook();
				if ( $company = $ab->get_company( $this->value ) ) {
					$name_field_value = $company['name'];
				}
				break;

				case 'project':
				$projects = new projects();
				if ( $project = $projects->get_project( $this->value ) ) {
					$name_field_value = $project['name'];
					if ( $project['description'] != '' ) {
						$name_field_value .= " (".$project['description'].")";
					}
				}
				break;

				case 'file':
				$name_field_value = basename($this->value);
				break;
			}
		}
		$return_string = '';
		$return_string .= '<input type="hidden" name="'.$this->type_field.'" value="'.$this->selected_type.'" />';
		$return_string .= '<input type="hidden" value="'.$this->value.'" name="'.$this->value_field.'" />'.
		'<input type="text" name="'.$this->name_field.'" class="textbox" style="width: '.$width.'px;" value="'.$name_field_value.'" disabled />';
		if ( $this->value != '' ) {
			$return_string .= '<a href="javascript:show_relation_'.$this->uniqid.'()"><img src="'.$GO_THEME->images['magnifier'].'" border="0" align="absmiddle" /></a>';
		}

		$return_string.= 	'<a href="javascript:clear_'.$this->uniqid.'()" title="'.$strDeleteItem.
		'"><img src="'.$GO_THEME->images['delete'].'" align="absmiddle" border="0"></a>';

		$return_string.=  	'<script type="text/javascript">'.
		'function clear_'.$this->uniqid.'(){'.
		'document.forms[\''.$this->form_name.'\'].elements[\''.addslashes($this->value_field).'\'].value="";'.
		'document.forms[\''.$this->form_name.'\'].elements[\''.addslashes($this->name_field).'\'].value="";}'.

		'function show_relation_'.$this->uniqid.'(){'.
		'if(document.forms[\''.$this->form_name.'\'].elements[\''.addslashes($this->value_field).'\'].value > 0 || document.forms[\''.$this->form_name.'\'].elements[\''.addslashes($this->value_field).'\'].value != \'\'){'.
		'document.location="';

		switch($this->selected_type)
		{
			case 'user':
			$return_string.=  $GO_CONFIG->control_url.'user.php?id='.$this->value;
			break;

			case 'contact':
			$return_string.=  $GO_MODULES->modules['addressbook']['url'].'contact.php?contact_id='.$this->value;
			break;

			case 'company':
			$return_string.=  $GO_MODULES->modules['addressbook']['url'].'company.php?company_id='.$this->value;
			break;

			case 'project':
			$return_string.=  $GO_MODULES->modules['projects']['url'].'project.php?project_id='.$this->value;
			break;

			case 'file':
			$return_string.=  $GO_MODULES->modules['filesystem']['url'].'index.php?path='.
			urlencode(dirname($this->value));
			break;
		}
		$return_string .= '&return_to='.urlencode($this->return_to).'";}}'.
		'</script>';
		return $return_string;
	}

	/**
   */
	function print_field($width='300')
	{
		echo $this->get_field($width);
	}
}
