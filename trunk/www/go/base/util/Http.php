<?php
/*
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * Common utilities
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id: config.class.inc.php 7687 2011-06-23 12:00:34Z mschering $
 * @copyright Copyright Intermesh BV.
 * @package GO.base.util 
 */
class GO_Base_Util_Http {

	/**
	 * Get information about the browser currently using Group-Office.
	 * 
	 * @return array('name','version')
	 */
	public static function getBrowser() {
		if(!isset($_SERVER['HTTP_USER_AGENT']))
			return array('version'=>0, 'name'=>'OTHER');
		
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

	/**
	 * Check if the current user is using internet explorer
	 * 
	 * @return boolean 
	 */
	public static function isInternetExplorer() {
		$b = self::getBrowser();

		return $b['name'] == 'MSIE';
	}
	
	
	
	public static function outputDownloadHeaders(GO_Base_Fs_File $file, $inline=false, $cache=false) {
		if($file->exists()){
			header('Content-Length: ' . $file->size());
			header("Last-Modified: " . gmdate("D, d M Y H:i:s", $file->mtime())." GMT");
			header("ETag: " . md5_file($file->path()));
		}
		header('Content-Transfer-Encoding: binary');		
		
		$disposition = $inline ? 'inline' : 'attachment';

		if ($cache) {
			header("Expires: " . date("D, j M Y G:i:s ", time() + 86400) . 'GMT'); //expires in 1 day
			header('Cache-Control: cache');
			header('Pragma: cache');
		}
		if (GO_Base_Util_Http::isInternetExplorer()) {
			header('Content-Type: application/download');
			header('Content-Disposition: '.$disposition.'; filename="' . $file->name() . '"');

			if (!$cache) {
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			}
		} else {
			header('Content-Type: ' .$file->mimeType());
			header('Content-Disposition: '.$disposition.'; filename="' . $file->name() . '"');

			if (!$cache) {
				header('Pragma: no-cache');
			}
		}
	}
	
	/**
	 * Check if the request was a post request.
	 * 
	 * @return boolean 
	 */
	public static function isPostRequest(){
		return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST';
	}
	
	/**
	 * Check if the request was made with ajax.
	 * 
	 * @return boolean 
	 */
	public static function isAjaxRequest(){
		return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest";  
	}

	
}