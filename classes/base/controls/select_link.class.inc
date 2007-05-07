<?php
/**
 * @copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.6 $ $Date: 2007/02/02 16:07:17 $

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Create a select box for linkable objects in Group-Office
 * 
 * This class is used to draw a select field on the website.
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */
class select_link
{

	var $name;
	var $link_type_field;
	var $link_id_field;
	var $text_field;
	var $form_name;
	var $link_type;
	var $link_id;

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
	function select_link($name, $link_type, $link_id, $text, $form_name='0')
	{
		global $GO_CONFIG;
		
		$this->form_name=$form_name;
		$this->link_type_field = $name.'[link_type]';
		$this->link_id_field = $name.'[link_id]';
		$this->text_field = $name.'[text]';
		$this->name = $name;
		$this->text=$text;
		$this->link_id=$link_id;
		$this->link_type=$link_type;
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
		
		$img = new image('addressbook_small');
		$img->set_attribute('style','border:0px;margin-right: 5px;');
		$img->set_attribute('align','absmiddle');
		
		$link = new hyperlink("javascript:popup('".$GO_CONFIG->control_url."select/global_select.php?multiselect=false&handler=".base64_encode($GO_CONFIG->control_url."/select/select_link.php?name=".urlencode($this->name)."&form_name=".urlencode($this->form_name))."','600','400');", $img->get_html().$text);
		$link->set_attribute('class','normal');
		return $link;
	}

	/**
   */
	function get_field($width='300px')
	{
		global $GO_CONFIG;
		load_control('autocomplete');
		
		$xml_fields=array('show'=>$this->text_field,'link_id'=>$this->link_id_field,'link_type'=>$this->link_type_field);	

		$autocomplete  = new autocomplete(
		'global_autocomplete',
		false,
		$this->text_field,
		$this->text,
		$this->form_name,
		$GO_CONFIG->control_url.'global_search_xml.php',
		'result',
		$xml_fields,
		false
		);
		$autocomplete->set_attribute('style','width:'.$width);
		
		$autocomplete->add_outerhtml_element(new input('hidden',$this->link_id_field, $this->link_id));
		$autocomplete->add_outerhtml_element(new input('hidden',$this->link_type_field, $this->link_type));
		
		return $autocomplete;
	}
}
