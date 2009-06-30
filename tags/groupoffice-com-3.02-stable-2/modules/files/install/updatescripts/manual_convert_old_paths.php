<?php
if(isset($argv[1]))
{
    define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));

require('../../../../Group-Office.php');

$db = new db();
$db->halt_on_error='report';

require('3_convert_old_paths.inc.php');