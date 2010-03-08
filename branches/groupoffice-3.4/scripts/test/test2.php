<?php
if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}


require('../../www/Group-Office.php');


$rfc822 = '';


require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
require_once($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
require_once($GO_MODULES->modules['sync']['class_path']."settings.class.inc.php");
$email = new email();
$sync_settings = new sync_settings();
$settings = $sync_settings->get_settings(1);
$account = $email->get_account($settings['account_id']);
$goswift = new GoSwiftImport($rfc822,true, $account['default_alias_id']);
$goswift->sendmail();