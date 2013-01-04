<?php


if(empty($SECOND_RUN)){

	if(!headers_sent()){
		header('Content-Type: text/html; charset=UTF-8');
	}
	ini_set('display_errors', 'on');

	$quiet=false;
	$line_break="\n";

	if(isset($argv[1]))
	{
		define('CONFIG_FILE', $argv[1]);
	}

	chdir(dirname(__FILE__));


	require_once('../Group-Office.php');
	ini_set('max_execution_time', '3600');

	if(is_dir($GO_CONFIG->file_storage_path.'cache'))
	{
		echo 'Removing cached javascripts...'.$line_break;

		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();

		$fs->delete($GO_CONFIG->file_storage_path.'cache');
	}
	mkdir($GO_CONFIG->file_storage_path.'cache', 0755, true);

	$log_dir = $GO_CONFIG->file_storage_path.'log/upgrade/';
	if(!is_dir($log_dir)){
		mkdir($log_dir,0750,true);
	}
	$log_file = $log_dir.date('Ymd_Gi').'.log';

	touch ($log_file);

	if(!is_writable($log_file)){
		die('Fatal error: Could not write to log file');
	}

	function ob_upgrade_log($buffer)
	{
		global $log_file;

		file_put_contents($log_file, $buffer, FILE_APPEND);
		return $buffer;
	}


	if(!defined('NOTINSTALLED') && !isset($RERUN_UPDATE) && !headers_sent())
	{
		//login event can cause problems
		SetCookie("GO_UN","",time()-3600,"/","",!empty($_SERVER['HTTPS']),true);
		SetCookie("GO_PW","",time()-3600,"/","",!empty($_SERVER['HTTPS']),true);
	}


	if(php_sapi_name() != 'cli'){
		echo '<pre>';
	}
	ob_start("ob_upgrade_log");
}

//update scripts can request to rerun the update process by setting $RERUN_UPDATE=true;
//this is useful when an update installs a module that might need updates too.
unset($RERUN_UPDATE, $CHECK_MODULES);



if(!$quiet)
echo 'Updating Group-Office database: '.$GO_CONFIG->db_name.$line_break;

$GO_MODULES->load_modules();

$db = new db();
$db->halt_on_error = 'report';

//suppress duplicate and drop errors
$db->suppress_errors=array(1060, 1091);

$old_version = $GO_CONFIG->get_setting('version');
if(!$old_version)
$old_version=0;

//if(!$quiet)
//echo 'Updating framework version: '.$old_version.$line_break;
require_once($GO_CONFIG->root_path.'install/sql/updates.inc.php');


for($i=$old_version;$i<count($updates);$i++)
{
	ob_flush();
	if(substr($updates[$i], 0, 7)=='script:')
	{
		$update_script=$GO_CONFIG->root_path.'install/updatescripts/'.substr($updates[$i], 7);
		if (!file_exists($update_script))
		{
			die($update_script.' not found!');
		}
		if(!$quiet)
		echo 'Running '.$update_script.$line_break;
			
		require_once($update_script);

	}else
	{
		//echo 'Excuting: '.$updates[$i].$line_break;
		$db->query($updates[$i]);		
	}

	$GO_CONFIG->save_setting('version', $i+1);
}

if(!$quiet)
{
	if($old_version!=$i)
	{
		echo 'Framework updated from '.$old_version.' to version: '.$i.$line_break;
	}
}


//Upgrade modules
foreach($GO_MODULES->modules as $update_module)
{
	unset($updates);
	$update_file = $GO_CONFIG->module_path.$update_module['id'].'/install/updates.inc.php';
	if(file_exists($update_file))
	{
		require($update_file);

		if(isset($updates))
		{
			//if(!$quiet)
			//echo 'Updating '.$update_module['id'].$line_break;

			for($updates_index=$update_module['version'];$updates_index<count($updates);$updates_index++)
			{
				ob_flush();
				if(strcmp(substr($updates[$updates_index],0,7),'script:')==0)
				{
					$update_script=$GO_CONFIG->module_path.$update_module['id'].'/install/updatescripts/'.substr($updates[$updates_index], 7);
					if (!file_exists($update_script))
					{
						die($update_script.' not found!');
					}
					//if(!$quiet)
					echo 'Running '.$update_script.$line_break;
					
					require_once($update_script);
				}else
				{
					//if(!$quiet)
						//echo 'Excuting: '.$updates[$updates_index].$line_break;

					$db->query($updates[$updates_index]);
				}
				
				$up_module['id']=$update_module['id'];
				$up_module['version']=$updates_index+1;//count($updates);
				$db->update_row('go_modules', 'id', $up_module);
			}
			if(!$quiet)
			{
				if($update_module['version']!=$updates_index)
				{
					echo 'Updated '.$update_module['id'].' from '.$update_module['version'].' to version '.$updates_index.$line_break;
				}
			}				
		}
	}
}

if(isset($RERUN_UPDATE))
{
	$SECOND_RUN=true;
	require(__FILE__);
	$SECOND_RUN=false;
}else
{
	echo 'Database is up to date now!'.$line_break.$line_break;

	if(isset($CHECK_MODULES))
	{
		echo 'Checking modules'.$line_break.$line_break;
		$GO_EVENTS->fire_event('check_database');
	}

	

	echo 'Done!'.$line_break.$line_break;

	$GO_CONFIG->save_setting('upgrade_mtime', $GO_CONFIG->mtime);
	
}

if(empty($SECOND_RUN)){
	ob_end_flush();
}

if(php_sapi_name() != 'cli'){
	echo '</pre>';
}

