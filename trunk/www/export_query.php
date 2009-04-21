<?php
require('Group-Office.php');

require_once($GO_CONFIG->class_path.'export_query.class.inc.php');
$eq = new export_query();
$eq->download_headers();

$fp = fopen('php://output','w');
$eq->export($fp);
fclose($fp);
?>