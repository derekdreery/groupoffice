<?php
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `count_users` `count_users` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `install_time` `install_time` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `total_logins` `total_logins` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `report_ctime` `report_ctime` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `database_usage` `database_usage` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `file_storage_usage` `file_storage_usage` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `admin_country` `admin_country` CHAR( 2 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `thousands_separator` `thousands_separator` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `billing` `billing` TINYINT( 1 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `professional` `professional` TINYINT( 1 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `status_change_time` `status_change_time` INT( 11 ) NOT NULL DEFAULT '0'";
$updates["201203201554"][]="ALTER TABLE `sm_installations` CHANGE `config_file` `config_file` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''";

$updates["201203291226"][]="ALTER TABLE `sm_installations` ADD `token` VARCHAR( 100 ) NOT NULL";

$updates["201203291226"][]="CREATE TABLE IF NOT EXISTS `sm_installation_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `installation_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `ctime` int(11) NOT NULL,
  `lastlogin` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `installation_id` (`installation_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8";


$updates["201203291226"][]="CREATE TABLE IF NOT EXISTS `sm_installation_user_modules` (
  `user_id` int(11) NOT NULL,
  `module_id` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`,`module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";


$updates["201203291226"][]="ALTER TABLE `sm_installations` CHANGE `lastlogin` `lastlogin` INT( 11 ) NOT NULL DEFAULT '0'";

$updates["201205091330"][]="CREATE TABLE IF NOT EXISTS `sm_auto_email` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL DEFAULT '',
	`days` int(5) NOT NULL DEFAULT '0',
	`mime` TEXT,
	`active` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$updates["201205091615"][]="INSERT INTO `sm_auto_email` (`id`,`name`,`days`,`mime`,`active`) VALUES (NULL , 'Example automatic email', '20',
'Message-ID: <1336645137.4fab9611360c0@localhost>
Date: Thu, 10 May 2012 12:18:57 +0200
Subject: Example automatic email
From: 
MIME-Version: 1.0
Content-Type: multipart/alternative;
 boundary=\"_=_swift_v4_13366451374fab961137ae9_=_\"
X-Mailer: Group-Office 4.0.12
X-MimeOLE: Produced by Group-Office 4.0.12


--_=_swift_v4_13366451374fab961137ae9_=_
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: quoted-printable

Dear {installation:admin_name},

You receive this e-mail becau=
se on {installation:ctime},
{automaticemail:days} days ago, you creat=
ed a 30 day trial
installation of Group-Office Professional at
h=
ttp://{installation:name}.
We want to remind you that the trial peri=
od will expire in 10 days.

We hope you are enjoying your trial p=
eriod and you will continue using
it. If you want to continue using =
Group-Office you must pay for the
service after this trial period ex=
pires. If you don't order within 20
days then we asume you don't want=
 to use Group-Office anymore and your
installation will be remov=
ed.

Thank you for using Group-Office!

With kind rega=
rds,

The Group-Office team.


--_=_swift_v4_13366451374fab961137ae9_=_
Content-Type: text/html; charset=UTF-8
Content-Transfer-Encoding: quoted-printable

Dear {installation:admin_name},<br><br>You receive this e-mail because o=
n {installation:ctime}, {automaticemail:days} days ago, you created a 30=
 day trial installation of Group-Office Professional at http://{installa=
tion:name}.<br>We want to remind you that the trial period will expire i=
n 10 days.<br><br>We hope you are enjoying your trial period and you wil=
l continue using it. If you want to continue using Group-Office you must=
 pay for the service after this trial period expires. If you don't order=
 within 20 days then we asume you don't want to use Group-Office anymore=
 and your installation will be removed.<br><br>Thank you for using Group=
-Office!<br><br>With kind regards,<br><br>The Group-Office team.<br>

--_=_swift_v4_13366451374fab961137ae9_=_--

', '0');";


$updates["201207051558"][]="ALTER TABLE `sm_installations` CHANGE `status` `status` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'ignore'";
$updates["201207051558"][]="UPDATE sm_installations set status='ignore' where status=''";
$updates["201207051558"][]="UPDATE sm_installations set status='trial' where status!='ignore'";