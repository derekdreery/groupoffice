TRUNCATE TABLE `go_state`;
delete from go_settings where name='version';

ALTER TABLE `go_users` ADD `mute_reminder_sound` ENUM( '0', '1' ) NOT NULL AFTER `mute_sound` ,
ADD `mute_new_mail_sound` ENUM( '0', '1' ) NOT NULL AFTER `mute_reminder_sound`;

ALTER TABLE `go_users` ADD `show_smilies` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `mute_new_mail_sound`;
ALTER TABLE `go_users` CHANGE `password` `password` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;