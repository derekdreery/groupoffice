<?php
$updates[12] =array();

$updates[11][]="ALTER TABLE `users` ADD `link_id` INT NOT NULL ;";

$updates[11][]="ALTER TABLE `users` ADD INDEX ( `link_id` ) ;";

$updates[11][]="INSERT INTO `settings` VALUES (0, 'registration_unconfirmed_subject', 'Your account still needs activation');";
$updates[11][]="INSERT INTO `settings` VALUES (0, 'registration_confirmation', '<html>\r\n    <head>\r\n    </head>\r\n    <body>\r\n        <font size=\"3\" face=\"Verdana\">Dear&nbsp;%beginning%&nbsp;%middle_name%%last_name%,<br />\r\n        <br />\r\n        Welcome to Group-Office! You can login at:<br />\r\n        <br />\r\n        %full_url%<br />\r\n        <br />\r\n        With:<br />\r\n        <br />\r\n        Username: %username%<br />\r\n        Password: %password%<br />\r\n        <br />\r\n        With kind regards,<br />\r\n        <br />\r\n        The Group-Office administrator</font>\r\n    </body>\r\n</html>');";
$updates[11][]="INSERT INTO `settings` VALUES (0, 'registration_unconfirmed', '<html>\r\n    <head>\r\n    </head>\r\n    <body>\r\n        <font size=\"3\" face=\"Verdana\">Dear&nbsp;%beginning% %middle_name%%last_name%,<br />\r\n        <br />\r\n        Thank you for your registration at Group-Office. You can login when an administrator activates your account. You will recieve an e-mail with login instructions at that time.<br />\r\n        <br />\r\n        With kind regards,<br />\r\n        <br />\r\n        The Group-Office administrator</font>\r\n    </body>\r\n</html>');";
$updates[11][]="INSERT INTO `settings` VALUES (0, 'registration_confirmation_subject', 'Welcome to Group-Office!');";


$updates[10][]="ALTER TABLE `users` ADD `address_no` VARCHAR( 10 ) NOT NULL AFTER `address` ;";
$updates[10][]="ALTER TABLE `users` ADD `work_address_no` VARCHAR( 10 ) NOT NULL AFTER `work_address` ;";
