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

require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('users');

$browser = detect_browser();

header("Content-type: text/x-csv;charset=UTF-8");
if ($browser['name'] == 'MSIE')
{
	header('Content-Disposition: inline; filename="importsample.csv"');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
} else {
	header('Pragma: no-cache');
	header('Content-Disposition: attachment; filename="importsample.csv"');
}

$contents = file_get_contents($GO_MODULES->modules['users']['path'].'importsample.csv');
$fp = fopen($GO_MODULES->modules['users']['path'].'importsample.csv','r');

if($fp){
	while($record = fgetcsv($fp, 4096, ',', '"')){
		echo $_SESSION['GO_SESSION']['text_separator'].implode($_SESSION['GO_SESSION']['text_separator'].$_SESSION['GO_SESSION']['list_separator'].$_SESSION['GO_SESSION']['text_separator'], $record).$_SESSION['GO_SESSION']['text_separator']."\r\n";
	}
}
fclose($fp);