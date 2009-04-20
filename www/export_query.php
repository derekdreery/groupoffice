<?php
require('Group-Office.php');

$db = new db();

$q = $_SESSION['GO_SESSION']['export_queries'][$_REQUEST['query']];

$params = array();
$types='';
	
	
if(is_array($q))
{
	$extra_sql=array();
	$sql = $q['query'];
	if(isset($q['extra_params']))
	{
		foreach($q['extra_params'] as $param=>$sqlpart)
		{
			if(!empty($_REQUEST[$param]))
			{
				$params[] = $_REQUEST[$param];
				$extra_sql[]=$sqlpart;
			}
		}
	}
	if(count($params))
	{
		$insert = ' ';
		if(!strpos($sql, 'WHERE'))
		{
			$insert .= 'WHERE ';
		}else
		{
			$insert .= ' AND ';
		}
		$insert .= implode(' AND ', $extra_sql);
	
		$pos = strpos($sql, 'ORDER');
		
		if(!$pos)
		{		
			$sql .= $insert;
		}else
		{
			$sql = substr($sql, 0, $pos).$insert.' '.substr($sql, $pos);	
		}
		
		$types=str_repeat('s',count($params));
	}
}else
{
	$sql = $q;
	
	$params=array();
	
}

$db->query($sql,$types,$params);


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

$columns=array();
$headers=array();
if(isset($_REQUEST['columns']))
{
	$indexesAndHeaders = explode(',', $_REQUEST['columns']);
	
	foreach($indexesAndHeaders as $i)
	{
		$indexAndHeader = explode(':', $i);
		
		$headers[]=$indexAndHeader[1];
		$columns[]=$indexAndHeader[0];
	}	
	
	echo $_SESSION['GO_SESSION']['text_separator'].implode($_SESSION['GO_SESSION']['text_separator'].$_SESSION['GO_SESSION']['list_separator'].$_SESSION['GO_SESSION']['text_separator'], $headers).$_SESSION['GO_SESSION']['text_separator']."\r\n";
}


while($record = $db->next_record())
{
	if(!count($columns))
	{

		foreach($record as $key=>$value)
		{
			$columns[]=$key;
			$headers[]=$key;
		}			
		
		echo $_SESSION['GO_SESSION']['text_separator'].implode($_SESSION['GO_SESSION']['text_separator'].$_SESSION['GO_SESSION']['list_separator'].$_SESSION['GO_SESSION']['text_separator'], $headers).$_SESSION['GO_SESSION']['text_separator']."\r\n";

	}
	
	if(is_array($q))
	{
		if(!empty($q['require']))
		{
			require_once($q['require']);
		}
		call_user_func_array(array($q['class'], $q['method']),array(&$record));
	}
	
	if(isset($record['user_id']) && isset($columns['user_id']))
	{
		$user = $GO_USERS->get_user($record['user_id']);
		$record['user_id']=$user['username'];
	}
	$values=array();
	foreach($columns as $index)
	{
		$values[] = $record[$index];
	}
	echo $_SESSION['GO_SESSION']['text_separator'].implode($_SESSION['GO_SESSION']['text_separator'].$_SESSION['GO_SESSION']['list_separator'].$_SESSION['GO_SESSION']['text_separator'], $values).$_SESSION['GO_SESSION']['text_separator']."\r\n";
}

?>