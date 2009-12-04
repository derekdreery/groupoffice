<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: json.tpl 2030 2008-06-04 10:12:13Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
require('../../Group-Office.php');
$GO_SECURITY->json_authenticate('postfixadmin');
require_once ($GO_MODULES->modules['postfixadmin']['class_path'].'postfixadmin.class.inc.php');
$postfixadmin = new postfixadmin();

$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';
try{
	switch($task)
	{
		case 'serverclient_get_usage':			
			$domains = explode(',', ($_POST['domains']));
			$response['domains']=array();
			foreach($domains as $domain)
			{
				$path = isset($GO_CONFIG->postfixadmin_vmail_root) ? $GO_CONFIG->postfixadmin_vmail_root.$domain : '/vmail/'.$domain;
				
				$domain = $postfixadmin->get_domain_by_domain($domain);
				
				$domain_info = $postfixadmin->get_domain_info($domain['id']);
				
				$response['domains'][]=array('domain'=>$domain, 'usage'=>$domain_info['usage']);				
			}
			
			$response['success']=true;			
			break;
		
		
		case 'alias':		
			
			$alias = $postfixadmin->get_alias(($_REQUEST['alias_id']));			
			
			$alias['mtime']=Date::get_timestamp($alias['mtime']);			
			
			$alias['ctime']=Date::get_timestamp($alias['ctime']);
			
			$response['data']=$alias;						
			$response['success']=true;
			
			break;
			
		case 'aliases':
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_aliases = json_decode(($_POST['delete_keys']));
					foreach($delete_aliases as $alias_id)
					{
						$postfixadmin->delete_alias($alias_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'address';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$query = isset($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';
			$domain_id=$_POST['domain_id'];
			$response['total'] = $postfixadmin->get_aliases($domain_id, $query, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($postfixadmin->next_record())
			{
				$alias = $postfixadmin->record;
				
				$alias['mtime']=Date::get_timestamp($alias['mtime']);
				$alias['ctime']=Date::get_timestamp($alias['ctime']);
				$response['results'][] = $alias;
			}
			break;
			
		
		case 'domain':
		
			
			$domain = $postfixadmin->get_domain(($_REQUEST['domain_id']));
			
			$user = $GO_USERS->get_user($domain['user_id']);
			
			$domain['user_name']=String::format_name($user);
			$domain['mtime']=Date::get_timestamp($domain['mtime']);
			$domain['ctime']=Date::get_timestamp($domain['ctime']);	
			$domain['quota']=Number::format($domain['quota']/1024);
			$domain['maxquota']=Number::format($domain['maxquota']/1024);	
			
			$response['data']=$domain;						
			$response['success']=true;
			break;
			
			
				
		case 'domains':
			$auth_type = isset($_POST['auth_type']) ? ($_POST['auth_type']) : 'write';
			
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_domains = json_decode(($_POST['delete_keys']));
					foreach($delete_domains as $domain_id)
					{
						$postfixadmin->delete_domain($domain_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'domain';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$query = isset($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';
			
			$response['total'] = $postfixadmin->get_authorized_domains($auth_type, $GO_SECURITY->user_id,  $query, $sort, $dir, $start, $limit);
			$response['results']=array();
			
			$pa2 = new postfixadmin();
			
			while($postfixadmin->next_record())
			{
				$domain = $postfixadmin->record;
				
				
				$user = $GO_USERS->get_user($domain['user_id']);
				$domain['user_name']=String::format_name($user);				
				
				$domain['mtime']=Date::get_timestamp($domain['mtime']);			
				$domain['ctime']=Date::get_timestamp($domain['ctime']);				
				
				$domain_info = $pa2->get_domain_info($postfixadmin->f('id'));
				
				$domain['quota']=Number::format_size($domain_info['usage']*1024).' / '.Number::format_size($domain['quota']*1024);
				$domain['aliases']=$domain_info['alias_count'].' / '.$domain['aliases'];
				$domain['mailboxes']=$domain_info['mailbox_count'].' / '.$domain['mailboxes'];
				
								
				$response['results'][] = $domain;
			}
			break;
			
		
		case 'fetchmail_config':
			$fetchmail_config = $postfixadmin->get_fetchmail_config(($_REQUEST['fetchmail_config_id']));			
			$response['data']=$fetchmail_config;
			$response['success']=true;
			
			break;
			
		case 'fetchmail_configs':
			
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_fetchmail_configs = json_decode(($_POST['delete_keys']));
					foreach($delete_fetchmail_configs as $fetchmail_config_id)
					{
						$postfixadmin->delete_fetchmail_config($fetchmail_config_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$query = isset($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';
			
			$response['total'] = $postfixadmin->get_fetchmail_configs( $query, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($postfixadmin->next_record())
			{
				$fetchmail_config = $postfixadmin->record;
				
				$response['results'][] = $fetchmail_config;
			}
			break;
			
		case 'serverclient_get_mailbox':
			
			$username = ($_POST['username']);
			$password = ($_POST['password']);
			
			$mailbox = $postfixadmin->get_mailbox_by_username($username);
			
			if(md5($password) != $mailbox['password'])
				throw new AccessDeniedException();
				
			$response['data']=$mailbox;
			$response['success']=true;
			
			break;
		
		case 'mailbox':			
			$mailbox = $postfixadmin->get_mailbox(($_REQUEST['mailbox_id']));		
			
			$mailbox['mtime']=Date::get_timestamp($mailbox['mtime']);		
			$mailbox['ctime']=Date::get_timestamp($mailbox['ctime']);
			//$mailbox['quota']=Number::format($mailbox['quota']/1024);
			$mailbox['quota']=Number::format_size($mailbox['usage']*1024).' / '.Number::format_size($mailbox['quota']/1024);
					
			$response['data']=$mailbox;						
			$response['success']=true;			
			break;
				
		case 'mailboxes':
			
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_mailboxes = json_decode(($_POST['delete_keys']));
					foreach($delete_mailboxes as $mailbox_id)
					{
						$postfixadmin->delete_mailbox($mailbox_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'username';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$query = isset($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';
			
			$domain_id=$_POST['domain_id'];
			
			$response['total'] = $postfixadmin->get_mailboxes($domain_id, $query, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($postfixadmin->next_record())
			{
				$mailbox = $postfixadmin->record;
				$mailbox['mtime']=Date::get_timestamp($mailbox['mtime']);
				$mailbox['ctime']=Date::get_timestamp($mailbox['ctime']);							
				$mailbox['quota']=Number::format($mailbox['quota']/1024);	
				$response['results'][] = $mailbox;
			}
			break;

	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
