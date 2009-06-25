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
		
		case 'check_installation':
			
			$installation = $servermanager->get_installation_by_name($_POST['installation_name']);
			
			if(!$installation)
			{
				throw new Exception('Not found');
			}
			
			$available = $servermanager->server_users_available();			
			if($available<0)
			{
				throw new Exception('There is a license problem!');
			}
			
			
			$response['secret']=md5($_POST['installation_name'].date('Ymd').'nogwatgeheims');
			
			$response['success']=true;
			debug($response);
			
			break;
		
		case 'check_server_users':
			
			$available = $servermanager->server_users_available();			
			$response['ok']=$available>-1;
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
			
			while($servermanager->next_record())
			{
				$report = $servermanager->record;
				
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
						$installation = $servermanager->get_installation($installation_id);
						
						//exec('sudo '.$GO_MODULES->modules['servermanager']['path'].'remove.sh '.$installation['name']);
						exec('sudo '.$GO_MODULES->modules['servermanager']['path'].'sudo.php '.$GO_CONFIG->get_config_file().' remove '.$installation['name']);
						
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
				
					$installation['title']=$config['title'];
					$installation['webmaster_email']=$config['webmaster_email'];
					$installation['max_users']=isset($config['max_users']) ? $config['max_users'] : 0;
				}
				
				$installation['mtime']=Date::get_timestamp($installation['mtime']);				
				$installation['ctime']=Date::get_timestamp($installation['ctime']);
				
								
				$response['results'][] = $installation;
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