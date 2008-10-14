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

//config file exists now so require it to get the properties.
require_once('../Group-Office.php');


$CONFIG_FILE = $GO_CONFIG->get_config_file();

require_once('install.inc');
require_once(dirname(dirname(__FILE__)).'/classes/filesystem.class.inc');

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : 'test';

if($task=='test')
	@session_destroy();

$tasks[] = 'test';
$tasks[] = 'license';
$tasks[] = 'release_notes';
$tasks[] = 'title';
$tasks[] = 'theme';
$tasks[] = 'url';
$tasks[] = 'userdir';
$tasks[] = 'new_database';
$tasks[] = 'create_database';
$tasks[] = 'database_connection';
$tasks[] = 'database_structure';
//$tasks[] = 'allow_password_change';
$tasks[] = 'default_module_access';
$tasks[] = 'default_groups';
$tasks[] = 'smtp';
$GO_USERS->Halt_On_Error='no';
/*
if ($task != 'database_structure' && !empty($GO_CONFIG->db_name) && !$GO_USERS->get_user(1))
{
	$tasks[] = 'administrator';
	//$tasks[] = 'send_info';
}
*/
$tasks[] = 'completed';

$menu_language['test'] = 'System test';
$menu_language['license'] = 'License';
$menu_language['release_notes'] = 'Release notes';
$menu_language['new_database'] = 'Database configuration';
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
	echo '<html><head>'.
	'<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />'.
	'<link href="install.css" rel="stylesheet" type="text/css" />'.
	'<title>Group-Office Installation</title>'.
	'</head>'.
	'<body style="font-family: Arial,Helvetica;">';
	echo '<form method="post" action="index.php">';
	echo '<table width="100%" cellpadding="0" cellspacing="0">';
	echo '<tr><td style="border-bottom:1px solid black;"><img src="../themes/Default/images/groupoffice.gif" border="0" align="middle" style="margin:10px" /></td>';
	echo '<td style="border-bottom:1px solid black;text-align:right;padding-right:10px;"><h1>Group-Office installation</h1></td></tr>';
	echo '<tr><td valign="top" style="">';

	foreach($GLOBALS['tasks'] as $task)
	{
		$class = $task == $GLOBALS['task'] ? 'menu_active' : 'menu';
		if(isset($GLOBALS['menu_language'][$task]))
		{
			echo '<a class="'.$class.'" href="'.$_SERVER['PHP_SELF'].'?task='.$task.'">'.$GLOBALS['menu_language'][$task].'</a>';
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
$config_location2 = $GO_CONFIG->root_path.'config.php';

if($task !='test')
{
	if(!file_exists($CONFIG_FILE))
	{
		print_head();
		echo '<input type="hidden" name="task" value="license" />';
		echo 'The configuration file does not exist. You must create an empty writable file at one of the following locations:<br />';
		echo '<ol><li>'.$config_location1.'</li>';
		echo '<li>'.$config_location2.'</li></ol></i></font>';
		echo 'The first location is more secure because the sensitive information is kept outside the document root but it does require root privileges on this machine.<br />The second advantage is that you will be able to seperate the source from the configuration. This can be very usefull with multiple installations on one machine.';
		echo ' If you choose this location then you have to make sure that in Apache\'s httpd.conf the following is set:<br /><br />';
		echo '<font color="#003399">';
		echo '<i>UseCanonicalName On</i></font><br />';
		echo 'This is to make sure it always finds your configuration file at the correct location.';
		echo '<br /><br /><font color="#003399">';
		echo '<i>$ touch config.php (Or FTP an empty config.php to the server)<br />';
		echo '$ chmod 666 config.php</i></font>';
		echo '<br /><br />If it does exist and you still see this message then it might be that safe_mode is enabled and the config.php is owned by another user then the Group-Office files.';		
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
		/*case 'administrator':
			$pass1=trim($_POST['pass1']);
			$pass2=trim($_POST['pass2']);
			$email=trim($_POST['email']);
			$username=trim($_POST['username']);


			if ($pass1 == '' || $username=='')
			{
				$feedback = '<font color="red">Please enter a password and a username!</font>';
			}elseif(!preg_match('/^[a-z0-9_-]*$/', $username))
			{
				$feedback = 'Invalid username. Only these charachters are allowed: a-z, 0-9,- en _';
			}elseif( strlen($pass1) < 4)
			{
				$feedback = '<font color="red">Password can\'t be shorter then 4 characters!</font>';
			}elseif($pass1 != $pass2)
			{
				$feedback = '<font color="red">Passwords did not match!</font>';
			}elseif(!String::validate_email( $email ))
			{
				$feedback = '<font color="red">Invalid E-mail address!</font>';
			}else
			{
				$GO_USERS->get_users();
				$user['id'] = $GO_USERS->nextid("go_users");

				$GO_GROUPS->query("DELETE FROM go_db_sequence WHERE seq_name='groups'");
				$GO_GROUPS->query("DELETE FROM go_groups");

				$admin_group_id = $GO_GROUPS->add_group($user['id'], addslashes($lang['group_admins']));
				$everyone_group_id = $GO_GROUPS->add_group($user['id'], addslashes($lang['group_everyone']));
				$internal_group_id = $GO_GROUPS->add_group($user['id'], addslashes($lang['group_internal']));
				
				$user_groups = array($admin_group_id, $everyone_group_id, $internal_group_id);
				
								
				//$user['language'] = $GO_LANGUAGE->language;
				$user['language'] = $GO_LANGUAGE->language;
				$user['first_name']='Group-Office';
				$user['middle_name']='';
				$user['last_name']=$lang['common']['admin'];
				$user['username'] = smart_addslashes($username);
				$user['password'] = smart_addslashes($pass1);
				$user['email'] = smart_addslashes($email);
				$user['sex'] = 'M';

				$GO_USERS->add_user($user,$user_groups,array($GO_CONFIG->group_everyone));

				$old_umask = umask(000);
				filesystem::mkdir_recursive($GO_CONFIG->file_storage_path.'users/'.smart_stripslashes($username), $GO_CONFIG->create_mode);
				umask($old_umask);

				$task = $nexttask;
				
				
				
			}
			break;*/

		case 'post_database_connection':
			$task = 'database_connection';
			$db = new db();
			$db->Halt_On_Error = 'no';

			$GO_CONFIG->db_host = smart_stripslashes($_POST['db_host']);
			$GO_CONFIG->db_name = smart_stripslashes($_POST['db_name']);
			$GO_CONFIG->db_user = smart_stripslashes($_POST['db_user']);
			$GO_CONFIG->db_pass = smart_stripslashes($_POST['db_pass']);

			if(@$db->connect($GO_CONFIG->db_name,
			$GO_CONFIG->db_host,
			$GO_CONFIG->db_user,
			$GO_CONFIG->db_pass))
			{

				if (save_config($GO_CONFIG))
				{
					$task = 'database_structure';
				}
			}else
			{
				$feedback ='<font color="red">Failed to connect to database</font>';
			}
			break;

		case 'database_structure':
			$db = new db();
			$db->Halt_On_Error = 'report';

			if (!$db->connect($GO_CONFIG->db_name, $GO_CONFIG->db_host, $GO_CONFIG->db_user, $GO_CONFIG->db_pass))
			{
				print_head();
				echo 'Can\'t connect to database!';
				echo '<br /><br />Correct this and refresh this page.';
				print_foot();
				exit();
			}else
			{
				//create new empty database
				//table is empty create the structure
				$queries = String::get_sql_queries($GO_CONFIG->root_path."install/sql/groupoffice.sql");
				//$queries = get_sql_queries($GO_CONFIG->root_path."lib/sql/groupoffice.sql");
				while ($query = array_shift($queries))
				{
					$db->query($query);
				}				
				
				require($GO_CONFIG->root_path."install/sql/updates.inc.php");
				//store the version number for future upgrades
				$GO_CONFIG->save_setting('version', count($updates));
				
				
				
				$user['id'] = $GO_USERS->nextid("go_users");

				$GO_GROUPS->query("DELETE FROM go_db_sequence WHERE seq_name='groups'");
				$GO_GROUPS->query("DELETE FROM go_groups");

				$admin_group_id = $GO_GROUPS->add_group($user['id'], addslashes($lang['common']['group_admins']));
				$everyone_group_id = $GO_GROUPS->add_group($user['id'], addslashes($lang['common']['group_everyone']));
				$internal_group_id = $GO_GROUPS->add_group($user['id'], addslashes($lang['common']['group_internal']));
				
				$user_groups = array($admin_group_id, $everyone_group_id, $internal_group_id);
				
				
				
				$GO_MODULES->load_modules();
				
				$fs = new filesystem();
			
				//install all modules
				$module_folders = $fs->get_folders($GO_CONFIG->root_path.'modules/');				
				
				$available_modules=array();
				foreach($module_folders as $folder)
				{
					$available_modules[]=$folder['name'];
				}				
				$priority_modules=array('summary','email','calendar','tasks','addressbook','files', 'notes', 'projects');

				for($i=0;$i<count($priority_modules);$i++)
				{
					if(in_array($priority_modules[$i], $available_modules))
					{
						$GO_MODULES->add_module($priority_modules[$i]);
					}
				}
				for($i=0;$i<count($available_modules);$i++)
				{
					if(!in_array($available_modules[$i], $priority_modules))
					{
						$GO_MODULES->add_module($available_modules[$i]);
					}
				}
				
				$user['language'] = $GO_LANGUAGE->language;
				$user['first_name']='Group-Office';
				$user['middle_name']='';
				$user['last_name']=$lang['common']['admin'];
				$user['username'] = 'admin';
				$user['password'] = 'admin';
				$user['email'] = $GO_CONFIG->webmaster_email;
				$user['sex'] = 'M';
				$user['enabled']='1';
				$user['country']=$GO_CONFIG->default_country;
				$user['work_country']=$GO_CONFIG->default_country;

				$GO_USERS->add_user($user,$user_groups,array($GO_CONFIG->group_everyone));
				//filesystem::mkdir_recursive($GO_CONFIG->file_storage_path.'users/admin/');
				
				

				$task = $nexttask;
			}
			break;

		case 'userdir':
			$tmpdir=smart_stripslashes($_POST['tmpdir']);

			if (!is__writable($_POST['userdir']))
			{
				$feedback = '<font color="red">The path you entered is not writable.<br />Please correct this and try again.</font>';
			}elseif($_POST['max_file_size'] > return_bytes(ini_get('upload_max_filesize')))
			{
				$feedback = '<font color="red">You entered a greater upload size then the PHP configuration allows.<br />Please correct this and try again.</font>';
			}elseif (!is__writable($_POST['local_path']))
			{
				$feedback = '<font color="red">The local path you entered is not writable.<br />Please correct this and try again.</font>';
			}elseif (!is__writable($tmpdir))
			{
				$feedback = '<font color="red">The path you entered is not writable.<br />Please correct this and try again.</font>';
			}

			if (substr($_POST['userdir'], -1) != '/') $_POST['userdir'] = $_POST['userdir'].'/';
			$GO_CONFIG->file_storage_path=smart_stripslashes($_POST['userdir']);
			//$GO_CONFIG->create_mode=smart_stripslashes($_POST['create_mode']);
			$GO_CONFIG->max_file_size=smart_stripslashes($_POST['max_file_size']);

			if (substr($_POST['local_path'], -1) != '/') $_POST['local_path'] = $_POST['local_path'].'/';
			if (substr($_POST['local_url'], -1) != '/') $_POST['local_url'] = $_POST['local_url'].'/';

			$GO_CONFIG->local_path=smart_stripslashes($_POST['local_path']);
			$GO_CONFIG->local_url=smart_stripslashes($_POST['local_url']);

			if (substr($tmpdir, -1) != '/') $tmpdir = $tmpdir.'/';
			$GO_CONFIG->tmpdir=$tmpdir;
			
			
			//autodetect helper program locations
			
			$GO_CONFIG->cmd_zip = whereis('zip') ? whereis('zip') : '/usr/bin/zip';
			$GO_CONFIG->cmd_unzip = whereis('unzip') ? whereis('unzip') : '/usr/bin/unzip';
			$GO_CONFIG->cmd_tar = whereis('tar') ? whereis('tar') : '/bin/tar';
			$GO_CONFIG->cmd_chpasswd = whereis('chpasswd') ? whereis('chpasswd') : '/usr/sbin/chpasswd';
			$GO_CONFIG->cmd_sudo = whereis('sudo') ? whereis('sudo') : '/usr/bin/sudo';
			$GO_CONFIG->cmd_xml2wbxml = whereis('xml2wbxml') ? whereis('xml2wbxml') : '/usr/bin/xml2wbxml';
			$GO_CONFIG->cmd_wbxml2xml = whereis('wbxml2xml') ? whereis('wbxml2xml') : '/usr/bin/wbxml2xml';
			$GO_CONFIG->cmd_tnef = whereis('tnef') ? whereis('tnef') : '/usr/bin/tnef';
			
		

			
			

			if (save_config($GO_CONFIG) && !isset($feedback))
			{
				//check for userdirs
				$GO_USERS->get_users();
				while($GO_USERS->next_record())
				{
					if(!file_exists($GO_CONFIG->file_storage_path.'users/'.$GO_USERS->f('username')))
					{
						filesystem::mkdir_recursive($GO_CONFIG->file_storage_path.'users/'.$GO_USERS->f('username'));
					}
				}
				$task = $nexttask;
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
				$GO_CONFIG->webmaster_email = smart_stripslashes($_POST['webmaster_email']);
				$GO_CONFIG->title = smart_stripslashes($_POST['title']);
				if (save_config($GO_CONFIG))
				{
					$task = $nexttask;
				}
			}
			break;

		case 'url':
			$host = smart_stripslashes(trim($_POST['host']));
			$full_url = smart_stripslashes(trim($_POST['full_url']));
			if ($host != '' && $full_url != '')
			{
				if ($host != '/')
				{
					if (substr($host , -1) != '/') $host  = $host.'/';
					if (substr($host , 0, 1) != '/') $host  = '/'.$host;
				}

				if(substr($full_url,-1) != '/') $full_url = $full_url.'/';

				$GO_CONFIG->host = $host;
				$GO_CONFIG->full_url = $full_url;
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
			$GO_CONFIG->language = $_POST['language'];
			
			$GO_LANGUAGE->set_language($GO_CONFIG->language);
			
			$GO_CONFIG->theme = smart_stripslashes($_POST['theme']);
			
			$GO_CONFIG->default_country = smart_addslashes($_POST['default_country']);
			$GO_CONFIG->default_timezone = smart_addslashes($_POST['default_timezone']);
			$GO_CONFIG->default_currency = smart_addslashes($_POST['default_currency']);
			$GO_CONFIG->default_date_format = smart_addslashes($_POST['default_date_format']);
			$GO_CONFIG->default_date_seperator = smart_addslashes($_POST['default_date_seperator']);
			$GO_CONFIG->default_time_format = smart_addslashes($_POST['default_time_format']);
			$GO_CONFIG->default_first_weekday = smart_addslashes($_POST['default_first_weekday']);
			$GO_CONFIG->default_decimal_seperator = smart_addslashes($_POST['default_decimal_seperator']);
			$GO_CONFIG->default_thousands_seperator = smart_addslashes($_POST['default_thousands_seperator']);
			

			if (save_config($GO_CONFIG))
			{
				$task = $nexttask;
			}
			break;

		case 'allow_password_change':
			$GO_CONFIG->allow_registration = isset($_POST['allow_registration']) ? true : false;
			$GO_CONFIG->auto_activate_accounts = isset($_POST['auto_activate_accounts']) ? true : false;
			$GO_CONFIG->notify_admin_of_registration = isset($_POST['notify_admin_of_registration']) ? true : false;

			$GO_CONFIG->allow_password_change =  isset($_POST['allow_password_change']) ? true : false;
			$GO_CONFIG->allow_themes =  isset($_POST['allow_themes']) ? true : false;
			
			$GO_CONFIG->registration_fields = isset($_POST['registration_fields']) ? implode(',',$_POST['registration_fields']) : '';
			$GO_CONFIG->required_registration_fields = isset($_POST['required_registration_fields']) ? implode(',',$_POST['required_registration_fields']) : '';
			
			if (save_config($GO_CONFIG))
			{
				$task = $nexttask;
			}

			break;
			
		case 'default_module_access':
			
			$GO_CONFIG->allow_password_change =  isset($_POST['allow_password_change']) ? true : false;
			$GO_CONFIG->allow_themes =  isset($_POST['allow_themes']) ? true : false;
			
			
			$GO_CONFIG->register_modules_read = isset($_POST['register_modules_read']) ? implode(',',$_POST['register_modules_read']) : '';
			$GO_CONFIG->register_modules_write = isset($_POST['register_modules_write']) ? implode(',',$_POST['register_modules_write']) : '';
			
			if (save_config($GO_CONFIG))
			{
				$task = $nexttask;
			}

			break;
			
		case 'default_groups':
			
			$GO_CONFIG->register_user_groups = isset($_POST['register_user_groups']) ? implode(',',$_POST['register_user_groups']) : '';
			$GO_CONFIG->register_visible_user_groups = isset($_POST['register_visible_user_groups']) ? implode(',',$_POST['register_visible_user_groups']) : '';
			
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

			//$GO_CONFIG->mailer = $_POST['mailer'];
			$GO_CONFIG->smtp_port = isset($_POST['smtp_port']) ? smart_stripslashes(trim($_POST['smtp_port'])) : '';
			$GO_CONFIG->smtp_server= isset($_POST['smtp_server']) ? smart_stripslashes(trim($_POST['smtp_server'])) : '';

			$GO_CONFIG->smtp_username= isset($_POST['smtp_username']) ? smart_stripslashes(trim($_POST['smtp_username'])) : '';
			$GO_CONFIG->smtp_password= isset($_POST['smtp_password']) ? smart_stripslashes(trim($_POST['smtp_password'])) : '';


			$GO_CONFIG->max_attachment_size= smart_stripslashes(trim($_POST['max_attachment_size']));
			$GO_CONFIG->email_connectstring_options = smart_stripslashes(trim($_POST['email_connectstring_options']));
			if (save_config($GO_CONFIG) && !isset($feedback))
			{
				$task = $nexttask;
			}
			break;

		case 'send_info':
			if ($_REQUEST['info'] != 'no')
			{
				$body = "Group-Office title: ".$GO_CONFIG->title."\r\n";
				$body = "Group-Office version: ".$GO_CONFIG->version."\r\n";
				$body .= "Usage: ".$_REQUEST['info']."\r\n";
				$body .= "Users: ".$_REQUEST['users']."\r\n";
				$body .= "Host: ".$GO_CONFIG->full_url."\r\n";
				$body .= "Webmaster: ".$GO_CONFIG->webmaster_email."\r\n";
				if ($_REQUEST['email'] != '')
				{
					$body .= "Contact about Group-Office Professional at: ".$_REQUEST['email']."\r\n";
					$body .= "Name: ".$_REQUEST['name']."\r\n";
				}

				sendmail('notify@intermesh.nl', $GO_CONFIG->webmaster_email, $GO_CONFIG->title, "Group-Office usage information", $body);
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
				$GO_CONFIG->db_name = '';
				$GO_CONFIG->db_pass = '';
				$GO_CONFIG->db_user = '';
				$GO_CONFIG->db_host = '';

				$db = new db();
				$db->Halt_On_Error = 'no';

				$GO_CONFIG->db_host = smart_stripslashes($_POST['db_host']);
				$GO_CONFIG->db_name = smart_stripslashes($_POST['db_name']);
				$GO_CONFIG->db_user = smart_stripslashes($_POST['db_user']);
				$GO_CONFIG->db_pass = smart_stripslashes($_POST['db_pass1']);

				if(@$db->connect('mysql', smart_stripslashes($_POST['db_host']), smart_stripslashes($_POST['admin_user']), smart_stripslashes($_POST['admin_pass'])))
				{
					$sql = 'CREATE DATABASE `'.$_POST['db_name'].'`;';
					if($db->query($sql))
					{
						$sql = "GRANT ALL PRIVILEGES ON ".smart_addslashes($_POST['db_name']).".*	TO ".
						"'".$_POST['db_user']."'@'".smart_addslashes($_POST['host_allow'])."' ".
						"IDENTIFIED BY '".smart_addslashes($_POST['db_pass1'])."' WITH GRANT OPTION";
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
							'<b>MySQL Error</b>: '.$db->Errno.' '.$db->Error.'</font>';
						}
					}else
					{
						$feedback ='<font color="red">Failed to create database.<br />'.
						'<b>MySQL Error</b>: '.$db->Errno.' '.$db->Error.'</font>';;
					}
				}else
				{
					$feedback ='<font color="red">Failed to connect to database as administrator.<br />'.
					'<b>MySQL Error</b>: '.$db->Errno.' '.$db->Error.'</font>';
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

switch($task)
{
	case 'test':
		print_head();
		echo '<input type="hidden" name="task" value="test" />';
		
		echo '<h1>Welcome!</h1><p>Thank you for installing Group-Office. This page checks if your system meets the requirements to run Group-Office.</p>';
		
		require_once($GO_CONFIG->root_path.'install/test.inc');

		if(isset($fatal_error))
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
		echo '<br /><br /><div align="right"><input type="submit" value="I agree to these terms" /></div>';
		print_foot();
		exit();
		break;

	case 'release_notes':
		print_head();
		echo '<input type="hidden" name="task" value="release_notes" />';
		echo 'Please read the release notes<br /><br />';
		echo '<iframe style="width: 100%; height: 300px; background: #ffffff;" src="../RELEASE.TXT"></iframe>';
		echo '<br /><br /><div align="right"><input type="submit" value="Continue" /></div>';
		print_foot();
		exit();
		break;

	case 'new_database':
		print_head();
		echo 'Do you wish to create a new database and user (Requires MySQL administration privileges) or do you want to use an existing database and user?<br /><br />';
		echo '<input type="hidden" name="task" value="new_database" />';
		echo '<div style="text-align:right"><input type="button" onclick="javascript:_go(\'create_database\');" value="Create new database" />&nbsp;&nbsp;';
		echo '<input type="button" onclick="javascript:_go(\'database_connection\');" value="Use existing database" /></div>';
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
			Enter the administrator username and password and fill in the other fields to create a new database and user for Group-Office.
			<br /><br />
			<table>
			<tr>
			<td>
			Host:
			</td>
			<td>
			<?php $db_host = isset($_POST['db_host']) ? $_POST['db_host'] : $GO_CONFIG->db_host; ?>
			<input type="text" size="40" name="db_host" value="<?php echo $db_host; ?>" />
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
			<?php $db_name = isset($_POST['db_name']) ? $_POST['db_name'] : $GO_CONFIG->db_name; ?>
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
					<?php $db_user = isset($_POST['db_user']) ? $_POST['db_user'] : $GO_CONFIG->db_user; ?>
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
					<div style="text-align:right"><input type="submit" value="Continue" /></div>
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
			<input type="text" size="40" name="db_host" value="<?php echo $GO_CONFIG->db_host; ?>" />
			</td>
			</tr>
			<tr>
			<td>
			Database:
			</td>
			<td>
			<input type="text" size="40" name="db_name" value="<?php echo $GO_CONFIG->db_name; ?>" />
			</td>
			</tr>

			<tr>
			<td>
			Username:
			</td>
			<td>
			<input type="text" size="40" name="db_user" value="<?php echo $GO_CONFIG->db_user; ?>"  />
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
			<div style="text-align:right"><input type="submit" value="Continue" /></div>

			<?php
			print_foot();
			exit();
			break;

			//database connection is setup now
			//next step isto check if the table structure is present.

	case 'database_structure':
		$db = new db();
		$db->Halt_On_Error = 'no';
		if (!@$db->connect($GO_CONFIG->db_name, $GO_CONFIG->db_host, $GO_CONFIG->db_user, $GO_CONFIG->db_pass))
		{
			print_head();
			echo 'Can\'t connect to database!';
			echo '<br /><br />Correct this and refresh this page.';
			print_foot();
			exit();
		}else
		{
			$settings_exist = false;
			$db->query("SHOW TABLES");
			if ($db->num_rows() > 0)
			{
				//structure exists see if the settings table exists
				while ($db->next_record(MYSQL_BOTH))
				{
					if ($db->f(0) == 'go_settings')
					{
						$settings_exist = true;
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
					require_once($GO_CONFIG->root_path.'install/sql/updates.inc.php');
					if (!empty($db_version) && !isset($updates[$db_version-1]))
					{
						$db_version = false;
					}
				}else
				{
					$db_version = false;
				}
				print_head();
				if (isset($feedback))
				{
					echo $feedback.'<br /><br />';
				}
				?>
					<input type="hidden" name="task" value="upgrade" />
					Group-Office has detected a previous installation of Group-Office. By pressing continue the database will be upgraded. This may take some time
					and you should <b>backup your database before you continue with this step!</b>
					<?php
					if (!$db_version)
					{
						echo '<br /><br />Group-Office was unable to detect your old Group-Office version.'.
						'The installer needs your old version number to determine updates that might apply.<br />'.
						'Please enter the version number below if you wish to perform an upgrade.';
					}
				?>
					<br /><br />
					<table width="100%" style="border-width: 0px;font-family: Arial,Helvetica; font-size: 12px;">
					<?php
					if (!$db_version)
					{
						echo '<tr><td>Version:</td><td>';
						$db_version = isset($db_version) ? $db_version : $GO_CONFIG->db_version;
						echo '<input type="text" size="4" maxlength="4" name="db_version" value="'.$db_version.'" /></td></tr>';
					}else
					{
						echo '<input type="hidden" name="db_version" value="'.$db_version.'" />';
					}
				?>
					<tr>
					<td colspan="2" align="right">
					<input type="submit" value="Continue" />
					&nbsp;&nbsp;
					</td>
					</tr>
					</table>
					<?php
					print_foot();
					exit();
			}else
			{
				print_head();
				echo 	'<input type="hidden" name="task" value="database_structure" />';


				echo 'Group-Office succesfully connected to your database!<br />'.
				'Click on \'Continue\' to create the tables for the Group-Office '.
				'base system. This can take some time. Don\'t interupt this process.<br /><br />';
				echo '<div align="right"><input type="submit" value="Continue" /></div>';
				print_foot();
				exit();
			}
		}
		break;

	case 'upgrade':
		print_head();
		echo 	'<input type="hidden" name="task" value="title" />';

		$db = new db();
		$db->Halt_On_Error = 'no';

		if (!$db->connect($GO_CONFIG->db_name, $GO_CONFIG->db_host, $GO_CONFIG->db_user, $GO_CONFIG->db_pass))
		{
			print_head();
			echo 'Can\'t connect to database!';
			echo '<br /><br />Correct this and refresh this page.';
		}else
		{
			$GO_MODULES->load_modules();
			require('upgrade.php');
			echo '<div align="right"><input type="button" value="Continue" onclick="javascript:document.location=\''.$_SERVER['PHP_SELF'].'?task=userdir\';" /></div>';
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
		Enter a title for your Group-Office and webmaster email address for your application.<br />
		The email address will receive information about new registered users.
		<br /><br />
		<table>
		<tr>
		<td>Title:</td>
		</tr>
		<tr>
		<?php
		$title = isset($_POST['title']) ? $_POST['title'] : $GO_CONFIG->title;
		$webmaster_email = isset($_POST['webmaster_email']) ? $_POST['webmaster_email'] : $GO_CONFIG->webmaster_email;
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
		$host = isset($_POST['host']) ? $_POST['host'] : $GO_CONFIG->host;
	?>
		<input type="text" size="40" name="host" value="<?php echo $host; ?>" />
		</td>
		</tr>
		<tr>
		<td>Absolute URL:</td>
		<td>
		<?php
		$full_url = isset($_POST['full_url']) ? $_POST['full_url'] : $GO_CONFIG->full_url;
	?>
		<input type="text" size="40" name="full_url" value="<?php echo $full_url; ?>" />
		</td>
		</tr>
		</table><br />
		<div align="right">
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
			Group-Office needs a place to store user data. Create a writable path for this purpose now and enter it in the box below.<br />
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
		$userdir = isset($_POST['userdir']) ? $_POST['userdir'] : $GO_CONFIG->file_storage_path;
		?>
		<td>User home directory:</td>
		<td><input type="text" size="50" name="userdir" value="<?php echo $userdir; ?>" /></td>
		</tr>
		<tr>
		<td>
		Maximum upload size:
		</td>
		<td>
		<input type="text" size="50" name="max_file_size" value="<?php 
		$max_ini = return_bytes(ini_get('upload_max_filesize'));
		if($GO_CONFIG->max_file_size > $max_ini)
		{
			$GO_CONFIG->max_file_size = $max_ini;
		}
		echo $GO_CONFIG->max_file_size; ?>"  />
		(Current PHP configuration allows <?php echo $max_ini; ?> bytes)
		</td>
		</tr>
		
		<?php


		if($GO_CONFIG->local_path == '')
		{
			$GO_CONFIG->local_path = $GO_CONFIG->root_path.'local/';
			$GO_CONFIG->local_url = $GO_CONFIG->host.'local/';
		}

	?>
			
		<tr>
			<td colspan="2">
			<br /><br />
			Group-Office needs a place to store that is available through a webbrowser so please provide the URL to access this path too.
			<br /><br />
			<font color="#003399"><i>
			$ su<br />
			$ mkdir <?php echo $GO_CONFIG->local_path; ?><br />
			$ chown apache:apache <?php echo $GO_CONFIG->local_path; ?><br />
			</i></font>

			<br /><br />
		</td>
		</tr>
		<tr>
			<td>Local path:</td>
			<?php
			$local_path = isset($_POST['local_path']) ? $_POST['local_path'] : $GO_CONFIG->local_path;
			?>
			<td><input type="text" size="50" name="local_path" value="<?php echo $local_path; ?>" /></td>
		</tr>
		<tr>
		<tr>
			<td>Local URL:</td>
			<?php
			$local_url = isset($_POST['local_url']) ? $_POST['local_url'] : $GO_CONFIG->local_url;
			?>
			<td><input type="text" size="50" name="local_url" value="<?php echo $local_url; ?>" /></td>
		</tr>
		<tr>
			<td colspan="2">	
			<br /><br />
		Group-Office needs a place to store temporarily data such as session data or file uploads. Create a writable path for this purpose now and enter it in the box below.<br />
		The /tmp directory is a good option.
		<br /><br />
		</td>
	</tr>
	<tr>
		<td>Temporarily files directory:</td>
		<?php
		$tmpdir = isset($_POST['tmpdir']) ? $_POST['tmpdir'] : $GO_CONFIG->tmpdir;
		?>
		<td><input type="text" size="50" name="tmpdir" value="<?php echo $tmpdir; ?>" /></td>
		</tr>
		</table><br />
		
		<div align="right">
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
		Select default regional settings for Group-Office. If your language is not in the list please select the closest match.<br />
		It would be nice if you added your missing language to the language/languages.inc file and send it to
		info@intermesh.nl!
		<br /><br />
		
		<table>
		<tr>
			<td>Country:</td>
			<td>
			<select name="default_country">
			<?php
			require($GO_LANGUAGE->get_base_language_file('countries'));
			
			foreach($countries as $key => $country)
			{
				echo '<option value="'.$key.'"';
				if($key==$GO_CONFIG->default_country)
				{
					echo ' selected';
				}				
				echo '>'.$country.'</option>';
			}
			?></select>
			</td>
		</tr>
		<tr>
			<td>Language:</td>
			<td><select name="language">
			<?php
			require($GO_CONFIG->root_path.'language/languages.inc.php');
			
			foreach($languages as $key => $language)
			{
				echo '<option value="'.$key.'"';
				if($key==$GO_CONFIG->language)
				{
					echo ' selected';
				}				
				echo '>'.$language.'</option>';
			}
			?></select></td>
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
				if($timezone==$GO_CONFIG->default_timezone)
				{
					echo ' selected';
				}				
				echo '>'.$timezone.'</option>';
			}
			?></select>
			</td>
		</tr>
		
		<tr>
			<td>Date format:</td>
			<td>
			<select name="default_date_format">
			<?php
		
			foreach($GO_CONFIG->date_formats as $format)
			{
				$friendly[strpos($format, 'Y')]='Year';
				$friendly[strpos($format, 'm')]='Month';
				$friendly[strpos($format, 'd')]='Day';
	
				$strFriendly = $friendly[0].$GO_CONFIG->default_date_seperator.
				$friendly[1].$GO_CONFIG->default_date_seperator.
				$friendly[2];
			
				echo '<option value="'.$format.'"';
				if($format==$GO_CONFIG->default_date_format)
				{
					echo ' selected';
				}				
				echo '>'.$strFriendly.'</option>';
			}
			?></select>
			</td>
		</tr>
		<tr>
			<td>Date seperator:</td>
			<td>
			<select name="default_date_seperator">
			<?php
		
			foreach($GO_CONFIG->date_seperators as $ds)
			{
				echo '<option value="'.$ds.'"';
				if($ds==$GO_CONFIG->default_date_seperator)
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
		
			foreach($GO_CONFIG->time_formats as $tf)
			{
				echo '<option value="'.$tf.'"';
				if($tf==$GO_CONFIG->default_time_format)
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
			if($GO_CONFIG->default_first_weekday=='0')			
				echo ' selected';
			
			echo '>Sunday</option>';
			echo '<option value="1"';
			if($GO_CONFIG->default_first_weekday=='1')			
				echo ' selected';
				
			echo '>Monday</option>';
			?></select>
			</td>
		</tr>

	
		<tr>
			<td>Thousands seperator:</td>
			<td><input name="default_thousands_seperator" maxlength="1" type="text" value="<?php echo $GO_CONFIG->default_thousands_seperator; ?>" /></td>
		</tr>


		<tr>
			<td>Decimal seperator:</td>
			<td><input name="default_decimal_seperator" maxlength="1" type="text" value="<?php echo $GO_CONFIG->default_decimal_seperator; ?>" /></td>
		</tr>
		

		<tr>
			<td>Currency:</td>
			<td><input name="default_currency" type="text" value="<?php echo $GO_CONFIG->default_currency; ?>" /></td>
		</tr>
		<tr>
		<td>Default theme:</td>
		<td>
		<select name="theme">
			<?php
			$themes = $GO_THEME->get_themes();
			foreach($themes as $theme)
			{
	
				echo '<option value="'.$theme.'"';
				if($theme==$GO_CONFIG->theme)
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
		<input type="checkbox" name="allow_themes" value="1" <?php if(isset($_POST['allow_themes']) ? true : $GO_CONFIG->allow_themes) echo 'checked'; ?> />Allow users to change the theme
		<br />
		<input type="checkbox" name="allow_password_change" value="1" <?php if(isset($_POST['allow_password_change']) ? true : $GO_CONFIG->allow_password_change) echo 'checked'; ?> />Allow users to change thier password
		<br />
		<input type="checkbox" name="allow_registration" value="1" <?php if(isset($_POST['allow_registration']) ? true : $GO_CONFIG->allow_registration) echo 'checked'; ?> />Allow anybody to register
		<br />
		<input type="checkbox" name="auto_activate_accounts" value="1" <?php if(isset($_POST['auto_activate_accounts']) ? true : $GO_CONFIG->auto_activate_accounts) echo 'checked'; ?> />Automatically activate accounts. If not the administrator needs to confirm them
		<br />
		<input type="checkbox" name="notify_admin_of_registration" value="1" <?php if(isset($_POST['notify_admin_of_registration']) ? true : $GO_CONFIG->notify_admin_of_registration) echo 'checked'; ?> />Notify the administrator of new accounts
		<?php 
		echo '<p>The following user data fields can be enabled or disabled in the registration form.</p>';
		
		$available_fields = explode(',', 'title_initials,sex,birthday,address,home_phone,fax,cellular,company,department,function,work_address,work_phone,work_fax,homepage');
		$enabled_fields = explode(',',$GO_CONFIG->registration_fields);
		$required_fields = explode(',',$GO_CONFIG->required_registration_fields);
		
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
		<input type="checkbox" name="allow_themes" value="1" <?php if(isset($_POST['allow_themes']) ? true : $GO_CONFIG->allow_themes) echo 'checked'; ?> />Allow users to change the theme
		<br />
		<input type="checkbox" name="allow_password_change" value="1" <?php if(isset($_POST['allow_password_change']) ? true : $GO_CONFIG->allow_password_change) echo 'checked'; ?> />Allow users to change thier password
		
		<?php
		echo '<p>New users will automatically have access to the following modules</p>';

		
		echo '<table><tr><td><b>Module</b></td><td><b>Use</b></td><td><b>Manage</b></td></tr>';		
		
		$module_count = $GO_MODULES->get_modules('0');
		while($GO_MODULES->next_record())
		{
			//require language file to obtain module name in the right language
			$language_file = $GO_LANGUAGE->get_language_file($GO_MODULES->f('id'));

			if(file_exists($language_file))
			{
				require_once($language_file);
			}

			$lang_var = isset($lang[$GO_MODULES->f('id')]['name']) ? $lang[$GO_MODULES->f('id')]['name'] : $GO_MODULES->f('id');

		
			echo '<tr><td>'.$lang_var.'</td><td>';


			$modules_read = isset($_POST['register_modules_read']) ? $_POST['register_modules_read'] : explode(',', $GO_CONFIG->register_modules_read);
			$read_check = in_array($GO_MODULES->f('id'), $modules_read);
			
			$modules_write = isset($_POST['register_modules_write']) ? $_POST['modules_write'] : explode(',', $GO_CONFIG->register_modules_write);
			$write_check = in_array($GO_MODULES->f('id'), $modules_write);
			
			echo '<input type="checkbox" name="register_modules_read[]" value="'.$GO_MODULES->f('id').'"';
			if($read_check)
			{
				echo ' checked';
			}
			echo ' /></td><td><input type="checkbox" name="register_modules_write[]" value="'.$GO_MODULES->f('id').'"';
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
		
		

		$GO_GROUPS->get_groups();
		
		$register_user_groups = explode(',',$GO_CONFIG->register_user_groups);
		$register_visible_user_groups = explode(',',$GO_CONFIG->register_visible_user_groups);

		
		echo '<table><tr><td><b>Group</b></td><td><b>Member</b></td><td><b>Visible</b></td></tr>';		

		while($GO_GROUPS->next_record())
		{
			echo '<tr><td>'.$GO_GROUPS->f('name').'</td><td>';

			echo '<input type="checkbox" name="register_user_groups[]" value="'.$GO_MODULES->f('name').'"';
			if($GO_GROUPS->f('id')==$GO_CONFIG->group_everyone)
			{
				echo ' checked disabled';
			}elseif(in_array($GO_GROUPS->f('name'), $register_user_groups) || $GO_GROUPS->f('id')==$GO_CONFIG->group_internal)
			{
				echo ' checked';
			}
			echo ' /></td><td>';
			
			echo '<input type="checkbox" name="register_visible_user_groups[]" value="'.$GO_MODULES->f('name').'"';
			if($GO_GROUPS->f('id')==$GO_CONFIG->group_root)
			{
				echo ' checked disabled';
			}elseif(in_array($GO_GROUPS->f('name'), $register_visible_user_groups) || $GO_GROUPS->f('id')==$GO_CONFIG->group_internal)
			{
				echo ' checked';
			}
			echo ' /></td></tr>';	
		}
		echo '</table>';
		?>
		<br />
		<div align="right">
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
		Group-Office has the ability to send and receive e-mail. Please configure your SMTP server. <br />
		Leave this blank use the php mail() function but then you won't be able use CC and BCC headers!
		<br />
		<br />
		<table>		
		<tr>
		<td>
		SMTP server:
		</td>
		<td>
		<input type="text" size="40" name="smtp_server" value="<?php echo $GO_CONFIG->smtp_server; ?>"  />
		</td>
		</tr>
		<tr>
		<td>
		SMTP port:
		</td>
		<td>
		<input type="text" size="40" name="smtp_port" value="<?php echo $GO_CONFIG->smtp_port; ?>" />
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
		<input type="text" size="40" name="smtp_username" value="<?php echo $GO_CONFIG->smtp_username; ?>" />
		</td>
		</tr>
		<tr>
		<td>
		SMTP password:
		</td>
		<td>
		<input type="text" size="40" name="smtp_password" value="<?php echo $GO_CONFIG->smtp_password; ?>" />
		</td>
		</tr>
		
		
		<tr><td colspan="2">&nbsp;</td></tr>
		
		<tr>
		<td valign="top">
		
		Maximum size of attachments:
		</td>
		<td>
		<input type="text" size="40" name="max_attachment_size" value="<?php 
		$max_ini  = return_bytes(ini_get('upload_max_filesize'));
		if($GO_CONFIG->max_attachment_size > $max_ini) $GO_CONFIG->max_attachment_size = $max_ini;
		echo $GO_CONFIG->max_attachment_size; ?>" /><br />
		Current PHP configuration allows <?php echo $max_ini; ?> bytes
		</td>
		</tr>
		<tr>
		<td colspan="2">
		<br />
		Some servers require some connection string options when connecting
		to an IMAP or POP-3 server using the PHP IMAP extension. For example most Redhat systems
		require '/notls' or '/novalidate-cert'.
		If you are not sure then leave this field blank.
		<br /><br />
		</td>
		</tr>
		<tr>
		<td>
		Connection options:
		</td>
		<td>
		<input type="text" size="40" name="email_connectstring_options" value="<?php echo $GO_CONFIG->email_connectstring_options; ?>" />
		</td>
		</tr>
		</table><br />
		<div align="right">
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
			Group-Office needs an administrator account. Please create one now.
			<br /><br />
			<table style="border-width: 0px;font-family: Arial,Helvetica; font-size: 12px;">
			<tr>
			<td>Username:</td>
			<td>
			<?php 
			$username = isset($_POST['username']) ? smart_stripslashes(htmlspecialchars($_POST['username'])) : 'admin';
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
			<?php $email = isset($email)? $email : $GO_CONFIG->webmaster_email;?>
			<input type="text" size="40" name="email" value="<?php echo $email; ?>" />
			</td>
			</tr>
			</table><br />
			<div align="right">
			<input type="submit" value="Continue" />
			</div>
			<?php
			print_foot();
			exit();
			break;

	
	case 'completed':

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
	If this is a fresh install you can login with the default administrator account:<br />
	<br />
	<b>Username: admin<br />
	Password: admin</b>
	<br /><br />
	Don't use this account for regular use!
	<br />
	Read this to get started with Group-Office: <a href="http://docs.group-office.com/index.php?folder_id=53&file_id=0" target="_blank">http://docs.group-office.com/index.php?folder_id=53&file_id=0</a>
	<ul>
	<li>Navigate to the menu: Administrator menu -> Modules and install the modules you wish to use.</li>
	<li>Navigate to the menu: Administrator menu -> User groups and create user groups.</li>
	<li>Navigate to the menu: Administrator menu -> Users users to add new users.</li>
	</ul>
	<br />
	<br />
	You can also configure external authentication servers such as an IMAP, POP-3 or LDAP server.
	Take a look at 'auth_sources.dist' for more information about this.
	<br />
	<br />
	For troubleshooting please consult the <a target="_blank" href="../FAQ">FAQ</a> included with the package. 
	If that doesn't help post on the <a target="_blank" href="http://www.group-office.com/forum/">forums</a>.<br />
	Developers should take a look at modules/example/index.php
	<br /><br />
	<div align="right">
	<input type="button" value="Launch Group-Office!" onclick="javascript:window.location='<?php echo $GO_CONFIG->host; ?>';" />
	</div>
	<?php
	print_foot();
	break;
}
