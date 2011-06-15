<?php
require('../www/Group-Office.php');
require(GO::config()->class_path.'mail/imap.class.inc');

$imap = new imap();
$ret = $imap->open('mail.imfoss.nl','IMAP', 143, 'test@intermesh.nl', 'test');
$imap->sort();

$uids = $imap->get_message_uids(0, 30);

$headers = $imap->get_message_headers($uids);

var_dump($headers);

$imap->close();