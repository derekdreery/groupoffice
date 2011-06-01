<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * This class holds the main configuration options of Group-Office
 * Don't modify this file. The values defined here are just default values.
 * They are overwritten by the configuration options in local/config.php.
 * To edit these options use install.php.
 *
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @since Group-Office 1.0
 * @package go.basic
 * @access public
 */


$root = dirname(__FILE__).'/';
require_once($root.'classes/GO.php');
GO::init();


//
////preload classes before session so they can be stored in the session
//if ( isset( $GO_INCLUDES ) ) {
//
//	//load configuration before session start because otherwise objects may be incomplete.
//	//We rather start the session before creating GO_CONFIG because we can save the location
//	//of config.php in the session. Otherwise we'll have to search for it every time.
//	$GO_CONFIG = new GO_CONFIG();
//
//	while ( $include = array_shift( $GO_INCLUDES ) ) {
//		require_once( $include );
//	}
//}












unset($type);



