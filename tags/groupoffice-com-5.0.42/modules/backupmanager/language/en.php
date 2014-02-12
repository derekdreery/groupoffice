<?php
$l['name']='Backup Manager';
$l['description']='Configure your backup cronjob';


$l["backupmanager"]='Backup Manager';
$l["rmachine"]='Remote machine';
$l["rport"]='Port';
$l["rtarget"]='Target folder';
$l["sources"]='Source folders';
$l["rotations"]='Rotations';
$l["quiet"]='Quiet';
$l["emailaddresses"]='Email addresses';
$l["emailsubject"]='Email subject';
$l["rhomedir"]='Remote homedir';
$l["rpassword"]='Password';
$l["publish"]='Publish';
$l["enablebackup"]='Start backup';
$l["disablebackup"]='Stop backup';
$l["successdisabledbackup"]='Backup is succesfully disabled!';
$l["publishkey"]='Enable backup';
$l["publishSuccess"]='Backup is succesfully enabled.';
$l["helpText"]='This module will backup files and all MySQL databases (make sure you include /home/mysqlbackup in the source folders) to a remote server with rsync and SSH. When you enable the backup it will publish the SSH public key to the server and it will check if the target directory exists. So first make sure the remote backup folder exists. By default the backup is scheduled at midnight in /etc/cron.d/groupoffice-backup. You can adjust the schedule in that file or create it if it does not exist. You can also manually run the backup by executing "php /usr/share/groupoffice/modules/backupmanager/cron.php" on the terminal.';
$l['save_error']='Error while saving settings';
$l['empty_key']='Key is empty';
$l['connection_error']='Couldn\'t connect to the server';
$l['no_mysql_config']='{product_name} was not able to find a mysql config file. This file is used to create a backup of the complete database. You can create this yourself by adding a file named backupmanager.inc.php in /etc/groupoffice/ with the following contents:;
    <br /><br />&lt;?php<br />;
    $bm_config[\'mysql_user\'] = \'\';<br />;
    $bm_config[\'mysql_pass\'] = \'\';<br />
    <br /><br />;
    Without this file backups are still created, but not from the database.';
$l['target_does_not_exist']='The target directory doesn\'t exist!';