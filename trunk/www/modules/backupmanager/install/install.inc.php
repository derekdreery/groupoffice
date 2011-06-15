<?php
$module = $this->get_module('backupmanager');

global $GO_LANGUAGE, $GO_SECURITY, $GO_CONFIG;

require_once($module['class_path'].'backupmanager.class.inc.php');
$backupmanager = new backupmanager();

$settings['emailsubject'] = GO::config()->title.' Backup';
$settings['emailaddress'] = GO::config()->webmaster_email;
$settings['id'] = 1;
$backupmanager->save_settings($settings);
