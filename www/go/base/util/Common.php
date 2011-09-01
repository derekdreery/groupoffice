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
 * @version $Id: Number.class.inc.php 4305 2010-03-02 15:48:48Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */
class GO_Base_Util_Common {

	/**
	 * Get information about the browser currently using Group-Office.
	 * 
	 * @return array('name','version')
	 */
	public static function getBrowser() {
		if (preg_match("'msie ([0-9].[0-9]{1,2})'i", $_SERVER['HTTP_USER_AGENT'], $log_version)) {
			$browser['version'] = $log_version[1];
			$browser['name'] = 'MSIE';
		} elseif (preg_match("'opera/([0-9].[0-9]{1,2})'i", $_SERVER['HTTP_USER_AGENT'], $log_version)) {
			$browser['version'] = $log_version[1];
			$browser['name'] = 'OPERA';
		} elseif (preg_match("'mozilla/([0-9].[0-9]{1,2}).*gecko/([0-9]+)'i", $_SERVER['HTTP_USER_AGENT'], $log_version)) {
			$browser['version'] = $log_version[1];
			$browser['name'] = 'MOZILLA';
			$browser['subversion'] = $log_version[2];
		} elseif (preg_match("'netscape/([0-9].[0-9]{1,2})'i", $_SERVER['HTTP_USER_AGENT'], $log_version)) {
			$browser['version'] = $log_version[1];
			$browser['name'] = 'NETSCAPE';
		} elseif (preg_match("'safari/([0-9]+.[0-9]+)'i", $_SERVER['HTTP_USER_AGENT'], $log_version)) {
			$browser['version'] = $log_version[1];
			$browser['name'] = 'SAFARI';
		} else {
			$browser['version'] = 0;
			$browser['name'] = 'OTHER';
		}
		return $browser;
	}
	
	public static function isInternetExplorer(){
		$b = self::getBrowser();
		
		return $b['name']=='MSIE';
	}

}