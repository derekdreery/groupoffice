<?php
/**
 * @copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision:  $ $Date:  $
 *
   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Creates a table that uses tabs to show multiple screens
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */

require_once($GO_CONFIG->class_path.'base/controls/html_element.class.inc');
 
class csv
{
	var $delimiter;
	var $encapsulator;
	var $csv;
	
	function csv($delimiter=',', $encapsulator='"')
	{
		$this->delimiter=$delimiter;
		$this->encapsulator=$encapsulator;
		
	}
	
	function add_record($values)
	{
		$this->csv .= $this->encapsulator.implode($this->encapsulator.$this->delimiter.$this->encapsulator, $values).$this->encapsulator."\n";
	}
	
	function get_csv()
	{
		return $this->csv;
	}
}