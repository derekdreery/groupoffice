<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.7 $ $Date: 2005/04/09 19:13:26 $

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Create a radio-list
 * 
 * This class is used to create and draw radio-lists on the website. If it is
 * disabled it's color is hardcoded to #7d7d7d. This function uses JavaScript
 * when selection an item (<b>select_radio</b>).
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */
class radio_list
{
  /**
   * The name of the radiolist.
   * 
   * @access private
   * @var string
   */
  var $group;
  /**
   * The value of the preselected item.
   * 
   * @access private
   * @var string
   */
  var $selected;
   
  /**
   * Create a new radio-list.
   * 
   * @access public
   * 
   * @param string $group is the name of this radio-list.
   * @param string $selected is the preselected item of the list.
   * 
   * @return void
   */
  function radio_list( $group, $selected='' )
  {
    $this->group = $group;
    $this->selected = $selected;
  }

  /**
   * Get a new option-field for the radio-list.
   * 
   * This function returns a string which represents the new option-field
   * of the current radio-list.
   * 
   * @access public
   * 
   * @param string $value is the value of the item we add to the list.
   * @param string $text is the caption of this item.
   * @param bool $disabled can be used to disable this item.
   * @param string $attributes
   * 
   * @return void
   */
  function get_option( $value, $text, $disabled=false, $attributes='' )
  {
    if ( $this->selected == $value ) {
      $selected = ' checked';
    } else {
      $selected = '';
    }

    $id = uniqid( time() );

    $disabled = $disabled ? ' disabled' : '';
    $option= '<input id="'.$id.'" type="radio" name="'.$this->group.
      '" value="'.$value.'"'.$selected.$disabled.' '.$attributes.' />';

    if ( $text != '' ) {
      if ( $disabled == ' disabled' ) {
	$option .=  '<span style="color: #7d7d7d">'.$text.'</span>';
      } else {
	$option .= '<a href="javascript:select_radio(\''.$id.'\')">'.$text.'</a>';
      }
    }
    return $option;
  }
  
  /**
   * Print a new option-field for the radio-list.
   * 
   * This function directly prints a new option-field of the current
   * radio-list into the website.
   * 
   * @access public
   * 
   * @param string $value is the value of the item we add to the list.
   * @param string $text is the caption of this item.
   * @param bool $disabled can be used to disable this item.
   * @param string $attributes
   * 
   * @return void
   */
  function add_option( $value, $text, $disabled=false, $attributes='' )
  {
    echo $this->get_option( $value, $text, $disabled, $attributes );
  }
}
