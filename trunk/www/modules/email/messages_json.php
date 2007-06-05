<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

require('../../Group-Office.php');

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');


require_once ($GO_CONFIG->class_path."mail/imap.class.inc");
require_once ($GO_MODULES->class_path."email.class.inc");
require_once ($GO_LANGUAGE->get_language_file('email'));
$mail = new imap();
$email = new email();

$account_id = isset ($_REQUEST['account_id']) ? $_REQUEST['account_id'] : 0;
$mailbox = isset ($_REQUEST['mailbox']) ? smart_stripslashes($_REQUEST['mailbox']) : 'INBOX';


if (!$account = $email->get_account($account_id)) {
	$account = $email->get_account(0);
}
if ($account) {
	if($account['user_id']!=$GO_SECURITY->user_id)
	{
		$result['success']=false;
		$result['errors']=$strAccessDenied;
		echo json_encode($result);
		exit();
	}
	if (!$mail->open($account['host'], $account['type'], $account['port'], $account['username'], $account['password'], $mailbox, 0, $account['use_ssl'], $account['novalidate_cert'])) {
		$result['success']=false;
		$result['errors']='Error connection to server!';
		echo json_encode($result);
		exit();
	}
}

$sort_field=SORTARRIVAL;
$sort_order=1;

$msg_count = $mail->sort($sort_field , $sort_order);

$mail->get_messages($_REQUEST['start'], $_REQUEST['limit']);

while($mail->next_message(($account['examine_headers']=='1' || isset($_POST['examine_headers']))))
{
		$messages[]=array(
		'uid'=>$mail->f('uid'),
		'subject'=>$mail->f('subject'),
		'from'=>$mail->f('from'),
		'size'=>format_size($mail->f('size')),
		'date'=>get_timestamp($mail->f('udate'))
		);
}
echo '({"total":"'.$msg_count.'","results":'.json_encode($messages).'})'; 