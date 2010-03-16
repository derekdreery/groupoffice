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
$GO_SECURITY->json_authenticate('servermanager');
require_once ($GO_MODULES->modules['servermanager']['class_path'].'servermanager.class.inc.php');
$servermanager = new servermanager();

require('/etc/groupoffice/servermanager.inc.php');
			
$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';
try{
	switch($task)
	{

		case 'modules':

			if(!empty($_POST['installation_id'])){
				$installation = $servermanager->get_installation($_POST['installation_id']);

				$config_file = '/etc/groupoffice/'.$installation['name'].'/config.php';
				require($config_file);
				if(!isset($config)) $config = array();

				$allowed_modules=empty($config['allowed_modules']) ? array() : explode(',', $config['allowed_modules']);
			}else
			{
				$allowed_modules=empty($default_config['allowed_modules']) ? array() : explode(',', $default_config['allowed_modules']);
			}

			$response['results']=array();

			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			$fs = new filesystem();

			$folders = $fs->get_folders($GO_CONFIG->module_path);

			foreach($folders as $modulefolder)
			{
				if($modulefolder['name']!='servermanager' && $modulefolder['name']!='postfixadmin')
				{
					$record = array(
						'id' => $modulefolder['name'],
						'name' => $modulefolder['name'],
						'installed' => false,
						'allowed' => !count($allowed_modules) || in_array($modulefolder['name'], $allowed_modules)
					);
					$response['results'][] = $record;
				}
			}

			$response['total']=count($response['results']);
		break;
		
		case 'check_installation':
			
			$installation = $servermanager->get_installation_by_name($_POST['installation_name']);
			
			if(!$installation)
			{
				throw new Exception('Not found');
			}
			
			/*$available = $servermanager->server_users_available();
			if($available<0)
			{
				throw new Exception('There is a license problem!');
			}*/
			
			
			$response['secret']=md5($_POST['installation_name'].date('Ymd').'nogwatgeheims');
			
			$response['success']=true;
			debug($response);
			
			break;
		
		case 'check_server_users':
			
			/*$available = $servermanager->server_users_available();
			$response['ok']=$available>-1;*/
			$response['ok']=1;
			break;
		
		case 'raw_report':
			$raw = true;
			
		case 'report':
			
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$query = isset($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';
			
			$response['results'] = array();
			$response['total']=$servermanager->get_reports($query, $sort, $dir, $start, $limit);
			
			while($report =$servermanager->next_record())
			{				
				
				//$report['total_usage']=$report['file_storage_usage']+$report['mailbox_usage']+$report['database_usage'];
				
				if(!isset($raw))
				{
					$report['total_usage']=Number::format_size($report['total_usage']*1024);
					$report['file_storage_usage']=Number::format_size($report['file_storage_usage']*1024);
					$report['mailbox_usage']=Number::format_size($report['mailbox_usage']*1024);
					$report['database_usage']=Number::format_size($report['database_usage']*1024);
					
					$report['install_time']=Date::get_timestamp($report['install_time']);
					$report['lastlogin']=Date::get_timestamp($report['lastlogin']);
					$report['ctime']=Date::get_timestamp($report['ctime']);
				}else
				{
					unset($report['total_usage']);		
				}
				$response['results'][] =$report;
			}
		
				
			
			break;
		
		case 'installation_with_items':
		case 'installation':
		
			
			$installation = $servermanager->get_installation(($_REQUEST['installation_id']));		
			
			$config_file = '/etc/groupoffice/'.$installation['name'].'/config.php';
			require($config_file);
			if(!isset($config)) $config = array();
			unset($config['db_pass']);
			$installation = array_merge($installation, $config);
			$installation['quota']=isset($installation['quota']) ? $installation['quota']/1024 : 0;
			
			$installation['mtime']=Date::get_timestamp($installation['mtime']);
			$installation['ctime']=Date::get_timestamp($installation['ctime']);	

			$response['data']=$installation;

			
			$config_file = '/etc/groupoffice/'.$installation['name'].'/config.php';
			
					
			$response['success']=true;
			
			
				
		case 'installations':			
			
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_installations = json_decode(($_POST['delete_keys']));
					foreach($delete_installations as $installation_id)
					{					
						$servermanager->delete_installation($installation_id);					
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
			
			$response['total'] = $servermanager->get_installations( $query, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($servermanager->next_record())
			{
				$installation = $servermanager->record;
				
				$config_file = '/etc/groupoffice/'.$installation['name'].'/config.php';
				if(file_exists($config_file))
				{
					require($config_file);
					if(isset($config))
					{
						$installation['enabled']=isset($config['enabled']) ? $config['enabled'] : true;
						$installation['title']=$config['title'];
						$installation['webmaster_email']=$config['webmaster_email'];
						$installation['max_users']=isset($config['max_users']) ? $config['max_users'] : 0;
					}
				}

				$installation['total_usage']=Number::format_size($installation['total_usage']*1024);
				$installation['file_storage_usage']=Number::format_size($installation['file_storage_usage']*1024);
				$installation['mailbox_usage']=Number::format_size($installation['mailbox_usage']*1024);
				$installation['database_usage']=Number::format_size($installation['database_usage']*1024);

				$installation['install_time']=Date::get_timestamp($installation['install_time']);
				$installation['lastlogin']=Date::get_timestamp($installation['lastlogin']);
				
				$installation['mtime']=Date::get_timestamp($installation['mtime']);				
				$installation['ctime']=Date::get_timestamp($installation['ctime']);
				
								
				$response['results'][] = $installation;
			}

			if(file_exists('/etc/groupoffice/license.inc.php')){
				require('/etc/groupoffice/license.inc.php');
				$response['max_users']=$max['users'];
				$response['max_billing']=$max['billing'];
				
				$usage = $servermanager->get_used_licenses();
				$response['total_users']=$usage['total_users'];
				$response['total_billing']=$usage['total_billing'];
			}else
			{
				$response['max_users']=0;
			}

			break;
			/* {TASKSWITCH} */
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);