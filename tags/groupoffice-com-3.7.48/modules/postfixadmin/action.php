<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
require_once("../../Group-Office.php");

$GO_SECURITY->json_authenticate('postfixadmin');
require_once ($GO_MODULES->modules['postfixadmin']['class_path']."postfixadmin.class.inc.php");
//require_once ($GO_LANGUAGE->get_language_file('postfixadmin'));
$postfixadmin = new postfixadmin();

try{
	switch($_REQUEST['task'])
	{		
		case 'serverclient_delete_installation':
				$postfixadmin->get_installation_mailboxes(($_POST['go_installation_id']));
				
				while($postfixadmin->next_record())
				{
					$mailboxes[]=$postfixadmin->f('username');
				}			

				if(!isset($mailboxes))
				{
					$response['success']=true;
					break;
				}
				
				go_debug(var_export($mailboxes, true));
				
		case 'delete_mailboxes':
			
			if(!isset($mailboxes))
				$mailboxes = explode(',', ($_POST['mailboxes']));
			
			foreach($mailboxes as $mailbox)
			{
				$expl = explode('@', $mailbox);
				$domain = $expl[1];
				
				$domain = $postfixadmin->get_domain_by_domain($domain);
			
				if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $domain['acl_id'])<GO_SECURITY::WRITE_PERMISSION)
				{
					throw new AccessDeniedException();
				}
				
				$mailbox = $postfixadmin->get_mailbox_by_username($mailbox);			
				$postfixadmin->delete_mailbox($mailbox['id']);				
			}
			
			$response['success']=true;
			
			break;
		
		case 'delete_mailbox':
			
			$username = ($_POST['username']);
			$domain = ($_POST['domain']);
			
			$domain = $postfixadmin->get_domain_by_domain($domain);
			
			if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $domain['acl_id'])<GO_SECURITY::WRITE_PERMISSION)
			{
				throw new AccessDeniedException();
			}
			
			$mailbox = $postfixadmin->get_mailbox_by_username($username);			
			$postfixadmin->delete_mailbox($mailbox['id']);
			
			$response['success']=true;
			break;

		case 'save_alias':

			$alias_id=$alias['id']=isset($_POST['alias_id']) ? ($_POST['alias_id']) : 0;

			$alias['domain_id']=$_POST['domain_id'];

			$domain = $postfixadmin->get_domain($alias['domain_id']);

			$alias['address']=$_POST['address'];
			$alias['goto']=$_POST['goto'];
			$alias['active']=isset($_POST['active']) ? '1' : '0';
			if($alias['id']>0)
			{
				$postfixadmin->update_alias($alias);
				$response['success']=true;
			}else
			{
				if(!empty($domain['aliases']))
				{
					$aliasescount=$postfixadmin->get_aliases($alias['domain_id']);				
					if($aliasescount>=$domain['aliases'])
					{
						throw new Exception('Your maximum number of aliases has been reached');
					}
				}
				
				$alias_id= $postfixadmin->add_alias($alias);

				$response['alias_id']=$alias_id;
				$response['success']=true;
			}

			break;

		case 'save_domain':

			$domain = array();

			$domain_id=$domain['id']=isset($_POST['domain_id']) ? ($_POST['domain_id']) : 0;

			if(isset($_POST['user_id']))
			$domain['user_id']=$_POST['user_id'];
			
			$domain['description']=$_POST['description'];
			if(isset($_POST['aliases']))
				$domain['aliases']=$_POST['aliases'];
				
			if(isset($_POST['mailboxes']))
				$domain['mailboxes']=$_POST['mailboxes'];
				
			if(isset($_POST['maxquota']))
				$domain['maxquota']=Number::to_phpnumber($_POST['maxquota'])*1024;;
				
			if(isset($_POST['quota']))
				$domain['quota']=Number::to_phpnumber($_POST['quota'])*1024;;
				
			$domain['transport']='virtual';
			//$domain['backupmx']=$_POST['backupmx'];
			$domain['backupmx']=isset($_POST['backupmx']) ? '1' : '0';
			$domain['active']=isset($_POST['active']) ? '1' : '0';

			if($domain['id']>0)
			{
				$postfixadmin->update_domain($domain);
				$response['success']=true;
			}else
			{
				$domain['domain']=$_POST['domain'];
				
				$domain['user_id']=$GO_SECURITY->user_id;

				$response['acl_id']=$domain['acl_id']=$GO_SECURITY->get_new_acl('domain');

				$domain_id= $postfixadmin->add_domain($domain);

				$response['domain_id']=$domain_id;
				$response['success']=true;
			}



			break;

		case 'save_fetchmail_config':

			$fetchmail_config_id=$fetchmail_config['id']=isset($_POST['fetchmail_config_id']) ? ($_POST['fetchmail_config_id']) : 0;



			$fetchmail_config['mailbox']=$_POST['mailbox'];
			$fetchmail_config['src_server']=$_POST['src_server'];
			$fetchmail_config['src_auth']=$_POST['src_auth'];
			$fetchmail_config['src_user']=$_POST['src_user'];
			$fetchmail_config['src_password']=$_POST['src_password'];
			$fetchmail_config['src_folder']=$_POST['src_folder'];
			$fetchmail_config['poll_time']=$_POST['poll_time'];
			$fetchmail_config['fetchall']=$_POST['fetchall'];
			$fetchmail_config['keep']=$_POST['keep'];
			$fetchmail_config['protocol']=$_POST['protocol'];
			$fetchmail_config['extra_options']=$_POST['extra_options'];
			$fetchmail_config['returned_text']=$_POST['returned_text'];
			$fetchmail_config['mda']=$_POST['mda'];
			$fetchmail_config['date']=$_POST['date'];
			if($fetchmail_config['id']>0)
			{
				$postfixadmin->update_fetchmail_config($fetchmail_config);
				$response['success']=true;
			}else
			{
				$fetchmail_config['user_id']=$GO_SECURITY->user_id;
				$fetchmail_config_id= $postfixadmin->add_fetchmail_config($fetchmail_config);

				$response['fetchmail_config_id']=$fetchmail_config_id;
				$response['success']=true;
			}
			break;
			
		case 'serverclient_set_password':
			
			$str_domain = ($_POST['domain']);			
			$domain= $postfixadmin->get_domain_by_domain($str_domain);
			
			if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $domain['acl_id'])<GO_SECURITY::WRITE_PERMISSION)
				throw new AccessDeniedException();
				
			$mailbox = $postfixadmin->get_mailbox_by_username(($_POST['username']));
			
			if($mailbox)
			{
				$up_mailbox['id']=$mailbox['id'];
				$up_mailbox['password']=md5(($_POST['password']));

				$postfixadmin->update_mailbox($up_mailbox);
			}
			
			$response['success']=true;
			
			
			
			break;
			
			
			
		case 'serverclient_set_vacation':
			
				$username = ($_POST['username']);
				$password = ($_POST['password']);
				
				$old_mailbox = $postfixadmin->get_mailbox_by_username($username);

				//can't check password with ldap auth. Admin user should be safe anyway
				//if(md5($password) != $old_mailbox['password'])
					//throw new AccessDeniedException();
					
				$mailbox['id']=$old_mailbox['id'];				
				$mailbox['vacation_active']=$_POST['vacation_active'];
				$mailbox['vacation_subject']=$_POST['vacation_subject'];
				$mailbox['vacation_body']=$_POST['vacation_body'];
				
				
				$vacation_alias = str_replace('@','#', $old_mailbox['username']).'@'.$GO_CONFIG->postfixadmin_autoreply_domain;			
				
				$postfixadmin->update_mailbox($mailbox);

				$alias = $postfixadmin->get_alias_by_address($old_mailbox['username']);
				$up_alias['id']=$alias['id'];
				$up_alias['goto']=$username;
				if($mailbox['vacation_active']=='1'){
					$up_alias['goto'].=','.$vacation_alias;
				}
				if(!empty($_POST['forward_to'])){
					$up_alias['goto'].=','.$_POST['forward_to'];
				}

				if($old_mailbox['vacation_active']=='1' && $mailbox['vacation_active']=='0')
				{	
					/*$alias = $postfixadmin->get_alias_by_address($old_mailbox['username']);
									
					$up_alias['id']=$alias['id'];
					$up_alias['goto']= preg_replace ( "/$vacation_alias,/", '', $alias['goto']);
          $up_alias['goto']= preg_replace ( "/,$vacation_alias/", '', $up_alias['goto']);
          $up_alias['goto']= preg_replace ( "/$vacation_alias/", '', $up_alias['goto']);
          
					$postfixadmin->update_alias($up_alias);*/
					
					$postfixadmin->remove_notifications($old_mailbox['username']);
					
				}/*elseif($old_mailbox['vacation_active']=='0' && $mailbox['vacation_active']=='1')
				{
					$alias = $postfixadmin->get_alias_by_address($old_mailbox['username']);
					
					$up_alias['id']=$alias['id'];
					$up_alias['goto']= $alias['goto'];
					
					if(!empty($up_alias['goto']))
						$up_alias['goto'] .= ',';
						
          $up_alias['goto'] .=$vacation_alias;
          
					$postfixadmin->update_alias($up_alias);
				}*/

				$postfixadmin->update_alias($up_alias);
				
				$response['success']=true;
				
			
			break;
			
		case 'serverclient_create_mailbox':
			
			$str_domain = ($_POST['domain']);
			
			$domain= $postfixadmin->get_domain_by_domain($str_domain);
			
			if(!$domain)
			{
				$domain['domain']=$str_domain;
				$domain['user_id']=$GO_SECURITY->user_id;
				$domain['transport']='virtual';
				$domain['active']='1';
				$domain['acl_id']=$GO_SECURITY->get_new_acl('domain');
				$domain['quota']=524288;
				$domain['id']=$postfixadmin->add_domain($domain);
			}
			$_POST['quota']=$domain['quota']/1024;
			$_POST['domain_id']=$domain['id'];
			
		case 'save_mailbox':

			if($_POST['password1']!=$_POST['password2'])
			throw new Exception($lang['common']['passwordMatchError']);

			$mailbox_id=$mailbox['id']=isset($_POST['mailbox_id']) ? ($_POST['mailbox_id']) : 0;

			$mailbox['domain_id']=$_POST['domain_id'];

			$domain = $postfixadmin->get_domain($mailbox['domain_id']);		
			
			if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $domain['acl_id'])<GO_SECURITY::WRITE_PERMISSION)
				throw new AccessDeniedException();			

			if(!empty($_POST['password1']))
				$mailbox['password']=md5(trim($_POST['password1']));
			
			$mailbox['name']=$_POST['name'];
			$mailbox['quota']=Number::to_phpnumber($_POST['quota'])*1024;
			
			if(!empty($domain['maxquota']) && empty($mailbox['quota']))
			{
				throw new Exception('You are not allowed to disable quota');
			}

			$mailbox['active']=isset($_POST['active']) ? '1' : '0';
			
			$mailbox['go_installation_id']=isset($_POST['go_installation_id']) ? (trim($_POST['go_installation_id'])) : '';
			
			$mailbox['vacation_active']=isset($_POST['vacation_active']) ? '1' : '0';
			$mailbox['vacation_subject']=$_POST['vacation_subject'];
			$mailbox['vacation_body']=$_POST['vacation_body'];
			
			
			
			
			if($mailbox['id']>0)
			{
				$old_mailbox = $postfixadmin->get_mailbox($mailbox['id']);
				$vacation_alias = str_replace('@','#', $old_mailbox['username']).'@'.$GO_CONFIG->postfixadmin_autoreply_domain;		

				if(!empty($domain['maxquota']))// && $old_mailbox['quota']!=$mailbox['quota'])
				{
					$totalquota=$postfixadmin->sum_quota($domain['id'])-$old_mailbox['quota'];				
					if($totalquota+$mailbox['quota']>$domain['maxquota'])
					{
						$left = $domain['maxquota']-$totalquota;
						throw new Exception('The maximum quota has been reached. You have '.Number::format($left/1024).'MB left');
					}
				}
				
				$postfixadmin->update_mailbox($mailbox);
				
				if($old_mailbox['vacation_active']=='1' && $mailbox['vacation_active']=='0')
				{	
					$alias = $postfixadmin->get_alias_by_address($old_mailbox['username']);
									
					$up_alias['id']=$alias['id'];
					$up_alias['goto']= preg_replace ( "/$vacation_alias,/", '', $alias['goto']);
          $up_alias['goto']= preg_replace ( "/,$vacation_alias/", '', $up_alias['goto']);
          $up_alias['goto']= preg_replace ( "/$vacation_alias/", '', $up_alias['goto']);
          
					$postfixadmin->update_alias($up_alias);
					
					$postfixadmin->remove_notifications($old_mailbox['username']);
					
				}elseif($old_mailbox['vacation_active']=='0' && $mailbox['vacation_active']=='1')
				{
					$alias = $postfixadmin->get_alias_by_address($old_mailbox['username']);
					
					$up_alias['id']=$alias['id'];
					$up_alias['goto']= $alias['goto'];
					
					if(!empty($up_alias['goto']))
						$up_alias['goto'] .= ',';
						
          $up_alias['goto'] .=$vacation_alias;
          
					$postfixadmin->update_alias($up_alias);
				}
				
				$response['success']=true;
			}else
			{
				
				
				$mailbox['maildir']=$domain['domain'].'/'.trim($_POST['username']).'/';
				$mailbox['username']=(str_replace('@'.$domain['domain'], '', trim($_POST['username']))).'@'.$domain['domain'];
				
				if($postfixadmin->get_mailbox_by_username($mailbox['username']))
					throw new Exception('The mailbox already exist');
					
				if(!empty($domain['mailboxes']))
				{
					$mailboxcount=$postfixadmin->get_mailboxes($domain['id']);				
					if($mailboxcount>=$domain['mailboxes'])
					{
						throw new Exception('The maximum number of mailboxes has been reached');
					}
				}
				
				if(!empty($domain['maxquota']))
				{
					$totalquota=$postfixadmin->sum_quota($domain['id']);				
					if($totalquota+$mailbox['quota']>$domain['maxquota'])
					{
						$left = $domain['maxquota']-$totalquota;
						throw new Exception('The maximum quota has been reached. You have '.Number::format($left/1024).'MB left');
					}
				}				
					
				$mailbox_id= $postfixadmin->add_mailbox($mailbox);
				$response['mailbox_id']=$mailbox_id;
				$response['success']=true;

				//create alias				
				if($mailbox['vacation_active']=='1')
				{
					$vacation_alias = str_replace('@','#', $mailbox['username']).'@'.$GO_CONFIG->postfixadmin_autoreply_domain;
					
					$alias['goto']=$mailbox['username'].','.$vacation_alias;
				}else
				{
					$alias['goto']=$mailbox['username'];	
				}

				$alias['domain_id']=$mailbox['domain_id'];
				$alias['address']=$mailbox['username'];
				
				$alias['active']=$mailbox['active'];
				
				

				$postfixadmin->add_alias($alias);

				if(isset($_POST['aliases'])){
					$aliases = array_map('trim', explode(',',$_POST['aliases']));
					foreach($aliases as $alias_address){
						if($mailbox['username']!=$alias_address && !empty($alias_address)){
							$alias['address']=$alias_address;
							$postfixadmin->add_alias($alias);
						}
					}
				}
				go_debug(var_export($alias, true));
			}
			
			



			break;

		
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
