<?php
if(isset($argv[1]))
define('CONFIG_FILE', $argv[1]);

$root_path = dirname(dirname(dirname(__FILE__)));

require($root_path.'/Group-Office.php');

require('/etc/groupoffice/servermanager.inc.php');

if(!isset($GO_MODULES->modules['serverclient']))
{
	die('Fatal error: serverclient module must be installed');
}

if(!isset($GO_MODULES->modules['servermanager']))
{
	die('Fatal error: servermanager module must be installed');
}

require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();

$roots=array('/var/www/groupoffice');
//$roots=array('/var/www/groupoffice');

$configs=array();
foreach($roots as $root)
{
	$folders = $fs->get_folders($root);

	foreach($folders as $folder)
	{
		$conf = '/etc/groupoffice/'.$folder['name'].'/config.php';
		if(file_exists($conf))
		{
			$configs[]=array('name'=>$folder['name'], 'conf'=>$conf);
		}else
		{
			$conf = $folder['path'].'/html/groupoffice/config.php';
			if(file_exists($conf))
			{
				$configs[]=array('name'=>$folder['name'], 'conf'=>$conf);
			}
		}
	}
}

$lang['common']['default_salutation']['M']='Dear Mr';
$lang['common']['default_salutation']['F']='Dear Ms';


require_once($GO_MODULES->modules['servermanager']['class_path'].'servermanager.class.inc.php');
require_once($GO_MODULES->modules['serverclient']['class_path'].'serverclient.class.inc.php');
$sc = new serverclient();
$sm = new servermanager();


$sm->query("DELETE FROM sm_reports");

$logged_in=false;

//$db = new db();
$db2 = new db();

$db2->halt_on_error='report';

foreach($configs as $conf)
{
	$config=array();
	require($conf['conf']);
	
	$installation=array();
	$installation['name']=$conf['name'];
	$installation['ctime']=time();
	$installation['comment']='';
	$features=array();
	
	if(empty($config['db_name']))
	{
		echo 'Warning: empty db_name in '.$conf['conf']."\n";
	}else
	{
		if(!$db2->query("USE `".$config['db_name']."`"))
		{
			$installation['comment'] .= 'Database '.$config['db_name'].' not found for: '.$conf['name'];		
		}else
		{
			$users_table='';		
			
			$db2->query("SHOW TABLE STATUS FROM `".$config['db_name']."`;");
			
			$projects_table='';
			$billing_table='';
			
			$installation['database_usage']=0;
			while($db2->next_record(DB_BOTH))
			{

				if($db2->f(0)=='go_users')
				{
					$users_table='go_users';
				}


				if($db2->f(0)=='users')
				{
					$users_table='users';
				}
				
				if($db2->f(0)=='pm_projects' || $db2->f(0)=='pmProjects')
				{
					$projects_table=$db2->f(0);
				}
				
				if($db2->f(0)=='bs_orders')
				{
					$billing_table=$db2->f(0);
				}				
				
				$installation['database_usage']+=$db2->f('Data_length');
				$installation['database_usage']+=$db2->f('Index_length');
			}
			
			if(empty($users_table))
			{
				echo "Warning: No users table found in ".$conf['conf']."\n";
				$sm->query('USE `'.$sm->database.'`');
			}else
			{

			$installation['database_usage']=$installation['database_usage']/1024;
			
		
			$sql = "SELECT count(*) AS count_users, MIN(registration_time) AS install_time, MAX(lastlogin) AS lastlogin, SUM(logins) AS total_logins FROM  $users_table";
			$db2->query($sql);
			$record = $db2->next_record();
			
			foreach($record as $key=>$value)
			{
				if(empty($value))
				{
					$record[$key]=0;
				}
			}
		
			$installation = array_merge($record, $installation);
			
			$sql = "SELECT sex, email, first_name, middle_name, last_name, country FROM $users_table WHERE id=1";
			$db2->query($sql);
			$record = $db2->next_record();
			
			$installation['admin_email']=empty($record['email']) ? '' : $record['email'];
			$installation['admin_name']=String::format_name($record);
			$installation['admin_country']=empty($record['country']) ? '' : $record['country'];
			
			$middle = $db2->f('middle_name') == '' ? ' ' : ' '.$db2->f('middle_name').' ';
			
			$installation['admin_salutation']=$lang['common']['default_salutation'][$db2->f('sex')].$middle.$db2->f('last_name');
			
			
			if(!empty($billing_table))
			{
				$sql = "SELECT count(*) AS count FROM bs_orders";
				$db2->query($sql);
				$db2->next_record();
				$features[]='orders:'.$db2->f('count');
			}
			
			if(!empty($projects_table))
			{
				$sql = "SELECT count(*) AS count FROM $projects_table";
				$db2->query($sql);
				$db2->next_record();
				$features[]='projects:'.$db2->f('count');
			}
			
		
			$installation['decimal_separator']=$config['default_decimal_separator'];
			$installation['thousands_separator']=$config['default_thousands_separator'];
			$installation['date_format']=Date::get_dateformat($config['default_date_format'], $config['default_date_separator']);
			$installation['features']=implode(',', $features);
			$installation['mail_domains']=isset($config['serverclient_domains']) ? $config['serverclient_domains'] : '';
			$installation['file_storage_usage']=File::get_directory_size($config['file_storage_path']);
			$installation['file_storage_usage']+=File::get_directory_size($config['local_path']);
			
			$installation['mailbox_usage']=0;
			
			if(!empty($GO_CONFIG->serverclient_server_url) && !empty($config['serverclient_domains']))
			{
				if(!$logged_in)
					$sc->login();
					
				$params=array(
							'task'=>'serverclient_get_usage',
							'domains'=>$config['serverclient_domains']
				);
		
				$response = $sc->send_request($sc->server_url.'modules/postfixadmin/json.php', $params);
				$response = json_decode($response, true);
		
				//debug(var_export($response, true));
		
				
				foreach($response['domains'] as $domain)
				{
					$installation['mailbox_usage']+=$domain['usage'];
				}
			}		
			
			if($users_table=='go_users')
			{
				$db2->query("REPLACE INTO go_settings VALUES(0, 'usage_date', '".time()."')");
				$db2->query("REPLACE INTO go_settings VALUES(0, 'mailbox_usage', '".$installation['mailbox_usage']."')");
				$db2->query("REPLACE INTO go_settings VALUES(0, 'file_storage_usage', '".$installation['file_storage_usage']."')");
				$db2->query("REPLACE INTO go_settings VALUES(0, 'database_usage', '".$installation['database_usage']."')");
			}
		}
		
		$sm->query('USE `'.$sm->database.'`');
		$sm->add_report($installation);
		}
	}
}
