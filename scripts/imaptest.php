<?php
require('../www/Group-Office.php');
require($GO_CONFIG->class_path.'mail/imap.class.inc.php');

$imap = new imap();
$ret = $imap->connect('mail.imfoss.nl', 143, 'test@intermesh.nl', 'test', false);

var_dump($ret);
//$folders = $imap->get_folders();

$mailbox = $imap->select_mailbox('INBOX');
//var_dump($mailbox);

//$unseen = $imap->get_mailbox_unseen('INBOX');
//var_dump($unseen);

$uids = $imap->sort_mailbox('ARRIVAL');

$uids = array_splice($uids, 0, 1);

//echo count($uids);


//$headers = $imap->get_message_headers($uids);
//var_dump($headers);

$imap->get_message_structure($uids[0]);

$imap->disconnect();
