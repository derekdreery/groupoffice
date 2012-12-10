<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/*
 *
 * Example usage to sync users/admin folder ( Path is relative from $config['file_storage_path']:
 *
 * php sync_filesystem.php /etc/groupoffice/config.php users/admin
 *
 * Sync all folders:
 *
 * php sync_filesystem.php /etc/groupoffice/config.php
 *
 */

//otherwise log module will log all items as added.
define('NOLOG', true);

//event firing will cause problems with Ioncube
define('NO_EVENTS',true);

if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}

ini_set('max_execution_time', 0);
ini_set('memory_limit','1000M');


chdir(dirname(__FILE__));

require_once("../../../Group-Office.php");

$path = isset($argv[2]) ? $argv[2] : '';

if(php_sapi_name()!='cli' && !$GLOBALS['GO_SECURITY']->has_admin_permission($GLOBALS['GO_SECURITY']->user_id))
{
	die('You must be admin or on the command line');
}

require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
$files = new files();

require_once($GLOBALS['GO_CONFIG']->class_path.'filesystem.class.inc');
$fs = new filesystem();

if(empty($path))
{
	$folders = $fs->get_folders($GLOBALS['GO_CONFIG']->file_storage_path);

	foreach($folders as $folder)
	{
		$dbfolder = $files->resolve_path($files->strip_server_path($folder['path']));
		if($dbfolder){
			echo 'Syncing '.$folder['path']."\n";
			$files->sync_folder($dbfolder, true);
			$files->touch_folder($dbfolder['id'], filemtime($folder['path']));

		}
	}
}else
{
	$dbfolder = $files->resolve_path($path);

	if(!$parent)
	{
		die('Fatal error! could not find database folder of '.$path.' in database. Try to sync without path parameter first.');
	}

	echo 'Syncing '.$path."\n";
	$files->sync_folder($dbfolder, true);
	$files->touch_folder($dbfolder['id'], filemtime($path));
}

echo "Done!\n";
