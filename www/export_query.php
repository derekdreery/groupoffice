<?php
require('Group-Office.php');

$db = new db();
$db->query($_SESSION['GO_SESSION']['export_queries'][$_REQUEST['query']]);

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

$first_record = $db->next_record();

if($first_record)
{
	while($record = $db->next_record())
	{
		if(!isset($first))
		{
			$headings = array();
			foreach($record as $key=>$value)
			{
				$headings[]=$key;
			}			
			echo $_SESSION['GO_SESSION']['text_separator'].implode($_SESSION['GO_SESSION']['text_separator'].$_SESSION['GO_SESSION']['list_separator'].$_SESSION['GO_SESSION']['text_separator'], $headings).$_SESSION['GO_SESSION']['text_separator']."\r\n";
			$first=true;	
		}
		
		if(isset($record['user_id']))
		{
			$user = $GO_USERS->get_user($record['user_id']);
			$record['user_id']=$user['username'];
		}
		echo $_SESSION['GO_SESSION']['text_separator'].implode($_SESSION['GO_SESSION']['text_separator'].$_SESSION['GO_SESSION']['list_separator'].$_SESSION['GO_SESSION']['text_separator'], $record).$_SESSION['GO_SESSION']['text_separator']."\r\n";
	}
}
?>