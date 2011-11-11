<?php
$updates["201110211025"][]="RENAME TABLE `ml_mailings` TO `ab_sent_mailings`";
$updates["201110211025"][]="RENAME TABLE `ml_mailing_groups` TO `ab_addresslists`";
$updates["201110211025"][]="RENAME TABLE `ml_mailing_contacts` TO `ab_addresslist_contacts`";
$updates["201110211025"][]="RENAME TABLE `ml_mailing_companies` TO `ab_addresslist_companies`";
$updates["201110211025"][]="ALTER TABLE `ab_sent_mailings` CHANGE `mailing_group_id` `addresslist_id` int(11) NOT NULL";
$updates["201110211025"][]="ALTER TABLE `ab_addresslist_contacts` CHANGE `group_id` `list_id` int(11) NOT NULL";
$updates["201110211025"][]="ALTER TABLE `ab_addresslist_companies` CHANGE `group_id` `list_id` int(11) NOT NULL";
$updates["201110211025"][]="ALTER TABLE `ab_addresslist_contacts` CHANGE `list_id` `addresslist_id` int(11) NOT NULL";
$updates["201110211025"][]="ALTER TABLE `ab_addresslist_companies` CHANGE `list_id` `addresslist_id` int(11) NOT NULL";
$updates["201110281015"][]="ALTER TABLE `ab_addresslists` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ";
$updates["201111081444"][]="RENAME TABLE `ml_templates` TO `ab_email_templates`";
?>