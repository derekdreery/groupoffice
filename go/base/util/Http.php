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

	public static function checkUrlForHttp($url,$https=false){
		
		$hasHttp = preg_match('|^http(s)?://.*|i', $url);
		
		if(!$hasHttp){
			$tmpUrl = 'http';
			if($https)
				$tmpUrl .= 's';
			$tmpUrl .= '://'.$url;
			$url = $tmpUrl;
		}
		
		return $url;
	}
		
	/**
	 * Get information about the browser currently using Group-Office.
	 * 
	 * @return array('name','version')
	 */
	public static function getBrowser() {
		if(!isset($_SERVER['HTTP_USER_AGENT']))
			return array('version'=>0, 'name'=>'OTHER');
		
		if (preg_match("'msie ([0-9]+.[0-9]{1,2})'i", $_SERVER['HTTP_USER_AGENT'], $log_version)) {
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
	
	/**
	 * Check if this request SSL secured
	 * 
	 * @return boolean
	 */
	public static function isHttps(){
		return !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'],'off');
	}
	
	
	/**
	 * Output the right headers for outputting file data to a browser.
	 * 
	 * @param GO_Base_Fs_File $file Use GO_Base_Fs_MemoryFile for outputting variables
	 * @param boolean $inline
	 * @param boolean $cache Cache the file for one day in the browser.
	 * @param array $extraHeaders  Key value array for extra headers
	 */
	public static function outputDownloadHeaders(GO_Base_Fs_File $file, $inline=true, $cache=false, $extraHeaders=array()) {
		
		header('Content-Transfer-Encoding: binary');		
		
		$disposition = $inline ? 'inline' : 'attachment';

		if ($cache) {
			header("Expires: " . date("D, j M Y G:i:s ", time() + 86400) . 'GMT'); //expires in 1 day
			header('Cache-Control: cache');
			header('Pragma: cache');
		}
		if (GO_Base_Util_Http::isInternetExplorer()) {
			header('Content-Type: application/download');
			header('Content-Disposition: '.$disposition.'; filename="' .rawurlencode($file->name()). '"');

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
		
		if($file->exists()){			
			if($file->extension()!='zip') //Don't set content lenght for zip files because gzip of apache will corrupt the download. http://www.heath-whyte.info/david/computers/corrupted-zip-file-downloads-with-php
				header('Content-Length: ' . $file->size());
			
			header("Last-Modified: " . gmdate("D, d M Y H:i:s", $file->mtime())." GMT");
			header("ETag: " . $file->md5Hash());
		}
		
		foreach($extraHeaders as $header=>$value){
			header($header.': '.$value);
		}
	}
	
	/**
	 * Download a file to the client
	 * 
	 * @param GO_Base_Fs_File $file Use GO_Base_Fs_MemoryFile for outputting variables
	 * @param boolean $inline
	 * @param boolean $cache Cache the file for one day in the browser.
	 * @param array $extraHeaders  Key value array for extra headers
	 */
	public static function downloadFile(GO_Base_Fs_File $file, $inline=true, $cache=false, $extraHeaders=array()){
		self::outputDownloadHeaders($file, $inline, $cache, $extraHeaders);
		$file->output();
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
	 * Reset the post array
	 */
	public static function resetPostRequest(){
		unset($_POST);
	}
	
	/**
	 * Check if the request was made with ajax.
	 * 
	 * @return boolean 
	 */
	public static function isAjaxRequest($withExtjsIframeHack=true){
		//dirty hack with $_FILES for extjs iframe file upload
		
		if($withExtjsIframeHack && !empty($_REQUEST['ajax']) || !empty($_FILES))
			return true;
		
		return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest";  
	}
	
	/**
	 * Unset a cookie
	 * 
	 * @param string $name 
	 */
	public static function unsetCookie($name){
		SetCookie($name,"",time()-3600,GO::config()->host,"",!empty($_SERVER['HTTPS']),true);
		unset($_COOKIE[$name]);
	}
	
	/**
	 * Set a cookie
	 * 
	 * @param string $name
	 * @param string $value
	 * @param string $expireTime Defaults to one month
	 */
	public static function setCookie($name, $value, $expireTime=2592000){
		SetCookie($name,$value,time()+$expireTime,GO::config()->host,"",!empty($_SERVER['HTTPS']),true);
	}
	
	
	/**
	 * Add GET parameters to a URL
	 *
	 * @param string $url
	 * @param array $params
	 * @param boolean $htmlspecialchars
	 * @return string 
	 */
	public static function addParamsToUrl($url,array $params, $htmlspecialchars=true) {
		$amp = $htmlspecialchars ? '&amp;' : '&';
		if (strpos($url, '?') === false) {
			$url .= '?';
		} else {
			$url .= $amp;
		}
		$first=true;
		foreach($params as $key=>$value){
			if($first)
				$first=false;
			else			
				$url .=$amp;
			
			$url .= urlencode($key).'='.urlencode($value);
		}
		
		return $url;
	}	
}