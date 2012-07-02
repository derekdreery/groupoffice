<?php

global $GO_CONFIG, $GO_MODULES;

require_once($GO_MODULES->modules['systemusers']['class_path'].'systemusers.class.inc.php');
$su = new systemusers();

$su->get_vacation_account_ids();
while($rec = $su->next_record())
{
	$account_id = $rec['account_id'];
	exec($GO_CONFIG->cmd_sudo.' '.$GO_MODULES->modules['systemusers']['path'].'sudo.php set_vacation '.$account_id.' uninstall');
}

?>
