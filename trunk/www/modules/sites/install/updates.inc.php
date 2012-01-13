<?php
$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `login_path` VARCHAR( 255 ) NOT NULL DEFAULT 'login'";
$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `logout_path` VARCHAR( 255 ) NOT NULL DEFAULT 'logout'";
$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `register_path` VARCHAR( 255 ) NOT NULL DEFAULT 'register'";
$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `reset_password_path` VARCHAR( 255 ) NOT NULL DEFAULT 'resetpassword'";
$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `lost_password_path` VARCHAR( 255 ) NOT NULL DEFAULT 'lostpassword'";
$updates["201112081020"][]="ALTER TABLE `si_pages` ADD `login_required` BOOLEAN NOT NULL DEFAULT '0'";


$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `ssl` BOOLEAN NOT NULL DEFAULT '0',
ADD `mod_rewrite` BOOLEAN NOT NULL DEFAULT '0',
ADD `mod_rewrite_base_path` VARCHAR( 50 ) NOT NULL DEFAULT '/'";

$updates["201201111000"][]="ALTER TABLE `si_sites` ADD `register_user_groups` VARCHAR( 50 ) NULL DEFAULT ''";