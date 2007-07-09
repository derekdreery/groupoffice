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

require_once ($GO_MODULES->class_path."email.class.inc");
require_once ($GO_LANGUAGE->get_language_file('email'));

switch($_REQUEST['type'])
{

	case 'filters':
			$count = $email->get_filters($account_id);
			
			while($email->next_record())
			{
				$filters[] = $email->Record;
			}
			echo '({"total":"'.$msg_count.'","results":'.json_encode($filters).'})';
		break;
	
	case 'messages':

		require_once ($GO_CONFIG->class_path."mail/imap.class.inc");

		$mail = new imap();
		$email = new email();

		$GO_THEME->load_module_theme('email');

		$account_id = isset ($_REQUEST['account_id']) ? $_REQUEST['account_id'] : 0;
		$mailbox = isset ($_REQUEST['mailbox']) ? smart_stripslashes($_REQUEST['mailbox']) : 'INBOX';
		/*
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
		*/

		$account = $email->get_account($account_id);



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

		$messages=array();

		while($mail->next_message(($account['examine_headers']=='1' || isset($_POST['examine_headers']))))
		//while($mail->next_message(true))
		{
			if($mail->f('answered'))
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
			'attachments'=>$mail->f('attachments'),
			'flagged'=>$mail->f('flagged')

			//'mailbox'=>$mailbox,
			//'account_id'=>$account['id'],
			//'folder_id'=>$folder['id']

			);
		}
		echo '({"total":"'.$msg_count.'","results":'.json_encode($messages).'})';
		break;



	case 'tree':

		$email = new email();
		$email2 = new email();


		//$account_id=isset($_REQUEST['account_id']) ? smart_addslashes($_REQUEST['account_id']) : 0;
		//$folder_id=isset($_REQUEST['folder_id']) ? smart_addslashes($_REQUEST['folder_id']) : 0;

		if(isset($_REQUEST['node']) && strpos($_REQUEST['node'],'_'))
		{
			$node = explode('_',$_REQUEST['node']);
			$node_type=$node[0];
			$node_id=$node[1];
		}else {
			$node_type='root';
			$node_id=0;
		}

		$nodes=array();
		if($node_id==0)
		{
			$count = $email->get_accounts($GO_SECURITY->user_id);

			while($email->next_record())
			{
				$nodes[] = array(
				'text'=>$email->f('email'),
				'id'=>'account_'.$email->f('id'),
				'iconCls'=>'folderIcon',
				'expanded'=>true,
				'account_id'=>$email->f('id'),
				'folder_id'=>0,
				'mailbox'=>'INBOX'
				);
			}
		}else
		{
			if($node_type=='account')
			{
				$account_id=$node_id;
				$folder_id=0;
			}else {
				$account_id=0;
				$folder_id=$node_id;
			}

			$email->get_subscribed($account_id, $folder_id);
			while($email->next_record())
			{


				if($email->f('name') == 'INBOX')
				{
					$folder_name = $ml_inbox;
				}else
				{
					$pos = strrpos($email->f('name'), $email->f('delimiter'));

					if ($pos && $email->f('delimiter') != '')
					{
						$folder_name = substr($email->f('name'),$pos+1);
					}else
					{
						$folder_name = $email->f('name');
					}
					$folder_name = utf7_imap_decode($folder_name);
				}

				//check for unread mail
				$unseen = $email->f('unseen');

				if ($unseen > 0)
				{
					$status = '&nbsp;(<span id="status_'.$email->f('id').'">'.$unseen.'</span>)';
				}else
				{
					$status = '&nbsp;<span id="status_'.$email->f('id').'"></span>';
				}

				if($email2->get_subscribed(0, $email->f('id')))
				{
					$nodes[] = array(
					'text'=>$folder_name.$status,
					'id'=>'folder_'.$email->f('id'),
					'iconCls'=>'folderIcon',
					'account_id'=>$email->f('account_id'),
					'folder_id'=>$email->f('id'),
					'mailbox'=>$email->f('name')
					);
				}else {
					$nodes[] = array(
					'text'=>$folder_name.$status,
					'id'=>'folder_'.$email->f('id'),
					'iconCls'=>'folderIcon',
					'account_id'=>$email->f('account_id'),
					'folder_id'=>$email->f('id'),
					'mailbox'=>$email->f('name'),
					'expanded'=>true,
					'children'=>array()
					);
				}
			}
		}

		echo json_encode($nodes);
		break;


	case 'tree-edit':

		$email = new email();
		$email2 = new email();


		if(isset($_REQUEST['node']) && strpos($_REQUEST['node'],'_'))
		{
			$node = explode('_',$_REQUEST['node']);
			$node_type=$node[0];
			$node_id=$node[1];
		}else {
			$node_type='root';
			$node_id=0;
		}

		$nodes=array();
		if($node_id==0)
		{
			$count = $email->get_accounts($GO_SECURITY->user_id);

			while($email->next_record())
			{
				$nodes[] = array(
				'text'=>$email->f('email'),
				'id'=>'account_'.$email->f('id'),
				'iconCls'=>'folderIcon',
				'expanded'=>true,
				'account_id'=>$email->f('id'),
				'folder_id'=>0,
				'mailbox'=>'INBOX'
				);
			}
		}else
		{
			if($node_type=='account')
			{
				$account_id=$node_id;
				$folder_id=0;
			}else {
				$account_id=0;
				$folder_id=$node_id;
			}

			$email->get_subscribed($account_id, $folder_id);
			while($email->next_record())
			{


				if($email->f('name') != 'INBOX')
				{

					$pos = strrpos($email->f('name'), $email->f('delimiter'));

					if ($pos && $email->f('delimiter') != '')
					{
						$folder_name = substr($email->f('name'),$pos+1);
					}else
					{
						$folder_name = $email->f('name');
					}
					$folder_name = utf7_imap_decode($folder_name);


					if($email2->get_subscribed(0, $email->f('id')))
					{
						$nodes[] = array(
						'text'=>$folder_name,
						'id'=>'folder_'.$email->f('id'),
						'iconCls'=>'folderIcon',
						'expanded'=>true,
						'account_id'=>$email->f('account_id'),
						'folder_id'=>$email->f('id'),
						'mailbox'=>$email->f('name')
						);
					}else {
						$nodes[] = array(
						'text'=>$folder_name,
						'id'=>'folder_'.$email->f('id'),
						'iconCls'=>'folderIcon',
						'account_id'=>$email->f('account_id'),
						'folder_id'=>$email->f('id'),
						'mailbox'=>$email->f('name'),
						'expanded'=>true,
						'children'=>array()
						);
					}
				}
			}
		}

		echo json_encode($nodes);
		break;

	case 'accounts':

		$email = new email();

		$accounts=array();

		$count = $email->get_accounts($GO_SECURITY->user_id);

		while($email->next_record())
		{
			$accounts[] = array(
			'id'=>$email->f('id'),
			'email'=>$email->f('email'),
			'host'=>$email->f('host'),
			'type'=>$email->f('type'),
			'standard'=>$email->f('standard')
			);
		}

		echo '({"total":"'.$count.'","results":'.json_encode($accounts).'})';

		break;

}