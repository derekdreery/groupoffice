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

@session_destroy();

//config file exists now so require it to get the properties.
require_once('../Group-Office.php');

load_basic_controls();
load_control('dropbox');

$CONFIG_FILE = $GO_CONFIG->get_config_file();

require_once('install.inc');

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : 'test';

$tasks[] = 'test';
$tasks[] = 'license';
$tasks[] = 'release_notes';
$tasks[] = 'new_database';
$tasks[] = 'create_database';
$tasks[] = 'database_connection';
$tasks[] = 'database_structure';
$tasks[] = 'title';
$tasks[] = 'url';
$tasks[] = 'userdir';
$tasks[] = 'allow_password_change';
$tasks[] = 'default_module_access';
$tasks[] = 'default_groups';
$tasks[] = 'theme';
$tasks[] = 'smtp';
$GO_USERS->Halt_On_Error='no';
if ($task != 'test' && (empty($GO_CONFIG->db_user) || !$GO_USERS->get_user(1)))
{
	$tasks[] = 'administrator';
	$tasks[] = 'send_info';
}
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
	'<link href="install.css" rel="stylesheet" type="text/css" />'.
	'<script type="text/javascript" src="../javascript/common.js"></script>'.
	'<title>Group-Office Installation</title>'.
	'</head>'.
	'<body style="font-family: Arial,Helvetica">';
	echo '<form method="post" action="install.php">';
	echo '<table width="100%" cellpadding="0" cellspacing="0">';
	echo '<tr><td style="border-bottom:1px solid black;"><img src="Intermesh.gif" border="0" align="middle" /></td>';
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
$script_path = __FILE__;
if ($script_path == '')
{
	print_head();
	echo '<b>Fatal error:</b> Could not get the path of the this script. The server variable \'SCRIPT_FILENAME\' and \'PATH_TRANSLATED\' are not set.';
	echo '<br /><br />Correct this and refresh this page. If you are not able to correct this try the manual installation described in the file \'INSTALL\'';
	print_foot();
	exit();
}

//check ifconfig exists and if the config file is writable
$config_location1 = '/etc/Group-Office/'.$_SERVER['SERVER_NAME'].str_replace($_SERVER['DOCUMENT_ROOT'],'', $GO_CONFIG->root_path).'config.php';
$config_location2 = $GO_CONFIG->root_path.'config.php';

if(!file_exists($CONFIG_FILE))
{
	print_head();
	echo 'The configuration file does not exist. You must create a writable configuration file at one of the following locations:<br />';
	echo '<ol><li>'.$config_location1.'</li>';
	echo '<li>'.$config_location2.'</li></ol></i></font>';
	echo 'The first location is more secure because the sensitive information is kept outside the document root but it does require root privileges on this machine.<br />The second advantage is that you will be able to seperate the source from the configuration. This can be very usefull with multiple installations on one machine.';
	echo ' If you choose this location then you have to make sure that in Apache\'s httpd.conf the following is set:<br /><br />';
	echo '<font color="#003399">';
	echo '<i>UseCanonicalName On</i></font><br />';
	echo 'This is to make sure it always finds your configuration file at the correct location.';
	echo '<br /><br /><font color="#003399">';
	echo '<i>$ touch config.php (Or FTP an empty config.php to the server)<br />';
	echo '$ chmod 666 config.php</i></font><br /><br /><div style="text-align: right;"><input type="submit" value="Continue" /></div>';
	print_foot();
	exit();
}elseif (!is_writable($CONFIG_FILE))
{
	print_head();
	echo 'The configuration file \''.$CONFIG_FILE.'\' exists but is not writable. If you wish to make changes then you have to make \''.$CONFIG_FILE.'\' writable during the configuration process.';
	echo '<br /><br />Correct this and refresh this page.';
	echo '<br /><br /><font color="#003399"><i>$ chmod 666 '.$CONFIG_FILE.'<br /></i></font>'.
	'<br /><br /><div style="text-align: right;"><input type="submit" value="Continue" /></div>';
	print_foot();
	exit();
}


$key = array_search($task, $tasks);
$nexttask = isset($tasks[$key+1]) ? $tasks[$key+1] : 'completed';


if ($_SERVER['REQUEST_METHOD'] =='POST')
{
	switch($task)
	{
		case 'administrator':
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
			}elseif(!validate_email( $email ))
			{
				$feedback = '<font color="red">Invalid E-mail address!</font>';
			}else
			{
				$GO_USERS->get_users();
				$user['id'] = $GO_USERS->nextid("users");

				$GO_GROUPS->query("DELETE FROM db_sequence WHERE seq_name='groups'");
				$GO_GROUPS->query("DELETE FROM groups");

				$admin_group_id = $GO_GROUPS->add_group($user['id'], 'Admins');
				$everyone_group_id = $GO_GROUPS->add_group($user['id'], 'Everyone');

				$user_groups = array($admin_group_id, $everyone_group_id);

				//$user['language'] = $GO_LANGUAGE->language;
				$user['username'] = smart_addslashes($username);
				$user['password'] = smart_addslashes($pass1);
				$user['email'] = smart_addslashes($email);
				$user['sex'] = 'M';

				$GO_USERS->add_user($user,	$user_groups,array($GO_CONFIG->group_everyone));

				$old_umask = umask(000);
				mkdir_recursive($GO_CONFIG->file_storage_path.'users/'.smart_stripslashes($username), $GO_CONFIG->create_mode);
				umask($old_umask);

				$task = $nexttask;
			}
			break;

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
				$queries = get_sql_queries($GO_CONFIG->root_path."lib/sql/groupoffice-installed.sql");
				//$queries = get_sql_queries($GO_CONFIG->root_path."lib/sql/groupoffice.sql");
				while ($query = array_shift($queries))
				{
					$db->query($query);
				}

				//store the version number for future upgrades
				$GO_CONFIG->save_setting('version', $GO_CONFIG->version);
				$db_version = $GO_CONFIG->version;

				install_required_modules();

				$task = $nexttask;
			}
			break;

		case 'userdir':
			$tmpdir=smart_stripslashes($_POST['tmpdir']);

			if (!is_writable($_POST['userdir']))
			{
				$feedback = '<font color="red">The path you entered is not writable.<br />Please correct this and try again.</font>';
			}elseif($_POST['max_file_size'] > return_bytes(ini_get('upload_max_filesize')))
			{
				$feedback = '<font color="red">You entered a greater upload size then the PHP configuration allows.<br />Please correct this and try again.</font>';
			}elseif (!is_writable($_POST['local_path']))
			{
				$feedback = '<font color="red">The local path you entered is not writable.<br />Please correct this and try again.</font>';
			}elseif (!is_writable($tmpdir))
			{
				$feedback = '<font color="red">The path you entered is not writable.<br />Please correct this and try again.</font>';
			}

			if (substr($_POST['userdir'], -1) != '/') $_POST['userdir'] = $_POST['userdir'].'/';
			$GO_CONFIG->file_storage_path=smart_stripslashes($_POST['userdir']);
			$GO_CONFIG->create_mode=smart_stripslashes($_POST['create_mode']);
			$GO_CONFIG->max_file_size=smart_stripslashes($_POST['max_file_size']);

			if (substr($_POST['local_path'], -1) != '/') $_POST['local_path'] = $_POST['local_path'].'/';
			if (substr($_POST['local_url'], -1) != '/') $_POST['local_url'] = $_POST['local_url'].'/';

			$GO_CONFIG->local_path=smart_stripslashes($_POST['local_path']);
			$GO_CONFIG->local_url=smart_stripslashes($_POST['local_url']);

			if (substr($tmpdir, -1) != '/') $tmpdir = $tmpdir.'/';
			$GO_CONFIG->tmpdir=$tmpdir;

			if (save_config($GO_CONFIG) && !isset($feedback))
			{
				//check for userdirs
				$GO_USERS->get_users();
				while($GO_USERS->next_record())
				{
					if(!file_exists($GO_CONFIG->file_storage_path.'users/'.$GO_USERS->f('username')))
					{
						mkdir_recursive($GO_CONFIG->file_storage_path.'users/'.$GO_USERS->f('username'));
					}
				}
				$task = $nexttask;
			}

			break;


		case 'title':
			if ($_POST['title'] == '')
			{
				$feedback = 'You didn\'t enter a title.';

			}elseif(!validate_email($_POST['webmaster_email']))
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
			$GO_CONFIG->theme = smart_stripslashes($_POST['theme']);
			$GO_CONFIG->login_screen = smart_stripslashes($_POST['login_screen']);
			
			
			$GO_CONFIG->default_country_id = smart_addslashes($_POST['default_country_id']);
			$GO_CONFIG->default_timezone = smart_addslashes($_POST['default_timezone']);
			$GO_CONFIG->default_dst = smart_addslashes($_POST['default_dst']);
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

			$GO_CONFIG->mailer = $_POST['mailer'];
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

	}
}
//Store all options in config array during install

switch($task)
{
	case 'test':
		print_head();
		echo '<input type="hidden" name="task" value="license" />';
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
		if(file_exists('../LICENSE.GPL'))
		{
			$license = '../LICENSE.GPL';
		}else
		{
			$license = '../LICENSE.PRO';
		}

		print_head();
		echo '<input type="hidden" name="task" value="release_notes" />';
		echo 'Do you agree to the terms of the license agreement?<br /><br />';
		echo '<iframe style="width: 100%; height: 300px; background: #ffffff;" src="'.$license.'"></iframe>';
		echo '<br /><br /><div align="right"><input type="submit" value="I agree to these terms" /></div>';
		print_foot();
		exit();
		break;

	case 'release_notes':
		print_head();
		echo '<input type="hidden" name="task" value="new_database" />';
		echo 'Please read the release notes<br /><br />';
		echo '<iframe style="width: 100%; height: 300px; background: #ffffff;" src="../RELEASE"></iframe>';
		echo '<br /><br /><div align="right"><input type="submit" value="Continue" /></div>';
		print_foot();
		exit();
		break;

	case 'new_database':
		print_head();
		echo 'Do you wish to create a new database and user (Requires MySQL administration privileges) or do you want to use an existing database and user?<br /><br />';
		echo '<input type="hidden" name="task" value="" />';
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
			<table width="100%" border="0">
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
				while ($db->next_record())
				{
					if ($db->f(0) == 'settings')
					{
						$settings_exist = true;
						break;
					}
				}
			}
			if ($settings_exist)
			{
				$db->query("SELECT value FROM settings WHERE name='version'");
				if ($db->next_record())
				{
					$db_version=str_replace('.','', $db->f('value'));
					require_once($GO_CONFIG->root_path.'lib/updates.inc');
					if (!isset($updates[$db_version]))
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
			echo '<div align="right"><input type="button" value="Continue" onclick="javascript:document.location=\''.$_SERVER['PHP_SELF'].'?task=title\';" /></div>';
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
		<tr>
		<td>
		Create mode:
		</td>
		<td>
		<?php


		if($GO_CONFIG->local_path == '')
		{
			$GO_CONFIG->local_path = $GO_CONFIG->root_path.'local/';
			$GO_CONFIG->local_url = $GO_CONFIG->host.'local/';
		}

	?>
		<input type="text" size="4" name="create_mode" value="<?php echo $GO_CONFIG->create_mode; ?>" />
		</td>
		</tr>		
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
				
		<?php
		
		$table = new table();
		
		$row = new table_row();
		$row->add_cell(new table_cell('Country:'));
		$select = new select('default_country_id', $GO_CONFIG->default_country_id);
		$select->add_value('0', 'Pleas select a country');
		$GO_USERS->get_countries();
		while($GO_USERS->next_record())
		{
			$select->add_value($GO_USERS->f('id'), $GO_USERS->f('name'));
		}
		$row->add_cell(new table_cell($select->get_html()));
		$table->add_row($row);

		$row = new table_row();
		$row->add_cell(new table_cell('Language:'));

		$select = new select('language', $GO_CONFIG->language);
		$languages = $GO_LANGUAGE->get_languages();
		foreach($languages as $language)
		{
			$select->add_value($language['code'], $language['description']);
		}
		$row->add_cell(new table_cell($select->get_html()));
		$table->add_row($row);

		$row = new table_row();
		$row->add_cell(new table_cell('Timezone:'));

		$select = new select('default_timezone', $GO_CONFIG->default_timezone);
		$select->add_value('12','+12 GMT');
		$select->add_value('11.5','+11.5 GMT');
		$select->add_value('11','+11 GMT');
		$select->add_value('10.5','+10.5 GMT');
		$select->add_value('10','+10 GMT');
		$select->add_value('9.5','+9.5 GMT');
		$select->add_value('9','+9 GMT');
		$select->add_value('8.5','+8.5 GMT');
		$select->add_value('8','+8 GMT');
		$select->add_value('7.5','+7.5 GMT');
		$select->add_value('7','+7 GMT');
		$select->add_value('6.5','+6.5 GMT');
		$select->add_value('6','+6 GMT');
		$select->add_value('5.5','+5.5 GMT');
		$select->add_value('5','+5 GMT');
		$select->add_value('4.5','+4.5 GMT');
		$select->add_value('4','+4 GMT');
		$select->add_value('3.5','+3.5 GMT');
		$select->add_value('3','+3 GMT');
		$select->add_value('2.5','+2.5 GMT');
		$select->add_value('2','+2 GMT');
		$select->add_value('1.5','+1.5 GMT');
		$select->add_value('1','+1 GMT');
		$select->add_value('0','GMT');
		$select->add_value('-1','-1 GMT');
		$select->add_value('-1.5','-1.5 GMT');
		$select->add_value('-2','-2 GMT');
		$select->add_value('-2.5','-2.5 GMT');
		$select->add_value('-3','-3 GMT');
		$select->add_value('-3.5','-3.5 GMT');
		$select->add_value('-4','-4 GMT');
		$select->add_value('-4.5','-4.5 GMT');
		$select->add_value('-5','-5 GMT');
		$select->add_value('-5.5','-5.5 GMT');
		$select->add_value('-6','-6 GMT');
		$select->add_value('-6.5','-6.5 GMT');
		$select->add_value('-7','-7 GMT');
		$select->add_value('-7.5','-7.5 GMT');
		$select->add_value('-8','-8 GMT');
		$select->add_value('-8.5','-8.5 GMT');
		$select->add_value('-9','-9 GMT');
		$select->add_value('-9.5','-9.5 GMT');
		$select->add_value('-10','-10 GMT');
		$select->add_value('-10.5','-10.5 GMT');
		$select->add_value('-11','-11 GMT');
		$select->add_value('-11.5','-11.5 GMT');
		$select->add_value('-12','-12 GMT');

		$checkbox = new checkbox('DST', 'default_dst', '1', 'Adjust time to Daylight Savings Time', ($GO_CONFIG->default_dst=='1'));

		$row->add_cell(new table_cell($select->get_html().$checkbox->get_html()));
		$table->add_row($row);

		$row = new table_row();
		$row->add_cell(new table_cell('Date format:'));

		$select = new select('default_date_format', $GO_CONFIG->default_date_format);
		for ($i=0;$i<count($GO_CONFIG->date_formats);$i++)
		{
			$friendly[strpos($GO_CONFIG->date_formats[$i], 'Y')]='Year';
			$friendly[strpos($GO_CONFIG->date_formats[$i], 'm')]='Month';
			$friendly[strpos($GO_CONFIG->date_formats[$i], 'd')]='Day';

			$strFriendly = $friendly[0].$GO_CONFIG->default_date_seperator.
			$friendly[1].$GO_CONFIG->default_date_seperator.
			$friendly[2];

			$select->add_value($GO_CONFIG->date_formats[$i], $strFriendly);
		}
		$row->add_cell(new table_cell($select->get_html()));
		$table->add_row($row);

		$row = new table_row();
		$row->add_cell(new table_cell('Date seperator:'));

		$select = new select('default_date_seperator', $GO_CONFIG->default_date_seperator);
		for ($i=0;$i<count($GO_CONFIG->date_seperators);$i++)
		{
			$select->add_value($GO_CONFIG->date_seperators[$i], $GO_CONFIG->date_seperators[$i]);
		}
		$row->add_cell(new table_cell($select->get_html()));
		$table->add_row($row);

		$row = new table_row();
		$row->add_cell(new table_cell('Time format:'));

		$select = new select('default_time_format', $GO_CONFIG->default_time_format);
		$select->add_value($GO_CONFIG->time_formats[0], '24 hour');
		$select->add_value($GO_CONFIG->time_formats[1], '12 hour');

		$row->add_cell(new table_cell($select->get_html()));
		$table->add_row($row);


		$row = new table_row();
		$row->add_cell(new table_cell('First day of the week:'));

		$select = new select('default_first_weekday',$GO_CONFIG->default_first_weekday);
		$select->add_value('0', $full_days[0]);
		$select->add_value('1', $full_days[1]);

		$row->add_cell(new table_cell($select->get_html()));
		$table->add_row($row);


		$row = new table_row();
		$row->add_cell(new table_cell('Thousands seperator:'));

		$input = new input('text', 'default_thousands_seperator', $GO_CONFIG->default_thousands_seperator);
		$input->set_attribute('style', 'width:50px;');
		$input->set_attribute('maxlength', '1');

		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);


		$row = new table_row();
		$row->add_cell(new table_cell('Decimal seperator:'));

		$input = new input('text', 'default_decimal_seperator', $GO_CONFIG->default_decimal_seperator);
		$input->set_attribute('style', 'width:50px;');
		$input->set_attribute('maxlength', '1');

		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);


		$row = new table_row();
		$row->add_cell(new table_cell('Currency:'));

		$input = new input('text', 'default_currency', $GO_CONFIG->default_currency);
		$input->set_attribute('style', 'width:50px;');
		$input->set_attribute('maxlength', '3');

		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);



		echo $table->get_html();

		
		?>
		<br /><br />
		Select the default theme for Group-Office and whether users are allowed to change the theme or not.
		<br /><br />
		<table>
		<tr>
		<td>Default theme:</td>
		<td>
		<?php
		$themes = $GO_THEME->get_themes();
		$dropbox = new dropbox();
		$dropbox->add_arrays($themes, $themes);
		$dropbox->print_dropbox("theme", $GO_CONFIG->theme);
	?>
		</td>
		</tr>
		<tr>
		<td>Login screen style:</td>
		<td>
		<?php
		$login_screens = $GO_THEME->get_login_screens();
		$dropbox = new dropbox();
		$dropbox->add_arrays($login_screens, $login_screens);
		$dropbox->print_dropbox("login_screen", $GO_CONFIG->login_screen);
		?>
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

		//allow_password_change
	case 'allow_password_change':
		print_head();
		if (isset($feedback))
		{
			echo $feedback.'<br /><br />';
		}
	?>
		<input type="hidden" name="task" value="allow_password_change" />
	
		<?php 
		$allow_themes = isset($_POST['allow_themes']) ? true : $GO_CONFIG->allow_themes;
		$checkbox = new checkbox('allow_themes', 'allow_themes', 'true',  'Allow users to change the theme',$allow_themes);
		echo $checkbox->get_html();

		echo '<br />';

		$allow_password_change = isset($_POST['allow_password_change']) ? true : $GO_CONFIG->allow_password_change;
		$checkbox = new checkbox('allow_password_change', 'allow_password_change', 'true',  'Allow users to change thier password',$allow_password_change);
		echo $checkbox->get_html();

		echo '<br />';

		$allow_registration = isset($_POST['allow_registration']) ? $_POST['allow_registration'] : $GO_CONFIG->allow_registration;
		$checkbox = new checkbox('allow_registration', 'allow_registration', 'true',  'Allow anybody to register',$allow_registration);
		echo $checkbox->get_html();

		echo '<br />';

		$auto_activate_accounts = isset($_POST['auto_activate_accounts']) ? $_POST['auto_activate_accounts'] : $GO_CONFIG->auto_activate_accounts;
		$checkbox = new checkbox('auto_activate_accounts', 'auto_activate_accounts', 'true',  'Automatically activate accounts. If not the administrator needs to confirm them.',$auto_activate_accounts);
		echo $checkbox->get_html();

		echo '<br />';

		$notify_admin_of_registration = isset($_POST['notify_admin_of_registration']) ? $_POST['notify_admin_of_registration'] : $GO_CONFIG->notify_admin_of_registration;
		$checkbox = new checkbox('notify_admin_of_registration', 'notify_admin_of_registration', 'true',  'Notify the administrator of new accounts',$notify_admin_of_registration);
		echo $checkbox->get_html();
		
		
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
		
		
		$table = new table();

		$row = new table_row();
		$row->add_cell(new table_cell('<b>Field</b>'));
		$row->add_cell(new table_cell('<b>Enable</b>'));
		$row->add_cell(new table_cell('<b>Required</b>'));

		$table->add_row($row);
		
		foreach($available_fields as $field)
		{
			$row = new table_row();
			$row->add_cell(new table_cell($names[$field]));
			
			$input = new input('checkbox','registration_fields[]', $field);
			if(in_array($field, $enabled_fields))
			{
				$input->set_attribute('checked','true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			
			$input = new input('checkbox','required_registration_fields[]', $field);
			if(in_array($field, $required_fields))
			{
				$input->set_attribute('checked','true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			
			$table->add_row($row);			
		}
		echo $table->get_html();





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
		
		echo '<p>New users will automatically have access to the following modules</p>';

		$table = new table();

		$row = new table_row();
		$row->add_cell(new table_cell('<b>Module</b>'));
		$row->add_cell(new table_cell('<b>Use</b>'));
		$row->add_cell(new table_cell('<b>Manage</b>'));

		$table->add_row($row);

		$module_count = $GO_MODULES->get_modules('0');
		while($GO_MODULES->next_record())
		{
			//require language file to obtain module name in the right language
			$language_file = $GO_LANGUAGE->get_language_file($GO_MODULES->f('id'));

			if(file_exists($language_file))
			{
				require_once($language_file);
			}

			$lang_var = isset($lang_modules[$GO_MODULES->f('id')]) ? $lang_modules[$GO_MODULES->f('id')] : $GO_MODULES->f('id');

			$row = new table_row();
			$row->add_cell(new table_cell($lang_var));


			$modules_read = isset($_POST['register_modules_read']) ? $_POST['register_modules_read'] : explode(',', $GO_CONFIG->register_modules_read);
			$read_check = in_array($GO_MODULES->f('id'), $modules_read);


			$checkbox = new checkbox(
			$GO_MODULES->f('acl_read'),
			'register_modules_read[]',
			$GO_MODULES->f('id'),
			'',
			$read_check);

			$cell = new table_cell($checkbox->get_html());
			$cell->set_attribute('align','center');
			$row->add_cell($cell);

			$modules_write = isset($_POST['register_modules_write']) ? $_POST['modules_write'] : explode(',', $GO_CONFIG->register_modules_write);
			$write_check = in_array($GO_MODULES->f('id'), $modules_write);


			$checkbox = new checkbox(
			$GO_MODULES->f('acl_write'),
			'register_modules_write[]',
			$GO_MODULES->f('id'),
			'',
			$write_check);

			$cell = new table_cell($checkbox->get_html());
			$cell->set_attribute('align','center');
			$row->add_cell($cell);

			$table->add_row($row);
		}

		echo $table->get_html();
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

		
		$table = new table();

		$row = new table_row();
		$row->add_cell(new table_cell('<b>Group</b>'));
		$row->add_cell(new table_cell('<b>Member</b>'));
		$row->add_cell(new table_cell('<b>Visible</b>'));

		$table->add_row($row);		

		while($GO_GROUPS->next_record())
		{
			$row = new table_row();
			$row->add_cell(new table_cell($GO_GROUPS->f('name')));
			
			$input = new input('checkbox','register_user_groups[]', $GO_GROUPS->f('name'));
			
			if($GO_GROUPS->f('id')==$GO_CONFIG->group_everyone)
			{
				$input->set_attribute('checked','true');
				$input->set_attribute('disabled','true');
			}elseif(in_array($GO_GROUPS->f('name'), $register_user_groups))
			{
				$input->set_attribute('checked','true');
				
			}
			$row->add_cell(new table_cell($input->get_html()));
			
			$input = new input('checkbox','register_visible_user_groups[]', $GO_GROUPS->f('name'));
			if(in_array($GO_GROUPS->f('name'), $register_visible_user_groups))
			{
				$input->set_attribute('checked','true');
			}
			$row->add_cell(new table_cell($input->get_html()));
			
			$table->add_row($row);		
		}
		echo $table->get_html();
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
		Mailer:
		</td>
		<td>
		<?php
		$dropbox = new dropbox();
		$dropbox->add_value('mail', 'PHP Mail() Function (Not recommended)');
		$dropbox->add_value('sendmail', 'Use local sendmail');
		$dropbox->add_value('qmail', 'Use local Qmail');
		$dropbox->add_value('smtp', 'Use remote SMTP');
		$dropbox->print_dropbox('mailer', $GO_CONFIG->mailer, 'onchange="javascript:change_mailer()"');
	?>
		</td>
		</tr>
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
		<script type="text/javascript">
		function change_mailer()
		{
			if(document.forms[0].mailer.value=='smtp')
			{
				document.forms[0].smtp_server.disabled=false;
				document.forms[0].smtp_port.disabled=false;
			}else
			{
				document.forms[0].smtp_server.disabled=true;
				document.forms[0].smtp_port.disabled=true;
			}
		}
		change_mailer();
	</script>
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
			Group-Office needs an administrator account. The username will be 'administrator'. Please create a password for 'administrator'.
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

	case 'send_info':
		print_head();
		if (isset($feedback))
		{
			echo $feedback.'<br /><br />';
		}
	?>
		<input type="hidden" name="task" value="completed" />
		Intermesh would like to know that you are using Group-Office to find out how many people are
		using Group-Office. Please select an Option. (If you already sent information before, please select 'No').
		<br /><br />
		<table cellpadding="10" style="border-width: 0px;font-family: Arial,Helvetica; font-size: 12px;">
		<tr>
		<td>
		<?php
		$info = isset($_REQUEST['info']) ? $_REQUEST['info'] : 'no';
		$users = isset($_REQUEST['users']) ? $_REQUEST['users'] : '&lt; 5';

		$radio_list = new radio_list('info', $info);
		$radio_list->add_option('no', "");
		echo "No, Don't send information to Intermesh <br />";
		$radio_list->add_option('business', "");
		echo "Yes, tell Intermesh that I'm using Group-Office for business purpose.<br />";
		$radio_list->add_option('personal', "");
		echo "Yes, tell Intermesh that I'm using Group-Office for personal use or I'm just testing.";
	?>
		</td>
		</tr>
		<tr>
		<td>
		How many users do you have in Group-Office?
		<?php
		$dropbox = new dropbox();
		$dropbox->add_value('&lt; 5', '&lt; 5');
		$dropbox->add_value('20-30', '20-30');
		$dropbox->add_value('30-50', '30-50');
		$dropbox->add_value('50-75', '50-75');
		$dropbox->add_value('75-100', '75-100');
		$dropbox->add_value('100-150', '100-150');
		$dropbox->add_value('150-200', '150-200');
		$dropbox->add_value('300-400', '300-400');
		$dropbox->add_value('&gt; 400', '&gt; 400');
		$dropbox->print_dropbox('users', $users);
	?>
		</td>
		</tr>
		<tr>
		<td>
		If you would like to receive information about Group-Office Professional
		please fill in a name and an e-mail address where Intermesh may contact you:<br />
		<?php
		$email = isset($email)? $email : $GO_CONFIG->webmaster_email;
		$name = isset($name)? $name : '';
	?>
		<table style="border-width: 0px;font-family: Arial,Helvetica; font-size: 12px;">
		<tr>
		<td>E-mail:</td>
		<td><input type="text" size="50" name="email" value="<?php echo $email; ?>" /></td>
		</tr>
		<tr>
		<td>Name:</td>
		<td><input type="text" size="50" name="name" value="<?php echo $name; ?>" /></td>
		</tr>
		</table>
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
	When you are done log in to Group-Office with the administrator account:<br />
	<br />
	<b>Username: admin<br />
	Password: admin</b>
	<br /><br />
	<ul>
	<li>Navigate to the menu: Administrator menu -> Modules and install the modules you wish to use.</li>
	<li>Navigate to the menu: Administrator menu -> User groups and create user groups.</li>
	<li>Navigate to the menu: Administrator menu -> Users users to add new users.</li>
	</ul>
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
