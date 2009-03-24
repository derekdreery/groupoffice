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
	$xml = @file_get_contents($feed);
	if($xml)
	{		
		$xml = str_replace('<content:encoded>', '<content>', $xml);
		$xml = str_replace('</content:encoded>', '</content>', $xml);
		$xml = str_replace('</dc:creator>', '</author>', $xml);
		echo str_replace('<dc:creator', '<author', $xml);
	}
}
?>