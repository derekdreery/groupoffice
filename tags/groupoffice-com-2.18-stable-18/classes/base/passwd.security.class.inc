<?php
/**
* @copyright Intermesh 2005
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.1 $ $Date: 2005/04/27 12:39:14 $
*
*  This program is free software; you can redistribute it and/or modify it
*  under the terms of the GNU General Public License as published by the
*  Free Software Foundation; either version 2 of the License, or (at your
*  option) any later version.
*/

require_once($GO_CONFIG->class_path.'base/sql.security.class.inc');

/**
* This is the passwd secuity management class. Dummy for the sql.security class
*
* @package  Framework
* @author   Merijn Schering <mschering@intermesh.nl>
* @since    Group-Office 2.05
*/

class passwd_security extends sql_security {
	
	/**
	* Constructor. Initialises base class of the security class family
	*
	* @access public
	* @return void
	*/
	function passwd_security() {
		$this->sql_security();
	}
}
