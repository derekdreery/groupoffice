<?php
$updates["201112081020"][]="ALTER TABLE `si_sites` ADD `login_page` VARCHAR( 255 ) NOT NULL DEFAULT 'login'";
$updates["201112081020"][]="ALTER TABLE `si_pages` ADD `login_required` BOOLEAN NOT NULL DEFAULT '0'";