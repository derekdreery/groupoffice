#!/usr/bin/php
<?php
chdir(dirname(__FILE__));
require('../../cli-functions.inc.php');



$args = parse_cli_args($argv);

$db_name = str_replace('.','_', $args['name']);

if($args['task']!='change_admin_password' && !preg_match('/^[a-z0-9-_\.]*$/',$args['name']))
{
	die('Invalid installation name');
}

if(isset($args['go_config']))
	define('CONFIG_FILE', $args['go_config']);


require('../../Group-Office.php');

go_debug($argv);

require_once ($GO_MODULES->modules['servermanager']['class_path']."servermanager.class.inc.php");
$sm = new servermanager();

$args['sm_config']=isset($args['sm_config']) ? $args['sm_config'] : '/etc/groupoffice/servermanager.inc.php';
require($args['sm_config']);


$go_root =$sm_config['install_path'].$args['name'].'/';
$apache_conf_file = $sm_config['apache_conf'].$args['name'];
$config_file = '/etc/groupoffice/'.$args['name'].'/config.php';


switch($args['task'])
{
	case 'change_admin_password':
		
		//when the admin password is changed it must also be changed in globalconfig.inc.php so all installations
		//can still connect.
		$new_password = $args['password'];
		
		$config=array();
		require('/etc/groupoffice/globalconfig.inc.php');
		$config['serverclient_password']=$new_password;

		$sm->write_config('/etc/groupoffice/globalconfig.inc.php', $config);
		
		break;
	
	case 'install':
		
		$tmp_config = $args['tmp_config'];
		
		if(is_dir('/etc/groupoffice/'.$args['name']))
			die('config file exists');
			
		if(is_dir($go_root))
			die('Group-Office files already exists');
		
		if(!file_exists($tmp_config))
			die('Temp config does not exist');
		
		if(is_dir('/var/lib/mysql/'.$args['name']))
			die('Database '.$args['name'].' already exists');
		
		
		if(file_exists($apache_conf_file))
			die('Apache conf for '.$args['name'].' already exists');

			
		$config['db_pass']=$GO_USERS->random_password();
		$config['db_user']=substr($db_name,0,16);
		
		
		$db = new db();
		$db->user=$sm_config['mysql_user'];
		$db->password=$sm_config['mysql_pass'];
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
		
		
		mkdir($go_root.'data',0755, true);		
		mkdir('/tmp/'.$args['name'],0777, true);
		mkdir('/etc/groupoffice/'.$args['name'],0755, true);


		/*if(floatval($GO_CONFIG->version)<3.3){
			mkdir($sm_config['install_path'].'sm-local/'.$args['name'].'/',0755,true);
			chown($sm_config['install_path'].'sm-local/'.$args['name'].'/', $sm_config['apache_user']);
		}*/
		
		/*
				
		$apache_conf_data = '#Install date '.date('r')."\n".
			'<VirtualHost '.$sm_config['ip_address'].'>'."\n".
			'DocumentRoot '.$go_root."groupoffice\n".
			'ServerName '.$args['name'].'.'.$sm_config['domain']."\n".
			'ErrorLog /var/log/apache2/'.$args['name'].'.'.$sm_config['domain'].'-error.log'."\n".
			'CustomLog /var/log/apache2/'.$args['name'].'.'.$sm_config['domain'].'-access.log common'."\n".
			"</VirtualHost>";
		
		file_put_contents($apache_conf_file, $apache_conf_data);*/
		
		symlink($sm_config['source'], $go_root.'groupoffice');
		
		rename($tmp_config, $config_file);
		chmod($config_file, 0640);
		chown($config_file, 'root');
		chgrp($config_file, 'www-data');

		//system('apache2ctl graceful');
	
		chdir($go_root.'groupoffice/install/');
		
		system('./autoinstall.php "'.$config_file.'" "'.$sm_config['install_modules'].'"');
		
		system('chown -R '.$sm_config['apache_user'].' '.$go_root.'data');
		
		if(isset($args['password']))
		{
			//set admin password
			
			$db->query('USE `'.$db_name.'`');
			$db->query("UPDATE go_users SET password=MD5('".$args['password']."') WHERE id=1");
		}
		
		break;
		
	case 'move_config':		
		$config_file = '/etc/groupoffice/'.$args['name'].'/config.php';
		system('mv '.$args['tmp_config'].' '.$config_file);
		
		if(isset($args['password']))
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
		$db->password=$sm_config['mysql_pass'];
		$db->host=$sm_config['mysql_host'];
		
		$db->query("SET NAMES UTF8");
		

		/*require_once($GO_MODULES->modules['serverclient']['class_path'].'serverclient.class.inc.php');
		$sc = new serverclient();
		
		try{
			$sc->login();
							
			$params=array(
					'task'=>'serverclient_delete_installation',
					'go_installation_id'=>$args['name']
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
		}*/

		if(!is_dir($go_root)){
			die("Error: ".$go_root." doesn't exits\n");
		}
		
		system('rm -Rf '.$go_root);
		//system('rm -Rf '.$sm_config['install_path'].'sm-local/'.$args['name']);
		system('rm -Rf /etc/groupoffice/'.$args['name']);
		system('rm -Rf /tmp/'.$args['name']);
		
		$db_name = str_replace('.', '_', $args['name']);
		$db->query("DROP DATABASE `".$db_name."`");		
		$db->query("DROP USER '".substr($db_name,0,16)."'");
		
		//unlink($apache_conf_file);
		
		break;
}
?>
