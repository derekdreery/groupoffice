<?php 
if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));


$updates[]="ALTER TABLE `fs_files` ADD `folder_id` INT NOT NULL AFTER `id`";
$updates[]="ALTER TABLE `fs_files` ADD `name` VARCHAR( 255 ) NOT NULL AFTER `folder_id`";
$updates[]="ALTER TABLE `fs_folders` ADD `name` VARCHAR( 255 ) NOT NULL AFTER `parent_id`";  

require('../../../../Group-Office.php');

$fs = new filesystem();

require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
$fsdb = new files();

function get_file($path, $parent_id)
{
	global $fs, $fsdb, $GO_CONFIG;
	
	$sql = "SELECT * FROM fs_files WHERE path='".$fsdb->escape($path)."';";
	$fsdb->query($sql);
	if($file = $fsdb->next_record())
	{
		$file['name']=utf8_basename($path);
		$file['folder_id']=$parent_id;		
		$fsdb->update_file($file);
		
		return $file['id'];
	}else
	{
		$file['path']=$path;
		$file['name']=utf8_basename($path);
		$file['ctime']=@filectime($GO_CONFIG->file_storage_path.$path);
		$file['mtime']=@filemtime($GO_CONFIG->file_storage_path.$path);
		$file['folder_id']=$parent_id;		
		
		return $fsdb->add_file($file);

	}
}

function get_folder($path, $parent_id)
{
	global $fs, $fsdb, $GO_CONFIG;
	
	$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";
	echo 'Getting folder '.$path.$line_break;
	
	$sql = "SELECT * FROM fs_folders WHERE path='".$fsdb->escape($path)."';";
	$fsdb->query($sql);
	if($folder = $fsdb->next_record())
	{
		$folder['name']=utf8_basename($path);
		$folder['parent_id']=$parent_id;
		$folder['ctime']=@filectime($GO_CONFIG->file_storage_path.$path);
		$fsdb->update_folder($folder);
				
		return $folder['id'];
	}else
	{
		$folder['path']=$path;
		$folder['name']=utf8_basename($path);
		$folder['ctime']=@filectime($GO_CONFIG->file_storage_path.$path);
		$folder['parent_id']=$parent_id;
		return $fsdb->add_folder($folder);
	}
}



function crawl($path, $parent_id)
{
	global $fs, $fsdb;
	
	$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";
	echo 'Crawling folder '.$path.$line_break;
	
	$folder_id = get_folder($fsdb->strip_server_path($path), $parent_id);
	
	$folders = $fs->get_folders($path);
	//var_dump($folders);
	while($folder = array_shift($folders))
	{
		crawl($folder['path'], $folder_id);
	}

	
	$files = $fs->get_files($path);
	while($file = array_shift($files))
	{
		get_file($fsdb->strip_server_path($file['path']),$folder_id);
	}
}

$folders = $fs->get_folders($GO_CONFIG->file_storage_path);

foreach($folders as $folder)
{
	crawl($folder['path'], 0);
}

?>