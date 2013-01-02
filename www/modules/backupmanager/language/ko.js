GO.backupmanager.lang.backupmanager='백업 관리자';
GO.backupmanager.lang.rmachine='Remote machine';
GO.backupmanager.lang.rport='포트';
GO.backupmanager.lang.rtarget='Target folder';
GO.backupmanager.lang.sources='Source folders';
GO.backupmanager.lang.rotations='Rotations';
GO.backupmanager.lang.quiet='Quiet';
GO.backupmanager.lang.emailaddresses='Email addresses';
GO.backupmanager.lang.emailsubject='Email subject';
GO.backupmanager.lang.rhomedir='Remote homedir';
GO.backupmanager.lang.rpassword='Password';
GO.backupmanager.lang.publish='Publish';
GO.backupmanager.lang.enablebackup='Start backup';
GO.backupmanager.lang.disablebackup='Stop backup';
GO.backupmanager.lang.successdisabledbackup='Backup is succesfully disabled!';
GO.backupmanager.lang.publishkey='Enable backup';
GO.backupmanager.lang.publishSuccess='Backup is succesfully enabled.';
GO.backupmanager.lang.helpText='This module will backup files and all MySQL databases (make sure you include /home/mysqlbackup in the source folders) to a remote server with rsync and SSH. When you enable the backup it will publish the SSH public key to the server and it will check if the target directory exists. So first make sure the remote backup folder exists. By default the backup is scheduled at midnight in /etc/cron.d/groupoffice-backup. You can adjust the schedule in that file or create it if it does not exist. You can also manually run the backup by executing "php /usr/share/groupoffice/modules/backupmanager/cron.php" on the terminal.';