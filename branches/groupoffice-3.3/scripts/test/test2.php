<?php
/*if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}*/


require('../../www/Group-Office.php');
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

//echo $GO_THEME->replace_url(file_get_contents($GO_CONFIG->root_path.'themes/Default/style.css'), $GO_CONFIG->host.'themes/');

$b = detect_browser();
var_dump($b);

?>
