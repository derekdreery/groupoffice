<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

$feed = $_REQUEST['feed'];
if($feed != '' && strpos($feed, 'http') === 0){
	header('Content-Type: text/xml');

	if(function_exists('curl_init')){
		$ch=curl_init();

		curl_setopt($ch, CURLOPT_URL,$feed);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		//for self-signed certificates
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		//suppress warning:
		//PHP Warning: curl_setopt() [<a href='function.curl-setopt'>function.curl-setopt</a>]:
		//CURLOPT_FOLLOWLOCATION cannot be activated when in safe_mode or an open_basedir is set in feed_proxy.php on line 29
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

		$xml = curl_exec($ch);
	}else
	{
		if(!GO_Base_Fs_File::checkPathInput($path))
			die("Invalid request");
		
		$xml = @file_get_contents($feed);
	}
	
	if($xml)
	{		
		$xml = str_replace('<content:encoded>', '<content>', $xml);
		$xml = str_replace('</content:encoded>', '</content>', $xml);
		$xml = str_replace('</dc:creator>', '</author>', $xml);
		echo str_replace('<dc:creator', '<author', $xml);
	}
}
?>