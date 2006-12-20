<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 28 Februari 2005

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

if(!defined('GO_LOADED'))
{
	require_once('../Group-Office.php');
}

$quiet=isset($quiet) ? $quiet : false;
$line_break=isset($line_break) ? $line_break : '<br />';

$GO_MODULES->load_modules();

$CONFIG_FILE = $GO_CONFIG->get_config_file();

require_once('install.inc');

/*if(!isset($_REQUEST['config_created']))
{
	save_config($GO_CONFIG);
	header('Location: '.$_SERVER['PHP_SELF'].'?config_created=true');
	exit();
}*/

if(!$quiet)
	echo 'Setting max_executing_time to one hour.'.$line_break;
if (!ini_set('max_execution_time', '3600'))
{
	if(!$quiet)
		echo '<b>WARNING:</b> Could not set max_execution_time to allow this script to run for a long time.'.$line_break;
}

$db = new db();
$db->Halt_On_Error = 'report';

$db->query("SELECT * FROM settings WHERE name='version'");
if($db->next_record())
{
	$old_version = intval(str_replace('.', '', $db->f('value')));
}else
{
	$old_version=195;
}
$new_go_version = intval(str_replace('.', '', $GO_CONFIG->db_version));

if(!$quiet)
	echo 'Current version: '.$old_version.$line_break;

require_once($GO_CONFIG->root_path.'lib/updates.inc');

if (!isset($updates[$old_version]))
{
	//invalid version, abort upgrade

	echo 'The version number '.$old_version.' is invalid'.$line_break;
	exit();
}else
{
	if(!$quiet)
		echo 'Updating Group-Office Framework'.$line_break;
	for ($cur_ver=$old_version;$cur_ver<$new_go_version;$cur_ver++)
	{
		if (isset($updates[$cur_ver]))
		{
			while($query = array_shift($updates[$cur_ver]))
			{
				if(!$quiet)
					echo 'Excuting: '.$query.$line_break;
				@$db->query($query);
			}
		}

		if (file_exists($GO_CONFIG->root_path.'lib/scripts/'.$cur_ver.'.inc'))
		{
			if(!$quiet)
				echo 'Running update script for version '.$cur_ver.'...'.$line_break;
			require_once($GO_CONFIG->root_path.'lib/scripts/'.$cur_ver.'.inc');
		}
	}
	$db_version = $GO_CONFIG->version;
	$_SESSION['completed']['database_structure'] = true;
	//store the version number for future upgrades

	$GO_CONFIG->save_setting('version', $GO_CONFIG->db_version);

	//Upgrade modules
	$GO_MODULES->get_modules();
	while($GO_MODULES->next_record())
	{
		$module_info = $GO_MODULES->get_module_info($GO_MODULES->f('id'));
		
		
		if($module_info)
		{
			$installed_version = intval(str_replace('.', '', $GO_MODULES->f('version')));						
			$new_version = intval(str_replace('.', '',$module_info['version']));		

			if($installed_version < $new_version)
			{						
				if(!$quiet)
					echo 'Updating module '.$GO_MODULES->f('id').' version '.$installed_version.$line_break;
				
				$update_file = $GO_CONFIG->root_path.'modules/'.$GO_MODULES->f('id').'/sql/'.$GO_MODULES->f('id').'.updates.inc';
				$updates = array();
				if(!$quiet)
					echo 'Loading update file:'.$update_file.$line_break;
				if(file_exists($update_file))
				{
					require_once($update_file);
					for ($cur_ver=$installed_version;$cur_ver<$new_version;$cur_ver++)
					{
						if (isset($updates[$cur_ver]))
						{
							while($query = array_shift($updates[$cur_ver]))
							{
								if(!$quiet)
									echo 'Excuting: '.$query.$line_break;
								@$db->query($query);
							}
						}
					}
				}				
			}else
			{
				if(!$quiet)
					echo 'Nothing todo for module '.$GO_MODULES->f('id').' version '.$installed_version.$line_break;
			}			
		}else
		{
			if(!$quiet)
				echo 'No module info found for '.$GO_MODULES->f('id').$line_break;
		}
	}
	
	if(!$quiet)
		echo 'Checking for module update scripts...'.$line_break;
	//Upgrade modules
	$GO_MODULES->get_modules();
	while($GO_MODULES->next_record())
	{
		$module_info = $GO_MODULES->get_module_info($GO_MODULES->f('id'));
		
		
		if($module_info)
		{
			$installed_version = intval(str_replace('.', '', $GO_MODULES->f('version')));						
			$new_version = intval(str_replace('.', '',$module_info['version']));		

			if($installed_version < $new_version)
			{						
				for ($cur_ver=$installed_version;$cur_ver<$new_version;$cur_ver++)
				{
					//echo $GO_CONFIG->root_path.'modules/'.$GO_MODULES->f('id').'/sql/'.$cur_ver.'.inc<br>';
					if (file_exists($GO_CONFIG->root_path.'modules/'.$GO_MODULES->f('id').'/sql/'.$cur_ver.'.inc'))
					{						
						if(!$quiet)
							echo 'Running update script for module \''.$GO_MODULES->f('id').'\' version '.$cur_ver.'...'.$line_break;
						require_once($GO_CONFIG->root_path.'modules/'.$GO_MODULES->f('id').'/sql/'.$cur_ver.'.inc');
					}					 			    		
				}				
				$sql = "UPDATE modules SET version='".$module_info['version']."' WHERE id='".$GO_MODULES->f('id')."'";	    	
				$db->query($sql);
				if(!$quiet)
					echo 'Module '.$GO_MODULES->f('id').' updated to version '.$new_version.$line_break;
			}else
			{
				if(!$quiet)
					echo 'Nothing todo for module '.$GO_MODULES->f('id').' version '.$installed_version.$line_break;
			}			
		}else
		{
			if(!$quiet)
				echo 'No module info found for '.$GO_MODULES->f('id').$line_break;
		}
	}
	install_required_modules();
	echo 'Database updated to version '.$new_go_version.$line_break;
}
?>
