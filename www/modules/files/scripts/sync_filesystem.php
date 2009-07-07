<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: files.class.inc.php 2763 2009-07-03 08:54:48Z mschering $
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

if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));

require_once("../../../Group-Office.php");

$path = isset($argv[2]) ? $argv[2] : '';

if(php_sapi_name()!='cli' && $GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
{
	die('You must be admin or on the command line');
}

require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
$files = new files();

if(empty($path))
{
	$folders = $fs->get_folders($GO_CONFIG->file_storage_path);

	foreach($folders as $folder)
	{
		echo 'Syncing '.$folder['path']."\n";
		$files->import_folder($folder['path'], 0);
	}
}else
{
	$parent = $files->resolve_path(dirname($path));
	
	if(!$parent)
	{
		die('Fatal error! could not find parent folder of '.$path.' in database. Try to sync without path parameter first.');
	}

	echo 'Syncing '.$path."\n";
	$files->import_folder($GO_CONFIG->file_storage_path.$path, $parent['id']);
}

echo "Done!\n";
