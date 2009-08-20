<?php
/*if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}*/


//require('../../www/Group-Office.php');

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

?>
