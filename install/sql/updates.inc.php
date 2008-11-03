<?php
$updates=array();

$updates[]="CREATE TABLE `go_mail_counter` (
`host` VARCHAR( 100 ) NOT NULL ,
`date` DATE NOT NULL ,
`count` INT NOT NULL ,
PRIMARY KEY ( `host` ) ,
INDEX ( `date` )
) ENGINE = MYISAM"; 

$updates[]="ALTER TABLE `go_search_cache` ADD PRIMARY KEY(`id`,`link_type`);";

$updates[]='script:1.inc.php';
$updates[]='script:2_ctime_in_links.inc.php';

$updates[]='script:3_install_comments_module.inc.php';
$updates[]="ALTER TABLE `go_users` ADD `mute_sound` ENUM( '0', '1' ) NOT NULL ;";
$updates[]="UPDATE go_users SET max_rows_list=50 WHERE max_rows_list>50;";
?>