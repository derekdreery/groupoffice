<?php

$updates[201108190000][]="RENAME TABLE `go_links_9` TO `go_links_em_emails`;";
$updates[201108190000][]="ALTER TABLE `go_links_em_emails` CHANGE `link_id` `model_id` INT( 11 ) NOT NULL";
$updates[201108190000][]="ALTER TABLE `go_links_em_emails` CHANGE `link_type` `model_type_id` INT( 11 ) NOT NULL";

$updates[201108301656][]="INSERT INTO `go_model_types` (
`id` ,
`model_name`
)
VALUES (
'9', 'GO_Email_Model_LinkedEmail'
);";