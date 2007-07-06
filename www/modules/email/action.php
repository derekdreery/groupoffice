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

ini_set('display_errors','off');


function connect($account_id, $mailbox='INBOX')
{
	global $email, $imap, $GO_SECURITY;
	if (!$account = $email->get_account($account_id)) {
		$result['success']=false;
		$result['errors']=$strDataError;
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
		$result['errors']=$ml_connect_failed.' '.$account['host'];
		echo json_encode($result);
		exit();
	}

	return $account;

}

//we are unsuccessfull by default
$result =array('success'=>false);

switch($_REQUEST['task'])
{
	case 'add_folder':

		$account = connect(smart_addslashes($_REQUEST['account_id']));

		$delimiter = $imap->get_mailbox_delimiter();
		$parent_id=smart_addslashes($_REQUEST['folder_id']);
		if($parent_id>0)
		{
			if($folder = $email->get_folder_by_id(smart_addslashes($parent_id)))
			{
				$new_folder_name=$folder['name'].$delimiter.utf7_imap_encode(smart_stripslashes($_POST['new_folder_name']));
			}else {
				$result['success']=false;
				$result['errors']=$strDataError;
				echo json_encode($result);
				exit();
			}

		}else {
			$new_folder_name=utf7_imap_encode(smart_stripslashes($_POST['new_folder_name']));
		}





		if($imap->create_folder($new_folder_name, $delimiter))
		{
			$result['succes']=$email->add_folder($account['id'], addslashes($new_folder_name), $parent_id, 1,$delimiter);
		}
		$imap->close();

		break;

	case 'delete_folder':

		if($folder = $email->get_folder_by_id(smart_addslashes($_REQUEST['folder_id'])))
		{
			$account = connect($folder['account_id']);



			if ($imap->delete_folder($folder['name'], $account['mbroot']))
			{
				/*
				(cyrus imap) if folder still exists then don't delete it from the
				database,
				because it contains at least one child mailbox
				*/
				if (!is_array($imap->get_mailboxes($folder['name'])))
				{
					$result['succes']=$email->delete_folder($account['id'], addslashes($folder['name']));
				}
			}
			$imap->close();
		}
		break;
	case 'rename_folder':

		if($folder = $email->get_folder_by_id(smart_addslashes($_REQUEST['folder_id'])))
		{
			$pos = strrpos($folder['name'], $folder['delimiter']);
			if ($pos && $folder['delimiter'] != '')
			{
				$location = substr($folder['name'],0,$pos+1);

			}else
			{
				$location = '';
			}

			$new_folder = $location.utf7_imap_encode(smart_stripslashes($_POST['new_name']));

			connect($folder['account_id']);

			//echo $folder['name'].' -> '.$new_folder;
			if ($imap->rename_folder($folder['name'], $new_folder))
			{
				$result['success']=$email->rename_folder($folder['account_id'], addslashes($folder['name']), addslashes($new_folder));
			}
			$imap->close();
		}
		break;

	case 'syncfolders':

		$account = $email->get_account($_REQUEST['account_id']);
		$email->synchronize_folders($account);
		break;

	case 'save_account_properties':



		$account['mbroot'] = isset($_POST['mbroot']) ? addslashes(utf7_imap_encode(smart_stripslashes($_POST['mbroot']))) : '';
		if ($_POST['name'] == "" ||
		$_POST['mail_address'] == "" ||
		$_POST['port'] == "" ||
		$_POST['email_user'] == "" ||
		$_POST['email_pass'] == "" ||
		$_POST['host'] == "")
		{
			$result['errors'] = $error_missing_field;

		}else
		{
			$account['id']=$_POST['account_id'];
			$account['mbroot'] = isset($_POST['mbroot']) ? smart_addslashes($_POST['mbroot']) : '';
			$account['use_ssl'] = isset($_REQUEST['use_ssl'])  ? $_REQUEST['use_ssl'] : '0';
			$account['novalidate_cert'] = isset($_REQUEST['novalidate_cert']) ? $_REQUEST['novalidate_cert'] : '0';
			$account['examine_headers'] = isset($_POST['examine_headers']) ? '1' : '0';
			$account['type']=smart_addslashes($_POST['type']);
			$account['host']=smart_addslashes($_POST['host']);
			$account['port']=smart_addslashes($_POST['port']);
			$account['username']=smart_addslashes($_POST['email_user']);
			$account['password']=smart_addslashes($_POST['email_pass']);
			$account['name']=smart_addslashes($_POST['name']);
			$account['email']=smart_addslashes($_POST['mail_address']);
			$account['signature']=smart_addslashes($_POST['signature']);


			if ($account['id'] > 0)
			{
				if(isset($_REQUEST['account_user_id']))
				{
					$account['user_id']=smart_addslashes($_REQUEST['account_user_id']);
				}

				if(!$email->update_account($account))
				{
					$result['errors'] = $ml_connect_failed.' '.
					$_POST['host'].' '.$ml_at_port.': '.$_POST['port'].' '.$email->last_error;
				}else
				{
					$result['success']=true;
				}
			}else
			{
				$account['user_id']=isset($_REQUEST['account_user_id']) ? smart_stripslashes($_REQUEST['account_user_id']) : $GO_SECURITY->user_id;

				if(!$account_id = $email->add_account($account))
				{
					$result['errors'] = $ml_connect_failed.' '.
					$_POST['host'].' '.$ml_at_port.': '.$_POST['port'].' '.$email->last_error;
				}else
				{
					$result['success']=true;
					$result['account_id']=$account_id;
				}
			}
		}
		break;

	case 'delete':


		$mailbox = smart_stripslashes($_REQUEST['mailbox']);
		$account_id = smart_stripslashes($_REQUEST['account_id']);

		$account = connect($account_id, $mailbox);

		$messages = json_decode(smart_stripslashes($_POST['messages']));

		if($mailbox != $account['trash'])
		{
			$result['success']=$imap->move($account['trash'], $messages);
		}else {
			$result['success']=$imap->delete($messages);
		}

		break;
	case 'mark_as_read':
		$mailbox = smart_stripslashes($_REQUEST['mailbox']);
		$account_id = smart_stripslashes($_REQUEST['account_id']);
		$account = connect($account_id, $mailbox);
		$messages = json_decode(smart_stripslashes($_POST['messages']));
		$result['success']=$imap->set_message_flag($mailbox, $messages, "\\Seen");
		break;
	case 'mark_as_unread':
		$mailbox = smart_stripslashes($_REQUEST['mailbox']);
		$account_id = smart_stripslashes($_REQUEST['account_id']);
		$account = connect($account_id, $mailbox);
		$messages = json_decode(smart_stripslashes($_POST['messages']));
		$result['success']=$imap->set_message_flag($mailbox, $messages, "\\Seen", "reset");
		break;
	case 'flag':
		$mailbox = smart_stripslashes($_REQUEST['mailbox']);
		$account_id = smart_stripslashes($_REQUEST['account_id']);
		$account = connect($account_id, $mailbox);
		$messages = json_decode(smart_stripslashes($_POST['messages']));
		$result['success']=$imap->set_message_flag($mailbox, $messages, "\\Flagged");
		break;
	case 'unflag':
		$mailbox = smart_stripslashes($_REQUEST['mailbox']);
		$account_id = smart_stripslashes($_REQUEST['account_id']);
		$account = connect($account_id, $mailbox);
		$messages = json_decode(smart_stripslashes($_POST['messages']));
		$result['success']=$imap->set_message_flag($mailbox, $messages, "\\Flagged", "reset");
		break;

	case 'move':
		$messages = json_decode(smart_stripslashes($_REQUEST['messages']));
		$from_mailbox = smart_stripslashes($_REQUEST['from_mailbox']);
		$to_mailbox = smart_stripslashes($_REQUEST['to_mailbox']);
		$account_id = smart_stripslashes($_REQUEST['account_id']);

		connect($account_id, $from_mailbox);

		$result['success']=$imap->move($to_mailbox, $messages);

		break;
}

echo json_encode($result);