<?php
/**
 * Group-Office
 * 
 * This file contains functions that are needed for compatibility with older
 * php versions.
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO
 */
if(!function_exists("quoted_printable_encode")){
	function quoted_printable_encode($string) {
		return preg_replace('/[^\r\n]{73}[^=\r\n]{2}/', "$0=\n", str_replace(array('%20', '%0D%0A', '%'), array(' ', "\r\n", '='), rawurlencode($string)));
	}
}