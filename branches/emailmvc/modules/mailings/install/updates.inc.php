<?php
$updates[201110211025][]="RENAME TABLE `ml_mailings` TO `ab_sent_mailings`";
$updates[201110211025][]="RENAME TABLE `ml_mailing_groups` TO `ab_addresslists`";
$updates[201110211025][]="RENAME TABLE `ml_mailing_contacts` TO `ab_addresslist_contacts`";
$updates[201110211025][]="RENAME TABLE `ml_mailing_companies` TO `ab_addresslist_companies`";
$updates[201110211025][]="ALTER TABLE `ab_sent_mailings` CHANGE `mailing_group_id` `addresslist_id` int(11) NOT NULL";
$updates[201110211025][]="ALTER TABLE `ab_addresslist_contacts` CHANGE `group_id` `list_id` int(11) NOT NULL";
$updates[201110211025][]="ALTER TABLE `ab_addresslist_companies` CHANGE `group_id` `list_id` int(11) NOT NULL";
$updates[201110211025][]="ALTER TABLE `ab_addresslist_contacts` CHANGE `list_id` `addresslist_id` int(11) NOT NULL";
$updates[201110211025][]="ALTER TABLE `ab_addresslist_companies` CHANGE `list_id` `addresslist_id` int(11) NOT NULL";
?>