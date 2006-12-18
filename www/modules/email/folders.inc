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

if($account['type'] == 'pop3')
{
	$tabstrip->innerHTML .= $ml_folder_not_available;
}else
{
	load_control('treeview');
	$form->add_html_element(new input('hidden','delete_folder'));
	$form->add_html_element(new input('hidden','edit_name'));


	if ($account && $mail->open($account['host'], $account['type'],
				$account['port'],$account['username'],
				$account['password'],'INBOX', 0, $account['use_ssl'], $account['novalidate_cert']))
	{
		if ($task == 'create_folder')
		{
			$folder_name = smart_stripslashes(trim($_POST['folder_name']));

			if ($folder_name == '')
			{
				$feedback = $error_missing_field;
			}else
			{
				$parent_folder_name = isset($_POST['parent_folder_name']) ?
						smart_stripslashes($_POST['parent_folder_name']) : '';
						
				$delimiter = $mail->get_mailbox_delimiter();

				if ($parent_folder_name != '' &&
						substr($parent_folder_name, -1) != $delimiter)
				{
					$parent_folder_name .= $delimiter;
				}
				
				$mail->create_folder($parent_folder_name.utf7_imap_encode($folder_name), $delimiter);
			}
		}

		if ($task == 'delete')
		{
			$delete_folder = smart_stripslashes($_REQUEST['delete_folder']);
			if ($mail->delete_folder($delete_folder, $account['mbroot']))
			{
				/*
					(cyrus imap) if folder still exists then don't delete it from the
					database,
					because it contains at least one child mailbox
				*/
				if (!is_array($mail->get_mailboxes($delete_folder)))
				{
					$email->delete_folder($account['id'], addslashes($delete_folder));
				}
			}
		}

		$edit_name = isset($_REQUEST['edit_name']) ?
										smart_stripslashes($_REQUEST['edit_name']) : '';

		$mail->close();

		//synchronise Group-Office with the IMAP server
		$email->synchronize_folders($account);
		

		//get all the Group-Office folders as an array
		$email->get_folders($account['id']);
		$go_mailboxes = array();
		while ($email->next_record())
		{
			$go_mailboxes[] = $email->Record;
			
		}
		$mcount = count($go_mailboxes);

		if(isset($feedback))
		{
			$p = new html_element('p',$feedback);
			$p->set_attribute('class','Error');
			$tabstrip->add_html_element($p);
		}
		
		$table = new table();
		
		$row = new table_row();
		$row->add_cell(new table_cell($ml_sent_items.':'));
		$select=new select('sent', $account['sent']);
		$select->add_value('', $ml_disable);
		for ($i=0;$i<$mcount;$i++)
		{
			if ($go_mailboxes[$i]['attributes'] != LATT_NOSELECT)
			{
				$select->add_value($go_mailboxes[$i]['name'], 
						utf7_imap_decode(str_replace('INBOX'.$go_mailboxes[$i]['delimiter'], '', $go_mailboxes[$i]['name'])));
			}
		}
		$row->add_cell(new table_cell($select->get_html()));
		$table->add_row($row);

		$row = new table_row();
		$row->add_cell(new table_cell($ml_drafts.':'));
		$select=new select('drafts', $account['drafts']);
		$select->add_value('', $ml_disable);
		for ($i=0;$i<$mcount;$i++)
		{
			if ($go_mailboxes[$i]['attributes'] != LATT_NOSELECT)
			{
				$select->add_value($go_mailboxes[$i]['name'], 
						utf7_imap_decode(str_replace('INBOX'.$go_mailboxes[$i]['delimiter'], '', $go_mailboxes[$i]['name'])));
			}
		}
		$row->add_cell(new table_cell($select->get_html()));
		$table->add_row($row);
		
		$row = new table_row();
		$row->add_cell(new table_cell($ml_trash.':'));
		$select=new select('trash', $account['trash']);
		$select->add_value('', $ml_disable);
		for ($i=0;$i<$mcount;$i++)
		{
			if ($go_mailboxes[$i]['attributes'] != LATT_NOSELECT)
			{
				$select->add_value($go_mailboxes[$i]['name'], 
						utf7_imap_decode(str_replace('INBOX'.$go_mailboxes[$i]['delimiter'], '', $go_mailboxes[$i]['name'])));
			}
		}
		
		$row->add_cell(new table_cell($select->get_html()));
		$table->add_row($row);
		
		$row = new table_row();
		$row->add_cell(new table_cell($ml_spam.':'));
		$select=new select('spam', $account['spam']);
		$select->add_value('', $ml_disable);
		for ($i=0;$i<$mcount;$i++)
		{
			if ($go_mailboxes[$i]['attributes'] != LATT_NOSELECT)
			{
				$select->add_value($go_mailboxes[$i]['name'], 
						utf7_imap_decode(str_replace('INBOX'.$go_mailboxes[$i]['delimiter'], '', $go_mailboxes[$i]['name'])));
			}
		}

		$row->add_cell(new table_cell($select->get_html()));
		$table->add_row($row);
		
		$row = new table_row();
		$row->add_cell(new table_cell($ml_spamtag.':'));
		$input = new input('text', 'spamtag', $account['spamtag']);		
		$input->set_attribute('style','width:100px;');
		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);
		
		$tabstrip->add_html_element($table);
		
		$table = new table();
		$row = new table_row();
		$row->add_cell(new table_cell($em_new_folder));
		
		$folder_name = isset($_POST['folder_name']) ? htmlspecialchars(smart_stripslashes($_POST['folder_name'])) : ''; 
		
		$input = new input('text','folder_name',$folder_name);
		$input->set_attribute('maxlength','100');
		$input->set_attribute('style','width:200px');
		$row->add_cell(new table_cell($input->get_html()));
		
		$row->add_cell(new table_cell($ml_inside));
		
		$parent_folder_name = isset($parent_folder_name) ? $parent_folder_name : '';
		$select=new select('parent_folder_name',$parent_folder_name);
		$select->add_value($account['mbroot'],$ml_root_mailbox);
		for ($i=0;$i<$mcount;$i++)
		{
			if ($go_mailboxes[$i]['attributes'] != LATT_NOINFERIORS)
			{
				$select->add_value($go_mailboxes[$i]['name'], 
						utf7_imap_decode(str_replace('INBOX'.$go_mailboxes[$i]['delimiter'], '', $go_mailboxes[$i]['name'])));
			}
		}		
		$row->add_cell(new table_cell($select->get_html()));	
		$button = new button($cmdOk, "javascript:_save('create_folder', 'false')");
		$row->add_cell(new table_cell($button->get_html()));
		$table->add_row($row);		
		
		$tabstrip->add_html_element($table);
	

		function buildTree($rootNode, $parent_folder_id=0)
		{	
			global $account, $mail, $GO_THEME, $ml_inbox, $strDeletePrefix, $strDeleteSuffix, $strEdit, $strDeleteItem, $edit_name, $tv;
			$email = new email();
			
			$email->get_mailboxes($account['id'], $parent_folder_id);
			
			while($email->next_record())
			{				
				if ($pos = strrpos($email->f('name'), $email->f('delimiter')) && $email->f('delimiter') != '')
				{
					$folder_name = substr($email->f('name'),$pos+1);
				}else
				{
					$folder_name = $email->f('name');
				}
				$decoded_folder_name =utf7_imap_decode($folder_name);
				
				if ($email->f('subscribed') == 1)
					$checked = 'checked';
				else
					$checked = '';		
										
				if($folder_name == 'INBOX') 
				{						
					$checkbox = '<input type="checkbox" name="dummy" value="" checked disabled />';
					$decoded_folder_name = $ml_inbox;					
				}else
				{
					$checkbox = '<input type="checkbox" name="use[]" value="'.$email->f('name').'" '.$checked.' />';
				}
				
				
				$closedFolderNode = $checkbox.'<img src="'.$GO_THEME->images['folderclosed'].'" border="0" height="22" width="24" align="middle" />';				
				$openedFolderNode = $checkbox.'<img src="'.$GO_THEME->images['folderopen'].'" border="0" height="22" width="24" align="middle" />';
				
				$node = '';
				if ($edit_name == $email->f('name'))
				{					
					if ($pos = strrpos($email->f('name'), $email->f('delimiter')) && $email->f('delimiter') != '')
					{
						$location = substr($email->f('name'),0,$pos+1);
					}else
					{
						$location = '';
					}
					$node .= '<input type="hidden" name="location" value="'.$location.'" />'.
					 '<input class="textbox" type="text" name="new_name" value="'.$decoded_folder_name.'" /><input type="hidden" name="old_name" value="'.$email->f('name').'" />';
				}else
				{
					$node .=  $decoded_folder_name;
				}			
							
				
				if($email->f('name') != 'INBOX')
				{
					$node .=  '&nbsp;<a href=\'javascript:edit_name("'.htmlspecialchars($email->f('name'), ENT_QUOTES).'");\'" title="'.$strEdit.' '.$decoded_folder_name.'"><img src="'.$GO_THEME->images['edit'].'" border="0" /></a>'.
						"&nbsp;<a href='javascript:confirm_delete(\"".htmlspecialchars($email->f('name'), ENT_QUOTES)."\",\"".htmlspecialchars(addslashes($strDeletePrefix." ".$decoded_folder_name." ".$strDeleteSuffix))."\")' title=\"".htmlspecialchars(addslashes($strDeleteItem." '".$decoded_folder_name."'"))."\"><img src=\"".$GO_THEME->images['delete']."\" border=\"0\"></a>";
				}
				
				$subNode = new treenode($tv, $email->f('id'), $closedFolderNode.$node, $openedFolderNode.$node, true, true);
				$subNode = buildTree($subNode, $email->f('id'));			
				$rootNode->addNode($subNode);
			}
			return $rootNode;		
		}		
			$tv = new treeview('email_fiolders');
			
			$rootNode = new treenode($tv, 'account_'.$account['id'],null,null, true, true);
			$rootNode = buildTree($rootNode);		
			$tv->addRootNode($rootNode);	
			
			$tabstrip->innerHTML .=  $tv->getTreeview();
			$mail->close();
			
			$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save_folders', 'true');"));
			$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save_folders', 'false');"));
			
			if ($edit_name != '')
			{
				$tabstrip->add_html_element(new button($cmdCancel,'javascript:document.location=\''.$link_back.'\''));
			}else
			{
				$tabstrip->add_html_element(new button($cmdClose,'javascript:document.location=\''.$return_to.'\''));
			}			
	}else
	{		
		$p = new html_element('p', $ml_connect_failed.' \''.$account['host'].'\' '.$ml_at_port.': '.$account['port'].'<br />'.imap_last_error().'</p><br />'.$ml_solve_error);
		$tabstrip->add_html_element($p);
		unset($_SESSION['email_id']);
	}
}
?>
<script type="text/javascript">
function confirm_delete(name, text)
{
	if(confirm(text))
	{	
		document.forms[0].task.value='delete';;	
		document.forms[0].delete_folder.value=name;
		document.forms[0].submit();
	}
}

function edit_name(name)
{
	document.forms[0].task.value='edit';;	
	document.forms[0].edit_name.value=name;
	document.forms[0].submit();	
}

</script>