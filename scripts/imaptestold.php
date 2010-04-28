<?php
require('../www/Group-Office.php');
require($GO_CONFIG->class_path.'mail/imap.class.inc');

$imap = new imap();
//$ret = $imap->open('mail.imfoss.nl','IMAP', 143, 'test@intermesh.nl', 'test');
$ret = $imap->open('imap.domeneshop.no', 'IMAP', 143, 'nri3', '7MQPHvwz');
$imap->sort();

$uids = $imap->get_message_uids(0, 30);

$headers = $imap->get_message_headers($uids);

var_dump($headers);

$imap->close();