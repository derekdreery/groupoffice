<?php
require('../../Group-Office.php');

$GO_SECURITY->json_authenticate('gnupg');
require_once ($GO_MODULES->modules['gnupg']['class_path'].'gnupg.class.inc.php');
$gnupg = new gnupg();

$filename='key.asc';

$data = $gnupg->export($_REQUEST['fingerprint']);


header('Content-Type: application/download');
header('Content-Length: '.strlen($data));
header('Content-Transfer-Encoding: binary');
header("Last-Modified: ".gmdate("D, d M Y H:i:s", time()-86400)." GMT");
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
echo $data;