<?php
/**
 * @copyright Intermesh 2004
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.24 $ $Date: 2005/11/25 11:57:43 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
//display number of unread e-mail messages
$email_module = isset($GO_MODULES->modules['email']) ? $GO_MODULES->modules['email'] : false;
if ($email_module && $email_module['read_permission'])
{			
	$_SESSION['notified_new_mail'] = isset($_SESSION['notified_new_mail']) ? $_SESSION['notified_new_mail'] : 0;
	$_SESSION['new_mail']=0;
	require_once($email_module['class_path'].'email.class.inc');
	require_once($GO_CONFIG->class_path.'mail/imap.class.inc');
	$imap = new imap();
	$email1 = new email();
	$email2 = new email();
  $checked_accounts = 0;
	if ($email1->get_accounts($GO_SECURITY->user_id))
	{
		while($email1->next_record())
		{
			$account = $email1->Record;
			if($account['password'] != '')
			{
				if ($imap->open($account['host'], $account['type'],$account['port'],$account['username'],$account['password'], 'INBOX', 0, $account['use_ssl'], $account['novalidate_cert']))
				{
					$checked_accounts++;
					if ($account['type'] == 'imap')
					{
						
						$status = $imap->status('INBOX');
						
						if ($status && $status->unseen > 0)
						{
							$_SESSION['new_mail'] += $status->unseen;
						}	
						
						$email2->get_subscribed($email1->f('id'));
						while($email2->next_record())
						{
							if($email2->f('name') != 'INBOX')
							{
								$status = $imap->status($email2->f('name'));
							
								if ($status && $status->unseen > 0)
								{
									$_SESSION['new_mail'] += $status->unseen;
								}
							}	
						}
					}else
					{
						$status = $imap->status('INBOX');
						
						if ($status && $status->unseen > 0)
						{
							$_SESSION['new_mail'] += $status->unseen;
						}	
					}
				}else
				{
					$email2->disable_auto_check($account['id']);
					echo '<script language="javascript" type="text/javascript">alert("'.$account['host'].' '.$ml_host_unreachable.'");</script>';
				}
			}
		}
		if ($checked_accounts > 0)
		{
			$em_table = new table();
			$em_table->set_attribute('style', 'border:0px;margin-top:10px;');
			
			$img = new image('email');
			$img->set_attribute('style', 'border:0px;margin-right:10px;');
			
			$em_cell = new table_cell();
			$em_cell->set_attribute('valign','top');
			$em_cell->add_html_element($img);
			
			$em_row = new table_row();
			$em_row->add_cell($em_cell);
			
			$link = new hyperlink($GO_MODULES->modules['email']['url'], $lang_modules['email']);
			
			$h2 = new html_element('h2',$link->get_html());
			$em_row->add_cell(new table_cell($h2->get_html()));
			$em_table->add_row($em_row);
			
			$em_row = new table_row();
			$em_row->add_cell(new table_cell('&nbsp;'));		
			
			$link = new hyperlink($GO_MODULES->modules['email']['url'],'');

			switch($_SESSION['new_mail'])
			{
				case 0:
					$link->innerHTML = $sum_no_new_mail;
				break;
				case 1:
					$link->innerHTML = $sum_you_have.' <b>'.$_SESSION['new_mail'].'</b> '.$sum_new_message;
				break;
				
				default:
					$link->innerHTML = $sum_you_have.' <b>'.$_SESSION['new_mail'].'</b> '.$sum_new_messages;
				break;
			}
			$_SESSION['notified_new_mail'] = $_SESSION['new_mail'];
			$em_row->add_cell(new table_cell($link->get_html()));	
			$em_table->add_row($em_row);

			$cell->add_html_element($em_table);
		}
	}
}
