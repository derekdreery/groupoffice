/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Dat Pham <datpx@fab.vn> +84907382345
 */
 
Ext.namespace('GO.backupmanager');
GO.backupmanager.lang={};

GO.backupmanager.lang.backupmanager='Quản lý sao lưu';
GO.backupmanager.lang.rmachine='Máy điều khiển';
GO.backupmanager.lang.rport='Cổng';
GO.backupmanager.lang.rtarget='Thư mục đích';
GO.backupmanager.lang.sources='Thư mục nguồn';
GO.backupmanager.lang.rotations='Quay';
GO.backupmanager.lang.quiet='Hoàn toàn';
GO.backupmanager.lang.emailaddresses='Địa chỉ email';
GO.backupmanager.lang.emailsubject='Chủ đề email';
GO.backupmanager.lang.rhomedir='Thư mục nhà';
GO.backupmanager.lang.rpassword='Mật khẩu';
GO.backupmanager.lang.publish='Xuất bản';
GO.backupmanager.lang.enablebackup='Bắt đầu sao lưu';
GO.backupmanager.lang.disablebackup='Bỏ sao lưu';
GO.backupmanager.lang.successdisabledbackup='Bỏ sao lưu thành công!';
GO.backupmanager.lang.publishkey='Bật sao lưu';
GO.backupmanager.lang.publishSuccess='Bật sao lưu thành công';
GO.backupmanager.lang.helpText='This module will backup files and all MySQL databases (make sure you include /home/mysqlbackup in the source folders) to a remote server with rsync and SSH. When you enable the backup it will publish the SSH public key to the server and it will check if the target directory exists. So first make sure the remote backup folder exists. By default the backup is scheduled at midnight in /etc/cron.d/groupoffice-backup. You can adjust the schedule in that file or create it if it does not exist. You can also manually run the backup by executing "php /usr/share/groupoffice/modules/backupmanager/cron.php" on the terminal.';