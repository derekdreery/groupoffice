<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.11 $ $Date: 2005/04/09 19:13:26 $

    This program is free software; you can redistribute it and/or modify it
    under the terms of the GNU General Public License as published by the
    Free Software Foundation; either version 2 of the License, or (at your
    option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Create a dropbox.
 * 
 * This class is used to draw dropboxes on the website.
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */
class dropbox
{
  /**
   * The values of the entries.
   * 
   * @access private
   * @var array of strings
   */
  var $value=array();

  /**
   * The names of the entries.
   * 
   * @access private
   * @var array of strings.
   */
  var $text=array();

  /**
   * Stores which entries belong to the same option group.
   * 
   * @access private
   * @var array of strings
   */
  var $optgroup;

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
    if ( $text != '' ) {
      $this->value[] = $value;
      $this->text[] = $text;
      return true;
    } else {
      return false;
    }
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
  function is_in_dropbox( $value, $is_text=false )
  {
    if ( $is_text ) {
      return in_array( $value, $this->text );
    } else {
      return in_array( $value, $this->value );
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
    $this->optgroup[count($this->value)] = $name;
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
      $this->value[] = $$sql_object->f( $value );
      $this->text[] = $$sql_object->f( $text );
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
  function add_arrays( $value, $text )
  {
    // check if both array are of the same size.
    if ( count( $value ) == count( $text ) ) {
      if ( is_array($this->value) ) {
	$this->value = array_merge( $this->value, $value );
	$this->text = array_merge( $this->text, $text );
      } else {
	$this->value = $value;
	$this->text = $text;
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
    return count( $this->value );
  }

  /**
   * Draw the dropbox.
   * 
   * ...
   * 
   * @access public
   * 
   * @param string $name
   * @param string $selected_field
   * @param string $attributes
   * @param bool $multiple
   * @param string $size
   * @param string $width
   * 
   * @return void
   */
  function print_dropbox(
      $name, $selected_field='', $attributes='', $multiple=false, $size='10', 
      $width='0' )
  {
    echo $this->get_dropbox(
	$name, $selected_field, $attributes, $multiple, $size, $width );	
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
  function get_dropbox(
      $name, $selected_field='', $attributes='', $multiple=false, $size='10',
      $width='0' )
  {
    $multiple_str = $multiple ? ' multiple="true" size="'.$size.'"' : '';

    $optgroup_open = false;
    $return_str = '<select name="'.$name.'" class="textbox"'.$multiple_str.
      ' '.$attributes;

    if ( $width > 0 ) {
      $return_str.=  'style="width: '.$width.'"';
    }
    $return_str.=  '>';

    for ( $i=0; $i<count($this->value); $i++ ) {
      if ( isset( $this->optgroup[$i] ) ) {
	if ( $optgroup_open == true ) {
	  $return_str.=  '</optgroup>';
	} else {
	  $optgroup_open = true;
	}
	$return_str.=  '<optgroup label="'.$this->optgroup[$i].'">';
      }

      if ( $this->text[$i] != '' ) {
	$return_str.=  '<option value="'.$this->value[$i].'"';

	if ( $multiple ) {
	  if ( in_array( $this->value[$i], $selected_field ) ) {
	    $return_str.=  ' selected';
	  }
	} else {
	  if ( $this->value[$i] == $selected_field ) {
	    $return_str.=  ' selected';
	  }
	}
	$return_str.=  '>';
	$return_str.=  $this->text[$i];
	$return_str.=  '</option>';
      }
    }
    if ( $optgroup_open == true ) {
      $return_str.=  '</optgroup>';
    }
    $return_str.=  '</select>';
    return $return_str;
  }
}
