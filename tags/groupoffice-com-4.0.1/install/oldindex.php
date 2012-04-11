<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

define('NOTINSTALLED', true);
//don't fire events yet
define('NO_EVENTS', true);

header('Content-Type: text/html; charset=UTF-8');

//config file exists now so require it to get the properties.
require_once('../Group-Office.php');

require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();


$CONFIG_FILE = $GLOBALS['GO_CONFIG']->get_config_file();

require_once('gotest.php');
require_once(dirname(dirname(__FILE__)).'/classes/filesystem.class.inc');

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : 'test';

if($task=='test')
{
	@session_destroy();
}

$tasks[] = 'test';
$tasks[] = 'license';
$tasks[] = 'release_notes';
$tasks[] = 'title';
//$tasks[] = 'url';

$tasks[] = 'userdir';


/*if($CONFIG_FILE=='/etc/groupoffice/config.php' && @file_exists('/etc/groupoffice/config-db.php'))
{
	require('/etc/groupoffice/config-db.php');

	$GLOBALS['GO_CONFIG']->db_host = empty($dbserver) ? 'localhost' : $dbserver;
	$GLOBALS['GO_CONFIG']->db_name = $dbname;
	$GLOBALS['GO_CONFIG']->db_user = $dbuser;
	$GLOBALS['GO_CONFIG']->db_pass = $dbpass;
	$GLOBALS['GO_CONFIG']->db_port = empty($dbport) ? 3306 : intval($dbport);

	$db = new db();
	$db->halt_on_error = 'no';
	$db->set_config($GO_CONFIG);

	if(@$db->connect())
	{
		if (save_config($GO_CONFIG)){

			$dbconn=true;
		}
	}
}*/

$db = new db();
$db->halt_on_error = 'no';

$tasks[] = 'theme';
$tasks[] = 'new_database';
$tasks[] = 'create_database';
$tasks[] = 'database_connection';
$tasks[] = 'database_structure';
if ($task != 'database_structure' && !empty($GLOBALS['GO_CONFIG']->db_name) && $db->connect() && !$GO_USERS->get_user(1))
{
	$tasks[] = 'administrator';
	//$tasks[] = 'send_info';
}
//$tasks[] = 'allow_password_change';
$tasks[] = 'default_module_access';
$tasks[] = 'default_groups';
$tasks[] = 'smtp';
$GO_USERS->halt_on_error='no';



$tasks[] = 'completed';

$menu_language['test'] = 'System test';
$menu_language['license'] = 'License';
$menu_language['release_notes'] = 'Release notes';
$menu_language['new_database'] = 'Database creation/upgrade';
$menu_language['title'] = 'Title';
$menu_language['url'] = 'URL configuration';
$menu_language['userdir'] = 'Filesystem storage';
$menu_language['allow_password_change'] = 'Registration defaults';
$menu_language['default_module_access'] = 'Default module access';
$menu_language['default_groups'] = 'Default user groups';
$menu_language['theme'] = 'Look & Feel';
$menu_language['smtp'] = 'SMTP configuration';
$menu_language['administrator'] = 'Administrator account';
$menu_language['send_info'] = 'Send information';




function print_head()
{
	global $GO_CONFIG;
	
	echo '<html><head>'.
	'<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />'.
	'<link href="install.css" rel="stylesheet" type="text/css" />'.
	'<title>'.$GLOBALS['GO_CONFIG']->product_name.' Installation</title>'.
	'</head>'.
	'<body style="font-family: Arial,Helvetica;">';
	echo '<form method="post" action="index.php">';
	echo '<table width="100%" cellpadding="0" cellspacing="0">';
	echo '<tr><td style="border-bottom:1px solid black;"><img src="logo.gif" border="0" align="middle" style="margin:10px" /></td>';
	echo '<td style="border-bottom:1px solid black;text-align:right;padding-right:10px;"><h1>'.$GLOBALS['GO_CONFIG']->product_name.' installation</h1></td></tr>';
	echo '<tr><td valign="top" style="">';

	foreach($GLOBALS['tasks'] as $task)
	{
		$class = $task == $GLOBALS['task'] ? 'menu_active' : 'menu';
		if(isset($GLOBALS['menu_language'][$task]))
		{
			echo '<div class="'.$class.'">'.$GLOBALS['menu_language'][$task].'</div>';
		}
	}
	echo '</td><td valign="top" style="padding:10px;width:100%;">';
}

function print_foot()
{
	echo '</td></tr></table></form></body></html>';
}

//destroy session when user closes browser
ini_set('session.cookie_lifetime','0');


//get the path of this script
$script_path = str_replace('\\','/',__FILE__);
if ($script_path == '')
{
	print_head();
	echo '<b>Fatal error:</b> Could not get the path of the this script. The server variable \'__FILE__\' is not set.';
	echo '<br /><br />Correct this and refresh this page. If you are not able to correct this try the manual installation described in the file \'INSTALL\'';
	print_foot();
	exit();
}

//check ifconfig exists and if the config file is writable
$config_location1 = '/etc/groupoffice/'.$_SERVER['SERVER_NAME'].'/config.php';
$config_location2 = dirname(substr($_SERVER['SCRIPT_FILENAME'], 0 ,-strlen($_SERVER['PHP_SELF']))).'/config.php';
$config_location3 = $GLOBALS['GO_CONFIG']->root_path.'config.php';

if($task !='test')
{
	if(!file_exists($CONFIG_FILE))
	{
		print_head();
		echo '<input type="hidden" name="task" value="license" />';
		echo 'The configuration file does not exist. You must create an empty writable file at one of the following locations:<br />';
		echo '<ol><li>'.$config_location1.'</li><li>'.$config_location2.'</li>';
		echo '<li>'.$config_location3.'</li></ol></i></font>';
		echo 'The first two locations are more secure because the sensitive information is kept outside the document root but it does require root privileges on this machine.<br />The second advantage is that you will be able to separate the source from the configuration. This can be very usefull with multiple installations on one machine.';
		echo '<br /><br />If you choose the third location then you have to make sure that in Apache\'s httpd.conf the following is set:<br /><br />';
		echo '<font color="#003399">';
		echo '<i>UseCanonicalName On</i></font><br />';
		echo 'This is to make sure it always finds your configuration file at the correct location.';
		echo '<br /><br /><font color="#003399">';
		echo '<i>$ touch config.php (Or FTP an empty config.php to the server)<br />';
		echo '$ chmod 666 config.php</i></font>';
		echo '<br /><br />If it does exist and you still see this message then it might be that safe_mode is enabled and the config.php is owned by another user then the '.$GLOBALS['GO_CONFIG']->product_name.' files.';
		echo '<br /><br /><div style="text-align: right;"><input type="submit" value="Continue" /></div>';
		print_foot();
		exit();
	}elseif (!is_writable($CONFIG_FILE))
	{
		print_head();
		echo '<input type="hidden" name="task" value="license" />';
		echo 'The configuration file \''.$CONFIG_FILE.'\' exists but is not writable. If you wish to make changes then you have to make \''.$CONFIG_FILE.'\' writable during the configuration process.';
		echo '<br /><br />Correct this and refresh this page.';
		echo '<br /><br /><font color="#003399"><i>$ chmod 666 '.$CONFIG_FILE.'<br /></i></font>'.
		'<br /><br /><div style="text-align: right;"><input type="submit" value="Continue" /></div>';
		print_foot();
		exit();
	}
}

$key = array_search($task, $tasks);
$nexttask = isset($tasks[$key+1]) ? $tasks[$key+1] : 'completed';


if ($_SERVER['REQUEST_METHOD'] =='POST')
{
	switch($task)
	{
		case 'post_database_connection':
			$task = 'database_connection';
			$db = new db();
			$db->halt_on_error = 'no';

			$GLOBALS['GO_CONFIG']->db_host = $_POST['db_host'];
			$GLOBALS['GO_CONFIG']->db_name = $_POST['db_name'];
			$GLOBALS['GO_CONFIG']->db_user = $_POST['db_user'];
			$GLOBALS['GO_CONFIG']->db_pass = $_POST['db_pass'];
			$GLOBALS['GO_CONFIG']->db_port = $_POST['db_port'];
			$GLOBALS['GO_CONFIG']->db_socket = $_POST['db_socket'];

			$db->set_config($GO_CONFIG);

			if(@$db->connect())
			{
				if (save_config($GO_CONFIG))
				{
					$task = 'database_structure';
				}
			}else
			{
				$feedback ='<font color="red">Failed to connect to database<br /><b>MySQL Error</b>: '.$db->errno.' ('.$db->error.')<br>\n</font>';
			}
			break;

		case 'database_structure':
			$db = new db();
			$db->halt_on_error = 'report';

			$db->set_config($GO_CONFIG);

			if (!$db->connect())
			{
				print_head();
				echo '<font color="red">Failed to connect to database<br /><b>MySQL Error</b>: '.$db->errno.' ('.$db->error.')<br>\n</font>';
				echo '<br /><br />Correct this and refresh this page.';
				print_foot();
				exit();
			}else
			{
				//create new empty database
				//table is empty create the structure
				$queries = String::get_sql_queries($GLOBALS['GO_CONFIG']->root_path."install/sql/groupoffice.sql");
				while ($query = array_shift($queries))
				{
					$db->query($query);
				}

				require($GLOBALS['GO_CONFIG']->root_path."install/sql/updates.inc.php");
				//store the version number for future upgrades
				$GLOBALS['GO_CONFIG']->save_setting('version', count($updates));

				require_once($GLOBALS['GO_CONFIG']->class_path.'base/groups.class.inc.php');
				$GO_GROUPS = new GO_GROUPS();

				

				$GO_GROUPS->query("DELETE FROM go_db_sequence WHERE seq_name='groups'");
				$GO_GROUPS->query("DELETE FROM go_groups");

				$admin_group_id = $GO_GROUPS->add_group(1, $lang['common']['group_admins']);
				$everyone_group_id = $GO_GROUPS->add_group(1, $lang['common']['group_everyone']);
				$internal_group_id = $GO_GROUPS->add_group(1, $lang['common']['group_internal']);

				$GLOBALS['GO_MODULES']->load_modules();

				$fs = new filesystem();

				//install all modules
				$module_folders = $fs->get_folders($GLOBALS['GO_CONFIG']->root_path.'modules/');

				$available_modules=array();
				foreach($module_folders as $folder)
				{
					if(!file_exists($folder['path'].'/install/noautoinstall'))
					{
						$available_modules[]=$folder['name'];
					}
				}
				$priority_modules=array('summary','email','calendar','tasks','addressbook','files', 'notes', 'projects');

				for($i=0;$i<count($priority_modules);$i++)
				{
					if(in_array($priority_modules[$i], $available_modules))
					{
						$GLOBALS['GO_MODULES']->add_module($priority_modules[$i]);
					}
				}
				for($i=0;$i<count($available_modules);$i++)
				{
					if(!in_array($available_modules[$i], $priority_modules))
					{
						$GLOBALS['GO_MODULES']->add_module($available_modules[$i]);
					}
				}

				$GLOBALS['GO_CONFIG']->save_setting('upgrade_mtime', $GLOBALS['GO_CONFIG']->mtime);
				
				$task = 'administrator';
			}
			break;

		case 'administrator':

			if($_POST['pass1']!=$_POST['pass2']){
				$feedback ='<font color="red">The passwords didn\'t match<br>\n</font>';
			}else
			{
				//increase counter
				$GO_USERS->nextid('go_users');
				
				$user['id']=1;
				$user['language'] = $GLOBALS['GO_LANGUAGE']->language;
				$user['first_name']=$GLOBALS['GO_CONFIG']->product_name;
				$user['middle_name']='';
				$user['last_name']=$lang['common']['admin'];
				$user['username'] = $_POST['username'];
				$user['password'] = $_POST['pass1'];
				$user['email'] = $_POST['email'];
				$user['sex'] = 'M';
				$user['enabled']='1';
				$user['country']=$GLOBALS['GO_CONFIG']->default_country;
				$user['work_country']=$GLOBALS['GO_CONFIG']->default_country;

				//$GO_USERS->debug=true;
				$GO_USERS->add_user($user,array(1,2,3),array($GLOBALS['GO_CONFIG']->group_everyone));

				
				$task = $nexttask;
			}
			break;

		case 'userdir':
			$tmpdir=$_POST['tmpdir'];

			if (!is__writable($_POST['userdir']))
			{
				$feedback = '<font color="red">The protected files path you entered is not writable.<br />Please correct this and try again.</font>';
			}elseif($_POST['max_file_size'] > return_bytes(ini_get('upload_max_filesize')))
			{
				$feedback = '<font color="red">You entered a greater upload size then the PHP configuration allows.<br />Please correct this and try again.</font>';
			}elseif (!is__writable($tmpdir))
			{
				$feedback = '<font color="red">The temporary files path you entered is not writable.<br />Please correct this and try again.</font>';
			}

			if (substr($_POST['userdir'], -1) != '/') $_POST['userdir'] = $_POST['userdir'].'/';
			$GLOBALS['GO_CONFIG']->file_storage_path=$_POST['userdir'];
			//$GLOBALS['GO_CONFIG']->create_mode=$_POST['create_mode'];
			$GLOBALS['GO_CONFIG']->max_file_size=$_POST['max_file_size'];

	
			if (substr($tmpdir, -1) != '/') $tmpdir = $tmpdir.'/';
			$GLOBALS['GO_CONFIG']->tmpdir=$tmpdir;


			//autodetect helper program locations

			$GLOBALS['GO_CONFIG']->cmd_zip = whereis('zip') ? whereis('zip') : '/usr/bin/zip';
			$GLOBALS['GO_CONFIG']->cmd_unzip = whereis('unzip') ? whereis('unzip') : '/usr/bin/unzip';
			$GLOBALS['GO_CONFIG']->cmd_tar = whereis('tar') ? whereis('tar') : '/bin/tar';
			$GLOBALS['GO_CONFIG']->cmd_chpasswd = whereis('chpasswd') ? whereis('chpasswd') : '/usr/sbin/chpasswd';
			$GLOBALS['GO_CONFIG']->cmd_sudo = whereis('sudo') ? whereis('sudo') : '/usr/bin/sudo';
			$GLOBALS['GO_CONFIG']->cmd_xml2wbxml = whereis('xml2wbxml') ? whereis('xml2wbxml') : '/usr/bin/xml2wbxml';
			$GLOBALS['GO_CONFIG']->cmd_wbxml2xml = whereis('wbxml2xml') ? whereis('wbxml2xml') : '/usr/bin/wbxml2xml';
			$GLOBALS['GO_CONFIG']->cmd_tnef = whereis('tnef') ? whereis('tnef') : '/usr/bin/tnef';

			if (save_config($GO_CONFIG) && !isset($feedback))
			{
				if(!is_dir($GLOBALS['GO_CONFIG']->file_storage_path.'cache'))
					mkdir($GLOBALS['GO_CONFIG']->file_storage_path.'cache', 0755, true);
				
				//check for userdirs
				/*$GO_USERS->get_users();
				while($GO_USERS->next_record())
				{
					if(!file_exists($GLOBALS['GO_CONFIG']->file_storage_path.'users/'.$GO_USERS->f('username')))
					{
						filesystem::mkdir_recursive($GLOBALS['GO_CONFIG']->file_storage_path.'users/'.$GO_USERS->f('username'));
					}
				}*/
				$task = 'theme';
			}

			break;


		case 'title':
			if ($_POST['title'] == '')
			{
				$feedback = 'You didn\'t enter a title.';

			}elseif(!String::validate_email($_POST['webmaster_email']))
			{
				$feedback = '<font color="red">You entered an invalid e-mail address.</font>';
			}else
			{
				$GLOBALS['GO_CONFIG']->webmaster_email = ($_POST['webmaster_email']);
				$GLOBALS['GO_CONFIG']->title = ($_POST['title']);
				if (save_config($GO_CONFIG))
				{
					$task = $nexttask;
				}
			}
			break;

		case 'url':
			$host = (trim($_POST['host']));
			//$full_url = (trim($_POST['full_url']));
			if ($host != '')
			{
				if ($host != '/')
				{
					if (substr($host , -1) != '/') $host  = $host.'/';
					if (substr($host , 0, 1) != '/') $host  = '/'.$host;
				}


				$GLOBALS['GO_CONFIG']->host = $host;
				if (save_config($GO_CONFIG))
				{
					$task = $nexttask;
				}

			}else
			{
				$feedback = '<font color="red">You didn\'t enter both fields.</font>';
			}
			break;

		case 'theme':
			$GLOBALS['GO_CONFIG']->language = $_POST['language'];

			$GLOBALS['GO_LANGUAGE']->set_language($GLOBALS['GO_CONFIG']->language);

			$GLOBALS['GO_CONFIG']->theme = ($_POST['theme']);

			$GLOBALS['GO_CONFIG']->default_country = ($_POST['default_country']);
			$GLOBALS['GO_CONFIG']->default_timezone = ($_POST['default_timezone']);
			$GLOBALS['GO_CONFIG']->default_currency = ($_POST['default_currency']);
			$GLOBALS['GO_CONFIG']->default_date_format = ($_POST['default_date_format']);
			$GLOBALS['GO_CONFIG']->default_date_separator = ($_POST['default_date_separator']);
			$GLOBALS['GO_CONFIG']->default_time_format = ($_POST['default_time_format']);
			$GLOBALS['GO_CONFIG']->default_first_weekday = ($_POST['default_first_weekday']);
			$GLOBALS['GO_CONFIG']->default_decimal_separator = ($_POST['default_decimal_separator']);
			$GLOBALS['GO_CONFIG']->default_thousands_separator = ($_POST['default_thousands_separator']);


			if (save_config($GO_CONFIG))
			{
				$task = $nexttask;
			}
			break;

		case 'allow_password_change':
			$GLOBALS['GO_CONFIG']->allow_registration = isset($_POST['allow_registration']) ? true : false;
			$GLOBALS['GO_CONFIG']->auto_activate_accounts = isset($_POST['auto_activate_accounts']) ? true : false;
			$GLOBALS['GO_CONFIG']->notify_admin_of_registration = isset($_POST['notify_admin_of_registration']) ? true : false;

			$GLOBALS['GO_CONFIG']->allow_password_change =  isset($_POST['allow_password_change']) ? true : false;
			$GLOBALS['GO_CONFIG']->allow_themes =  isset($_POST['allow_themes']) ? true : false;

			$GLOBALS['GO_CONFIG']->registration_fields = isset($_POST['registration_fields']) ? implode(',',$_POST['registration_fields']) : '';
			$GLOBALS['GO_CONFIG']->required_registration_fields = isset($_POST['required_registration_fields']) ? implode(',',$_POST['required_registration_fields']) : '';

			if (save_config($GO_CONFIG))
			{
				$task = $nexttask;
			}

			break;

		case 'default_module_access':

			$GLOBALS['GO_CONFIG']->allow_password_change =  isset($_POST['allow_password_change']) ? true : false;
			$GLOBALS['GO_CONFIG']->allow_themes =  isset($_POST['allow_themes']) ? true : false;


			$GLOBALS['GO_CONFIG']->register_modules_read = isset($_POST['register_modules_read']) ? implode(',',$_POST['register_modules_read']) : '';
			$GLOBALS['GO_CONFIG']->register_modules_write = isset($_POST['register_modules_write']) ? implode(',',$_POST['register_modules_write']) : '';

			if (save_config($GO_CONFIG))
			{
				$task = $nexttask;
			}

			break;

		case 'default_groups':

			$GLOBALS['GO_CONFIG']->register_user_groups = isset($_POST['register_user_groups']) ? implode(',',$_POST['register_user_groups']) : '';
			$GLOBALS['GO_CONFIG']->register_visible_user_groups = isset($_POST['register_visible_user_groups']) ? implode(',',$_POST['register_visible_user_groups']) : '';

			if (save_config($GO_CONFIG))
			{
				$task = $nexttask;
			}

			break;


		case 'smtp':
			if($_POST['max_attachment_size'] > return_bytes(ini_get('upload_max_filesize')))
			{
				$feedback = '<font color="red">You entered a greater upload size then the PHP configuration allows.<br />Please correct this and try again.</font>';
			}

			//$GLOBALS['GO_CONFIG']->mailer = $_POST['mailer'];
			$GLOBALS['GO_CONFIG']->smtp_port = isset($_POST['smtp_port']) ? (trim($_POST['smtp_port'])) : '';
			$GLOBALS['GO_CONFIG']->smtp_server= isset($_POST['smtp_server']) ? (trim($_POST['smtp_server'])) : '';

			$GLOBALS['GO_CONFIG']->smtp_username= isset($_POST['smtp_username']) ? (trim($_POST['smtp_username'])) : '';
			$GLOBALS['GO_CONFIG']->smtp_password= isset($_POST['smtp_password']) ? (trim($_POST['smtp_password'])) : '';
			$GLOBALS['GO_CONFIG']->smtp_encryption= isset($_POST['smtp_password']) ? (trim($_POST['smtp_encryption'])) : '';


			$GLOBALS['GO_CONFIG']->max_attachment_size= (trim($_POST['max_attachment_size']));

			if (save_config($GO_CONFIG) && !isset($feedback))
			{
				$task = $nexttask;
			}
			break;

		case 'send_info':
			if ($_REQUEST['info'] != 'no')
			{
				$body = "Group-Office title: ".$GLOBALS['GO_CONFIG']->title."\r\n";
				$body = "Group-Office version: ".$GLOBALS['GO_CONFIG']->version."\r\n";
				$body .= "Usage: ".$_REQUEST['info']."\r\n";
				$body .= "Users: ".$_REQUEST['users']."\r\n";
				$body .= "Host: ".$GLOBALS['GO_CONFIG']->full_url."\r\n";
				$body .= "Webmaster: ".$GLOBALS['GO_CONFIG']->webmaster_email."\r\n";
				if ($_REQUEST['email'] != '')
				{
					$body .= "Contact about Group-Office Professional at: ".$_REQUEST['email']."\r\n";
					$body .= "Name: ".$_REQUEST['name']."\r\n";
				}

				sendmail('notify@intermesh.nl', $GLOBALS['GO_CONFIG']->webmaster_email, $GLOBALS['GO_CONFIG']->title, "Group-Office usage information", $body);
			}
			$task = $nexttask;
			break;

		case 'post_create_database':
			$task = 'create_database';
			if($_POST['db_host'] == '' || $_POST['db_user'] == '' || $_POST['db_name'] == '' || $_POST['host_allow'] == '')
			{
				$feedback ='<font color="red">You did not fill in all the required fields</font>';
			}elseif($_POST['db_pass1'] != $_POST['db_pass2'])
			{
				$feedback ='<font color="red">Passwords did not match</font>';
			}else
			{
				$db = new db();
				$db->halt_on_error = 'no';

				$GLOBALS['GO_CONFIG']->db_host = $_POST['db_host'];
				$GLOBALS['GO_CONFIG']->db_name = $_POST['db_name'];
				$GLOBALS['GO_CONFIG']->db_user = $_POST['db_user'];
				$GLOBALS['GO_CONFIG']->db_pass = $_POST['db_pass1'];
				$GLOBALS['GO_CONFIG']->db_port = $_POST['db_port'];
				$GLOBALS['GO_CONFIG']->db_socket = $_POST['db_socket'];

				$db->set_parameters($_POST['db_host'], null, $_POST['admin_user'], $_POST['admin_pass'], $_POST['db_port'], $_POST['db_socket']);

				if($db->connect())
				{
					$sql = 'CREATE DATABASE `'.$_POST['db_name'].'`;';
					if($db->query($sql))
					{
						$sql = "GRANT ALL PRIVILEGES ON `".($_POST['db_name'])."`.*	TO ".
						"'".$_POST['db_user']."'@'".($_POST['host_allow'])."' ".
						"IDENTIFIED BY '".($_POST['db_pass1'])."' WITH GRANT OPTION";
						if($db->query($sql))
						{
							$db->query("FLUSH PRIVILEGES;");

							if (save_config($GO_CONFIG))
							{
								$task = 'database_structure';
							}

						}else
						{
							$feedback ='<font color="red">Failed to create user.<br />'.
							'<b>MySQL Error</b>: '.$db->errno.' '.$db->error.'</font>';
						}
					}else
					{
						$feedback ='<font color="red">Failed to create database.<br />'.
						'<b>MySQL Error</b>: '.$db->errno.' '.$db->error.'</font>';;
					}
				}else
				{
					$feedback ='<font color="red">Failed to connect to database as administrator.<br />'.
					'<b>MySQL Error</b>: '.$db->errno.' '.$db->error.'</font>';
				}
			}
			break;
		case 'database_connection':

			break;
		case 'create_database':

			break;
		case 'upgrade':

			break;

		default:
			$task = $nexttask;
			break;

	}
}
//Store all options in config array during install


$lasttask = 'test';
foreach($tasks as $_task)
{
	if($task==$_task)
	{
		break;
	}
	$lasttask=$_task;
}



switch($task)
{
	case 'test':
		print_head();
		echo '<input type="hidden" name="task" value="test" />';

		echo '<h1>Welcome!</h1><p>Thank you for installing '.$GLOBALS['GO_CONFIG']->product_name.'. This page checks if your system meets the requirements to run '.$GLOBALS['GO_CONFIG']->product_name.'.</p>'.
			'<p>If this page prints errors or warnings, please visit this page for more information: <a target="_blank" href="http://www.group-office.com/wiki/Installation">http://www.group-office.com/wiki/Installation</a></p>';

		if(!output_system_test())
		{
			echo '<p style="color: red;">Because of a fatal error in your system setup the installation can\'t continue. Please fix the errors above first.</p>';
		}else
		{
			echo '<br /><div align="right"><input type="submit" value="Continue" /></div>';
		}
		print_foot();
		exit();
		break;


	case 'license':

		print_head();
		echo '<input type="hidden" name="task" value="license" />';
		echo 'Do you agree to the terms of the license agreement?<br /><br />';
		echo '<iframe style="width: 100%; height: 300px; background: #ffffff;" src="../LICENSE.TXT"></iframe>';
		echo '<br /><br /><div align="right"><input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task='.$lasttask.'\';" value="Back" />&nbsp;&nbsp;<input type="submit" value="I agree to these terms" /></div>';
		print_foot();
		exit();
		break;

	case 'release_notes':
		print_head();
		echo '<input type="hidden" name="task" value="release_notes" />';
		echo 'Please read the release notes<br /><br />';
		echo '<iframe style="width: 100%; height: 300px; background: #ffffff;" src="../RELEASE.TXT"></iframe>';

		echo '<br /><br /><div align="right"><input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task='.$lasttask.'\';" value="Back" />&nbsp;&nbsp;<input type="submit" value="Continue" /></div>';
		print_foot();
		exit();
		break;

	case 'new_database':
		print_head();
		echo 'Do you wish to create a new database and user (Requires MySQL administration privileges) or do you want to use an existing database and user?<br /><br />';
		echo '<input type="hidden" name="task" value="new_database" />';
		echo '<div style="text-align:right"><input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task='.$lasttask.'\';" value="Back" />&nbsp;&nbsp;<input type="button" onclick="javascript:_go(\'create_database\');" value="Create new database" />&nbsp;&nbsp;';

		$buttonText = !isset($dbconn) ? 'Use existing database' : 'Upgrade database \''.$GLOBALS['GO_CONFIG']->db_name.'\'';

		echo '<input type="button" onclick="javascript:_go(\'database_connection\');" value="'.$buttonText.'" /></div>';
		echo '<script type="text/javascript">';
		echo 'function _go(task){document.forms[0].task.value=task;document.forms[0].submit();}</script>';

		print_foot();
		break;

	case 'create_database':
		print_head();
		if (isset($feedback))
		{
			echo $feedback.'<br /><br />';
		}
		?>
			<input type="hidden" name="task" value="post_create_database" />
			Enter the administrator username and password and fill in the other fields to create a new database and user for <?php echo $GLOBALS['GO_CONFIG']->product_name; ?>.
			<br /><br />
			<table>
			<tr>
			<td>
			Host:
			</td>
			<td>
			<?php $db_host = isset($_POST['db_host']) ? $_POST['db_host'] : $GLOBALS['GO_CONFIG']->db_host; ?>
			<input type="text" size="40" name="db_host" value="<?php echo $db_host; ?>" />
			</td>
			</tr>
			<tr>
			<td>
			Port:
			</td>
			<td>
			<?php $db_port = isset($_POST['db_port']) ? $_POST['db_port'] : $GLOBALS['GO_CONFIG']->db_port; ?>
			<input type="text" size="40" name="db_port" value="<?php echo $db_port; ?>" />
			</td>
			</tr>
			<tr>
			<td>
			Or you can use a socket:
			</td>
			<td>
			<?php $db_socket = isset($_POST['db_socket']) ? $_POST['db_socket'] : $GLOBALS['GO_CONFIG']->db_socket; ?>
			<input type="text" size="40" name="db_socket" value="<?php echo $db_socket; ?>" />
			</td>
			</tr>
			<tr>
			<td>
			Administrator username:
			</td>
			<td>
			<?php $admin_user = isset($_POST['admin_user']) ? $_POST['admin_user'] : 'root'; ?>
			<input type="text" size="40" name="admin_user" value="<?php echo $admin_user; ?>"  />
			</td>
			</tr>
			<tr>
			<td>
			Administrator password:
			</td>
			<td>
			<input type="password" size="40" name="admin_pass" value=""  />
			</td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
			<td>
			Database:
			</td>
			<td>
			<?php $db_name = isset($_POST['db_name']) ? $_POST['db_name'] : $GLOBALS['GO_CONFIG']->db_name; ?>
			<input type="text" size="40" name="db_name" value="<?php echo $db_name; ?>" />
			</td>
			</tr>
			<tr>
			<td>
			Allow connections from host ('%' for any host):
				</td>
					<td>
					<?php $host_allow = isset($_POST['host_allow']) ? $_POST['host_allow'] : 'localhost'; ?>
					<input type="text" size="40" name="host_allow" value="<?php echo $host_allow; ?>" />
					</td>
					</tr>

					<tr>
					<td>
					Username:
					</td>
					<td>
					<?php $db_user = isset($_POST['db_user']) ? $_POST['db_user'] : $GLOBALS['GO_CONFIG']->db_user; ?>
					<input type="text" size="40" name="db_user" value="<?php echo $db_user; ?>"  />
					</td>
					</tr>
					<tr>
					<td>
					Password:
					</td>
					<td>
					<input type="password" size="40" name="db_pass1" value=""  />
					</td>
					</tr>
					<tr>
					<td>
					Confirm password:
					</td>
					<td>
					<input type="password" size="40" name="db_pass2" value=""  />
					</td>
					</tr>
					</table>
					<div style="text-align:right">
						<?php echo '<input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task=new_database\';" value="Back" />&nbsp;&nbsp;'; ?>
						<input type="submit" value="Continue" /></div>
					<?php
					print_foot();
					exit();
					break;


					//Get the database parameters first
					//if option database_connection is set then we have succesfully set up database
	case 'database_connection':
		print_head();
		if (isset($feedback))
		{
			echo $feedback.'<br /><br />';
		}
		?>
			<input type="hidden" name="task" value="post_database_connection" />
			Create a database now and fill in the values to connect to your database.<br />
			The database user should have permission to perform select-, insert-, update- and delete queries. It must also be able to lock tables.<br /><br />

			If you are upgrading then now is the last time to back up your database! Fill in the fields and click at 'Continue' to upgrade your database structure.
			<br /><br />

			<font color="#003399"><i>
			$ mysql -u root -p<br />
			mysql&#62; CREATE DATABASE groupoffice;<br />
			mysql&#62; GRANT ALL PRIVILEGES ON groupoffice.* TO 'groupoffice'@'localhost'<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&#62; IDENTIFIED BY 'some_pass' WITH GRANT OPTION;<br />
			mysql&#62; quit;<br />
			</i></font>

			<br /><br />
			<table>
			<tr>
			<td>
			Host:
			</td>
			<td>
			<input type="text" size="40" name="db_host" value="<?php echo $GLOBALS['GO_CONFIG']->db_host; ?>" />
			</td>
			</tr>
			<tr>
			<td>
			Port:
			</td>
			<td>
			<input type="text" size="40" name="db_port" value="<?php echo $GLOBALS['GO_CONFIG']->db_port; ?>" />
			</td>
			</tr>
			<tr>
			<td>
			Or you can use a socket:
			</td>
			<td>
			<input type="text" size="40" name="db_socket" value="<?php echo $GLOBALS['GO_CONFIG']->db_socket; ?>" />
			</td>
			</tr>
			<tr>
			<td>
			Database:
			</td>
			<td>
			<input type="text" size="40" name="db_name" value="<?php echo $GLOBALS['GO_CONFIG']->db_name; ?>" />
			</td>
			</tr>

			<tr>
			<td>
			Username:
			</td>
			<td>
			<input type="text" size="40" name="db_user" value="<?php echo $GLOBALS['GO_CONFIG']->db_user; ?>"  />
			</td>
			</tr>
			<tr>
			<td>
			Password:
			</td>
			<td>
			<input type="password" size="40" name="db_pass" value=""  />
			</td>
			</tr>
			</table>
			<div style="text-align:right">
				<?php echo '<input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task=new_database\';" value="Back" />&nbsp;&nbsp;'; ?>
				<input type="submit" value="Continue" /></div>

			<?php
			print_foot();
			exit();
			break;

			//database connection is setup now
			//next step isto check if the table structure is present.

	case 'database_structure':
		$db = new db();
		$db->halt_on_error = 'no';
		print_head();
		if (!@$db->connect($GLOBALS['GO_CONFIG']->db_name, $GLOBALS['GO_CONFIG']->db_host, $GLOBALS['GO_CONFIG']->db_user, $GLOBALS['GO_CONFIG']->db_pass))
		{

			echo 'Can\'t connect to database!';
			echo '<br /><br />Correct this and refresh this page.';

		}else
		{
			$db->query("SELECT @@session.sql_mode;");
			$record = $db->next_record(MYSQL_BOTH);
			if(strstr($record[0], 'STRICT')!==false)
			{
				echo '<p style="color:red">The sql-mode setting in the MySQL config my.cnf is set to STRICT_TRANS_TABLES, STRICT_ALL_TABLES or TRADITIONAL. '.$GLOBALS['GO_CONFIG']->product_name.' does not yet work with this setting. You might want this setting enabled if you are a developer, but for production use you should disable it.</p>';
			}

			$settings_exist = false;
			$is_old_go=false;
			$db->query("SHOW TABLES;");
			if ($db->num_rows() > 0)
			{
				//structure exists see if the settings table exists
				while ($db->next_record(DB_BOTH))
				{
					if ($db->f(0) == 'go_settings')
					{
						$settings_exist = true;
						break;
					}
					if($db->f(0)=='users')
					{
						$is_old_go=true;
						break;
					}
				}
			}
			if ($settings_exist)
			{
				$db->query("SELECT value FROM go_settings WHERE name='version'");
				if ($db->next_record())
				{
					$db_version=$db->f('value');
					require_once($GLOBALS['GO_CONFIG']->root_path.'install/sql/updates.inc.php');
					if (!empty($db_version) && !isset($updates[$db_version-1]))
					{
						$db_version = false;
					}
				}else
				{
					$db_version = false;
				}

				?>
					<input type="hidden" name="task" value="upgrade" />
					<?php echo $GLOBALS['GO_CONFIG']->product_name; ?> has detected a previous installation of <?php echo $GLOBALS['GO_CONFIG']->product_name; ?> By pressing continue the database will be upgraded. This may take some time
					and you should <b>backup your database before you continue with this step!</b>
					<?php
					/*if (!$db_version)
					{
						echo '<br /><br />Group-Office was unable to detect your old Group-Office version.'.
						'The installer needs your old version number to determine updates that might apply.<br />'.
						'Please enter the version number below if you wish to perform an upgrade.';
					}*/
				?>
					<br /><br />
					<table width="100%" style="border-width: 0px;font-family: Arial,Helvetica; font-size: 12px;">
					<?php
					/*if (!$db_version)
					{
						echo '<tr><td>Version:</td><td>';
						$db_version = isset($db_version) ? $db_version : $GLOBALS['GO_CONFIG']->db_version;
						echo '<input type="text" size="4" maxlength="4" name="db_version" value="'.$db_version.'" /></td></tr>';
					}else
					{
						echo '<input type="hidden" name="db_version" value="'.$db_version.'" />';
					}*/
				?>
					<tr>
					<td colspan="2" align="right">
					<?php echo '<input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task=database_connection\';" value="Back" />&nbsp;&nbsp;'; ?>
					<input type="submit" value="Continue" />
					&nbsp;&nbsp;
					</td>
					</tr>
					</table>
					<?php
			}else if($is_old_go)
			{
				?>
				<?php echo $GLOBALS['GO_CONFIG']->product_name; ?> has detected an older version of <?php echo $GLOBALS['GO_CONFIG']->product_name; ?>. The installer can't automatically upgrade this database.
				<a href="../INSTALL.TXT">Read this for upgrade instructions</a>
				<?php
			}else
			{
				echo 	'<input type="hidden" name="task" value="database_structure" />';

				echo $GLOBALS['GO_CONFIG']->product_name.' succesfully connected to your database!<br />'.
				'Click on \'Continue\' to create the tables for the '.$GLOBALS['GO_CONFIG']->product_name.' '.
				'base system. This can take some time. Don\'t interupt this process.<br /><br />';

				echo '<div align="right"><input type="submit" value="Continue" /></div>';
			}
		}
		print_foot();
		exit();
		break;

	case 'upgrade':
		print_head();
		echo 	'<input type="hidden" name="task" value="title" />';

		$db = new db();
		$db->halt_on_error = 'no';

		if (!$db->connect($GLOBALS['GO_CONFIG']->db_name, $GLOBALS['GO_CONFIG']->db_host, $GLOBALS['GO_CONFIG']->db_user, $GLOBALS['GO_CONFIG']->db_pass))
		{
			print_head();
			echo 'Can\'t connect to database!';
			echo '<br /><br />Correct this and refresh this page.';
		}else
		{
			$GLOBALS['GO_MODULES']->load_modules();
			require('upgrade.php');
			echo '<div align="right"><input type="button" value="Continue" onclick="javascript:document.location=\''.$_SERVER['PHP_SELF'].'?task=default_module_access\';" /></div>';
		}
		print_foot();
		exit();

		break;

		//the title of Group-Office
	case 'title':
		print_head();
		if (isset($feedback))
		{
			echo $feedback.'<br /><br />';
		}
	?>
		<input type="hidden" name="task" value="title" />
		Enter a title for your <?php echo $GLOBALS['GO_CONFIG']->product_name; ?> and webmaster email address for your application.<br />
		The email address will receive information about new registered users.
		<br /><br />
		<table>
		<tr>
		<td>Title:</td>
		</tr>
		<tr>
		<?php
		if(empty($GLOBALS['GO_CONFIG']->title))
			$GLOBALS['GO_CONFIG']->title=$GLOBALS['GO_CONFIG']->product_name;

		$title = isset($_POST['title']) ? $_POST['title'] : $GLOBALS['GO_CONFIG']->title;
		$webmaster_email = isset($_POST['webmaster_email']) ? $_POST['webmaster_email'] : $GLOBALS['GO_CONFIG']->webmaster_email;
	?>
		<td><input type="text" size="50" name="title" value="<?php echo $title; ?>" /></td>
		</tr>
		<tr>
		<td>
		Webmaster E-mail:
		</td>
		</tr>
		<tr>
		<td>
		<input type="text" size="50" name="webmaster_email" value="<?php echo $webmaster_email; ?>" />
		</td>
		</tr>
		</table><br />
		<div align="right">
		<?php echo '<input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task='.$lasttask.'\';" value="Back" />&nbsp;&nbsp;'; ?>
		<input type="submit" value="Continue" />
		</div>
		<?php
		print_foot();
		break;

	case 'url':
		print_head();
		if (isset($feedback))
		{
			echo $feedback.'<br /><br />';
		}
	?>
		<input type="hidden" name="task" value="url" />
		Enter a relative and an absolute url.<br /><br />
		<font color="#003399"><i>
		Example:<br />
		Relative URL: /groupoffice/<br />
		Absolute URL: http://www.intermesh.nl/groupoffice/</i>
		</font>
		<br /><br />
		<table width="100%" style="border-width: 0px;font-family: Arial,Helvetica; font-size: 12px;">
		<tr>
		<td>
		Relative URL:
		</td>
		<td>
		<?php
		$host = isset($_POST['host']) ? $_POST['host'] : $GLOBALS['GO_CONFIG']->host;
	?>
		<input type="text" size="40" name="host" value="<?php echo $host; ?>" />
		</td>
		</tr>
		</table><br />
		<div align="right">
		<?php echo '<input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task='.$lasttask.'\';" value="Back" />&nbsp;&nbsp;'; ?>
		<input type="submit" value="Continue" />
		</div>
		<?php
		print_foot();
		exit();
		break;
		//database structure exists now and is up to date
		//now we get the userdir

	case 'userdir':
		print_head();
		if (isset($feedback))
		{
			echo $feedback.'<br /><br />';
		}
	?>
		<input type="hidden" name="task" value="userdir" />
		<table>
		<tr>
			<td colspan="2">
			<?php echo $GLOBALS['GO_CONFIG']->product_name; ?> needs a place to store protected data. This folder should not be accessible through the webserver. Create a writable path for this purpose now and enter it in the box below.<br />
			The path should be have 0777 permissions or should be owned by the webserver user. You probably need to be root to do the last.
			<br />Also enter a maximum number of bytes to upload and a valid octal value for the file permissions.
			<br /><br />
			<font color="#003399"><i>
			$ su<br />
			$ mkdir /home/groupoffice<br />
			$ chown apache:apache /home/groupoffice<br />
			</i></font>
			<br /><br />
			</td>
		</tr>
		<tr>
		<?php
		$userdir = isset($_POST['userdir']) ? $_POST['userdir'] : $GLOBALS['GO_CONFIG']->file_storage_path;
		?>
		<td>Protected files directory:</td>
		<td><input type="text" size="50" name="userdir" value="<?php echo $userdir; ?>" /></td>
		</tr>
		<tr>
		<td>
		Maximum upload size:
		</td>
		<td>
		<input type="text" size="50" name="max_file_size" value="<?php
		$max_ini = return_bytes(ini_get('upload_max_filesize'));
		if($GLOBALS['GO_CONFIG']->max_file_size > $max_ini)
		{
			$GLOBALS['GO_CONFIG']->max_file_size = $max_ini;
		}
		echo $GLOBALS['GO_CONFIG']->max_file_size; ?>"  />
		(Current PHP configuration allows <?php echo $max_ini; ?> bytes)
		</td>
		</tr>
		
		<tr>
			<td colspan="2">
			<br /><br />
		<?php echo $GLOBALS['GO_CONFIG']->product_name; ?> needs a place to store temporary data such as session data or file uploads. Create a writable path for this purpose now and enter it in the box below.<br />
		The /tmp directory is a good option.
		<br /><br />
		</td>
	</tr>
	<tr>
		<td>Temporary files directory:</td>
		<?php
		$tmpdir = isset($_POST['tmpdir']) ? $_POST['tmpdir'] : $GLOBALS['GO_CONFIG']->tmpdir;
		?>
		<td><input type="text" size="50" name="tmpdir" value="<?php echo $tmpdir; ?>" /></td>
		</tr>
		</table><br />

		<div align="right">
		<?php echo '<input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task='.$lasttask.'\';" value="Back" />&nbsp;&nbsp;'; ?>
		<input type="submit" value="Continue" />
		</div>
		<?php
		print_foot();
		exit();
		break;



	case 'theme':
		print_head();
		if (isset($feedback))
		{
			echo $feedback.'<br /><br />';
		}
	?>
		<input type="hidden" name="task" value="theme" />
		Select default regional settings for <?php echo $GLOBALS['GO_CONFIG']->product_name; ?>. If your language is not in the list please select the closest match.<br />
		It would be nice if you added your missing language to the language/languages.inc file and send it to
		info@intermesh.nl!
		<br /><br />

		<table>
		<tr>
			<td>Language:</td>
			<td><select name="language">
			<?php
			require($GLOBALS['GO_CONFIG']->root_path.'language/languages.inc.php');

			foreach($languages as $key => $language)
			{
				echo '<option value="'.$key.'"';
				if($key==$GLOBALS['GO_CONFIG']->language)
				{
					echo ' selected';
				}
				echo '>'.$language.'</option>';
			}
			?></select></td>
		</tr>
		<tr>
			<td>Country:</td>
			<td>
			<select name="default_country">
			<?php
			require($GLOBALS['GO_LANGUAGE']->get_base_language_file('countries'));
			asort($countries);
			
			foreach($countries as $key => $country)
			{
				echo '<option value="'.$key.'"';
				if($key==$GLOBALS['GO_CONFIG']->default_country)
				{
					echo ' selected';
				}
				echo '>'.$country.'</option>';
			}
			?></select>
			</td>
		</tr>
		
		<tr>
			<td>Timezone:</td>
			<td>
			<select name="default_timezone">
			<?php

			$timezone_identifiers = DateTimeZone::listIdentifiers();
			foreach($timezone_identifiers as $timezone)
			{
				echo '<option value="'.$timezone.'"';
				if($timezone==$GLOBALS['GO_CONFIG']->default_timezone)
				{
					echo ' selected';
				}
				echo '>'.$timezone.'</option>';
			}
			?></select>
			</td>
		</tr>
		<tr><td><br /></td></tr>

		<tr>
			<td>Date format:</td>
			<td>
			<select name="default_date_format">
			<?php

			foreach($GLOBALS['GO_CONFIG']->date_formats as $format)
			{
				$friendly[strpos($format, 'Y')]='Year';
				$friendly[strpos($format, 'm')]='Month';
				$friendly[strpos($format, 'd')]='Day';

				$strFriendly = $friendly[0].$GLOBALS['GO_CONFIG']->default_date_separator.
				$friendly[1].$GLOBALS['GO_CONFIG']->default_date_separator.
				$friendly[2];

				echo '<option value="'.$format.'"';
				if($format==$GLOBALS['GO_CONFIG']->default_date_format)
				{
					echo ' selected';
				}
				echo '>'.$strFriendly.'</option>';
			}
			?></select>
			</td>
		</tr>
		<tr>
			<td>Date separator:</td>
			<td>
			<select name="default_date_separator">
			<?php

			foreach($GLOBALS['GO_CONFIG']->date_separators as $ds)
			{
				echo '<option value="'.$ds.'"';
				if($ds==$GLOBALS['GO_CONFIG']->default_date_separator)
				{
					echo ' selected';
				}
				echo '>'.$ds.'</option>';
			}
			?></select>
			</td>
		</tr>

		<tr>
			<td>Time format:</td>
			<td>
			<select name="default_time_format">
			<?php

			foreach($GLOBALS['GO_CONFIG']->time_formats as $tf)
			{
				echo '<option value="'.$tf.'"';
				if($tf==$GLOBALS['GO_CONFIG']->default_time_format)
				{
					echo ' selected';
				}
				echo '>'.$tf.'</option>';
			}
			?></select>
			</td>
		</tr>

		<tr>
			<td>'First day of the week:</td>
			<td>
			<select name="default_first_weekday">
			<?php
			echo '<option value="0"';
			if($GLOBALS['GO_CONFIG']->default_first_weekday=='0')
				echo ' selected';

			echo '>Sunday</option>';
			echo '<option value="1"';
			if($GLOBALS['GO_CONFIG']->default_first_weekday=='1')
				echo ' selected';

			echo '>Monday</option>';
			?></select>
			</td>
		</tr>


		<tr>
			<td>Thousands separator:</td>
			<td><input name="default_thousands_separator" maxlength="1" type="text" value="<?php echo $GLOBALS['GO_CONFIG']->default_thousands_separator; ?>" /></td>
		</tr>


		<tr>
			<td>Decimal separator:</td>
			<td><input name="default_decimal_separator" maxlength="1" type="text" value="<?php echo $GLOBALS['GO_CONFIG']->default_decimal_separator; ?>" /></td>
		</tr>


		<tr>
			<td>Currency:</td>
			<td><input name="default_currency" type="text" value="<?php echo $GLOBALS['GO_CONFIG']->default_currency; ?>" /></td>
		</tr>
		<tr>
		<td>Default theme:</td>
		<td>
		<select name="theme">
			<?php
			require_once($GLOBALS['GO_CONFIG']->class_path.'base/theme.class.inc.php');
			$GO_THEME = new GO_THEME();


			$themes = $GLOBALS['GO_THEME']->get_themes();
			foreach($themes as $theme)
			{

				echo '<option value="'.$theme.'"';
				if($theme==$GLOBALS['GO_CONFIG']->theme)
				{
					echo ' selected';
				}
				echo '>'.$theme.'</option>';
			}
			?></select>
		</td>
		</tr>
		</table><br />
		<div align="right">
		<?php echo '<input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task='.$lasttask.'\';" value="Back" />&nbsp;&nbsp;'; ?>
		<input type="submit" value="Continue" />
		</div>
		<?php
		print_foot();
		exit();
		break;

	case 'allow_password_change':
		print_head();
		if (isset($feedback))
		{
			echo $feedback.'<br /><br />';
		}
	?>
		<input type="hidden" name="task" value="allow_password_change" />
		<input type="checkbox" name="allow_themes" value="1" <?php if(isset($_POST['allow_themes']) ? true : $GLOBALS['GO_CONFIG']->allow_themes) echo 'checked'; ?> />Allow users to change the theme
		<br />
		<input type="checkbox" name="allow_password_change" value="1" <?php if(isset($_POST['allow_password_change']) ? true : $GLOBALS['GO_CONFIG']->allow_password_change) echo 'checked'; ?> />Allow users to change their password
		<br />
		<input type="checkbox" name="allow_registration" value="1" <?php if(isset($_POST['allow_registration']) ? true : $GLOBALS['GO_CONFIG']->allow_registration) echo 'checked'; ?> />Allow anybody to register
		<br />
		<input type="checkbox" name="auto_activate_accounts" value="1" <?php if(isset($_POST['auto_activate_accounts']) ? true : $GLOBALS['GO_CONFIG']->auto_activate_accounts) echo 'checked'; ?> />Automatically activate accounts. If not the administrator needs to confirm them
		<br />
		<input type="checkbox" name="notify_admin_of_registration" value="1" <?php if(isset($_POST['notify_admin_of_registration']) ? true : $GLOBALS['GO_CONFIG']->notify_admin_of_registration) echo 'checked'; ?> />Notify the administrator of new accounts
		<?php
		echo '<p>The following user data fields can be enabled or disabled in the registration form.</p>';

		$available_fields = explode(',', 'title_initials,sex,birthday,address,home_phone,fax,cellular,company,department,function,work_address,work_phone,work_fax,homepage');
		$enabled_fields = explode(',',$GLOBALS['GO_CONFIG']->registration_fields);
		$required_fields = explode(',',$GLOBALS['GO_CONFIG']->required_registration_fields);

		$names['title_initials']='Title/Initials';
		$names['sex']='Sex';
		$names['birthday']='Birthday';
		$names['address']='Home address';
		$names['home_phone']='Home phone';
		$names['fax']='Fax';
		$names['cellular']='Cellular';
		$names['company']='Company';
		$names['department']='Department';
		$names['function']='Function';
		$names['work_address']='Work address';
		$names['work_phone']='Work phone';
		$names['work_fax']='Work fax';
		$names['homepage']='Homepage';

		echo '<table><tr><td><b>Field</b></td><td><b>Enable</b></td><td><b>Required</b></td></tr>';
		foreach($available_fields as $field)
		{
			echo '<tr><td>'.$names[$field].'</td><td><input type="checkbox" name="registration_fields[]" value="'.$field.'"';
			if(in_array($field, $enabled_fields))
			{
				echo ' checked';
			}
			echo ' /></td><td><input type="checkbox" name="required_registration_fields[]" value="'.$field.'"';
			if(in_array($field, $required_fields))
			{
				echo ' checked';
			}
			echo ' /></td></tr>';
		}
		echo '</table>';
		?>
		<br />
		<div align="right">
		<?php echo '<input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task='.$lasttask.'\';" value="Back" />&nbsp;&nbsp;'; ?>
		<input type="submit" value="Continue" />
		</div>
		<?php
		print_foot();
		exit();
		break;


	case 'default_module_access':

		print_head();
		if (isset($feedback))
		{
			echo $feedback.'<br /><br />';
		}

		echo '<input type="hidden" name="task" value="default_module_access" />';
		?>
		<p>Restricted functions</p>
		<input type="checkbox" name="allow_themes" value="1" <?php if(isset($_POST['allow_themes']) ? true : $GLOBALS['GO_CONFIG']->allow_themes) echo 'checked'; ?> />Allow users to change the theme
		<br />
		<input type="checkbox" name="allow_password_change" value="1" <?php if(isset($_POST['allow_password_change']) ? true : $GLOBALS['GO_CONFIG']->allow_password_change) echo 'checked'; ?> />Allow users to change their password

		<?php
		echo '<p>New users will automatically have access to the following modules</p>';


		echo '<table><tr><td><b>Module</b></td><td><b>Use</b></td><td><b>Manage</b></td></tr>';

		$modules_read = isset($_POST['register_modules_read']) ? $_POST['register_modules_read'] : explode(',', $GLOBALS['GO_CONFIG']->register_modules_read);
		$modules_write = isset($_POST['register_modules_write']) ? $_POST['modules_write'] : explode(',', $GLOBALS['GO_CONFIG']->register_modules_write);

		$module_count = $GLOBALS['GO_MODULES']->get_modules('0');
		while($GLOBALS['GO_MODULES']->next_record())
		{
			//require language file to obtain module name in the right language
			$language_file = $GLOBALS['GO_LANGUAGE']->get_language_file($GLOBALS['GO_MODULES']->f('id'));

			if(file_exists($language_file))
			{
				require_once($language_file);
			}

			$lang_var = isset($lang[$GLOBALS['GO_MODULES']->f('id')]['name']) ? $lang[$GLOBALS['GO_MODULES']->f('id')]['name'] : $GLOBALS['GO_MODULES']->f('id');


			echo '<tr><td>'.$lang_var.'</td><td>';			
			$read_check = in_array($GLOBALS['GO_MODULES']->f('id'), $modules_read);
			$write_check = in_array($GLOBALS['GO_MODULES']->f('id'), $modules_write);

			echo '<input type="checkbox" name="register_modules_read[]" value="'.$GLOBALS['GO_MODULES']->f('id').'"';
			if($read_check)
			{
				echo ' checked';
			}
			echo ' /></td><td><input type="checkbox" name="register_modules_write[]" value="'.$GLOBALS['GO_MODULES']->f('id').'"';
			if($write_check)
			{
				echo ' checked';
			}
			echo ' /></td></tr>';
		}

		echo '</table>';
		?>
		<br />
		<div align="right">
		<?php echo '<input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task='.$lasttask.'\';" value="Back" />&nbsp;&nbsp;'; ?>
		<input type="submit" value="Continue" />
		</div>
		<?php
		print_foot();
		exit();
		break;

	case 'default_groups':

		print_head();
		if (isset($feedback))
		{
			echo $feedback.'<br /><br />';
		}

		echo '<p>New users will automatically be "member of"/"visible to" the selected groups.</p>';

		echo '<input type="hidden" name="task" value="default_groups" />';


		require_once($GLOBALS['GO_CONFIG']->class_path.'base/groups.class.inc.php');
		$GO_GROUPS = new GO_GROUPS();


		$GO_GROUPS->get_groups();

		$register_user_groups = explode(',',$GLOBALS['GO_CONFIG']->register_user_groups);
		$register_visible_user_groups = explode(',',$GLOBALS['GO_CONFIG']->register_visible_user_groups);


		echo '<table><tr><td><b>Group</b></td><td><b>Member</b></td><td><b>Visible</b></td></tr>';

		while($GO_GROUPS->next_record())
		{
			echo '<tr><td>'.$GO_GROUPS->f('name').'</td><td>';

			echo '<input type="checkbox" name="register_user_groups[]" value="'.$GLOBALS['GO_MODULES']->f('name').'"';
			if($GO_GROUPS->f('id')==$GLOBALS['GO_CONFIG']->group_everyone)
			{
				echo ' checked disabled';
			}elseif(in_array($GO_GROUPS->f('name'), $register_user_groups) || $GO_GROUPS->f('id')==$GLOBALS['GO_CONFIG']->group_internal)
			{
				echo ' checked';
			}
			echo ' /></td><td>';

			echo '<input type="checkbox" name="register_visible_user_groups[]" value="'.$GLOBALS['GO_MODULES']->f('name').'"';
			if($GO_GROUPS->f('id')==$GLOBALS['GO_CONFIG']->group_root)
			{
				echo ' checked disabled';
			}elseif(in_array($GO_GROUPS->f('name'), $register_visible_user_groups) || $GO_GROUPS->f('id')==$GLOBALS['GO_CONFIG']->group_internal)
			{
				echo ' checked';
			}
			echo ' /></td></tr>';
		}
		echo '</table>';
		?>
		<br />
		<div align="right">
		<?php echo '<input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task='.$lasttask.'\';" value="Back" />&nbsp;&nbsp;'; ?>
		<input type="submit" value="Continue" />
		</div>
		<?php
		print_foot();
		exit();
		break;


	case 'smtp':
		print_head();
		if (isset($feedback))
		{
			echo $feedback.'<br /><br />';
		}
	?>
		<input type="hidden" name="task" value="smtp" />
		<?php echo $GLOBALS['GO_CONFIG']->product_name; ?> needs to connect to an SMTP server to send and receive e-mail.
		<br />
		<br />
		<table>
		<tr>
		<td>
		SMTP server:
		</td>
		<td>
		<input type="text" size="40" name="smtp_server" value="<?php echo $GLOBALS['GO_CONFIG']->smtp_server; ?>"  />
		</td>
		</tr>
		<tr>
		<td>
		SMTP port:
		</td>
		<td>
		<input type="text" size="40" name="smtp_port" value="<?php echo $GLOBALS['GO_CONFIG']->smtp_port; ?>" />
		</td>
		</tr>

		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
		<td colspan="2">
		If your SMTP server requires authentication please fill in the username and password.
		</td>
		</tr>

		<tr>
		<td>
		SMTP username:
		</td>
		<td>
		<input type="text" size="40" name="smtp_username" value="<?php echo $GLOBALS['GO_CONFIG']->smtp_username; ?>" />
		</td>
		</tr>
		<tr>
		<td>
		SMTP password:
		</td>
		<td>
		<input type="text" size="40" name="smtp_password" value="<?php echo $GLOBALS['GO_CONFIG']->smtp_password; ?>" />
		</td>
		</tr>

		<tr>
		<td>
		Encryption:
		</td>
		<td>
		<select name="smtp_encryption">
			<?php

			$encryptions = array(
				''=>'No encryption',
				'ssl'=>'SSL',
				'tls'=>'TLS');

			foreach($encryptions as $key => $value)
			{
				echo '<option value="'.$key.'"';
				if($key==$GLOBALS['GO_CONFIG']->smtp_encryption)
				{
					echo ' selected';
				}
				echo '>'.$value.'</option>';
			}
			?></select>


		<tr><td colspan="2">&nbsp;</td></tr>

		<tr>
		<td valign="top">

		Maximum size of attachments:
		</td>
		<td>
		<input type="text" size="40" name="max_attachment_size" value="<?php
		$max_ini  = return_bytes(ini_get('upload_max_filesize'));
		if($GLOBALS['GO_CONFIG']->max_attachment_size > $max_ini) $GLOBALS['GO_CONFIG']->max_attachment_size = $max_ini;
		echo $GLOBALS['GO_CONFIG']->max_attachment_size; ?>" /><br />
		Current PHP configuration allows <?php echo $max_ini; ?> bytes
		</td>
		</tr>
		</table><br />
		<div align="right">
		<?php echo '<input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task='.$lasttask.'\';" value="Back" />&nbsp;&nbsp;'; ?>
		<input type="submit" value="Continue" />
		</div>
		<?php
		print_foot();
		exit();
		break;


		//check if we need to add the administrator account

	case 'administrator':

		print_head();
		if (isset($feedback))
		{
			echo $feedback.'<br /><br />';
		}
		?>
			<input type="hidden" name="task" value="administrator" />
			<?php echo $GLOBALS['GO_CONFIG']->product_name; ?> needs an administrator account. Please create one now.
			<br /><br />
			<table style="border-width: 0px;font-family: Arial,Helvetica; font-size: 12px;">
			<tr>
			<td>Username:</td>
			<td>
			<?php
			$username = isset($_POST['username']) ? (htmlspecialchars($_POST['username'])) : '';
		?>
			<input name="username" type="text" value="<?php echo $username; ?>" />
			</tr>
			<tr>
			<td>
			Password:
			</td>
			<td>
			<input type="password" name="pass1" />
			</td>
			</tr>
			<tr>
			<td>
			Confirm password:
			</td>
			<td>
			<input type="password" name="pass2" />
			</td>
			</tr>
			<tr>
			<td>
			E-mail:
			</td>
			<td>
			<?php $email = isset($email)? $email : $GLOBALS['GO_CONFIG']->webmaster_email;?>
			<input type="text" size="40" name="email" value="<?php echo $email; ?>" />
			</td>
			</tr>
			</table><br />
			<div align="right">
			<?php echo '<input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task='.$lasttask.'\';" value="Back" />&nbsp;&nbsp;'; ?>
			<input type="submit" value="Continue" />
			</div>
			<?php
			print_foot();
			exit();
			break;


	case 'completed':

		@session_destroy();

		print_head();
	?>
	<h1>Installation complete!</h1>
	<br />
	Please make sure '<?php echo $CONFIG_FILE; ?>' is not writable anymore now.<br />
	<br />
	<font color="#003399"><i>
	$ chmod 644 <?php echo $CONFIG_FILE; ?>
	</i></font>
	<br />
	<br />
	If you don't have shell access then you should download <?php echo $CONFIG_FILE; ?>, delete <?php echo $CONFIG_FILE; ?>
	from the server and upload it back to the server. This way you change the ownership to your account.
	<br />
	<br />
	Don't use the administrator account for regular use! Only use it for administrative tasks.
	<br />
	Read this to get started with <?php echo $GLOBALS['GO_CONFIG']->product_name; ?>: <a href="http://www.group-office.com/wiki/Getting_started" target="_blank">http://www.group-office.com/wiki/Getting_started</a>
	<ul>
	<li>Navigate to the menu: Administrator menu -&gt; Modules and remove the modules you do not wish to use.</li>
	<li>Navigate to the menu: Administrator menu -&gt; User groups and create user groups.</li>
	<li>Navigate to the menu: Administrator menu -&gt; Users users to add new users.</li>
	</ul>
	<br />
	You can also configure external authentication servers such as an LDAP,IMAP or POP-3 server.
	Read more about it here: <a target="_blank" href="http://www.group-office.com/wiki/IMAP_or_LDAP_authentication">http://www.group-office.com/wiki/IMAP_or_LDAP_authentication</a>
	<br />
	<br />
	For troubleshooting please visit <a target="_blank" href="http://www.group-office.com/wiki/Troubleshooting">http://www.group-office.com/wiki/Troubleshooting</a><br />
	If that doesn't help post on the <a target="_blank" href="http://www.group-office.com/forum/">forums</a>.<br />
	<br /><br />
	<div align="right">
	<?php echo '<input type="button" onclick="document.location=\''.$_SERVER['PHP_SELF'].'?task='.$lasttask.'\';" value="Back" />&nbsp;&nbsp;'; ?>
	<input type="button" value="Launch <?php echo $GLOBALS['GO_CONFIG']->product_name; ?>!" onclick="javascript:window.location='<?php echo $GLOBALS['GO_CONFIG']->host; ?>';" />
	</div>
	<?php
	print_foot();
	break;
}
