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

ini_set('display_errors','off');


require_once ($GO_CONFIG->class_path."mail/imap.class.inc");
require_once ($GO_MODULES->class_path."email.class.inc");
require_once ($GO_LANGUAGE->get_language_file('email'));
$mail = new imap();
$email = new email();


$GO_THEME->load_module_theme('email');

$account_id = isset ($_REQUEST['account_id']) ? $_REQUEST['account_id'] : 0;
$mailbox = isset ($_REQUEST['mailbox']) ? smart_stripslashes($_REQUEST['mailbox']) : 'INBOX';

if(isset($_REQUEST['node']) && strpos($_REQUEST['node'],'_'))
{
	$node = explode('_',$_REQUEST['node']);
	$node_type=$node[0];
	$node_id=$node[1];
	
	if($node_type=='account')
	{
		$account_id=$node_id;
		$mailbox == 'INBOX';
	}else {
		$folder = $email->get_folder_by_id($node_id);
		$mailbox = $folder['name'];
		$account_id=$folder['account_id'];
	}	
	
}else {
	$mailbox = 'INBOX';
	
}


if (!$account = $email->get_account($account_id)) {
	$account = $email->get_account(0);
}

if(!isset($folder))
{
	$folder = $email->get_folder($account['id'],$mailbox);
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
		$result['errors']='Could not connect to server: '.$account['host'];
		echo json_encode($result);
		exit();
	}
}

$sort_field=SORTARRIVAL;
$sort_order=1;

$msg_count = $mail->sort($sort_field , $sort_order);

$mail->get_messages($_REQUEST['start'], $_REQUEST['limit']);

//require($GO_CONFIG->class_path.'mail/RFC822.class.inc');
$RFC822 = new RFC822();

while($mail->next_message(($account['examine_headers']=='1' || isset($_POST['examine_headers']))))
{
	if($mail->f('new'))
	{
		$icon = $GO_THEME->images['message_new'];
	}elseif($mail->f('answered'))
	{
		$icon = $GO_THEME->images['message_answered'];
	}else {
		$icon = $GO_THEME->images['message'];
	}
	$messages[]=array(
		'uid'=>$mail->f('uid'),
		'icon'=>$icon,
		'new'=>$mail->f('new'),
		'subject'=>$mail->f('subject'),
		//'from'=>htmlspecialchars($RFC822->write_address($mail->f('from'),$mail->f('sender'))),
		'from'=>$mail->f('from'),
		'size'=>format_size($mail->f('size')),
		'date'=>get_timestamp($mail->f('udate')),
		'mailbox'=>$mailbox,
		'account_id'=>$account['id'],
		'folder_id'=>$folder['id']
		
	);
}
echo '({"total":"'.$msg_count.'","results":'.json_encode($messages).'})';