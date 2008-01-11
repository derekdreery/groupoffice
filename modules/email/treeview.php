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

load_basic_controls();
load_control('treeview');

require_once ($GO_CONFIG->class_path."mail/imap.class.inc");
require_once ($GO_MODULES->class_path."email.class.inc");
require_once ($GO_LANGUAGE->get_language_file('email'));
$mail = new imap();
$email = new email();

$GO_CONFIG->set_help_url($ml_help_url);


function buildTree($rootNode, $parent_folder_id=0)
{	
	global $account, $GO_THEME, $ml_inbox, $ml_confirm_empty_mailbox, $tv;
	$email = new email();
	
	$email->get_subscribed($account['id'], $parent_folder_id);
	while($email->next_record())
	{
		$pos = strrpos($email->f('name'), $email->f('delimiter'));
		if ($pos && $email->f('delimiter') != '')
		{
			$folder_name = substr($email->f('name'),$pos+1);
		}else
		{
			$folder_name = $email->f('name');
		}

		//check for unread mail
		if(!$tv->nodeIsOpen($email->f('id')))
		{
			$email2 = new email();
			$unseen = $email2->get_unseen_recursive($email->f('id'));
		}else
		{
			$unseen = $email->f('unseen');
		}		
		
		if ($unseen > 0)
		{
			$status = '<span class="count">&nbsp;('.$unseen.')</span>';
		}else
		{
			$status = '';
		}
		
		if($folder_name == 'INBOX') 
		{
			$folder_name = $ml_inbox;
		}else
		{
			$folder_name = utf7_imap_decode($folder_name);
		}
		
		$short_name = cut_string($folder_name, 30);
	
		$folderLink = '<a href="messages.php?account_id='.$email->f('account_id').'&mailbox='.urlencode($email->f('name')).'" title="'.htmlspecialchars(utf7_imap_decode($email->f('name'))).'" target="messages">';
		
		$closedFolderNode = $folderLink.
			'<img src="'.$GO_THEME->images['folderclosed'].'" border="0" align="absmiddle" />'.
		$short_name.'</a>'.$status;
		
		/*$openedFolderNode = $folderLink.
			'<img src="'.$GO_THEME->images['folderopen'].'" border="0" align="absmiddle" />'.
		$short_name.'</a>'.$status;*/
		
		if($email->f('name') == $account['trash'] || eregi('spam',$email->f('name')))
		{
			$emptyTrashLink = '&nbsp;<a href="javascript:parent.messages.confirm_empty_folder('.
				$email->f('account_id').', 
				\''.htmlspecialchars(addslashes($email->f('name'))).'\',\''.
				htmlspecialchars(addslashes(sprintf($ml_confirm_empty_mailbox, 
				utf7_imap_decode($email->f('name'))))).'\');"><img src="'.
				$GO_THEME->images['delete'].'" border="0" align="absmiddle" /></a>';
				
			//$openedFolderNode .= $emptyTrashLink;
			$closedFolderNode .= $emptyTrashLink;
		}
		
		$subNode = new treenode($tv, $email->f('id'), $closedFolderNode);
		if($rootNode->open)
		{
			$subNode = buildTree($subNode, $email->f('id'));			
		}
		$rootNode->addNode($subNode);	
	}

	return $rootNode;
	
}

$tv = new treeview('email_tv'); 
$open_accounts = (count($tv->nodeState) == 0);
if(count($tv->rootNodes) == 0)
{	
	$count = $email->get_accounts($GO_SECURITY->user_id);

	while($email->next_record())
	{
		$account = $email->Record;
		
		$short_name = cut_string($email->f('email'), 30);
		
		$accountNodeText = '<a href="messages.php?account_id='.$email->f('id').
			'&mailbox=INBOX'.
			'" title="'.$email->f('email').'" target="messages"><img src="'.$GO_THEME->images['inbox'].'" style="border:0px;margin-right:3px;" align="absmiddle" />'.$short_name.'</a>';

		if($email->f('type') == 'imap')
		{
			$accountNodeId = 'account_'.$email->f('id');
			if($open_accounts)
			{
				$open_accounts=false;
					$tv->setOpen($accountNodeId);
			}elseif(!$tv->nodeIsOpen($accountNodeId) || $email->f('mbroot') != '')
			{
				//check for unread mail			
				$email2 = new email();
				$unseen = $email2->get_account_unseen($email->f('id'));				
				if ($unseen > 0)
				{
					$accountNodeText .= '<span class="count">&nbsp;('.$unseen.')</span>';
				}					
			}
			$accountNode = new treenode($tv, $accountNodeId, $accountNodeText, $accountNodeText);
			$accountNode = buildTree($accountNode);
		}else
		{
			$accountNode = new treenode($tv, 'account_'.$email->f('id'), $accountNodeText, $accountNodeText);
		}		
		$tv->addRootNode($accountNode);	
	}	
}
$GO_HEADER['nomessages'] = true;
require($GO_THEME->theme_path.'header.inc');

echo '<form name="treeview_form" method="post" action="'.$_SERVER['PHP_SELF'].'">';
echo $tv->getTreeview();
echo '</form>';

require($GO_THEME->theme_path.'footer.inc');
