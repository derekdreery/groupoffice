<?php
/*if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}*/


//require('../../www/Group-Office.php');
/*
$record['test']='bla';

$template= '{test|number}

{test2}

{name},<br>{status_name}';

function replace($tag, $record){
	var_dump($tag);

	var_dump($record);

	echo "-----\n\n";
}

echo preg_replace('/{[^}]*}/eU', "replace('$0', \$record)", $template)
 *
 */

$pattern = '/^\S{1}[^:]*:.*/';

$line[] = 'DTSTART:1234545456';
$line[] = ' DTSTART:1234545456';
$line[] = 'sdvds : ds  ds';

foreach($line as $l){
	$ret = preg_match($pattern, $l);
	var_dump($ret);
}


?>
