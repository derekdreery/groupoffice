<?php
//Uncomment this line in new translations!
//require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('backupmanager'));

$lang['backupmanager']['name']='Backup Manager';
$lang['backupmanager']['description']='Configure your backup cronjob';
$lang['backupmanager']['save_error']='Error while saving settings';
$lang['backupmanager']['empty_key']='Key is empty';
$lang['backupmanager']['connection_error']='Couldn\'t connect to the server';
$lang['backupmanager']['no_mysql_config']='{product_name} was not able to find a mysql config file. This file is used to create a backup of the complete database. You can create this yourself by adding a file named backupmanager.inc.php in /etc/groupoffice/ with the following contents:
    <br /><br />&lt;?php<br />
    $bm_config[\'mysql_user\'] = \'\';<br />
    $bm_config[\'mysql_pass\'] = \'\';<br />
    ?><br /><br />
    Without this file backups are still created, but not from the database.';
$lang['backupmanager']['target_does_not_exist']='The target directory doesn\'t exist!';