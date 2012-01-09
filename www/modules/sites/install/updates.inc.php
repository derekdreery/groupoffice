<?php
$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `login_page` VARCHAR( 255 ) NOT NULL DEFAULT 'login'";
$updates["201112081020"][]="ALTER TABLE `si_pages` ADD `login_required` BOOLEAN NOT NULL DEFAULT '0'";


$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `ssl` BOOLEAN NOT NULL DEFAULT '0',
ADD `mod_rewrite` BOOLEAN NOT NULL DEFAULT '0',
ADD `mod_rewrite_base_path` VARCHAR( 50 ) NOT NULL DEFAULT '/'";