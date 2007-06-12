<?php
/**
* @copyright Intermesh 2007
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.13 $ $Date: 2006/10/20 12:36:43 $3
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 

require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

require_once ($GO_CONFIG->class_path."mail/imap.class.inc");
require_once ($GO_MODULES->class_path."email.class.inc");
require_once ($GO_LANGUAGE->get_language_file('email'));
$imap = new imap();
$email = new email();


function connect($account_id, $mailbox='INBOX')
{
	global $email, $imap, $GO_SECURITY;
	if (!$account = $email->get_account($account_id)) {
		$result['success']=false;
		$result['errors']='Database error!';
		echo json_encode($result);
		exit();
	}

	if($account['user_id']!=$GO_SECURITY->user_id)
	{
		$result['success']=false;
		$result['errors']=$strAccessDenied;
		echo json_encode($result);
		exit();
	}
	if (!$imap->open($account['host'], $account['type'], $account['port'], $account['username'], $account['password'], $mailbox, 0, $account['use_ssl'], $account['novalidate_cert'])) {
		$result['success']=false;
		$result['errors']='Could not connect to server: '.$account['host'];
		echo json_encode($result);
		exit();
	}

}


$result =array();
switch($_REQUEST['task'])
{
    case 'delete':
        
        $selectedRows = json_decode(smart_stripslashes($_POST['selectedRows']));
        foreach($selectedRows as $note_id)
        {
            $notes->delete_note($note_id);            
        }
        $result['success']=true;
        $result['errors']='Messages deleted successfully';
        
        break;
        
    case 'move':
    	$messages = json_decode(smart_stripslashes($_REQUEST['messages']));
    	$from_mailbox = smart_stripslashes($_REQUEST['from_mailbox']);
    	$to_mailbox = smart_stripslashes($_REQUEST['to_mailbox']);
    	$account_id = smart_stripslashes($_REQUEST['account_id']);
    	
    	connect($account_id, $from_mailbox);
    	
    	$imap->move($to_mailbox, $messages);    	
    	

        $result['success']=true;
        $result['errors']='Messages moved successfully';    	
    	break;
}

echo json_encode($result);