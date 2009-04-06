<?php
$quiet=false;
$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));

require_once('../Group-Office.php');
ini_set('max_execution_time', '3600');

//login event can cause problems
SetCookie("GO_UN","",time()-3600,"/","",!empty($_SERVER['HTTPS']),false);
SetCookie("GO_PW","",time()-3600,"/","",!empty($_SERVER['HTTPS']),false);


if(!$quiet)
echo 'Updating Group-Office database: '.$GO_CONFIG->db_name.$line_break;

$GO_MODULES->load_modules();

$db = new db();
$db->halt_on_error = 'report';

$old_version = $GO_CONFIG->get_setting('version');
if(!$old_version)
$old_version=0;

//if(!$quiet)
//echo 'Updating framework version: '.$old_version.$line_break;
require_once($GO_CONFIG->root_path.'install/sql/updates.inc.php');


for($i=$old_version;$i<count($updates);$i++)
{
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
		@$db->query($updates[$i]);

		//echo 'Excuting: '.$updates[$i].$line_break;
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
$GO_MODULES->get_modules();
while($GO_MODULES->next_record())
{
	unset($updates);
	$update_file = $GO_CONFIG->module_path.$GO_MODULES->f('id').'/install/updates.inc.php';
	if(file_exists($update_file))
	{
		require($update_file);

		if(isset($updates))
		{
			//if(!$quiet)
			//echo 'Updating '.$GO_MODULES->f('id').$line_break;

			for($i=$GO_MODULES->f('version');$i<count($updates);$i++)
			{

				if(substr($updates[$i], 0, 7)=='script:')
				{
					$update_script=$GO_CONFIG->module_path.$GO_MODULES->f('id').'/install/updatescripts/'.substr($updates[$i], 7);
					if (!file_exists($update_script))
					{
						die($update_script.' not found!');
					}
					if(!$quiet)
					echo 'Running '.$update_script.$line_break;

					require_once($update_script);

				}else
				{
					@$db->query($updates[$i]);
					//echo 'Excuting: '.$updates[$i].$line_break;
				}

				$module['id']=$GO_MODULES->f('id');
				$module['version']=count($updates);
				$db->update_row('go_modules', 'id', $module);
			}
			if(!$quiet)
			{
				if($GO_MODULES->f('version')!=$i)
				{
					echo 'Updated '.$GO_MODULES->f('id').' from '.$GO_MODULES->f('version').' to version '.$i.$line_break;
				}
			}
				
		}
	}
}
echo 'Database is up to date now!'.$line_break.$line_break;

if(is_dir($GO_CONFIG->local_path.'cache'))
{
	echo 'Removing cached javascripts from '.$GO_CONFIG->local_path.'cache ...'.$line_break;
	
	require_once($GO_CONFIG->class_path.'filesystem.class.inc');
	$fs = new filesystem();
	
	$fs->delete($GO_CONFIG->local_path.'cache');
	echo 'Done!'.$line_break.$line_break;
}

