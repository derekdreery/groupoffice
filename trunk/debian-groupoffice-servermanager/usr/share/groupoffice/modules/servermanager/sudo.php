#!/usr/bin/php
<?php
$task = $argv[2];
$name = $argv[3];
$db_name = str_replace('.','_', $name);

if($task!='change_admin_password' && !preg_match('/^[a-z0-9-_\.]*$/',$name))
{
	die('Invalid installation name');
}

define('CONFIG_FILE', $argv[1]);

chdir(dirname(__FILE__));
require('../../Group-Office.php');

require_once ($GO_MODULES->modules['servermanager']['class_path']."servermanager.class.inc.php");
$sm = new servermanager();

require('/etc/groupoffice/servermanager.inc.php');


$go_root =$sm_config['install_path'].$name.'/';
$apache_conf_file = $sm_config['apache_conf'].$name;
$config_file = '/etc/groupoffice/'.$name.'/config.php';


switch($task)
{
	case 'change_admin_password':
		
		//when the admin password is changed it must also be changed in globalconfig.inc.php so all installations
		//can still connect.
		$new_password = $argv[3];
		
		$config=array();
		require('/etc/groupoffice/globalconfig.inc.php');
		$config['serverclient_password']=$new_password;

		$sm->write_config('/etc/groupoffice/globalconfig.inc.php', $config);
		
		break;
	
	case 'install':
		
		$tmp_config = $argv[4];
		
		if(is_dir('/etc/groupoffice/'.$name))
			die('config file exists');
			
		if(is_dir($go_root))
			die('Group-Office files already exists');
		
		if(!file_exists($tmp_config))
			die('Temp config does not exist');
		
		if(is_dir('/var/lib/mysql/'.$name))
			die('Database '.$name.' already exists');
		
		
		if(file_exists($apache_conf_file))
			die('Apache conf for '.$name.' already exists');

			
		$config['db_pass']=$GO_USERS->random_password();
		$config['db_user']=substr($db_name,0,16);
		
		
		$db = new db();
		$db->user=$sm_config['mysql_user'];
		$db->pass=$sm_config['mysql_pass'];
		$db->host=$sm_config['mysql_host'];
		
		$db->query("SET NAMES UTF8");
		$db->query("CREATE DATABASE `$db_name`");
		
		$sql = "GRANT ALL PRIVILEGES ON `".$db_name."`.*	TO ".
						"'".$config['db_user']."'@'".$sm_config['mysql_host']."' ".
						"IDENTIFIED BY '".$config['db_pass']."' WITH GRANT OPTION";
		$db->query($sql);
		
		$db->query('FLUSH PRIVILEGES');
		
		$sm->write_config($tmp_config, $config);
			
		//mkdir($go_root.'local',0755, true);
		
		mkdir($sm_config['install_path'].'local/'.$name.'/',0755,true);
		mkdir($go_root.'data',0755, true);		
		mkdir('/tmp/'.$name,0777, true);
		mkdir('/etc/groupoffice/'.$name,0755, true);
		
		chown($sm_config['install_path'].'local/'.$name.'/', $sm_config['apache_user']);
		
		/*
				
		$apache_conf_data = '#Install date '.date('r')."\n".
			'<VirtualHost '.$sm_config['ip_address'].'>'."\n".
			'DocumentRoot '.$go_root."groupoffice\n".
			'ServerName '.$name.'.'.$sm_config['domain']."\n".
			'ErrorLog /var/log/apache2/'.$name.'.'.$sm_config['domain'].'-error.log'."\n".
			'CustomLog /var/log/apache2/'.$name.'.'.$sm_config['domain'].'-access.log common'."\n".
			"</VirtualHost>";
		
		file_put_contents($apache_conf_file, $apache_conf_data);*/
		
		symlink($sm_config['source'], $go_root.'groupoffice');
		
		rename($tmp_config, $config_file);
		chmod($config_file, 0644);
		chown($config_file, 'root');
		chgrp($config_file, 'root');

		//system('apache2ctl graceful');
	
		chdir($go_root.'groupoffice/install/');
		
		system('./autoinstall.php "'.$config_file.'" "'.$sm_config['install_modules'].'"');
		
		system('chown -R '.$sm_config['apache_user'].' '.$go_root.'data');
		
		if(isset($argv[5]))
		{
			//set admin password
			
			$db->query('USE `'.$db_name.'`');
			$db->query("UPDATE go_users SET password=MD5('".$argv[5]."') WHERE id=1");
		}
		
		break;
		
	case 'move_config':		
		$config_file = '/etc/groupoffice/'.$name.'/config.php';		
		system('mv '.$argv[4].' '.$config_file);	
		
		if(isset($argv[5]))
		{
			//set admin password
			$db = new db();
			$db->query('USE `'.$db_name.'`');
			$db->query("UPDATE go_users SET password=MD5('".$argv[5]."') WHERE id=1");
		}
		break;
		
	case 'remove':		
		$db = new db();
		$db->halt_on_error='report';
		$db->user=$sm_config['mysql_user'];
		$db->pass=$sm_config['mysql_pass'];
		$db->host=$sm_config['mysql_host'];
		
		$db->query("SET NAMES UTF8");
		

		require_once($GO_MODULES->modules['serverclient']['class_path'].'serverclient.class.inc.php');
		$sc = new serverclient();
		
		try{
			$sc->login();
							
			$params=array(
					'task'=>'serverclient_delete_installation',
					'go_installation_id'=>$name
			);
			
			//$response = $sc->send_request($sc->server_url.'modules/postfixadmin/action.php', $params);
			//$response = json_decode($response, true);
			
			//debug('Start remove mailboxes');
			//debug(var_export($params, true));
		//	debug(var_export($response, true));
			//debug('End remove mailboxes');
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
		
		system('rm -Rf '.$go_root);
		system('rm -Rf '.$sm_config['install_path'].'local/'.$name);
		system('rm -Rf /etc/groupoffice/'.$name);
		system('rm -Rf /tmp/'.$name);
		
		$db_name = str_replace('.', '_', $name);
		$db->query("DROP DATABASE `".$db_name."`");		
		$db->query("DROP USER '".substr($db_name,0,16)."'");
		
		//unlink($apache_conf_file);
		
		break;
}
?>
