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


require_once ($GO_MODULES->class_path."email.class.inc");
require_once ($GO_LANGUAGE->get_language_file('email'));

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
			'cls'=>'folder', 
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
				'cls'=>'folder', 
				'account_id'=>$email->f('account_id'),
				'folder_id'=>$email->f('id'),				
				'mailbox'=>$email->f('name')
				);
		}else {
			$nodes[] = array(
				'text'=>$folder_name.$status, 
				'id'=>'folder_'.$email->f('id'), 
				'cls'=>'folder', 
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

