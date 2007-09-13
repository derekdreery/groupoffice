<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.35 $ $Date: 2006/11/21 16:25:34 $

    This program is free software; you can redistribute it and/or modify it
    under the terms of the GNU General Public License as published by the
    Free Software Foundation; either version 2 of the License, or (at your
    option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Create a select dropdown list
 * 
 * This class is used to draw dropboxes on the website.
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */

require_once($GO_CONFIG->class_path.'base/controls/html_element.class.inc');

class select extends html_element
{
	/**
   * The values of the entries.
   * 
   * @access private
   * @var array of strings
   */
	var $values=array();

	/**
   * The names of the entries.
   * 
   * @access private
   * @var array of strings.
   */
	var $texts=array();

	/**
   * Stores which entries belong to the same option group.
   * 
   * @access private
   * @var array of strings
   */
	var $optgroup;

	var $multiple;
	
	var $required;

	function select($name, $value='', $multiple=false, $required=false)
	{
		$this->required=$required;
		
		$this->lb = "\n";

		$this->name = $name;
		$this->set_attribute('id',$name);

		$this->multiple = $multiple;
		if($multiple)
		{
			$this->value=is_array($value) ? $value : array();
			$this->set_attribute('multiple','true');
		}else
		{
			$this->value=$value;
		}
		
		$this->set_attribute('class',  'x-form-select-one x-form-field');
		
		if($this->required && isset($_POST[$name]) && empty($_POST[$name]))
		{
			$this->set_attribute('class','textbox_error');
		}

	}

	/**
   * Add a new value to the dropbox.
   * 
   * This function adds a new value/description pair to the dropbox. If the
   * description is empty the value is not added to the dropbox.
   * 
   * @access public
   * 
   * @param string $value is the value of this entry.
   * @param string $text is the name of this entry.
   * 
   * @return bool
   */
	function add_value( $value, $text )
	{
		$this->values[] = $value;
		$this->texts[] = $text;
	}

	/**
   * Check if a specified entry is in the dropbox.
   * 
   * This function checks if there is an entry in the dropbox with the given
   * name or value. If $is_text is false it checks the value, otherwise it
   * checks the name.
   * 
   * @access public
   * 
   * @param string $value
   * @param bool $is_text
   * 
   * @return bool
   */
	function is_in_select( $value, $is_text=false )
	{
		if ( $is_text ) {
			return in_array( $value, $this->texts );
		} else {
			return in_array( $value, $this->values );
		}
	}

	/**
   * Inserts a new option group at the current position.
   * 
   * The next entry added will belong to the given option group.
   * 
   * @access public
   * 
   * @param string $name
   * 
   * @return void
   */
	function add_optgroup( $name )
	{
		$this->optgroup[count($this->values)] = $name;
	}

	/**
   * Adds value/description pairs based on a pending SQL query.
   * 
   * If you pass a class that extends the MySQL DB class and a value and text
   * field index to this function it will add the fields. The class must have
   * a query pending.
   * 
   * @access public
   * 
   * @param type $sql_object
   * @param string $value
   * @param string $text
   * 
   * @return void
   */
	function add_sql_data( $sql_object, $value, $text )
	{
		global $$sql_object;

		while ( $$sql_object->next_record() ) {
			$this->values[] = $$sql_object->f( $value );
			$this->texts[] = $$sql_object->f( $text );
		}
	}

	/**
   * Add a lot of value/description pairs to this dropbox.
   * 
   * If the number of values and descriptions equals this function adds the
   * given pairs to the dropbox.
   * 
   * @access public
   * 
   * @param array of strings $value
   * @param array of strings $text
   * 
   * @return bool true if the number of values and descriptions are equal.
   */
	function add_arrays( $values, $texts )
	{
		// check if both array are of the same size.
		if ( count( $values ) == count( $texts ) ) {
			if ( is_array($this->values) ) {
				$this->values = array_merge( $this->values, $values );
				$this->texts = array_merge( $this->texts, $texts );
			} else {
				$this->values = $values;
				$this->texts = $texts;
			}
			return true;
		}
		return false;
	}

	/**
   * Count the number of entries in this dropbox.
   * 
   * This function returns the number of entries in the dropbox.
   * 
   * @access public
   * 
   * @param void
   * 
   * @return integer is the number of entries.
   */
	function count_options()
	{
		return count( $this->values );
	}


	/**
   * Create a dropbox
   * 
   * ...
   * 
   * @access public
    
   * @param string $name
   * @param string $selected_field
   * @param string $attributes
   * @param bool $multiple
   * @param string $size
   * @param string $width
   * 
   * @return string
   */
	function get_html()
	{

		if(!isset($this->attributes['class']))
		{
			$this->set_attribute('class','textbox');
		}
		
		if(!isset($this->attributes['id']))
		{
			$this->set_attribute('id',$this->name);
		}

		$optgroup_open = false;
		$this->outerHTML .= '<select name="'.$this->name.'"';
		foreach($this->attributes as $name=>$value)
		{
			$this->outerHTML .= ' '.$name.'="'.$value.'"';
		}
		$this->outerHTML.=  '>'.$this->lb;

		for ( $i=0; $i<count($this->values); $i++ ) {
			if ( isset( $this->optgroup[$i] ) ) {
				if ( $optgroup_open == true ) {
					$this->outerHTML.=  '</optgroup>'.$this->lb;
				} else {
					$optgroup_open = true;
				}
				$this->outerHTML.=  '<optgroup label="'.htmlspecialchars($this->optgroup[$i]).'">'.$this->lb;
			}

			if ( $this->texts[$i] != '' ) {
				$this->outerHTML.=  '<option value="'.htmlspecialchars($this->values[$i]).'"';
				if ( $this->multiple ) {
					if ( in_array( $this->values[$i], $this->value ) ) {
						$this->outerHTML.=  ' selected';
					}
				} else {
					
					if ( $this->values[$i] ==  $this->value ) {
						$this->outerHTML.=  ' selected';
						//$this->value='';
					}
				}
				$this->outerHTML.=  '>';
				$this->outerHTML.=  str_replace(' ', '&nbsp;',htmlspecialchars($this->texts[$i]));
				$this->outerHTML.=  '</option>'.$this->lb;
			}
		}
		if ( $optgroup_open == true ) {
			$this->outerHTML.=  '</optgroup>'.$this->lb;
		}
		$this->outerHTML.=  '</select>'.$this->lb;
		return $this->outerHTML;
	}
}
