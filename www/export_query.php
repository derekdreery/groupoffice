<?php
require('Group-Office.php');

$filename = $_REQUEST['query'].'.csv';

$browser = detect_browser();
header("Content-type: text/x-csv;charset=UTF-8");
if ($browser['name'] == 'MSIE')
{
	header('Content-Disposition: inline; filename="'.$filename.'"');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
} else {
	header('Pragma: no-cache');
	header('Content-Disposition: attachment; filename="'.$filename.'"');
}

$fp = fopen('php://output','w');
export_query($fp);
fclose($fp);
?>